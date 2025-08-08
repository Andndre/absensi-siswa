<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->middleware('auth');
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Halaman pengaturan WhatsApp
     */
    public function index()
    {
        return view('admin.whatsapp.index');
    }

    /**
     * Test koneksi WhatsApp
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15'
        ]);

        try {
            $testMessage = "🔄 Test koneksi sistem absensi\n\n";
            $testMessage .= "📅 Waktu: " . now()->format('d/m/Y H:i:s') . "\n";
            $testMessage .= "🏫 Dari: " . \App\Models\Setting::get('school.name', 'SMK Negeri 1') . "\n\n";
            $testMessage .= "✅ Jika Anda menerima pesan ini, berarti sistem notifikasi WhatsApp berfungsi dengan baik.\n\n";
            $testMessage .= "_Pesan test otomatis_";

            $response = $this->whatsAppService->sendMessage($request->phone, $testMessage);

            return redirect()->back()->with('success', 
                'Test pesan berhasil dikirim! Silakan cek WhatsApp di nomor: ' . $request->phone);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                'Gagal mengirim test pesan: ' . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi manual ke siswa tertentu
     */
    public function sendManualNotification(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'message' => 'required|string|max:1000'
        ]);

        $student = Student::findOrFail($request->student_id);

        if (!$student->parent_whatsapp_number) {
            return redirect()->back()->with('error', 
                'Siswa tidak memiliki nomor WhatsApp orang tua.');
        }

        try {
            $customMessage = "📢 *PESAN DARI " . strtoupper(\App\Models\Setting::get('school.name', 'SMK NEGERI 1')) . "*\n\n";
            $customMessage .= "👤 *Untuk:* Orang tua dari {$student->name}\n";
            $customMessage .= "📅 *Tanggal:* " . now()->format('d/m/Y H:i:s') . "\n\n";
            $customMessage .= "💬 *Pesan:*\n{$request->message}\n\n";
            $customMessage .= "_Pesan dari admin " . \App\Models\Setting::get('school.name', 'SMK Negeri 1') . "_";

            $this->whatsAppService->sendMessage($student->parent_whatsapp_number, $customMessage);

            return redirect()->back()->with('success', 
                "Pesan berhasil dikirim ke orang tua {$student->name}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                'Gagal mengirim pesan: ' . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi broadcast ke semua orang tua
     */
    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'class_id' => 'nullable|exists:school_classes,id'
        ]);

        $query = Student::whereNotNull('parent_whatsapp_number');
        
        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 
                'Tidak ada siswa dengan nomor WhatsApp orang tua.');
        }

        $successCount = 0;
        $failedCount = 0;

        foreach ($students as $student) {
            try {
                $broadcastMessage = "📢 *PENGUMUMAN " . strtoupper(\App\Models\Setting::get('school.name', 'SMK NEGERI 1')) . "*\n\n";
                $broadcastMessage .= "👤 *Untuk:* Orang tua dari {$student->name}\n";
                $broadcastMessage .= "📅 *Tanggal:* " . now()->format('d/m/Y H:i:s') . "\n\n";
                $broadcastMessage .= "📝 *Pengumuman:*\n{$request->message}\n\n";
                $broadcastMessage .= "_Pengumuman resmi dari " . \App\Models\Setting::get('school.name', 'SMK Negeri 1') . "_";

                $this->whatsAppService->sendMessage($student->parent_whatsapp_number, $broadcastMessage);
                $successCount++;

                // Delay untuk menghindari spam
                sleep(2);

            } catch (\Exception $e) {
                $failedCount++;
                Log::error('Failed to send broadcast to student: ' . $student->name, [
                    'error' => $e->getMessage(),
                    'student_id' => $student->id
                ]);
            }
        }

        $message = "Broadcast selesai! Berhasil: {$successCount}, Gagal: {$failedCount}";
        
        return redirect()->back()->with($failedCount > 0 ? 'warning' : 'success', $message);
    }
}
