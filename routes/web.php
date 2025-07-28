<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Student\AuthController as StudentAuthController;
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
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', [DashboardController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Public QR Code Scanning Routes (No Auth Required)
Route::get('/scan/{token}', [QrCodeController::class, 'scanQr'])->name('attendance.scan');
Route::post('/scan/{token}', [QrCodeController::class, 'scanQr'])->name('attendance.scan.process');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Attendance Routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    
    // QR Code Management Routes
    Route::prefix('qr-code')->name('qr-code.')->group(function () {
        Route::get('/today', [QrCodeController::class, 'showTodayQr'])->name('today');
        Route::post('/generate', [QrCodeController::class, 'generateTodayQr'])->name('generate');
        Route::post('/deactivate', [QrCodeController::class, 'deactivateQr'])->name('deactivate');
        Route::get('/history', [QrCodeController::class, 'history'])->name('history');
    });
    
    // Student Routes
    Route::resource('students', StudentController::class);
    
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
        Route::put('/attendance', [SettingsController::class, 'updateAttendance'])->name('attendance');
        Route::put('/system', [SettingsController::class, 'updateSystem'])->name('system');
        Route::post('/test-whatsapp', [SettingsController::class, 'testWhatsApp'])->name('test-whatsapp');
    });
});

// Student Routes
Route::prefix('student')->name('student.')->group(function () {
    // Authentication Routes
    Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
    
    // Protected Student Routes
    Route::middleware('student.auth')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::post('/scan-qr', [StudentDashboardController::class, 'scanQr'])->name('scan-qr');
        
        // Debug route
        Route::get('/debug-qr', function() {
            $qr = \App\Models\DailyQrCode::where('date', today())->first();
            return response()->json([
                'qr_exists' => $qr ? 'yes' : 'no',
                'qr_data' => $qr ? [
                    'id' => $qr->id,
                    'qr_token' => $qr->qr_token,
                    'is_active' => $qr->is_active,
                    'valid_from' => $qr->valid_from,
                    'valid_until' => $qr->valid_until,
                    'current_time' => now()->format('H:i:s'),
                    'isValidNow' => $qr->isValidNow(),
                    'canStillScan' => $qr->canStillScan()
                ] : null
            ]);
        })->name('debug-qr');
        
        // Change Password Routes
        Route::get('/change-password', [StudentAuthController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('/change-password', [StudentAuthController::class, 'changePassword'])->name('change-password.submit');
    });
});
