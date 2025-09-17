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
        // Add deleted_at column to telecaller_sessions table
        if (Schema::hasTable('telecaller_sessions') && !Schema::hasColumn('telecaller_sessions', 'deleted_at')) {
            Schema::table('telecaller_sessions', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add deleted_at column to telecaller_idle_times table
        if (Schema::hasTable('telecaller_idle_times') && !Schema::hasColumn('telecaller_idle_times', 'deleted_at')) {
            Schema::table('telecaller_idle_times', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add deleted_at column to telecaller_activity_logs table
        if (Schema::hasTable('telecaller_activity_logs') && !Schema::hasColumn('telecaller_activity_logs', 'deleted_at')) {
            Schema::table('telecaller_activity_logs', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove deleted_at column from telecaller_sessions table
        if (Schema::hasTable('telecaller_sessions') && Schema::hasColumn('telecaller_sessions', 'deleted_at')) {
            Schema::table('telecaller_sessions', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Remove deleted_at column from telecaller_idle_times table
        if (Schema::hasTable('telecaller_idle_times') && Schema::hasColumn('telecaller_idle_times', 'deleted_at')) {
            Schema::table('telecaller_idle_times', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Remove deleted_at column from telecaller_activity_logs table
        if (Schema::hasTable('telecaller_activity_logs') && Schema::hasColumn('telecaller_activity_logs', 'deleted_at')) {
            Schema::table('telecaller_activity_logs', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
