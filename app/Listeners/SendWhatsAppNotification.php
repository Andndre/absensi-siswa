<?php

namespace App\Listeners;

use App\Events\AttendanceRecorded;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $whatsAppService;

    /**
     * Create the event listener.
     */
    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Handle the event.
     */
    public function handle(AttendanceRecorded $event): void
    {
        $attendance = $event->attendance;
        $student = $attendance->student;

        // Cek apakah student memiliki nomor WhatsApp orang tua
        if (!$student->parent_whatsapp_number) {
            Log::warning('No parent WhatsApp number for student', [
                'student_id' => $student->id,
                'student_name' => $student->name
            ]);
            return;
        }

        // Siapkan data untuk notifikasi
        $attendanceData = [
            'status' => $attendance->status,
            'time' => $attendance->attendance_time->format('H:i:s'),
            'date' => $attendance->attendance_time->format('d/m/Y'),
        ];

        // Kirim notifikasi WhatsApp
        try {
            $this->whatsAppService->sendAttendanceNotification(
                $student->name,
                $student->parent_whatsapp_number,
                $attendanceData
            );

            Log::info('WhatsApp notification queued successfully', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'parent_phone' => $student->parent_whatsapp_number,
                'status' => $attendance->status
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to queue WhatsApp notification', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'error' => $e->getMessage()
            ]);

            // Retry logic bisa ditambahkan di sini
            $this->release(30); // Retry setelah 30 detik
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(AttendanceRecorded $event, \Throwable $exception): void
    {
        Log::error('WhatsApp notification job failed', [
            'student_id' => $event->attendance->student->id,
            'student_name' => $event->attendance->student->name,
            'error' => $exception->getMessage()
        ]);
    }
}
