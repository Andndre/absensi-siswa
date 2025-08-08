<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentQrCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate QR codes untuk semua siswa yang belum punya QR code
        Student::whereNull('qr_code')->chunk(100, function ($students) {
            foreach ($students as $student) {
                $student->update([
                    'qr_code' => $student->generateUniqueQrCode()
                ]);
            }
        });
        
        $this->command->info('QR codes generated for all students without QR codes.');
    }
}
