<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
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
        $query = Student::with('schoolClass');
        
        // Filter berdasarkan kelas
        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }
        
        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }
        
        // Pagination per page
        $perPage = $request->filled('per_page') ? (int)$request->per_page : 15;
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 15;
        
        $students = $query->orderBy('name')->paginate($perPage);
        $classes = SchoolClass::orderBy('name')->get();
        
        return view('students.index', compact('students', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $classes = SchoolClass::orderBy('name')->get();
        $selectedClassId = $request->get('class_id');
        
        return view('students.create', compact('classes', 'selectedClassId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:students,nis',
            'email' => 'nullable|email|unique:students,email',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'parent_whatsapp_number' => 'required|string|max:20',
        ]);

        Student::create([
            'name' => $request->name,
            'nis' => $request->nis,
            'email' => $request->email ?: $request->nis . '@student.sekolah.id',
            'password' => Hash::make($request->nis), // Default password = NIS
            'school_class_id' => $request->school_class_id,
            'parent_whatsapp_number' => $request->parent_whatsapp_number,
            'is_active' => true,
        ]);

        return redirect()->route('admin.students.index')
                        ->with('success', 'Siswa berhasil ditambahkan. Password default: NIS siswa.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student->load(['schoolClass', 'attendances' => function($query) {
            $query->orderBy('attendance_time', 'desc')->limit(10);
        }]);
        
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('students.edit', compact('student', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => ['required', 'string', 'max:20', Rule::unique('students')->ignore($student->id)],
            'email' => ['nullable', 'email', Rule::unique('students')->ignore($student->id)],
            'school_class_id' => 'nullable|exists:school_classes,id',
            'parent_whatsapp_number' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'name' => $request->name,
            'nis' => $request->nis,
            'email' => $request->email ?: $request->nis . '@student.sekolah.id',
            'school_class_id' => $request->school_class_id,
            'parent_whatsapp_number' => $request->parent_whatsapp_number,
            'is_active' => $request->has('is_active'),
        ];

        $student->update($updateData);

        return redirect()->route('admin.students.index')
                        ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        
        return redirect()->route('admin.students.index')
                        ->with('success', 'Siswa berhasil dihapus.');
    }
    
    /**
     * Reset password siswa ke NIS
     */
    public function resetPassword(Student $student)
    {
        $student->resetPasswordToDefault();
        
        return redirect()->back()
                        ->with('success', 'Password siswa berhasil direset ke NIS: ' . $student->nis);
    }
    
    /**
     * Toggle active status siswa
     */
    public function toggleStatus(Student $student)
    {
        $student->update([
            'is_active' => !$student->is_active
        ]);
        
        $status = $student->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
                        ->with('success', 'Status siswa berhasil ' . $status);
    }
}
