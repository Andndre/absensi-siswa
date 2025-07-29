# Sistem Absensi Siswa

Aplikasi web untuk mengelola absensi siswa dengan QR Code dan notifikasi WhatsApp otomatis.

## Fitur Utama

- ✅ Absensi dengan QR Code
- 📱 Notifikasi WhatsApp otomatis ke orang tua
- 👥 Management data siswa dan kelas
- 📊 Laporan absensi detail
- 🔔 Broadcast pesan ke orang tua
- ⚙️ Pengaturan sistem yang fleksibel

## Teknologi

- Laravel 10
- Bootstrap 5
- MySQL/MariaDB
- API Fonnte WhatsApp

## Persyaratan Sistem

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM
- Akun Fonnte WhatsApp API

## Instalasi

1. Clone repositori ini
```bash
git clone https://github.com/yourusername/absensi-siswa.git
cd absensi-siswa
```

2. Install dependensi PHP
```bash
composer install
```

3. Install dependensi Node.js
```bash
npm install
```

4. Salin file .env
```bash
cp .env.example .env
```

5. Generate key aplikasi
```bash
php artisan key:generate
```

6. Konfigurasi database di file .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_siswa
DB_USERNAME=root
DB_PASSWORD=
```

7. Jalankan migrasi database
```bash
php artisan migrate --seed
```

8. Build assets
```bash
npm run dev
```

9. Jalankan server
```bash
php artisan serve
```

## Konfigurasi WhatsApp

1. Daftar akun di [Fonnte](https://fonnte.com)
2. Login ke dashboard Fonnte
3. Klik menu "Device" dan hubungkan WhatsApp dengan scan QR
4. Klik tombol "Token" pada device yang sudah dibuat untuk untuk menyalin token
5. Masuk ke menu Pengaturan di aplikasi
6. Masukkan token Fonnte di tab WhatsApp

## Cron Jobs

Aplikasi menggunakan cron jobs untuk:
1. Mengirim notifikasi WhatsApp
2. Menandai siswa yang tidak hadir sebagai alpha

Tambahkan crontab berikut di server:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Akun Default

### Admin
- Email: admin@admin.com
- Password: password

### Siswa
- NIS: [NIS siswa]
- Password: [sama dengan NIS]

## Penggunaan

1. Admin dapat:
   - Mengelola data siswa dan kelas
   - Generate QR Code absensi
   - Melihat laporan absensi
   - Mengirim pesan broadcast
   - Mengatur sistem

2. Siswa dapat:
   - Login dengan NIS
   - Scan QR Code untuk absen
   - Melihat riwayat absensi
   - Mengubah password

## Lisensi

MIT License

## Kontribusi

Silakan buat pull request untuk kontribusi.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
