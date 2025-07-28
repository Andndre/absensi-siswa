<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\SchoolClass;
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
        $perPage = $request->filled('per_page') ? (int)$request->per_page : 15;
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 15;
        
        $attendances = $query->orderBy('attendance_time', 'desc')->paginate($perPage);
        $classes = SchoolClass::orderBy('name')->get();
        
        // Statistik untuk hari yang dipilih
        $selectedDate = $request->filled('date') ? $request->date : Carbon::today()->format('Y-m-d');
        $stats = $this->getAttendanceStats($selectedDate, $request->class_id);
        
        return view('attendance.index', compact('attendances', 'classes', 'stats', 'selectedDate'));
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
}
