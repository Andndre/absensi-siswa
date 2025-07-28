<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Attendance Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the attendance settings for your application.
    |
    */

    // Waktu mulai absensi (format H:i:s)
    'start_time' => '06:00:00',
    
    // Waktu batas absensi tepat waktu (format H:i:s)
    'on_time_until' => '07:30:00',
    
    // Waktu batas akhir absensi (setelah ini dianggap alpha jika QR tidak aktif)
    'end_time' => '08:30:00',
    
    // Jam berapa sistem menandai siswa yang tidak absen sebagai alpha
    'mark_alpha_at' => '23:00:00',
    
    // Hari kerja (1 = Senin, 7 = Minggu)
    'working_days' => [1, 2, 3, 4, 5, 6], // Senin - Sabtu
    
    // Mode perhitungan status
    // 'strict' = berdasarkan waktu mutlak (setelah 07:30 = terlambat)
    // 'flexible' = berdasarkan periode QR aktif (selama QR aktif = hadir)
    'status_mode' => 'flexible',
];
