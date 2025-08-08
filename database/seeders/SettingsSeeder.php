<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // WhatsApp Settings
            [
                'key' => 'whatsapp.fonnte_token',
                'value' => '',
                'type' => 'string',
                'group' => 'whatsapp',
                'label' => 'Token Fonnte API',
                'description' => 'Token API dari Fonnte untuk mengirim pesan WhatsApp',
                'is_encrypted' => true
            ],
            [
                'key' => 'whatsapp.auto_notification',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'whatsapp',
                'label' => 'Notifikasi Otomatis',
                'description' => 'Kirim notifikasi otomatis ke orang tua setelah absensi',
                'is_encrypted' => false
            ],
            [
                'key' => 'whatsapp.notification_format',
                'value' => "Assalamu'alaikum {parent_name},\n\nKami informasikan bahwa putra/putri Anda:\n\nNama: {student_name}\nKelas: {class_name}\nStatus: {status}\nWaktu: {time}\nTanggal: {date}\n\nTerima kasih atas perhatiannya.\n\n{school_name}",
                'type' => 'string',
                'group' => 'whatsapp',
                'label' => 'Format Pesan Notifikasi',
                'description' => 'Template pesan notifikasi WhatsApp',
                'is_encrypted' => false
            ],

            // School Settings
            [
                'key' => 'school.name',
                'value' => 'SMK Negeri 1',
                'type' => 'string',
                'group' => 'school',
                'label' => 'Nama Sekolah',
                'description' => 'Nama sekolah yang akan muncul di notifikasi',
                'is_encrypted' => false
            ],
            [
                'key' => 'school.address',
                'value' => '',
                'type' => 'string',
                'group' => 'school',
                'label' => 'Alamat Sekolah',
                'description' => 'Alamat lengkap sekolah',
                'is_encrypted' => false
            ],
            [
                'key' => 'school.phone',
                'value' => '',
                'type' => 'string',
                'group' => 'school',
                'label' => 'Telepon Sekolah',
                'description' => 'Nomor telepon sekolah',
                'is_encrypted' => false
            ],
            [
                'key' => 'school.email',
                'value' => '',
                'type' => 'string',
                'group' => 'school',
                'label' => 'Email Sekolah',
                'description' => 'Email sekolah',
                'is_encrypted' => false
            ],

            // System Settings
            [
                'key' => 'system.app_name',
                'value' => 'Sistem Absensi',
                'type' => 'string',
                'group' => 'system',
                'label' => 'Nama Aplikasi',
                'description' => 'Nama aplikasi yang akan ditampilkan',
                'is_encrypted' => false
            ],
            [
                'key' => 'system.timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'group' => 'system',
                'label' => 'Zona Waktu',
                'description' => 'Zona waktu sistem',
                'is_encrypted' => false
            ],
            [
                'key' => 'system.date_format',
                'value' => 'd/m/Y',
                'type' => 'string',
                'group' => 'system',
                'label' => 'Format Tanggal',
                'description' => 'Format tampilan tanggal',
                'is_encrypted' => false
            ],
            [
                'key' => 'system.records_per_page',
                'value' => '10',
                'type' => 'integer',
                'group' => 'system',
                'label' => 'Data per Halaman',
                'description' => 'Jumlah data yang ditampilkan per halaman',
                'is_encrypted' => false
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
