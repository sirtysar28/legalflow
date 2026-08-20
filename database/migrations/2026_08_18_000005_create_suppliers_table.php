<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('npwp')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_person')->nullable();
            $table->boolean('is_registered')->default(false);          // supplier sudah terdaftar?
            $table->boolean('assessment_available')->default(false);   // assessment tersedia?
            $table->decimal('assessment_score', 5, 2)->nullable();
            $table->date('assessment_date')->nullable();
            $table->boolean('data_complete')->default(false);          // data supplier lengkap?
            $table->boolean('documents_complete')->default(false);     // dokumen supplier lengkap?
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
