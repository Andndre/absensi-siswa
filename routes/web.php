<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Custom Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/home', function() {
    if (auth()->guard('web')->check()) {
        return redirect()->route('admin.dashboard');
    } else if (auth()->guard('student')->check()) {
        return redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Attendance Routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    
    // QR Scanner Routes for Admin
    Route::get('/scanner', [AttendanceController::class, 'scanner'])->name('scanner');
    Route::post('/scan-student-qr', [AttendanceController::class, 'scanStudentQr'])->name('scan-student-qr');
    
    // Student Routes
    Route::resource('students', StudentController::class);
    
    // Student Import/Export Routes
    Route::get('/students/template/download', [StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::post('/students/export', [StudentController::class, 'export'])->name('students.export');
    
    // School Class Routes
    Route::resource('classes', SchoolClassController::class);
    
    // WhatsApp Routes
    Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
        Route::get('/', [WhatsAppController::class, 'index'])->name('index');
        Route::post('/test', [WhatsAppController::class, 'testConnection'])->name('test');
        Route::post('/manual', [WhatsAppController::class, 'sendManualNotification'])->name('manual');
        Route::post('/broadcast', [WhatsAppController::class, 'sendBroadcast'])->name('broadcast');
    });
    
    // Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/whatsapp', [SettingsController::class, 'updateWhatsApp'])->name('whatsapp');
        Route::put('/school', [SettingsController::class, 'updateSchool'])->name('school');
        Route::put('/system', [SettingsController::class, 'updateSystem'])->name('system');
        Route::post('/test-whatsapp', [SettingsController::class, 'testWhatsApp'])->name('test-whatsapp');
    });
});

// Student Routes
Route::prefix('student')->name('student.')->group(function () {
    // Protected Student Routes
    Route::middleware('student.auth')->group(function () {
        // Dashboard
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        
        // Profile Management
        Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
        
        // Change Password
        Route::get('/change-password', [App\Http\Controllers\Student\AuthController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('/change-password', [App\Http\Controllers\Student\AuthController::class, 'changePassword'])->name('change-password.submit');
    });
});
