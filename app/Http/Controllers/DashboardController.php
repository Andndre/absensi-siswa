<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Statistik umum
        $totalStudents = Student::count();
        $totalClasses = SchoolClass::count();
        
        // Absensi hari ini
        $today = Carbon::today();
        $presentToday = Attendance::whereDate('attendance_time', $today)
            ->where('status', 'hadir')
            ->distinct('student_id')
            ->count();
        
        $lateToday = Attendance::whereDate('attendance_time', $today)
            ->where('status', 'terlambat')
            ->distinct('student_id')
            ->count();
            
        $absentToday = $totalStudents - ($presentToday + $lateToday);
        
        // Data absensi terbaru (10 terakhir)
        $recentAttendances = Attendance::with(['student', 'student.schoolClass', 'dailyQrCode'])
            ->orderBy('attendance_time', 'desc')
            ->limit(10)
            ->get();
        
        // Data untuk grafik mingguan (7 hari terakhir)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $presentCount = Attendance::whereDate('attendance_time', $date)
                ->where('status', 'hadir')
                ->distinct('student_id')
                ->count();
            $lateCount = Attendance::whereDate('attendance_time', $date)
                ->where('status', 'terlambat')
                ->distinct('student_id')
                ->count();
            $izinCount = Attendance::whereDate('attendance_time', $date)
                ->where('status', 'izin')
                ->distinct('student_id')
                ->count();
            $sakitCount = Attendance::whereDate('attendance_time', $date)
                ->where('status', 'sakit')
                ->distinct('student_id')
                ->count();
            $alphaCount = Attendance::whereDate('attendance_time', $date)
                ->where('status', 'alpha')
                ->distinct('student_id')
                ->count();
            $weeklyData[] = [
                'date' => $date->format('D'),
                'present' => $presentCount,
                'late' => $lateCount,
                'izin' => $izinCount,
                'sakit' => $sakitCount,
                'alpha' => $alphaCount,
                'total' => $presentCount + $lateCount + $izinCount + $sakitCount + $alphaCount
            ];
        }

        return view('dashboard', compact(
            'totalStudents',
            'totalClasses', 
            'presentToday',
            'lateToday',
            'absentToday',
            'recentAttendances',
            'weeklyData'
        ));
    }
}
