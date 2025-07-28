# Sistem Absensi QR Code - Dokumentasi

## Cara Kerja Sistem Status Absensi

Sistem absensi ini menggunakan 5 status utama:
- **Hadir**: Siswa scan QR sebelum jam {{ config('attendance.on_time_until', '07:30:00') }}
- **Terlambat**: Siswa scan QR setelah jam {{ config('attendance.on_time_until', '07:30:00') }}
- **Izin**: Diinput manual oleh admin untuk siswa yang izin
- **Sakit**: Diinput manual oleh admin untuk siswa yang sakit
- **Alpha**: Siswa yang tidak melakukan absensi sama sekali (otomatis di-mark sistem)

## Konfigurasi Waktu Absensi

File konfigurasi: `config/attendance.php`

- `start_time`: Waktu mulai absensi (default: 06:00:00)
- `on_time_until`: Batas waktu absensi tepat waktu (default: 07:30:00)
- `end_time`: Batas akhir absensi (default: 08:30:00)
- `mark_alpha_at`: Jam berapa sistem menandai alpha (default: 23:00:00)
- `working_days`: Hari kerja (1=Senin, 7=Minggu)

## Proses Otomatis

### 1. Marking Alpha Students
Setiap hari pada jam 23:00 (atau sesuai konfigurasi), sistem akan:
- Mengecek semua siswa yang belum memiliki record absensi hari itu
- Menandai mereka sebagai "alpha"
- Hanya pada hari kerja (default: Senin-Sabtu)

### 2. Command Manual
Admin dapat menjalankan command manual:
```bash
php artisan attendance:mark-absent [tanggal]
```

Contoh:
```bash
# Mark alpha untuk hari ini
php artisan attendance:mark-absent

# Mark alpha untuk tanggal tertentu
php artisan attendance:mark-absent 2025-07-25
```

## Status Timeline Siswa

1. **06:00 - 07:30**: Scan QR = Status "Hadir"
2. **07:30 - 08:30**: Scan QR = Status "Terlambat" 
3. **08:30 - 23:00**: Scan QR = Status "Terlambat" (masih bisa scan)
4. **23:00**: Sistem otomatis mark yang belum absen = "Alpha"

## Scheduler Laravel

Sistem ini menggunakan Laravel Scheduler. Untuk menjalankan di production:

1. Tambahkan cron job di server:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

2. Atau untuk development, jalankan:
```bash
php artisan schedule:work
```

## Manual Override

Admin tetap bisa mengubah status absensi secara manual melalui dashboard admin jika diperlukan.
