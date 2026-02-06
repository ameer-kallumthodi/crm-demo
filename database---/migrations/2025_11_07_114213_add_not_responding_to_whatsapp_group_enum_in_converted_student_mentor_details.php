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
        // Modify the enum to include 'Not Responding' and 'Task Complete'
        DB::statement("ALTER TABLE converted_student_mentor_details MODIFY COLUMN whatsapp_group ENUM('Sent link', 'Task Completed', 'Not Responding', 'Task Complete') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        // Note: This will fail if there are any records with 'Not Responding' or 'Task Complete' values
        DB::statement("ALTER TABLE converted_student_mentor_details MODIFY COLUMN whatsapp_group ENUM('Sent link', 'Task Completed') NULL");
    }
};
