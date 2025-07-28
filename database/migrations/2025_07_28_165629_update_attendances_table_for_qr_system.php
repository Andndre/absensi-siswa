<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Tambah referensi ke daily_qr_codes
            $table->foreignId('daily_qr_code_id')->nullable()->constrained('daily_qr_codes')->onDelete('set null')->after('student_id');
            
            // Update status enum dengan nilai yang benar
            $table->dropColumn('status');
        });
        
        // Tambah kolom status dengan enum yang benar
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'])->default('hadir')->after('attendance_time');
            $table->text('notes')->nullable()->after('status'); // Catatan tambahan
            $table->string('scan_method')->default('qr_code')->after('notes'); // Metode absen: qr_code, manual
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['daily_qr_code_id']);
            $table->dropColumn(['daily_qr_code_id', 'notes', 'scan_method']);
            $table->dropColumn('status');
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha'])->default('Hadir');
        });
    }
};
