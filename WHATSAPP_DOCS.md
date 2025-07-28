# Sistem Notifikasi WhatsApp - Dokumentasi

## Overview
Sistem ini secara otomatis mengirim notifikasi WhatsApp ke orang tua siswa setelah melakukan absensi menggunakan API Fonnte.

## Fitur
- ✅ Notifikasi otomatis setelah absensi (hadir, terlambat, izin, sakit, alpha)
- ✅ Test koneksi WhatsApp
- ✅ Kirim pesan manual ke orang tua tertentu
- ✅ Broadcast pengumuman ke semua/filter kelas
- ✅ Queue system untuk menghindari timeout
- ✅ Logging untuk tracking pesan

## Setup dan Konfigurasi

### 1. Daftar Akun Fonnte
1. Kunjungi https://fonnte.com
2. Daftar akun baru
3. Verifikasi email dan nomor WhatsApp
4. Login ke dashboard Fonnte

### 2. Dapatkan Token API
1. Di dashboard Fonnte, pergi ke menu "API"
2. Copy token API Anda
3. Simpan token ini dengan aman

### 3. Konfigurasi Laravel
1. Buka file `.env` di root project
2. Tambahkan konfigurasi berikut:
```env
# Fonnte WhatsApp API Configuration
FONNTE_TOKEN=your_fonnte_token_here

# School Configuration  
SCHOOL_NAME="SMK Negeri 1"
```

### 4. Setup Queue Processing
1. Pastikan jobs table sudah di-migrate:
```bash
php artisan migrate
```

2. Jalankan queue worker (di production gunakan supervisor):
```bash
php artisan queue:work
```

## Cara Menggunakan

### 1. Akses Halaman Admin WhatsApp
- Login sebagai admin
- Klik menu "WhatsApp Notifikasi" di sidebar
- URL: `/admin/whatsapp`

### 2. Test Koneksi
- Masukkan nomor WhatsApp test (format: 08123456789)
- Klik "Kirim Test Pesan"
- Cek WhatsApp untuk memastikan pesan diterima

### 3. Kirim Pesan Manual
- Pilih siswa dari dropdown
- Tulis pesan (maksimal 1000 karakter)
- Klik "Kirim Pesan"

### 4. Broadcast Pengumuman
- Pilih filter kelas (opsional)
- Tulis pengumuman
- Klik "Kirim Broadcast"
- Konfirmasi pengiriman

### 5. Notifikasi Otomatis
Notifikasi akan dikirim otomatis saat:
- Siswa scan QR code absensi
- Admin input absensi manual
- Status absensi berubah

## Format Pesan Notifikasi

### Contoh Pesan Hadir
```
*NOTIFIKASI ABSENSI SISWA* 😊

📚 *SMK Negeri 1*
👤 *Nama:* Ahmad Rizki
📅 *Tanggal:* 28/07/2025
🕐 *Waktu:* 07:15:32
📋 *Status:* ✅ HADIR

Alhamdulillah, anak Anda telah hadir tepat waktu di sekolah. 👍

_Pesan otomatis dari sistem absensi SMK Negeri 1_
```

### Contoh Pesan Terlambat
```
*NOTIFIKASI ABSENSI SISWA* 😅

📚 *SMK Negeri 1*
👤 *Nama:* Ahmad Rizki
📅 *Tanggal:* 28/07/2025
🕐 *Waktu:* 07:45:12
📋 *Status:* ⏰ TERLAMBAT

Anak Anda terlambat masuk sekolah. Mohon diingatkan untuk berangkat lebih awal. 🙏

_Pesan otomatis dari sistem absensi SMK Negeri 1_
```

## Command Line Interface

### Test WhatsApp Connection
```bash
php artisan whatsapp:test
# atau dengan nomor langsung
php artisan whatsapp:test 08123456789
```

## Troubleshooting

### 1. Pesan Tidak Terkirim
- Cek token Fonnte di file `.env`
- Pastikan device WhatsApp connected di dashboard Fonnte
- Cek nomor WhatsApp format yang benar (08xxx atau 62xxx)
- Lihat log Laravel untuk error details

### 2. Queue Not Processing
- Pastikan queue worker berjalan: `php artisan queue:work`
- Cek failed jobs: `php artisan queue:failed`
- Restart queue worker: `php artisan queue:restart`

### 3. Nomor WhatsApp Invalid
- Format yang benar: 08123456789, +6281234567890, 6281234567890
- Pastikan nomor aktif dan terdaftar WhatsApp
- Cek di data siswa apakah nomor sudah diisi

## Log dan Monitoring

### Lokasi Log
- Laravel Log: `storage/logs/laravel.log`
- Queue Log: Lihat di bagian `INFO` dan `ERROR`

### Log Format
```
[2025-07-28 20:55:00] local.INFO: WhatsApp notification sent 
{
    "student": "Ahmad Rizki",
    "phone": "6281234567890", 
    "status": "hadir",
    "response": {...}
}
```

## Best Practices

1. **Test Terlebih Dahulu**: Selalu test koneksi sebelum go-live
2. **Monitor Queue**: Pastikan queue worker selalu berjalan
3. **Backup Token**: Simpan token Fonnte dengan aman
4. **Rate Limiting**: Fonnte memiliki rate limit, hindari spam
5. **Valid Phone Numbers**: Pastikan semua nomor valid dan aktif

## Security

1. **Environment Variables**: Jangan commit token ke git
2. **Access Control**: Hanya admin yang bisa akses fitur broadcast
3. **Input Validation**: Semua input di-validate
4. **Rate Limiting**: Implementasi delay untuk broadcast

## Maintenance

### Regular Tasks
1. Monitor queue performance
2. Check failed jobs dan retry jika perlu
3. Update token Fonnte jika expired
4. Backup log files secara berkala

### Monthly Tasks
1. Review penggunaan quota Fonnte
2. Clean up old logs
3. Update nomor WhatsApp siswa yang berubah
