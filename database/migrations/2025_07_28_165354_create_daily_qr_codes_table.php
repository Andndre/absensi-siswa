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
        Schema::create('daily_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Tanggal untuk QR code ini
            $table->string('qr_token')->unique(); // Token unik untuk QR code
            $table->time('valid_from')->default('06:00:00'); // Jam mulai berlaku
            $table->time('valid_until')->default('08:00:00'); // Jam berakhir berlaku
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif
            $table->integer('refresh_count')->default(0); // Berapa kali di-refresh
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Admin yang membuat
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['date', 'is_active']);
            $table->unique(['date', 'qr_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_qr_codes');
    }
};
