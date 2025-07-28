<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DailyQrCode extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'date',
        'qr_token',
        'valid_from',
        'valid_until',
        'is_active',
        'refresh_count',
        'created_by'
    ];
    
    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean'
    ];
    
    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
    
    public function scopeCurrentlyValid($query)
    {
        $now = Carbon::now()->format('H:i:s');
        return $query->where('valid_from', '<=', $now)
                    ->where('valid_until', '>=', $now);
    }
    
    // Methods
    public static function generateForToday($userId)
    {
        $today = Carbon::today();
        
        // Cek apakah sudah ada QR code untuk hari ini
        $existing = self::forDate($today)->first();
        
        if ($existing) {
            // Refresh existing QR code
            $existing->update([
                'qr_token' => Str::uuid(),
                'refresh_count' => $existing->refresh_count + 1,
                'is_active' => true
            ]);
            return $existing;
        }
        
        // Buat QR code baru
        return self::create([
            'date' => $today,
            'qr_token' => Str::uuid(),
            'valid_from' => '06:00:00',
            'valid_until' => '08:00:00',
            'is_active' => true,
            'refresh_count' => 0,
            'created_by' => $userId
        ]);
    }
    
    public function isValidNow()
    {
        if (!$this->is_active) {
            return false;
        }
        
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        
        // Bandingkan string time langsung
        return $currentTime >= $this->valid_from && $currentTime <= $this->valid_until;
    }
    
    /**
     * Check if QR can still be used for attendance (even if late)
     * Returns true until end of day or mark_alpha_at time
     * Assumes is_active has already been checked
     */
    public function canStillScan()
    {
        $currentTime = Carbon::now()->format('H:i:s');
        $markAlphaAt = config('attendance.mark_alpha_at', '23:00:00');
        
        // Debug logging
        Log::info('canStillScan Debug', [
            'qr_id' => $this->id,
            'is_active' => $this->is_active,
            'current_time' => $currentTime,
            'valid_from' => $this->valid_from,
            'mark_alpha_at' => $markAlphaAt,
            'time_after_start' => $currentTime >= $this->valid_from,
            'time_before_end' => $currentTime <= $markAlphaAt
        ]);
        
        // QR masih bisa digunakan dari valid_from sampai jam mark_alpha_at
        return $currentTime >= $this->valid_from && $currentTime <= $markAlphaAt;
    }
    
    public function getQrCodeUrl()
    {
        // Generate URL untuk QR code
        return route('attendance.scan', ['token' => $this->qr_token]);
    }
    
    /**
     * Get the attendance status that should be given based on current time
     */
    public function getAttendanceStatusForCurrentTime()
    {
        $currentTime = Carbon::now()->format('H:i:s');
        $onTimeUntil = config('attendance.on_time_until', '07:30:00');
        $statusMode = config('attendance.status_mode', 'strict');
        
        if ($statusMode === 'flexible') {
            // Mode flexible: selama QR masih bisa discan
            if ($this->isValidNow()) {
                // Masih dalam periode optimal QR
                return 'hadir';
            } elseif ($this->canStillScan()) {
                // Sudah lewat periode optimal tapi masih bisa scan
                return 'terlambat';
            } else {
                // Sudah tidak bisa scan sama sekali
                return 'alpha';
            }
        } else {
            // Mode strict: berdasarkan waktu mutlak
            if ($currentTime <= $onTimeUntil) {
                return 'hadir';        // Sebelum jam batas = hadir
            } else {
                return 'terlambat';    // Setelah jam batas = terlambat
            }
        }
    }
    
    /**
     * Check if attendance is still possible (before mark as alpha time)
     */
    public function canStillAttend()
    {
        $currentTime = Carbon::now()->format('H:i:s');
        $markAlphaAt = config('attendance.mark_alpha_at', '23:00:00');
        
        return $currentTime < $markAlphaAt;
    }
}
