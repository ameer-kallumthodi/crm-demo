<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'Not Respond'
        DB::statement("ALTER TABLE converted_student_mentor_details MODIFY COLUMN app ENUM('Provided app', 'OTP Problem', 'Task Completed', 'Not Respond') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values (remove 'Not Respond')
        // Note: This will fail if there are any records with 'Not Respond' value
        DB::statement("ALTER TABLE converted_student_mentor_details MODIFY COLUMN app ENUM('Provided app', 'OTP Problem', 'Task Completed') NULL");
    }
};
