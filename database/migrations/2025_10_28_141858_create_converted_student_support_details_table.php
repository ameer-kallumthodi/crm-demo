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
        Schema::create('converted_student_support_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('converted_student_id');
            $table->foreign('converted_student_id')->references('id')->on('converted_leads')->onDelete('cascade');
            
            // Support-specific fields
            $table->string('registration_status')->nullable();
            $table->string('technology_side')->nullable();
            $table->string('student_status')->nullable();
            $table->string('call_1')->nullable();
            $table->string('app')->nullable();
            $table->string('whatsapp_group')->nullable();
            $table->string('telegram_group')->nullable();
            $table->text('problems')->nullable();
            
            // Additional support fields that might be needed
            $table->string('support_notes')->nullable();
            $table->string('support_status')->nullable();
            $table->timestamp('last_support_contact')->nullable();
            $table->string('support_priority')->nullable();
            
            $table->timestamps();
            
            // Add index for better performance
            $table->index('converted_student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('converted_student_support_details');
    }
};
