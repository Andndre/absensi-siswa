<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAttendancesForStudentQrSystem extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Kolom untuk menyimpan siapa yang melakukan scan (admin/guru)
            $table->foreignId('scanned_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Kolom untuk menyimpan QR code siswa yang di-scan
            $table->string('student_qr_code')->nullable();
            
            // Update scan_method enum untuk menambahkan opsi baru
            // Hapus kolom lama dan buat ulang dengan opsi yang lebih lengkap
            $table->dropColumn('scan_method');
        });
        
        // Tambah kembali scan_method dengan enum yang lebih lengkap
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('scan_method', ['student_qr_scan', 'daily_qr_scan', 'manual', 'import'])->default('student_qr_scan')->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['scanned_by']);
            $table->dropColumn(['scanned_by', 'student_qr_code']);
            $table->dropColumn('scan_method');
        });
        
        // Kembalikan scan_method ke format lama
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('scan_method')->default('qr_code')->after('notes');
        });
    }
}
