<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'whatsapp' => Setting::getByGroup('whatsapp'),
            'school' => Setting::getByGroup('school'),
            'system' => Setting::getByGroup('system'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function updateWhatsApp(Request $request)
    {
        // Debug logging
        Log::info('WhatsApp settings update request', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);

        $request->validate([
            'fonnte_token' => 'nullable|string|max:255',
            'notification_format' => 'nullable|string',
        ]);

        // Update settings with proper parameters
        Setting::set('whatsapp.fonnte_token', $request->fonnte_token ?? '', 'string', 'whatsapp', 'Token Fonnte API', 'Token API dari Fonnte untuk mengirim pesan WhatsApp', true);
        Setting::set('whatsapp.auto_notification', $request->has('auto_notification') && $request->auto_notification == '1', 'boolean', 'whatsapp', 'Notifikasi Otomatis', 'Kirim notifikasi otomatis ke orang tua setelah absensi', false);
        Setting::set('whatsapp.notification_format', $request->notification_format ?? '', 'string', 'whatsapp', 'Format Pesan Notifikasi', 'Template pesan notifikasi WhatsApp', false);

        Log::info('WhatsApp settings updated successfully');

        return back()->with('success', 'Pengaturan WhatsApp berhasil diperbarui.');
    }

    public function updateSchool(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Setting::set('school.name', $request->name ?? '', 'string', 'school', 'Nama Sekolah', 'Nama sekolah yang akan muncul di notifikasi', false);
        Setting::set('school.address', $request->address ?? '', 'string', 'school', 'Alamat Sekolah', 'Alamat lengkap sekolah', false);
        Setting::set('school.phone', $request->phone ?? '', 'string', 'school', 'Telepon Sekolah', 'Nomor telepon sekolah', false);
        Setting::set('school.email', $request->email ?? '', 'string', 'school', 'Email Sekolah', 'Email sekolah', false);

        return back()->with('success', 'Informasi sekolah berhasil diperbarui.');
    }

    public function updateSystem(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:50',
            'date_format' => 'nullable|string|max:20',
            'records_per_page' => 'nullable|integer|min:5|max:100',
        ]);

        Setting::set('system.app_name', $request->app_name ?? 'Sistem Absensi', 'string', 'system', 'Nama Aplikasi', 'Nama aplikasi yang akan ditampilkan', false);
        Setting::set('system.timezone', $request->timezone ?? 'Asia/Jakarta', 'string', 'system', 'Zona Waktu', 'Zona waktu sistem', false);
        Setting::set('system.date_format', $request->date_format ?? 'd/m/Y', 'string', 'system', 'Format Tanggal', 'Format tampilan tanggal', false);
        Setting::set('system.records_per_page', (int) ($request->records_per_page ?? 10), 'integer', 'system', 'Data per Halaman', 'Jumlah data yang ditampilkan per halaman', false);

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    public function testWhatsApp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:1000',
        ]);

        try {
            $whatsappService = new \App\Services\WhatsAppService();
            $result = $whatsappService->sendMessage($request->phone, $request->message);

            if ($result['success']) {
                return back()->with('success', 'Pesan WhatsApp berhasil dikirim.');
            } else {
                return back()->with('error', 'Gagal mengirim pesan: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
