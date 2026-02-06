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
        Schema::create('sslc_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_detail_id')->nullable();
            $table->unsignedBigInteger('converted_student_detail_id')->nullable();
            $table->string('certificate_path');
            $table->string('original_filename')->nullable();
            $table->string('file_type')->nullable(); // pdf, jpg, jpeg, png
            $table->integer('file_size')->nullable(); // in bytes
            $table->enum('verification_status', ['pending', 'verified'])->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('lead_detail_id')->references('id')->on('leads_details')->onDelete('cascade');
            $table->foreign('converted_student_detail_id')->references('id')->on('converted_student_details')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['lead_detail_id', 'verification_status'], 'sslc_lead_verification_idx');
            $table->index(['converted_student_detail_id', 'verification_status'], 'sslc_converted_verification_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sslc_certificates');
    }
};