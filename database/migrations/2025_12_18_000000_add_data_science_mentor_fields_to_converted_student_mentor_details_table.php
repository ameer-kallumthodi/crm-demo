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
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            // Orientation and Class Dates
            $table->date('orientation_class_date')->nullable();
            $table->date('class_start_date')->nullable();
            $table->date('class_end_date')->nullable();
            
            // WhatsApp Group Status
            $table->enum('whatsapp_group_status', ['sent link', 'task complete'])->nullable();
            
            // 1st Month Exam
            $table->date('first_month_exam_date')->nullable();
            $table->string('first_month_marks')->nullable();
            $table->text('first_month_feedback')->nullable();
            
            // 2nd Month Exam
            $table->date('second_month_exam_date')->nullable();
            $table->string('second_month_marks')->nullable();
            $table->text('second_month_feedback')->nullable();
            
            // AI Workshop Attendance
            $table->enum('ai_workshop_attendance', ['Attended', 'Not Attended'])->nullable();
            
            // 3rd Month Exam
            $table->date('third_month_exam_date')->nullable();
            $table->string('third_month_marks')->nullable();
            $table->text('third_month_feedback')->nullable();
            
            // Initial Project
            $table->date('initial_project_start_date')->nullable();
            $table->date('initial_project_end_date')->nullable();
            $table->string('initial_project_marks')->nullable();
            $table->text('initial_project_feedback')->nullable();
            
            // 4th Month Exam
            $table->date('fourth_month_exam_date')->nullable();
            $table->string('fourth_month_marks')->nullable();
            $table->text('fourth_month_feedback')->nullable();
            
            // Mock Test
            $table->date('mock_test_date')->nullable();
            $table->string('mock_test_marks')->nullable();
            $table->text('mock_test_feedback')->nullable();
            
            // Mock Interview
            $table->date('mock_interview_date')->nullable();
            $table->string('mock_interview_marks')->nullable();
            $table->text('mock_interview_feedback')->nullable();
            
            // Final Project
            $table->date('final_project_start_date')->nullable();
            $table->date('final_project_end_date')->nullable();
            $table->string('final_project_marks')->nullable();
            $table->text('final_project_feedback')->nullable();
            
            // Data Science Workshop Attendance
            $table->enum('data_science_workshop_attendance', ['Attended', 'Not Attended'])->nullable();
            
            // Attendance Summary
            $table->integer('total_class')->nullable();
            $table->integer('total_present')->nullable();
            $table->integer('total_absent')->nullable();
            
            // Final Certificate Examination
            $table->date('final_certificate_examination_date')->nullable();
            $table->string('certificate_examination_marks')->nullable();
            
            // Final Interview
            $table->date('final_interview_date')->nullable();
            $table->string('interview_marks')->nullable();
            
            // Certificate Distribution
            $table->date('certificate_distribution_date')->nullable();
            $table->date('experience_certificate_distribution_date')->nullable();
            
            // Cancelled Date
            $table->date('cancelled_date')->nullable();
            
            // Remarks
            $table->text('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropColumn([
                'orientation_class_date',
                'class_start_date',
                'class_end_date',
                'whatsapp_group_status',
                'first_month_exam_date',
                'first_month_marks',
                'first_month_feedback',
                'second_month_exam_date',
                'second_month_marks',
                'second_month_feedback',
                'ai_workshop_attendance',
                'third_month_exam_date',
                'third_month_marks',
                'third_month_feedback',
                'initial_project_start_date',
                'initial_project_end_date',
                'initial_project_marks',
                'initial_project_feedback',
                'fourth_month_exam_date',
                'fourth_month_marks',
                'fourth_month_feedback',
                'mock_test_date',
                'mock_test_marks',
                'mock_test_feedback',
                'mock_interview_date',
                'mock_interview_marks',
                'mock_interview_feedback',
                'final_project_start_date',
                'final_project_end_date',
                'final_project_marks',
                'final_project_feedback',
                'data_science_workshop_attendance',
                'total_class',
                'total_present',
                'total_absent',
                'final_certificate_examination_date',
                'certificate_examination_marks',
                'final_interview_date',
                'interview_marks',
                'certificate_distribution_date',
                'experience_certificate_distribution_date',
                'cancelled_date',
                'remarks',
            ]);
        });
    }
};

