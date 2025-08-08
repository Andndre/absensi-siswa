<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 
        'nis', 
        'email',
        'password',
        'school_class_id', 
        'parent_whatsapp_number',
        'is_active',
        'qr_code'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

        // Auto-generate password dan QR code saat create
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($student) {
            // Generate password default dari NIS jika tidak ada
            if (empty($student->password)) {
                $student->password = Hash::make($student->nis);
            }
            
            // Generate QR code unik jika tidak ada
            if (empty($student->qr_code)) {
                $student->qr_code = $student->generateUniqueQrCode();
            }
        });
        
        static::updating(function ($student) {
            // Update last login saat login
            if ($student->isDirty('remember_token') && !empty($student->remember_token)) {
                $student->last_login_at = now();
            }
        });
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    // Helper methods
    public function getTodayAttendance()
    {
        return $this->attendances()
                   ->whereDate('attendance_time', today())
                   ->first();
    }
    
    public function hasAttendedToday()
    {
        return $this->getTodayAttendance() !== null;
    }
    
    public function getAttendanceStatsForMonth($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        $stats = $this->attendances()
                   ->whereMonth('attendance_time', $month)
                   ->whereYear('attendance_time', $year)
                   ->selectRaw('status, COUNT(*) as count')
                   ->groupBy('status')
                   ->get()
                   ->pluck('count', 'status')
                   ->toArray();
                   
        // Ensure all statuses are present with 0 as default
        return array_merge([
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0
        ], $stats);
    }
    
    // Authentication helpers
    public function isActive()
    {
        return $this->is_active;
    }
    
    public function getDefaultPassword()
    {
        return $this->nis; // Password default adalah NIS
    }
    
    public function resetPasswordToDefault()
    {
        $this->update([
            'password' => Hash::make($this->nis)
        ]);
    }
    
    // QR Code methods
    public function generateUniqueQrCode()
    {
        do {
            $qrCode = 'STD-' . strtoupper(uniqid()) . '-' . $this->nis;
        } while (self::where('qr_code', $qrCode)->exists());
        
        return $qrCode;
    }
    
    public function regenerateQrCode()
    {
        $this->update([
            'qr_code' => $this->generateUniqueQrCode()
        ]);
        
        return $this->qr_code;
    }
    
    public static function findByQrCode($qrCode)
    {
        return self::where('qr_code', $qrCode)->where('is_active', true)->first();
    }
}
