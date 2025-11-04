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
        // Check if the column exists before attempting to rename
        if (Schema::hasColumn('converted_student_mentor_details', 'screening_office')) {
            Schema::table('converted_student_mentor_details', function (Blueprint $table) {
                $table->renameColumn('screening_office', 'screening_officer');
            });
        }
        // If the column doesn't exist, it means screening_officer was created directly
        // in a later migration, so we can skip this rename
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the column exists before attempting to rename
        if (Schema::hasColumn('converted_student_mentor_details', 'screening_officer') && 
            !Schema::hasColumn('converted_student_mentor_details', 'screening_office')) {
            Schema::table('converted_student_mentor_details', function (Blueprint $table) {
                $table->renameColumn('screening_officer', 'screening_office');
            });
        }
    }
};
