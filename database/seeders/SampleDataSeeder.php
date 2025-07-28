<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat kelas-kelas
        $classes = [
            'Kelas 10-A',
            'Kelas 10-B', 
            'Kelas 11-A',
            'Kelas 11-B',
            'Kelas 12-A'
        ];

        foreach ($classes as $className) {
            SchoolClass::create(['name' => $className]);
        }

        // Buat siswa sample
        $students = [
            ['name' => 'Ahmad Rizki', 'nis' => '1001', 'class' => 'Kelas 10-A', 'phone' => '081234567890'],
            ['name' => 'Siti Nurhaliza', 'nis' => '1002', 'class' => 'Kelas 10-A', 'phone' => '081234567891'],
            ['name' => 'Budi Santoso', 'nis' => '1003', 'class' => 'Kelas 10-B', 'phone' => '081234567892'],
            ['name' => 'Dewi Kartika', 'nis' => '1004', 'class' => 'Kelas 10-B', 'phone' => '081234567893'],
            ['name' => 'Eko Prasetyo', 'nis' => '1005', 'class' => 'Kelas 11-A', 'phone' => '081234567894'],
            ['name' => 'Fitri Handayani', 'nis' => '1006', 'class' => 'Kelas 11-A', 'phone' => '081234567895'],
            ['name' => 'Galih Pratama', 'nis' => '1007', 'class' => 'Kelas 11-B', 'phone' => '081234567896'],
            ['name' => 'Hani Rahmawati', 'nis' => '1008', 'class' => 'Kelas 11-B', 'phone' => '081234567897'],
            ['name' => 'Indra Wijaya', 'nis' => '1009', 'class' => 'Kelas 12-A', 'phone' => '081234567898'],
            ['name' => 'Jasmine Putri', 'nis' => '1010', 'class' => 'Kelas 12-A', 'phone' => '081234567899'],
        ];

        foreach ($students as $studentData) {
            $class = SchoolClass::where('name', $studentData['class'])->first();
            
            $student = Student::create([
                'name' => $studentData['name'],
                'nis' => $studentData['nis'],
                'school_class_id' => $class->id,
                'parent_whatsapp_number' => $studentData['phone'],
            ]);
        }

        // Buat data absensi sample untuk 7 hari terakhir
        $students = Student::all();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            foreach ($students as $student) {
                // Random chance untuk hadir (80% kemungkinan hadir)
                if (rand(1, 100) <= 80) {
                    $hour = rand(7, 8);
                    $minute = rand(0, 59);
                    
                    // Tentukan status berdasarkan waktu
                    $status = ($hour == 7 && $minute <= 30) ? 'hadir' : 'terlambat';
                    
                    Attendance::create([
                        'student_id' => $student->id,
                        'attendance_time' => $date->copy()->addHours($hour)->addMinutes($minute),
                        'status' => $status,
                        'scan_method' => 'qr_code',
                        'notes' => 'Sample attendance data'
                    ]);
                } else {
                    // 20% kemungkinan tidak hadir dengan status random
                    $statuses = ['alpha', 'izin', 'sakit'];
                    Attendance::create([
                        'student_id' => $student->id,
                        'attendance_time' => $date->copy()->addHours(rand(7, 8))->addMinutes(rand(0, 59)),
                        'status' => $statuses[array_rand($statuses)],
                        'scan_method' => 'manual',
                        'notes' => 'Sample attendance data'
                    ]);
                }
            }
        }
    }
}
