<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyQrCode;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Setting;
use App\Events\AttendanceRecorded;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QrCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display today's QR code for admin
     */
    public function showTodayQr()
    {
        $today = Carbon::today();
        $qrCode = DailyQrCode::forDate($today)->active()->first();
        
        // Get default times from settings
        $defaultStartTime = Setting::get('attendance.start_time', '06:00');
        $defaultEndTime = Setting::get('attendance.end_time', '08:00');
        
        // Statistics for today's QR usage
        $stats = [];
        if ($qrCode) {
            $stats = [
                'total_scans' => $qrCode->attendances()->count(),
                'present' => $qrCode->attendances()->where('status', 'present')->count(),
                'late' => $qrCode->attendances()->where('status', 'late')->count(),
                'last_scan' => $qrCode->attendances()->latest('attendance_time')->first()
            ];
        }
        
        return view('qr-code.today', compact('qrCode', 'stats', 'today', 'defaultStartTime', 'defaultEndTime'));
    }
    
    /**
     * Generate or refresh today's QR code
     */
    public function generateTodayQr(Request $request)
    {
        $request->validate([
            'valid_from' => 'nullable|date_format:H:i',
            'valid_until' => 'nullable|date_format:H:i|after:valid_from'
        ]);
        
        $qrCode = DailyQrCode::generateForToday(Auth::id());
        
        // Update waktu berlaku jika disediakan
        if ($request->filled('valid_from') || $request->filled('valid_until')) {
            $qrCode->update([
                'valid_from' => $request->valid_from ?? $qrCode->valid_from,
                'valid_until' => $request->valid_until ?? $qrCode->valid_until,
            ]);
        }
        
        return redirect()->route('admin.qr-code.today')
                        ->with('success', 'QR Code berhasil di-generate/refresh!');
    }
    
    /**
     * Deactivate today's QR code
     */
    public function deactivateQr()
    {
        $today = Carbon::today();
        $qrCode = DailyQrCode::forDate($today)->active()->first();
        
        if ($qrCode) {
            $qrCode->update(['is_active' => false]);
            return redirect()->route('admin.qr-code.today')
                            ->with('success', 'QR Code berhasil dinonaktifkan!');
        }
        
        return redirect()->route('admin.qr-code.today')
                        ->with('error', 'QR Code tidak ditemukan!');
    }
    
    /**
     * Student scan QR code endpoint
     */
    public function scanQr(Request $request, $token)
    {
        // Cari QR code berdasarkan token
        $qrCode = DailyQrCode::where('qr_token', $token)->first();
        
        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid!'
            ], 404);
        }
        
        // Validasi apakah QR code masih berlaku
        if (!$qrCode->isValidNow()) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah tidak berlaku atau belum waktunya!'
            ], 400);
        }
        
        // Jika request GET, tampilkan form scan
        if ($request->isMethod('GET')) {
            return view('qr-code.scan', compact('qrCode'));
        }
        
        // Jika request POST, proses absensi
        return $this->processAttendance($request, $qrCode);
    }
    
    /**
     * Process attendance from QR scan
     */
    private function processAttendance(Request $request, DailyQrCode $qrCode)
    {
        $request->validate([
            'nis' => 'required|string|exists:students,nis'
        ]);
        
        $student = Student::where('nis', $request->nis)->first();
        
        // Cek apakah siswa sudah absen hari ini
        if ($student->hasAttendedToday()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi hari ini!'
            ], 400);
        }
        
        // Tentukan status berdasarkan waktu scan
        $now = Carbon::now();
        $lateThreshold = Carbon::createFromFormat('H:i:s', '07:30:00'); // Batas terlambat
        
        $status = $now->format('H:i:s') <= $lateThreshold->format('H:i:s') ? 'present' : 'late';
        
        // Buat record absensi
        $attendance = Attendance::create([
            'student_id' => $student->id,
            'daily_qr_code_id' => $qrCode->id,
            'attendance_time' => $now,
            'status' => $status,
            'scan_method' => 'qr_code',
            'notes' => 'Absen via QR Code scan'
        ]);
        
        // Trigger event untuk mengirim notifikasi WhatsApp
        event(new AttendanceRecorded($attendance));
        
        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat!',
            'data' => [
                'student' => $student->name,
                'nis' => $student->nis,
                'class' => $student->schoolClass->name ?? 'Tidak ada kelas',
                'time' => $attendance->attendance_time->format('H:i:s'),
                'status' => $attendance->getStatusLabel(),
                'date' => $attendance->attendance_time->format('d/m/Y')
            ]
        ]);
    }
    
    /**
     * QR Code history and management
     */
    public function history(Request $request)
    {
        $query = DailyQrCode::with(['creator', 'attendances'])
                           ->withCount('attendances');
        
        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        
        $qrCodes = $query->orderBy('date', 'desc')->paginate(15);
        
        return view('qr-code.history', compact('qrCodes'));
    }
}
