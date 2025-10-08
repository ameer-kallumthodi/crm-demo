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
        Schema::create('converted_leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('dob')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable(); // encrypted
            $table->string('status')->nullable(); // Paid, Admission cancel, etc.
            $table->string('register_number', 50)->nullable();
            $table->timestamp('reg_updated_at')->nullable();
            $table->unsignedBigInteger('reg_updated_by')->nullable();
            $table->unsignedBigInteger('board_id')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('academic_assistant_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('remarks')->nullable();
            $table->string('reg_fee')->nullable(); // Received, Not Received
            $table->string('exam_fee')->nullable(); // Pending, Not Paid, Paid
            $table->string('ref_no')->nullable();
            $table->string('enroll_no')->nullable();
            $table->string('id_card')->nullable(); // Not Paid, Paid
            $table->string('tma')->nullable(); // Not Paid, Paid
            $table->unsignedBigInteger('admission_batch_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('reg_updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('admission_batch_id')->references('id')->on('admission_batches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('converted_leads');
    }
};
