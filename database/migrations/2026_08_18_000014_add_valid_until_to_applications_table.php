<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Masa berlaku izin / tanggal berakhir kontrak (untuk deteksi kadaluarsa)
            $table->date('valid_until')->nullable()->after('rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('valid_until');
        });
    }
};
