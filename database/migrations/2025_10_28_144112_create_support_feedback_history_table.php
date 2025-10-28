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
        Schema::create('support_feedback_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('converted_student_id');
            $table->foreign('converted_student_id')->references('id')->on('converted_leads')->onDelete('cascade');
            
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('feedback_type')->default('general'); // general, call, issue, resolution, etc.
            $table->text('feedback_content');
            $table->string('feedback_status')->nullable(); // pending, resolved, in_progress, etc.
            $table->string('priority')->nullable(); // low, medium, high, urgent
            $table->timestamp('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('converted_student_id');
            $table->index('created_by');
            $table->index('feedback_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_feedback_history');
    }
};
