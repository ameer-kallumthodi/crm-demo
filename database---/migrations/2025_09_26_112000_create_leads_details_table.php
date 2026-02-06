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
        Schema::create('leads_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            
            // Personal Information
            $table->string('student_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('email')->nullable();
            $table->string('personal_number')->nullable();
            $table->string('personal_code', 10)->nullable();
            $table->string('parents_number')->nullable();
            $table->string('parents_code', 10)->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('whatsapp_code', 10)->nullable();
            
            // Academic Information
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->enum('class', ['sslc', 'plustwo'])->nullable();
            $table->enum('second_language', ['malayalam', 'hindi'])->nullable();
            
            // Address Information
            $table->text('street')->nullable();
            $table->string('locality')->nullable();
            $table->string('post_office')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('pin_code')->nullable();
            
            // Document Uploads
            $table->string('birth_certificate')->nullable();
            $table->string('passport_photo')->nullable();
            $table->string('adhar_front')->nullable();
            $table->string('adhar_back')->nullable();
            $table->string('signature')->nullable();
            $table->string('plustwo_certificate')->nullable();
            $table->string('sslc_certificate')->nullable();
            $table->string('ug_certificate')->nullable();
            
            // Additional Information
            $table->text('message')->nullable();
            
            // Status and Tracking
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_remarks')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            // Course Information
            $table->unsignedBigInteger('course_id')->nullable();
            
            // Document verification status fields
            $table->enum('sslc_verification_status', ['pending', 'verified'])->default('pending');
            $table->unsignedBigInteger('sslc_verified_by')->nullable();
            $table->timestamp('sslc_verified_at')->nullable();
            
            $table->enum('plustwo_verification_status', ['pending', 'verified'])->default('pending');
            $table->unsignedBigInteger('plustwo_verified_by')->nullable();
            $table->timestamp('plustwo_verified_at')->nullable();
            
            $table->enum('ug_verification_status', ['pending', 'verified'])->default('pending');
            $table->unsignedBigInteger('ug_verified_by')->nullable();
            $table->timestamp('ug_verified_at')->nullable();
            
            $table->enum('passport_photo_verification_status', ['pending', 'verified'])->default('pending');
            $table->unsignedBigInteger('passport_photo_verified_by')->nullable();
            $table->timestamp('passport_photo_verified_at')->nullable();
            
            $table->enum('adhar_front_verification_status', ['pending', 'verified'])->default('pending');
            $table->unsignedBigInteger('adhar_front_verified_by')->nullable();
            $table->timestamp('adhar_front_verified_at')->nullable();
            
            $table->enum('adhar_back_verification_status', ['pending', 'verified'])->default('pending');
            $table->unsignedBigInteger('adhar_back_verified_by')->nullable();
            $table->timestamp('adhar_back_verified_at')->nullable();
            
            $table->enum('signature_verification_status', ['pending', 'verified'])->default('pending');
            $table->unsignedBigInteger('signature_verified_by')->nullable();
            $table->timestamp('signature_verified_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Document verification foreign keys
            $table->foreign('sslc_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('plustwo_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('ug_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('passport_photo_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('adhar_front_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('adhar_back_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('signature_verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads_details');
    }
};
