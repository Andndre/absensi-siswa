<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;

class TestWhatsAppConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:test {phone?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp connection using Fonnte API';

    protected $whatsAppService;

    /**
     * Create a new command instance.
     */
    public function __construct(WhatsAppService $whatsAppService)
    {
        parent::__construct();
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');

        if (!$phone) {
            $phone = $this->ask('Masukkan nomor WhatsApp untuk test (contoh: 08123456789)');
        }

        $this->info('Testing WhatsApp connection...');

        try {
            // Test dengan mengirim pesan sederhana
            $testMessage = "🔄 Test koneksi sistem absensi\n\n";
            $testMessage .= "📅 Waktu: " . now()->format('d/m/Y H:i:s') . "\n";
            $testMessage .= "🏫 Dari: " . config('app.school_name') . "\n\n";
            $testMessage .= "✅ Jika Anda menerima pesan ini, berarti sistem notifikasi WhatsApp berfungsi dengan baik.\n\n";
            $testMessage .= "_Pesan test otomatis_";

            $formattedPhone = $this->formatPhoneNumber($phone);
            
            $this->info("Mengirim pesan test ke: " . $formattedPhone);

            $response = $this->sendTestMessage($formattedPhone, $testMessage);

            if ($response && isset($response['status']) && $response['status']) {
                $this->info('✅ Test berhasil!');
                $this->info('Detail response:');
                $this->line('- Status: ' . ($response['status'] ? 'Success' : 'Failed'));
                $this->line('- Message: ' . $response['detail']);
                if (isset($response['id'])) {
                    $this->line('- Message ID: ' . implode(', ', $response['id']));
                }
                return 0;
            } else {
                $this->error('❌ Test gagal!');
                $this->error('Response: ' . json_encode($response));
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }

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

    private function sendTestMessage($target, $message)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
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
                'Authorization: ' . config('services.fonnte.token')
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
        
        return json_decode($response, true);
    }
}
