<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:student')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // Cast remember to boolean
        $remember = (bool) $request->remember;

        // Coba login sebagai admin
        if (Auth::attempt([
            'email' => $request->username,
            'password' => $request->password
        ], $remember)) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Jika gagal, coba login sebagai siswa
        if (Auth::guard('student')->attempt([
            'nis' => $request->username,
            'password' => $request->password
        ], $remember)) {
            return redirect()->intended(route('student.dashboard'));
        }

        // Jika keduanya gagal
        return back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors(['username' => 'Username atau password salah']);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('student')->check()) {
            Auth::guard('student')->logout();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Tentukan apakah user adalah student atau admin
        if (Auth::guard('student')->check()) {
            $user = Student::find(Auth::guard('student')->id());
        } else {
            $user = User::find(Auth::id());
        }

        if (!$user) {
            return back()->withErrors(['error' => 'User tidak ditemukan']);
        }

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()
            ->back()
            ->with('success', 'Password berhasil diubah');
    }
}
