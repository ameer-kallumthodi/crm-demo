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
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_course_id')->nullable()->after('course_id');
            $table->foreign('sub_course_id')->references('id')->on('sub_courses')->onDelete('set null');
            $table->index('sub_course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropForeign(['sub_course_id']);
            $table->dropIndex(['sub_course_id']);
            $table->dropColumn('sub_course_id');
        });
    }
};
