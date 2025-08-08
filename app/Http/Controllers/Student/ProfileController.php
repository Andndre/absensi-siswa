<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $student = Student::find(Auth::guard('student')->id());
        
        if (!$student) {
            abort(404, 'Student not found');
        }
        
        return view('student.profile.show', compact('student'));
    }
    
    public function update(Request $request)
    {
        $student = Student::find(Auth::guard('student')->id());
        
        if (!$student) {
            abort(404, 'Student not found');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_whatsapp_number' => 'nullable|string|max:20',
        ]);
        
        $student->update([
            'name' => $request->name,
            'parent_whatsapp_number' => $request->parent_whatsapp_number,
        ]);
        
        return redirect()->route('student.profile')->with('success', 'Profil berhasil diperbarui');
    }
}
