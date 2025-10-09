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
        Schema::table('telecaller_sessions', function (Blueprint $table) {
            // Add working hours minutes field
            $table->integer('working_hours_minutes')->nullable()->after('idle_duration_minutes')->comment('Working hours duration in minutes (9:30 AM - 7:30 PM)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telecaller_sessions', function (Blueprint $table) {
            // Remove working hours minutes field
            $table->dropColumn('working_hours_minutes');
        });
    }
};
