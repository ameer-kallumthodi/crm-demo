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
        Schema::create('team_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->string('legal_name')->nullable();
            $table->string('institution_category')->nullable();
            $table->string('registration_number')->nullable();

            // Address Details
            $table->string('building_name')->nullable();
            $table->string('street_name')->nullable();
            $table->string('locality_name')->nullable();
            $table->string('city')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();

            // Communication Officer Details
            $table->string('comm_officer_name')->nullable();
            $table->string('comm_officer_mobile')->nullable();
            $table->string('comm_officer_alt_mobile')->nullable();
            $table->string('comm_officer_whatsapp')->nullable();
            $table->string('comm_officer_email')->nullable();

            // Authorized Stakeholder Details
            $table->string('auth_person_name')->nullable();
            $table->string('auth_person_designation')->nullable();
            $table->string('auth_person_mobile')->nullable();
            $table->string('auth_person_email')->nullable();

            // Courses / Academic Delivery Structure
            $table->json('interested_courses_details')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_details');
    }
};
