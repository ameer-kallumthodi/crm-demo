<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('online_teaching_faculties', function (Blueprint $table) {
            $table->id();

            // A. Personal Details
            $table->string('full_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable(); // Male / Female
            $table->string('primary_mobile_number', 30);
            $table->string('alternate_contact_number', 30)->nullable();
            $table->string('official_email_address')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();

            // Residential Address (split fields)
            $table->string('address_house_name_flat_no')->nullable();
            $table->string('address_area_locality')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_district')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_pin_code', 20)->nullable();

            $table->string('highest_educational_qualification')->nullable();
            $table->text('additional_certifications')->nullable();
            $table->boolean('teaching_experience')->nullable(); // Yes/No
            $table->string('department_name')->nullable(); // E-School, EduThanzeel, ...

            // B. Document Submission (uploads)
            $table->string('document_resume_cv')->nullable();
            $table->string('document_10th_certificate')->nullable();
            $table->string('document_educational_qualification_certificates')->nullable();
            $table->string('document_aadhaar_front')->nullable();
            $table->string('document_aadhaar_back')->nullable();
            $table->string('document_other_1')->nullable();
            $table->string('document_other_2')->nullable();

            // C. HOD Review & Academic Hiring Tracking Format
            $table->string('faculty_id')->nullable();
            $table->string('class_level')->nullable(); // Basic, LP, UP, ...
            $table->string('employment_type')->nullable(); // Full-Time, Part-Time
            $table->string('work_schedule_mode')->nullable(); // Day, Night, Full-Time
            $table->string('candidate_status')->nullable(); // New, Shortlisted, ...
            $table->string('preferred_teaching_platform')->nullable(); // Google Meet, Zoom, Both
            $table->string('technical_readiness_confirmation')->nullable(); // Yes, No
            $table->date('demo_class_date')->nullable();
            $table->string('demo_conducted_by')->nullable();
            $table->date('offer_letter_issued_date')->nullable();
            $table->date('joining_date')->nullable();
            $table->text('remarks')->nullable();
            $table->string('offer_letter_upload')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_teaching_faculties');
    }
};
