<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $student = Auth::guard('student')->user();
        
        return view('student.profile.show', compact('student'));
    }
    
    public function update(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'parent_whatsapp_number' => 'nullable|string|max:20',
        ]);
        
        $student->update([
            'name' => $request->name,
            'email' => $request->email,
            'parent_whatsapp_number' => $request->parent_whatsapp_number,
        ]);
        
        return redirect()->route('student.profile')->with('success', 'Profil berhasil diperbarui');
    }
}
