<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Setting;

class SchoolClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SchoolClass::withCount('students');
        
        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $perPage = Setting::get('system.records_per_page', 10);
        $classes = $query->orderBy('name')->paginate($perPage);
        
        return view('classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('classes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:school_classes,name',
        ]);

        SchoolClass::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.classes.index')
                        ->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolClass $class)
    {
        $students = $class->students()->orderBy('name')->paginate(10);
        $studentsCount = $class->students()->count();
        
        // Get attendance statistics for this class
        $attendanceStats = [
            'present' => \App\Models\Attendance::whereHas('student', function($query) use ($class) {
                $query->where('school_class_id', $class->id);
            })->where('status', 'present')->count(),
            
            'late' => \App\Models\Attendance::whereHas('student', function($query) use ($class) {
                $query->where('school_class_id', $class->id);
            })->where('status', 'late')->count(),
            
            'absent' => \App\Models\Attendance::whereHas('student', function($query) use ($class) {
                $query->where('school_class_id', $class->id);
            })->where('status', 'absent')->count(),
            
            'excused' => \App\Models\Attendance::whereHas('student', function($query) use ($class) {
                $query->where('school_class_id', $class->id);
            })->where('status', 'excused')->count(),
        ];
        
        return view('classes.show', compact('class', 'students', 'studentsCount', 'attendanceStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolClass $class)
    {
        return view('classes.edit', compact('class'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchoolClass $class)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:school_classes,name,' . $class->id,
        ]);

        $class->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.classes.index')
                        ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolClass $class)
    {
        // Check if there are students in this class
        if ($class->students()->count() > 0) {
            return redirect()->route('admin.classes.index')
                            ->with('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa.');
        }
        
        $class->delete();
        
        return redirect()->route('admin.classes.index')
                        ->with('success', 'Kelas berhasil dihapus.');
    }
}
