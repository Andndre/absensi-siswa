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
        Schema::table('students', function (Blueprint $table) {
            $table->string('email')->nullable()->after('nis');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('password')->nullable()->after('email_verified_at');
            $table->boolean('is_active')->default(true)->after('parent_whatsapp_number');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->rememberToken()->after('last_login_at');
        });
        
        // Update existing students dengan email dan password default
        DB::table('students')->whereNull('email')->update([
            'email' => DB::raw("CONCAT(nis, '@student.sekolah.id')"),
            'password' => DB::raw("'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'"), // password hash untuk 'password'
        ]);
        
        // Setelah data diupdate, baru tambahkan unique constraint
        Schema::table('students', function (Blueprint $table) {
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'email', 
                'email_verified_at', 
                'password', 
                'is_active', 
                'last_login_at', 
                'remember_token'
            ]);
        });
    }
};
