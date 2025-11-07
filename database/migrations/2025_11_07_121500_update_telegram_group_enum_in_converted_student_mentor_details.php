<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure legacy values do not break the enum contraction
        DB::statement("UPDATE converted_student_mentor_details SET telegram_group = NULL WHERE telegram_group NOT IN ('Sent link', 'task complete')");

        DB::statement("ALTER TABLE converted_student_mentor_details MODIFY COLUMN telegram_group ENUM('Sent link', 'task complete') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE converted_student_mentor_details MODIFY COLUMN telegram_group ENUM('Call not answered', 'switched off', 'line busy', 'student asks to call later', 'lack of interest in conversation', 'wrong contact', 'inconsistent responses', 'task complete') NULL");
    }
};

