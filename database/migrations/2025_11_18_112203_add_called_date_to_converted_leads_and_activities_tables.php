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
            $table->date('called_date')->nullable()->after('call_status');
        });

        Schema::table('converted_student_activities', function (Blueprint $table) {
            $table->date('called_date')->nullable()->after('call_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropColumn('called_date');
        });

        Schema::table('converted_student_activities', function (Blueprint $table) {
            $table->dropColumn('called_date');
        });
    }
};
