<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_access_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->text('reason');
            $table->string('status')->default('ACCESS_REQUESTED'); // ACCESS_REQUESTED|ACCESS_APPROVED|ACCESS_REJECTED|ACCESS_EXPIRED
            $table->string('access_type')->nullable();             // VIEW | DOWNLOAD
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->date('expired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_access_requests');
    }
};
