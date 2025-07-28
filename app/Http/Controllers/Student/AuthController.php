<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\DailyQrCode;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:student')->except(['logout', 'showChangePasswordForm', 'changePassword']);
    }
    
    /**
     * Show student login form
     */
    public function showLoginForm()
    {
        return view('student.login');
    }
    
    /**
     * Handle student login
     */
    public function login(Request $request)
    {
        $request->validate([
            'nis' => 'required|string',
            'password' => 'required|string',
        ]);
        
        $student = Student::where('nis', $request->nis)->first();
        
        if (!$student) {
            return back()->withErrors([
                'nis' => 'NIS tidak ditemukan.',
            ])->withInput($request->only('nis'));
        }
        
        if (!$student->is_active) {
            return back()->withErrors([
                'nis' => 'Akun Anda tidak aktif. Hubungi admin.',
            ])->withInput($request->only('nis'));
        }
        
        if (!Hash::check($request->password, $student->password)) {
            return back()->withErrors([
                'password' => 'Password salah.',
            ])->withInput($request->only('nis'));
        }
        
        // Login berhasil
        Auth::guard('student')->login($student, $request->filled('remember'));
        
        // Update last login using save method
        $student->last_login_at = now();
        $student->save();
        
        $request->session()->regenerate();
        
        return redirect()->intended(route('student.dashboard'));
    }
    
    /**
     * Handle student logout
     */
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('student.login')
                        ->with('success', 'Anda berhasil logout.');
    }
    
    /**
     * Show change password form
     */
    public function showChangePasswordForm()
    {
        return view('student.change-password');
    }
    
    /**
     * Handle change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        
        $studentFromAuth = Auth::guard('student')->user();
        
        // Ambil student dari database untuk memastikan semua method Eloquent tersedia
        $student = Student::find($studentFromAuth->id);
        
        if (!Hash::check($request->current_password, $student->password)) {
            return back()->withErrors([
                'current_password' => 'Password lama tidak sesuai.',
            ]);
        }
        
        // Update password using update method
        $student->update([
            'password' => Hash::make($request->new_password)
        ]);
        
        return redirect()->route('student.dashboard')
                        ->with('success', 'Password berhasil diubah!');
    }
}
