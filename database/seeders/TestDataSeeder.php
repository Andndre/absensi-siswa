<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat kelas jika belum ada
        $kelas = SchoolClass::firstOrCreate(
            ['name' => 'X IPA 1']
        );

        // Buat beberapa siswa untuk testing
        $students = [
            [
                'name' => 'Ahmad Budi Santoso',
                'nis' => '20240001',
                'parent_whatsapp_number' => '081234567890'
            ],
            [
                'name' => 'Siti Nurhaliza',
                'nis' => '20240002',
                'parent_whatsapp_number' => '081234567891'
            ],
            [
                'name' => 'Muhammad Rizki',
                'nis' => '20240003',
                'parent_whatsapp_number' => '081234567892'
            ],
            [
                'name' => 'Dewi Sartika',
                'nis' => '20240004',
                'parent_whatsapp_number' => '081234567893'
            ],
            [
                'name' => 'Bayu Pratama',
                'nis' => '20240005',
                'parent_whatsapp_number' => '081234567894'
            ]
        ];

        foreach ($students as $studentData) {
            if (!Student::where('nis', $studentData['nis'])->exists()) {
                Student::create([
                    'name' => $studentData['name'],
                    'nis' => $studentData['nis'],
                    'school_class_id' => $kelas->id,
                    'parent_whatsapp_number' => $studentData['parent_whatsapp_number'],
                    'is_active' => true,
                    // QR code akan di-generate otomatis oleh model boot method
                ]);
            }
        }

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Student login example: 20240001 / 20240001');
    }
}
