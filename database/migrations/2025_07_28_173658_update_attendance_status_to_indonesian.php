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
        // First, change enum to text to allow updates
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('status')->change();
        });
        
        // Update existing attendance status values to Indonesian
        DB::table('attendances')->where('status', 'present')->update(['status' => 'hadir']);
        DB::table('attendances')->where('status', 'late')->update(['status' => 'terlambat']);
        DB::table('attendances')->where('status', 'excused')->update(['status' => 'izin']);
        DB::table('attendances')->where('status', 'absent')->update(['status' => 'alpha']);
        
        // Now change back to enum with Indonesian values + 'terlambat'
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to English status values
        DB::table('attendances')->where('status', 'hadir')->update(['status' => 'present']);
        DB::table('attendances')->where('status', 'terlambat')->update(['status' => 'late']);
        DB::table('attendances')->where('status', 'izin')->update(['status' => 'excused']);
        DB::table('attendances')->where('status', 'alpha')->update(['status' => 'absent']);
        
        // Revert enum back to English values
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['present', 'late', 'absent', 'excused'])->change();
        });
    }
};
