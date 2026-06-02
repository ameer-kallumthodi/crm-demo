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
            if (! Schema::hasColumn('converted_student_mentor_details', 'call_time')) {
                $table->time('call_time')->nullable()->after('call_1');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            if (Schema::hasColumn('converted_student_mentor_details', 'call_time')) {
                $table->dropColumn('call_time');
            }
        });
    }
};
