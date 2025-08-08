<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'daily_qr_code_id',
        'scanned_by',
        'student_qr_code',
        'attendance_time', 
        'status',
        'notes',
        'scan_method'
    ];

    protected $casts = [
        'attendance_time' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function dailyQrCode()
    {
        return $this->belongsTo(DailyQrCode::class);
    }
    
    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
    
    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('attendance_time', $date);
    }
    
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }
    
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }
    
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }
    
    public function scopeExcused($query)
    {
        return $query->where('status', 'excused');
    }
    
    // Helper methods
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'present' => 'bg-success',
            'late' => 'bg-warning',
            'absent' => 'bg-danger',
            'excused' => 'bg-info',
            default => 'bg-secondary'
        };
    }
    
    public function getStatusLabel()
    {
        return match($this->status) {
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Tidak Hadir',
            'excused' => 'Izin/Sakit',
            default => 'Tidak Diketahui'
        };
    }
}
