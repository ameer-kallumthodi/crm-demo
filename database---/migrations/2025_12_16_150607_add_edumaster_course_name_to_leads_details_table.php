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
            if (!Schema::hasColumn('leads_details', 'edumaster_course_name')) {
                $table->string('edumaster_course_name')->nullable()->after('course_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            if (Schema::hasColumn('leads_details', 'edumaster_course_name')) {
                $table->dropColumn('edumaster_course_name');
            }
        });
    }
};
