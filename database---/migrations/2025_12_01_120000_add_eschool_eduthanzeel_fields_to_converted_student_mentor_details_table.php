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
            // Screening and Class Information
            $table->date('screening_date')->nullable()->after('whatsapp_group');
            $table->string('screening_officer')->nullable()->after('screening_date');
            $table->time('class_time')->nullable()->after('screening_officer');
            
            // Tutor Information (tutor_phone_number stored here, but teacher_id is in converted_student_details)
            $table->string('tutor_phone_number')->nullable()->after('class_time');
            
            // Class Status
            $table->enum('class_status', ['Active', 'In Progress', 'Inactive', 'Dropped Out', 'Completed', 'Rejoining'])->nullable()->after('tutor_phone_number');
            
            // First PA Fields
            $table->enum('first_pa', ['Pending', 'Not Written', 'Completed'])->nullable()->after('class_status');
            $table->string('first_pa_mark')->nullable()->after('first_pa');
            $table->text('feedback_call_1')->nullable()->after('first_pa_mark');
            $table->text('first_pa_remarks')->nullable()->after('feedback_call_1');
            
            // Second PA Fields
            $table->enum('second_pa', ['Pending', 'Not Written', 'Completed'])->nullable()->after('first_pa_remarks');
            $table->string('second_pa_mark')->nullable()->after('second_pa');
            $table->text('feedback_call_2')->nullable()->after('second_pa_mark');
            $table->text('second_pa_remarks')->nullable()->after('feedback_call_2');
            
            // Third PA Fields
            $table->enum('third_pa', ['Pending', 'Not Written', 'Completed'])->nullable()->after('second_pa_remarks');
            $table->string('third_pa_mark')->nullable()->after('third_pa');
            $table->text('feedback_call_3')->nullable()->after('third_pa_mark');
            $table->text('third_pa_remarks')->nullable()->after('feedback_call_3');
            
            // Certification Exam Fields
            $table->enum('certification_exam', ['Pending', 'Not Written', 'Completed'])->nullable()->after('third_pa_remarks');
            $table->string('certification_exam_mark')->nullable()->after('certification_exam');
            
            // Course Completion Fields
            $table->enum('course_completion_feedback', ['yes', 'no'])->nullable()->after('certification_exam_mark');
            $table->enum('certificate_collection', ['Pending', 'Collected', 'Not Required'])->nullable()->after('course_completion_feedback');
            $table->enum('continuing_studies', ['yes', 'no'])->nullable()->after('certificate_collection');
            $table->text('reason')->nullable()->after('continuing_studies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropColumn([
                'screening_date',
                'screening_officer',
                'class_time',
                'tutor_phone_number',
                'class_status',
                'first_pa',
                'first_pa_mark',
                'feedback_call_1',
                'first_pa_remarks',
                'second_pa',
                'second_pa_mark',
                'feedback_call_2',
                'second_pa_remarks',
                'third_pa',
                'third_pa_mark',
                'feedback_call_3',
                'third_pa_remarks',
                'certification_exam',
                'certification_exam_mark',
                'course_completion_feedback',
                'certificate_collection',
                'continuing_studies',
                'reason',
            ]);
        });
    }
};

