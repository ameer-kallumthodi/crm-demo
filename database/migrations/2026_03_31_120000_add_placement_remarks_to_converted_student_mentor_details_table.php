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
            if (!Schema::hasColumn('converted_student_mentor_details', 'placement_remarks')) {
                $table->text('placement_remarks')->nullable()->after('remarks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            if (Schema::hasColumn('converted_student_mentor_details', 'placement_remarks')) {
                $table->dropColumn('placement_remarks');
            }
        });
    }
};
