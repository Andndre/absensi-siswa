<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Attendance::with(['student', 'student.schoolClass']);
        
        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $query->whereDate('attendance_time', $request->date);
        } else {
            // Default: hari ini
            $query->whereDate('attendance_time', Carbon::today());
        }
        
        // Filter berdasarkan kelas
        if ($request->filled('class_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('school_class_id', $request->class_id);
            });
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan nama siswa
        if ($request->filled('search')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }
        
        // Pagination per page
        $defaultPerPage = Setting::get('system.records_per_page', 10);
        $perPage = $request->filled('per_page') ? (int)$request->per_page : $defaultPerPage;
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : $defaultPerPage;
        
        $attendances = $query->orderBy('attendance_time', 'desc')->paginate($perPage);
        $classes = SchoolClass::orderBy('name')->get();
        $students = Student::with('schoolClass')->orderBy('name')->get();
        
        // Statistik untuk hari yang dipilih
        $selectedDate = $request->filled('date') ? $request->date : Carbon::today()->format('Y-m-d');
        $stats = $this->getAttendanceStats($selectedDate, $request->class_id);
        
        return view('attendance.index', compact('attendances', 'classes', 'students', 'stats', 'selectedDate'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpha',
            'notes' => 'nullable|string|max:255'
        ], [
            'student_id.required' => 'Siswa harus dipilih.',
            'student_id.exists' => 'Siswa yang dipilih tidak valid.',
            'attendance_date.required' => 'Tanggal absensi harus diisi.',
            'attendance_date.date' => 'Format tanggal tidak valid.',
            'status.required' => 'Status absensi harus dipilih.',
            'status.in' => 'Status absensi tidak valid.',
            'notes.max' => 'Keterangan maksimal 255 karakter.'
        ]);

        // Gunakan tanggal yang dipilih dengan waktu saat ini
        $attendanceDateTime = Carbon::createFromFormat('Y-m-d', $request->attendance_date)
            ->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second);

        // Cek apakah sudah ada absensi untuk siswa pada tanggal yang sama
        $existingAttendance = Attendance::where('student_id', $request->student_id)
            ->whereDate('attendance_time', $request->attendance_date)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()
                ->with('error', 'Siswa sudah memiliki record absensi pada tanggal tersebut. Gunakan fitur edit jika ingin mengubah status.');
        }

        // Buat record absensi baru
        Attendance::create([
            'student_id' => $request->student_id,
            'attendance_time' => $attendanceDateTime,
            'status' => $request->status,
            'notes' => $request->notes,
            'scanned_by' => auth()->id(), // Mencatat siapa yang menambahkan (manual entry)
            'scan_method' => 'manual' // Menandai bahwa ini adalah input manual
        ]);

        return redirect()->back()
            ->with('success', 'Absensi berhasil ditambahkan secara manual.');
    }

    public function report(Request $request)
    {
        $startDate = $request->filled('start_date') ? $request->start_date : Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : Carbon::now()->format('Y-m-d');
        
        $classes = SchoolClass::orderBy('name')->get();
        $selectedClassId = $request->class_id;
        
        // Ambil data rekap per siswa
        $query = Student::with(['schoolClass', 'attendances' => function($q) use ($startDate, $endDate) {
            $q->whereBetween(DB::raw('DATE(attendance_time)'), [$startDate, $endDate]);
        }]);
        
        if ($selectedClassId) {
            $query->where('school_class_id', $selectedClassId);
        }
        
        $students = $query->orderBy('name')->get();
        
        // Hitung total hari kerja dalam periode
        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        
        // Statistik keseluruhan
        $overallStats = $this->getOverallStats($startDate, $endDate, $selectedClassId);
        
        return view('attendance.report', compact(
            'students', 
            'classes', 
            'startDate', 
            'endDate', 
            'selectedClassId',
            'totalDays',
            'overallStats'
        ));
    }
    
    private function getAttendanceStats($date, $classId = null)
    {
        $baseQuery = Attendance::whereDate('attendance_time', $date);
        
        if ($classId) {
            $baseQuery->whereHas('student', function($q) use ($classId) {
                $q->where('school_class_id', $classId);
            });
        }
        
        $stats = [
            'total' => (clone $baseQuery)->distinct('student_id')->count(),
            'hadir' => (clone $baseQuery)->where('status', 'hadir')->distinct('student_id')->count(),
            'terlambat' => (clone $baseQuery)->where('status', 'terlambat')->distinct('student_id')->count(),
            'izin' => (clone $baseQuery)->where('status', 'izin')->distinct('student_id')->count(),
            'sakit' => (clone $baseQuery)->where('status', 'sakit')->distinct('student_id')->count(),
            'alpha' => (clone $baseQuery)->where('status', 'alpha')->distinct('student_id')->count(),
        ];
        
        // Hitung siswa yang belum absen
        $totalStudents = Student::when($classId, function($q) use ($classId) {
            $q->where('school_class_id', $classId);
        })->count();
        
        $stats['belum_absen'] = $totalStudents - $stats['total'];
        
        return $stats;
    }
    
    private function getOverallStats($startDate, $endDate, $classId = null)
    {
        $baseQuery = Attendance::whereBetween(DB::raw('DATE(attendance_time)'), [$startDate, $endDate]);
        
        if ($classId) {
            $baseQuery->whereHas('student', function($q) use ($classId) {
                $q->where('school_class_id', $classId);
            });
        }
        
        $stats = [
            'total_records' => (clone $baseQuery)->count(),
            'unique_students' => (clone $baseQuery)->distinct('student_id')->count(),
            'hadir' => (clone $baseQuery)->where('status', 'hadir')->count(),
            'terlambat' => (clone $baseQuery)->where('status', 'terlambat')->count(),
            'izin' => (clone $baseQuery)->where('status', 'izin')->count(),
            'sakit' => (clone $baseQuery)->where('status', 'sakit')->count(),
            'alpha' => (clone $baseQuery)->where('status', 'alpha')->count(),
        ];
        
        return $stats;
    }

    /**
     * Update the attendance status.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpha',
        ]);

        $oldStatus = $attendance->status;
        $attendance->update([
            'status' => $request->status
        ]);

        // Refresh stats cache if needed
        $date = $attendance->attendance_time->format('Y-m-d');
        $this->getAttendanceStats($date, $attendance->student->school_class_id);

        return back()->with('success', 'Status absensi berhasil diubah dari ' . ucfirst($oldStatus) . ' menjadi ' . ucfirst($request->status));
    }
    
    /**
     * Show QR scanner page for admin
     */
    public function scanner()
    {
        $classes = SchoolClass::orderBy('name')->get();
        $todayStats = $this->getAttendanceStats(Carbon::today()->format('Y-m-d'));
        
        return view('attendance.scanner', compact('classes', 'todayStats'));
    }
    
    /**
     * Process student QR code scan
     */
    public function scanStudentQr(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpha',
            'notes' => 'nullable|string|max:255'
        ]);
        
        try {
            // Debug log QR code
            Log::info('QR Code yang dipindai: ' . $request->qr_code);
            
            // Cari siswa berdasarkan QR code
            $student = Student::findByQrCode($request->qr_code);
            
            if (!$student) {
                Log::warning('QR Code tidak ditemukan: ' . $request->qr_code);
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak ditemukan atau siswa tidak aktif. QR Code: ' . $request->qr_code
                ]);
            }
            
            // Cek apakah siswa sudah absen hari ini
            $today = Carbon::today();
            $existingAttendance = $student->attendances()
                ->whereDate('attendance_time', $today)
                ->first();
                
            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa ' . $student->name . ' sudah melakukan absensi hari ini dengan status: ' . ucfirst($existingAttendance->status),
                    'student' => [
                        'name' => $student->name,
                        'nis' => $student->nis,
                        'class' => $student->schoolClass->name ?? 'Tidak ada kelas',
                        'existing_status' => $existingAttendance->status,
                        'attendance_time' => $existingAttendance->attendance_time->format('H:i:s')
                    ]
                ]);
            }
            
            // Buat record absensi baru
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'scanned_by' => auth()->id(),
                'student_qr_code' => $request->qr_code,
                'attendance_time' => now(),
                'status' => $request->status,
                'scan_method' => 'student_qr_scan',
                'notes' => $request->notes
            ]);
            
            // Trigger event untuk notifikasi WhatsApp
            event(new \App\Events\AttendanceRecorded($attendance));
            
            Log::info('Absensi berhasil dicatat untuk siswa: ' . $student->name);
            
            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil dicatat',
                'student' => [
                    'name' => $student->name,
                    'nis' => $student->nis,
                    'class' => $student->schoolClass->name ?? 'Tidak ada kelas',
                    'status' => $request->status,
                    'attendance_time' => $attendance->attendance_time->format('H:i:s')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error scanning student QR: ' . $e->getMessage(), [
                'qr_code' => $request->qr_code,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses QR code: ' . $e->getMessage()
            ]);
        }
    }
}
