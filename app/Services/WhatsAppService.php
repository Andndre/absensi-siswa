<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class WhatsAppService
{
    private $apiUrl;
    private $token;

    public function __construct()
    {
        $this->apiUrl = 'https://api.fonnte.com/send';
        
        try {
            $this->token = Setting::get('whatsapp.fonnte_token');
        } catch (\Exception $e) {
            // Fallback to config if settings table doesn't exist yet
            $this->token = config('services.fonnte.token');
        }
    }

    /**
     * Kirim pesan WhatsApp ke orang tua setelah absensi
     */
    public function sendAttendanceNotification($studentName, $parentPhone, $attendanceData)
    {
        try {
            // Format nomor WhatsApp (hapus 0 di depan, tambah 62)
            $formattedPhone = $this->formatPhoneNumber($parentPhone);
            
            // Buat pesan berdasarkan status absensi
            $message = $this->createAttendanceMessage($studentName, $attendanceData);
            
            // Kirim pesan
            $response = $this->sendMessageToFonnte($formattedPhone, $message);
            
            // Log hasil pengiriman
            Log::info('WhatsApp notification sent', [
                'student' => $studentName,
                'phone' => $formattedPhone,
                'status' => $attendanceData['status'],
                'response' => $response
            ]);
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp notification', [
                'student' => $studentName,
                'phone' => $parentPhone,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Format nomor telepon untuk WhatsApp
     */
    private function formatPhoneNumber($phone)
    {
        // Hapus semua karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Jika dimulai dengan 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Jika belum ada kode negara, tambahkan 62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Buat pesan berdasarkan status absensi
     */
    private function createAttendanceMessage($studentName, $attendanceData)
    {
        $status = $attendanceData['status'];
        $time = $attendanceData['time'];
        $date = $attendanceData['date'];

        // Format status dengan huruf besar di awal dan emoji
        $statusFormats = [
            'hadir' => 'Hadir ✅',
            'terlambat' => 'Terlambat ⏰',
            'izin' => 'Izin 📝',
            'sakit' => 'Sakit 🏥',
            'alpha' => 'Alpha ❌'
        ];

        $formattedStatus = $statusFormats[$status] ?? ucfirst($status);
        
        try {
            $messageFormat = Setting::get('whatsapp.notification_format');
            $schoolName = Setting::get('school.name', 'SMK Negeri 1');
        } catch (\Exception $e) {
            Log::warning('Failed to get message format from settings, using default', [
                'error' => $e->getMessage()
            ]);
            // Fallback to default format if settings are not available
            $messageFormat = "Assalamu'alaikum {parent_name},\n\nKami informasikan bahwa putra/putri Anda:\n\nNama: {student_name}\nKelas: {class_name}\nStatus: {status}\nWaktu: {time}\nTanggal: {date}\n\nTerima kasih atas perhatiannya.\n\n{school_name}";
            $schoolName = config('app.school_name', 'SMK Negeri 1');
        }

        // Replace placeholders with actual values
        $replacements = [
            '{student_name}' => $studentName,
            '{status}' => $formattedStatus,
            '{time}' => $time,
            '{date}' => $date,
            '{school_name}' => $schoolName
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $messageFormat
        );
    }

    /**
     * Kirim pesan WhatsApp umum
     */
    public function sendMessage($target, $message)
    {
        try {
            // Format nomor WhatsApp
            $formattedTarget = $this->formatPhoneNumber($target);
            
            // Kirim pesan
            $response = $this->sendMessageToFonnte($formattedTarget, $message);
            
            // Log hasil pengiriman
            Log::info('WhatsApp message sent', [
                'target' => $formattedTarget,
                'response' => $response
            ]);
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp message', [
                'target' => $target,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Kirim pesan melalui Fonnte API
     */
    private function sendMessageToFonnte($target, $message)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
                'typing' => false,
                'delay' => '0',
                'connectOnly' => true
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $this->token
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            throw new \Exception("cURL Error: " . $error_msg);
        }
        
        curl_close($curl);
        
        $decodedResponse = json_decode($response, true);
        
        if ($httpCode !== 200 || !$decodedResponse['status']) {
            throw new \Exception("API Error: " . ($decodedResponse['detail'] ?? 'Unknown error'));
        }
        
        return $decodedResponse;
    }

    /**
     * Test koneksi ke Fonnte API
     */
    public function testConnection()
    {
        try {
            $testMessage = "Test koneksi dari sistem absensi - " . now()->format('d/m/Y H:i:s');
            $response = $this->sendMessageToFonnte('6282227097005', $testMessage); // Ganti dengan nomor test
            return $response;
        } catch (\Exception $e) {
            throw new \Exception("Test connection failed: " . $e->getMessage());
        }
    }
}
