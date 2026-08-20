<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->string('document_type');      // nama persyaratan dokumen / "Lainnya"
            $table->string('file_name');          // nama asli file
            $table->string('file_path');          // path pada disk private
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('version')->default(1);
            $table->string('status')->default('UPLOADED'); // UPLOADED | ISSUED
            $table->string('folder')->nullable();  // folder divisi setelah approval
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
