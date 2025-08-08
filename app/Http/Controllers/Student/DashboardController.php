<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

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
            ->with('scannedBy')
            ->orderBy('attendance_time', 'desc')
            ->limit(10)
            ->get();
        
        return view('student.dashboard', compact('stats', 'todayAttendance', 'recentAttendances'));
    }
}
