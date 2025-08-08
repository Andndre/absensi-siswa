# Sistem Absensi QR Code Individual - Dokumentasi Lengkap

## Overview Sistem

Sistem absensi ini menggunakan **QR Code individual** untuk setiap siswa. Setiap siswa memiliki QR Code unik yang tidak berubah dan dapat digunakan untuk absensi setiap hari.

### Fitur Utama:
- ✅ QR Code individual per siswa (permanent)
- ✅ Scanner QR untuk admin/guru  
- ✅ Dashboard siswa dengan QR masing-masing
- ✅ Import/Export data siswa Excel
- ✅ Notifikasi WhatsApp ke orang tua
- ✅ Responsive design (mobile-friendly)
- ✅ Multi-guard authentication (admin & siswa)

## Alur Absensi

### 1. **Admin/Guru Melakukan Scan**
- Buka halaman Scanner QR (`/admin/scanner`)
- Scan QR Code siswa menggunakan kamera
- Sistem otomatis mencatat absensi berdasarkan waktu scan

### 2. **Siswa Menampilkan QR**
- Login ke dashboard siswa (`/student/dashboard`)
- QR Code individual ditampilkan di dashboard
- Siswa bisa download/print QR untuk kemudahan

### 3. **Notifikasi Otomatis**
- Setelah absensi tercatat, sistem mengirim notifikasi WhatsApp ke orang tua
- Berisi informasi: nama siswa, status absensi, waktu, dan tanggal

## Konfigurasi Waktu Absensi

File konfigurasi: `config/attendance.php`

- `start_time`: Waktu mulai absensi (default: 06:00:00)
- `on_time_until`: Batas waktu absensi tepat waktu (default: 07:30:00)
- `end_time`: Batas akhir absensi (default: 08:30:00)
- `mark_alpha_at`: Jam berapa sistem menandai alpha (default: 23:00:00)
- `working_days`: Hari kerja (1=Senin, 7=Minggu)

## Manajemen Data Siswa

### Import Data Excel
1. Klik tombol **"Import Excel"** di halaman Manajemen Siswa
2. Download template Excel yang disediakan
3. Isi data siswa sesuai format: Nama, NIS, Kelas, No. WhatsApp Ortu
4. Upload file Excel
5. Pilih opsi:
   - ☑️ Update data jika NIS sudah ada
   - ☑️ Buat kelas otomatis jika tidak ditemukan

### Export Data Excel
1. Klik tombol **"Export Excel"** di halaman Manajemen Siswa
2. Pilih filter:
   - Semua kelas atau kelas tertentu
   - Sertakan siswa tidak aktif (opsional)
3. Download file Excel dengan format professional

### QR Code Management
- Setiap siswa otomatis mendapat QR Code unik saat didaftarkan
- Format QR: `student_{NIS}_{random_string}`
- QR Code dapat di-download/print dalam format PNG professional
- QR Code bersifat permanent (tidak berubah)

## Proses Otomatis

### 1. Marking Alpha Students
Setiap hari pada jam 23:00 (atau sesuai konfigurasi), sistem akan:
- Mengecek semua siswa yang belum memiliki record absensi hari itu
- Menandai mereka sebagai "alpha"
- Hanya pada hari kerja (default: Senin-Sabtu)

### 2. WhatsApp Notifications
Sistem otomatis mengirim notifikasi WhatsApp kepada orang tua saat:
- Siswa melakukan absensi (Hadir/Terlambat)
- Admin mencatat izin/sakit manual
- Menggunakan queue system untuk performa optimal

### 3. Command Manual
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

## Authentication System

### Multi-Guard Setup
- **Admin Guard**: Login dengan email + password
- **Student Guard**: Login dengan NIS + password (default: NIS)

### Login URLs
- Admin: `/login` 
- Student: `/login` (auto-detect berdasarkan format input)

### Password Policy
- Admin: Password custom yang strong
- Siswa: Default password = NIS (dapat diubah di profile)

## WhatsApp Integration

### Setup Fonnte API
1. Daftar akun di [Fonnte.com](https://fonnte.com)
2. Dapatkan API Token
3. Update konfigurasi di Settings > WhatsApp
4. Test koneksi untuk memastikan berfungsi

### Format Notifikasi
```
🎓 ABSENSI SISWA

Nama: [Nama Siswa]
NIS: [NIS]
Kelas: [Kelas]
Status: [Status Absensi]
Waktu: [Jam Absensi]
Tanggal: [Tanggal]

---
Sistem Absensi [Nama Sekolah]
```

## Technical Architecture

### Database Tables
- `users`: Admin accounts
- `students`: Student accounts with QR codes
- `school_classes`: Class management
- `attendances`: Daily attendance records
- `settings`: System configurations

### Key Routes
- `/admin/scanner`: QR Scanner page
- `/admin/students`: Student management
- `/admin/attendance`: Attendance reports
- `/student/dashboard`: Student dashboard with QR

### Dependencies
- Laravel 10.x
- Bootstrap 5
- SimpleSoftwareIO QR Code
- PhpSpreadsheet (Excel)
- Fonnte WhatsApp API

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

## Setup Instructions

### 1. Installation
```bash
# Clone repository
git clone [repository-url]
cd absensi-siswa

# Install dependencies
composer install
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate
```

### 2. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed initial data
php artisan db:seed
```

### 3. Configuration
1. Update `.env` dengan database credentials
2. Configure WhatsApp API di Settings > WhatsApp
3. Set attendance time configurations
4. Configure queue driver (database/redis)

### 4. Production Deployment
```bash
# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Setup queue worker
php artisan queue:work --daemon

# Setup scheduler (cron job)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Troubleshooting

### Common Issues

1. **QR Scanner tidak berfungsi**
   - Pastikan akses kamera diizinkan
   - Gunakan HTTPS untuk production
   - Check browser compatibility

2. **WhatsApp notifikasi gagal**
   - Verify Fonnte API token
   - Check WhatsApp number format (08xxx)
   - Monitor queue jobs

3. **Import Excel error**
   - Pastikan format sesuai template
   - Check file encoding (UTF-8)
   - Verify class names exist

4. **Login siswa gagal**
   - Default password = NIS
   - Check student account status (active)
   - Verify NIS format

### Performance Tips

1. **Use Queue for WhatsApp**
   ```bash
   php artisan queue:work --timeout=60
   ```

2. **Database Indexing**
   - Index pada student.nis
   - Index pada attendance.attendance_time
   - Index pada attendance.student_id

3. **Cache Configuration**
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

## Support & Maintenance

### Backup Procedures
- Daily database backup
- Export attendance data monthly
- Backup QR codes dan student photos

### Monitoring
- Monitor queue jobs status
- Check WhatsApp API quota
- Monitor disk space untuk logs

### Updates
- Regular Laravel security updates
- Monitor dependencies vulnerabilities
- Test QR scanner compatibility dengan browser baru
