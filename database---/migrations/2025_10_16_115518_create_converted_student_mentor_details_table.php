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
        Schema::create('converted_student_mentor_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('converted_student_id');
            
            // Basic Information
            $table->string('application_number')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->enum('registration_status', ['Paid', 'Not Paid'])->nullable();
            $table->enum('technology_side', ['No Knowledge', 'Limited Knowledge', 'Moderate Knowledge', 'High Knowledge'])->nullable();
            $table->enum('student_status', ['Low Level', 'Below Medium', 'Medium Level', 'Advanced Level'])->nullable();
            
            // Call and Communication Fields
            $table->enum('call_1', ['Call Not Answered', 'Switched Off', 'Line Busy', 'Student Asks to Call Later', 'Lack of Interest in Conversation', 'Wrong Contact', 'Inconsistent Responses', 'Task Complete'])->nullable();
            $table->enum('app', ['Provided app', 'OTP Problem', 'Task Completed'])->nullable();
            $table->enum('whatsapp_group', ['Sent link', 'Task Completed'])->nullable();
            $table->enum('telegram_group', ['Call not answered', 'switched off', 'line busy', 'student asks to call later', 'lack of interest in conversation', 'wrong contact', 'inconsistent responses', 'task complete'])->nullable();
            $table->text('problems')->nullable();
            $table->enum('call_2', ['Call Not Answered', 'Switched Off', 'Line Busy', 'Student Asks to Call Later', 'Lack of Interest in Conversation', 'Wrong Contact', 'Inconsistent Responses', 'Task Complete'])->nullable();
            $table->enum('mentor_live_1', ['Not Respond', 'Task Complete'])->nullable();
            $table->enum('first_live', ['Not Respond', '1 subject attend', '2 subject attend', '3 subject attend', '4 subject attend', '5 subject attend', '6 subject attend', 'Task complete'])->nullable();
            $table->enum('first_exam_registration', ['Did not', 'Task complete'])->nullable();
            $table->enum('first_exam', ['not respond', '1 subject attend', '2 subject attend', '3 subject attend', '4 subject attend', '5 subject attend', '6 subject attend', 'task complete'])->nullable();
            $table->enum('call_3', ['Call Not Answered', 'Switched Off', 'Line Busy', 'Student Asks to Call Later', 'Lack of Interest in Conversation', 'Wrong Contact', 'Inconsistent Responses', 'Task Complete'])->nullable();
            $table->enum('mentor_live_2', ['Not Respond', 'Task Complete'])->nullable();
            $table->enum('second_live', ['Not Respond', '1 subject attend', '2 subject attend', '3 subject attend', '4 subject attend', '5 subject attend', '6 subject attend', 'Task complete'])->nullable();
            $table->enum('second_exam', ['not respond', '1 subject attend', '2 subject attend', '3 subject attend', '4 subject attend', '5 subject attend', '6 subject attend', 'task complete'])->nullable();
            $table->enum('call_4', ['Call Not Answered', 'Switched Off', 'Line Busy', 'Student Asks to Call Later', 'Lack of Interest in Conversation', 'Wrong Contact', 'Inconsistent Responses', 'Task Complete'])->nullable();
            $table->enum('mentor_live_3', ['Not Respond', 'Task Complete'])->nullable();
            $table->enum('model_exam_live', ['not respond', '1 subject attend', '2 subject attend', '3 subject attend', '4 subject attend', '5 subject attend', '6 subject attend', 'task complete'])->nullable();
            $table->enum('model_exam', ['not respond', '1 subject attend', '2 subject attend', '3 subject attend', '4 subject attend', '5 subject attend', '6 subject attend', 'task complete'])->nullable();
            $table->enum('practical', ['Did not', 'Task complete'])->nullable();
            $table->enum('call_5', ['Call Not Answered', 'Switched Off', 'Line Busy', 'Student Asks to Call Later', 'Lack of Interest in Conversation', 'Wrong Contact', 'Inconsistent Responses', 'Task Complete'])->nullable();
            $table->enum('mentor_live_4', ['Not Respond', 'Task Complete'])->nullable();
            $table->enum('self_registration', ['Did not', 'Task complete'])->nullable();
            $table->enum('call_6', ['Call Not Answered', 'Switched Off', 'Line Busy', 'Student Asks to Call Later', 'Lack of Interest in Conversation', 'Wrong Contact', 'Inconsistent Responses', 'Task Complete'])->nullable();
            $table->enum('assignment', ['not respond', '1 subject attend', '2 subject attend', '3 subject attend', '4 subject attend', '5 subject attend', '6 subject attend', 'task complete'])->nullable();
            $table->enum('call_7', ['Call Not Answered', 'Switched Off', 'Line Busy', 'Student Asks to Call Later', 'Lack of Interest in Conversation', 'Wrong Contact', 'Inconsistent Responses', 'Task Complete'])->nullable();
            $table->enum('mock_test', ['Did not', 'Task complete'])->nullable();
            $table->enum('call_8', ['Call Not Answered', 'Switched Off', 'Line Busy', 'Student Asks to Call Later', 'Lack of Interest in Conversation', 'Wrong Contact', 'Inconsistent Responses', 'Task Complete'])->nullable();
            $table->enum('admit_card', ['Did not', 'Task complete'])->nullable();
            $table->enum('call_9', ['Call Not Answered', 'Switched Off', 'Line Busy', 'Student Asks to Call Later', 'Lack of Interest in Conversation', 'Wrong Contact', 'Inconsistent Responses', 'Task Complete'])->nullable();
            $table->enum('mentor_live_5', ['Not Respond', 'Task complete'])->nullable();
            
            // Exam Subject Fields
            $table->enum('exam_subject_1', ['Did not log in on time', 'missed the exam', 'technical issue', 'task complete'])->nullable();
            $table->enum('exam_subject_2', ['Did not log in on time', 'missed the exam', 'technical issue', 'task complete'])->nullable();
            $table->enum('exam_subject_3', ['Did not log in on time', 'missed the exam', 'technical issue', 'task complete'])->nullable();
            $table->enum('exam_subject_4', ['Did not log in on time', 'missed the exam', 'technical issue', 'task complete'])->nullable();
            $table->enum('exam_subject_5', ['Did not log in on time', 'missed the exam', 'technical issue', 'task complete'])->nullable();
            $table->enum('exam_subject_6', ['Did not log in on time', 'missed the exam', 'technical issue', 'task complete'])->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('converted_student_id')->references('id')->on('converted_leads')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('converted_student_mentor_details');
    }
};
