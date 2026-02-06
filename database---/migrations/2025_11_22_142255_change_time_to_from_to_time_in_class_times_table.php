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
        Schema::table('class_times', function (Blueprint $table) {
            $table->dropColumn('time');
            $table->time('from_time')->after('course_id');
            $table->time('to_time')->after('from_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_times', function (Blueprint $table) {
            $table->dropColumn(['from_time', 'to_time']);
            $table->string('time')->after('course_id');
        });
    }
};
