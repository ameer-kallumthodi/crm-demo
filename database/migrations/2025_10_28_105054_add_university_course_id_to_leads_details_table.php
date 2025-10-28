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
        Schema::table('leads_details', function (Blueprint $table) {
            $table->foreignId('university_course_id')->nullable()->constrained('university_courses')->onDelete('set null');
            $table->index('university_course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->dropForeign(['university_course_id']);
            $table->dropIndex(['university_course_id']);
            $table->dropColumn('university_course_id');
        });
    }
};
