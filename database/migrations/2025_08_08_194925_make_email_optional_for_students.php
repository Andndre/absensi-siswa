<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove unique constraint on email if exists
        try {
            Schema::table('students', function (Blueprint $table) {
                $table->dropUnique(['email']);
            });
        } catch (\Exception $e) {
            // Ignore if constraint doesn't exist
        }
        
        // Make email truly optional by setting existing auto-generated emails to null
        DB::table('students')
            ->where('email', 'LIKE', '%@student.sekolah.id')
            ->update(['email' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore auto-generated emails
        DB::table('students')
            ->whereNull('email')
            ->update([
                'email' => DB::raw("CONCAT(nis, '@student.sekolah.id')")
            ]);
            
        // Add unique constraint back
        Schema::table('students', function (Blueprint $table) {
            $table->unique('email');
        });
    }
};
