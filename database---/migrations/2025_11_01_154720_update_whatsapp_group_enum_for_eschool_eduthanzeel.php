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
        // Update whatsapp_group enum to include values used by E-School and Eduthanzeel mentor pages
        // Current: ['Sent link', 'Task Completed']
        // New: ['Sent link', 'Task Completed', 'Not Responding', 'Task Complete']
        // This allows backward compatibility while adding new values
        DB::statement("ALTER TABLE `converted_student_mentor_details` MODIFY COLUMN `whatsapp_group` ENUM('Sent link', 'Task Completed', 'Not Responding', 'Task Complete') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values (may cause data loss if new values are in use)
        DB::statement("ALTER TABLE `converted_student_mentor_details` MODIFY COLUMN `whatsapp_group` ENUM('Sent link', 'Task Completed') NULL");
    }
};
