<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyQrCode;
use App\Models\Attendance;
use App\Events\AttendanceRecorded;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    /**
     * Show student dashboard
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        
        // Get attendance statistics using direct query
        $attendanceQuery = Attendance::where('student_id', $student->id);
        
        $stats = [
            'hadir' => (clone $attendanceQuery)->where('status', 'hadir')->count(),
            'terlambat' => (clone $attendanceQuery)->where('status', 'terlambat')->count(),
            'izin' => (clone $attendanceQuery)->where('status', 'izin')->count(),
            'sakit' => (clone $attendanceQuery)->where('status', 'sakit')->count(),
            'alpha' => (clone $attendanceQuery)->where('status', 'alpha')->count(),
        ];
        
        // Check if already attended today
        $todayAttendance = Attendance::where('student_id', $student->id)
            ->whereDate('attendance_time', today())
            ->with('scannedBy')
            ->first();
        
        // Get recent attendances (last 10)
        $recentAttendances = Attendance::where('student_id', $student->id)
            ->with(['dailyQrCode', 'scannedBy'])
            ->orderBy('attendance_time', 'desc')
            ->limit(10)
            ->get();
        
        return view('student.dashboard', compact('stats', 'todayAttendance', 'recentAttendances'));
    }

    /**
     * Handle QR code scanning for attendance
     */
    public function scanQr(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $student = Auth::guard('student')->user();
        
        // Check if already attended today
        $todayAttendance = Attendance::where('student_id', $student->id)
            ->whereDate('attendance_time', today())
            ->first();
            
        if ($todayAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi hari ini.'
            ]);
        }

        // Extract token from QR code (could be URL or direct token)
        $qrToken = $request->qr_code;
        
        // If QR code contains URL, extract the token
        if (preg_match('/\/scan\/([a-f0-9-]+)/', $qrToken, $matches)) {
            $qrToken = $matches[1];
        }

        // Find QR code using qr_token (don't filter by is_active yet)
        $qrCode = DailyQrCode::where('qr_token', $qrToken)
            ->where('date', today())
            ->first();

        // Debug logging
        Log::info('QR Scan Debug', [
            'scanned_token' => $qrToken,
            'today' => today()->format('Y-m-d'),
            'qr_found' => $qrCode ? 'yes' : 'no',
            'qr_data' => $qrCode ? [
                'id' => $qrCode->id,
                'is_active' => $qrCode->is_active,
                'valid_from' => $qrCode->valid_from,
                'valid_until' => $qrCode->valid_until,
                'can_still_scan' => $qrCode->canStillScan(),
                'current_time' => now()->format('H:i:s')
            ] : null
        ]);

        if (!$qrCode) {
            // Try to find any QR for today to debug
            $anyQrToday = DailyQrCode::where('date', today())->first();
            
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak ditemukan untuk hari ini.',
                'debug' => [
                    'scanned_token' => $qrToken,
                    'today' => today()->format('Y-m-d'),
                    'any_qr_today' => $anyQrToday ? [
                        'id' => $anyQrToday->id,
                        'token' => $anyQrToday->qr_token,
                        'is_active' => $anyQrToday->is_active
                    ] : 'none'
                ]
            ]);
        }

        // Check if QR is active
        if (!$qrCode->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code telah dinonaktifkan oleh admin.'
            ]);
        }

        // Check if QR can still be used (even if outside normal hours)
        if (!$qrCode->canStillScan()) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah tidak bisa digunakan. Waktu absensi telah berakhir.',
                'debug' => [
                    'current_time' => now()->format('H:i:s'),
                    'valid_from' => $qrCode->valid_from,
                    'valid_until' => $qrCode->valid_until,
                    'mark_alpha_at' => config('attendance.mark_alpha_at', '23:00:00'),
                    'can_still_scan' => $qrCode->canStillScan()
                ]
            ]);
        }

        // Determine attendance status based on time using QR code model
        $now = now();
        $status = $qrCode->getAttendanceStatusForCurrentTime();

        // Create attendance record
        $attendance = Attendance::create([
            'student_id' => $student->id,
            'daily_qr_code_id' => $qrCode->id,
            'attendance_time' => $now,
            'status' => $status,
            'scan_method' => 'qr_code',
            'notes' => 'Absensi via QR Code scan'
        ]);

        // Trigger event untuk mengirim notifikasi WhatsApp
        event(new AttendanceRecorded($attendance));

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat!',
            'status' => $status,
            'time' => $now->format('H:i:s'),
            'attendance_id' => $attendance->id
        ]);
    }
}
