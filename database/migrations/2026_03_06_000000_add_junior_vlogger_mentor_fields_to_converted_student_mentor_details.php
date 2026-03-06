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
            $table->unsignedInteger('total_class_days')->nullable()->after('remarks');
            $table->string('first_term_fee_status')->nullable()->after('total_class_days');
            $table->date('first_term_start_date')->nullable()->after('first_term_fee_status');
            $table->string('first_term_trainer_name_phone')->nullable()->after('first_term_start_date');
            $table->date('first_term_task_1_date')->nullable()->after('first_term_trainer_name_phone');
            $table->date('first_term_task_2_date')->nullable()->after('first_term_task_1_date');
            $table->unsignedInteger('first_term_number_of_days')->nullable()->after('first_term_task_2_date');
            $table->date('first_term_completion_date')->nullable()->after('first_term_number_of_days');
            $table->string('second_term_fee_status')->nullable()->after('first_term_completion_date');
            $table->date('second_term_start_date')->nullable()->after('second_term_fee_status');
            $table->string('second_term_trainer_name_phone')->nullable()->after('second_term_start_date');
            $table->date('second_term_task_1_date')->nullable()->after('second_term_trainer_name_phone');
            $table->date('second_term_task_2_date')->nullable()->after('second_term_task_1_date');
            $table->unsignedInteger('second_term_number_of_days')->nullable()->after('second_term_task_2_date');
            $table->date('second_term_completion_date')->nullable()->after('second_term_number_of_days');
            $table->string('third_term_fee_status')->nullable()->after('second_term_completion_date');
            $table->date('third_term_start_date')->nullable()->after('third_term_fee_status');
            $table->string('third_term_trainer_name_phone')->nullable()->after('third_term_start_date');
            $table->date('third_term_project_1_date')->nullable()->after('third_term_trainer_name_phone');
            $table->date('third_term_project_2_date')->nullable()->after('third_term_project_1_date');
            $table->date('third_term_project_3_date')->nullable()->after('third_term_project_2_date');
            $table->unsignedInteger('third_term_number_of_days')->nullable()->after('third_term_project_3_date');
            $table->date('third_term_completion_date')->nullable()->after('third_term_number_of_days');
            $table->text('jv_feedback_notes')->nullable()->after('third_term_completion_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropColumn([
                'total_class_days',
                'first_term_fee_status',
                'first_term_start_date',
                'first_term_trainer_name_phone',
                'first_term_task_1_date',
                'first_term_task_2_date',
                'first_term_number_of_days',
                'first_term_completion_date',
                'second_term_fee_status',
                'second_term_start_date',
                'second_term_trainer_name_phone',
                'second_term_task_1_date',
                'second_term_task_2_date',
                'second_term_number_of_days',
                'second_term_completion_date',
                'third_term_fee_status',
                'third_term_start_date',
                'third_term_trainer_name_phone',
                'third_term_project_1_date',
                'third_term_project_2_date',
                'third_term_project_3_date',
                'third_term_number_of_days',
                'third_term_completion_date',
                'jv_feedback_notes',
            ]);
        });
    }
};
