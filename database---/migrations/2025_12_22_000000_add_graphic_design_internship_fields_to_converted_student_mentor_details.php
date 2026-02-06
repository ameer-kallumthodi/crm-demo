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
            $table->date('first_term_internship_exam_date')->nullable()->after('experience_certificate_distribution_date');
            $table->string('first_term_internship_exam_marks')->nullable()->after('first_term_internship_exam_date');
            $table->text('first_term_internship_exam_feedback')->nullable()->after('first_term_internship_exam_marks');
            $table->date('final_internship_certification_exam_date')->nullable()->after('first_term_internship_exam_feedback');
            $table->string('final_internship_certification_exam_marks')->nullable()->after('final_internship_certification_exam_date');
            $table->text('final_internship_certification_exam_feedback')->nullable()->after('final_internship_certification_exam_marks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropColumn([
                'first_term_internship_exam_date',
                'first_term_internship_exam_marks',
                'first_term_internship_exam_feedback',
                'final_internship_certification_exam_date',
                'final_internship_certification_exam_marks',
                'final_internship_certification_exam_feedback',
            ]);
        });
    }
};


