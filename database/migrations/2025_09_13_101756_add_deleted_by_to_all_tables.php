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
        // Add deleted_by to all main tables
        $tables = [
            'users',
            'user_roles', 
            'teams',
            'leads',
            'lead_statuses',
            'lead_sources',
            'countries',
            'courses',
            'lead_activities',
            'converted_leads',
            'boards',
            'batches',
            'subjects',
            'settings'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Check if updated_by column exists, if not add after updated_at
                    if (Schema::hasColumn($tableName, 'updated_by')) {
                        $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
                    } else {
                        $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_at');
                    }
                    $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'user_roles', 
            'teams',
            'leads',
            'lead_statuses',
            'lead_sources',
            'countries',
            'courses',
            'lead_activities',
            'converted_leads',
            'boards',
            'batches',
            'subjects',
            'settings'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['deleted_by']);
                    $table->dropColumn('deleted_by');
                });
            }
        }
    }
};
