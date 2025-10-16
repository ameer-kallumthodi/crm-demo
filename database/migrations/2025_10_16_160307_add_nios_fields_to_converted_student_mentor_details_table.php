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
            // Add NIOS specific fields
            $table->enum('exam_fees', ['Not Respond', 'Task Complete'])->nullable()->after('assignment');
            $table->enum('pcp_class', ['Not Respond', 'Task Complete'])->nullable()->after('call_6');
            $table->enum('practical_record', ['Not Respond', '1 Subject Attend', '2 Subject Attend', '3 Subject Attend', '4 Subject Attend', '5 Subject Attend', '6 Subject Attend', 'Task Complete'])->nullable()->after('call_7');
            $table->enum('id_card', ['Did Not', 'Task Complete'])->nullable()->after('model_exam');
            $table->enum('practical_hall_ticket', ['Did Not', 'Task Complete'])->nullable()->after('id_card');
            $table->enum('particle_exam', ['Did not log in on time', 'missed the exam', 'technical issue', 'task complete'])->nullable()->after('call_9');
            $table->enum('theory_hall_ticket', ['Did Not', 'Task Complete'])->nullable()->after('particle_exam');
            $table->enum('call_10', ['Call Not Answered', 'Switched Off', 'Line Busy', 'Student Asks to Call Later', 'Lack of Interest in Conversation', 'Wrong Contact', 'Inconsistent Responses', 'Task Complete'])->nullable()->after('theory_hall_ticket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropColumn([
                'exam_fees',
                'pcp_class',
                'practical_record',
                'id_card',
                'practical_hall_ticket',
                'particle_exam',
                'theory_hall_ticket',
                'call_10'
            ]);
        });
    }
};