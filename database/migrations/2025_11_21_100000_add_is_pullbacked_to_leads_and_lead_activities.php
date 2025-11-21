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
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'is_pullbacked')) {
                $table->boolean('is_pullbacked')
                    ->default(false)
                    ->after('is_converted');
            }
        });

        Schema::table('lead_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('lead_activities', 'is_pullbacked')) {
                $table->boolean('is_pullbacked')
                    ->default(false)
                    ->after('activity_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_activities', function (Blueprint $table) {
            if (Schema::hasColumn('lead_activities', 'is_pullbacked')) {
                $table->dropColumn('is_pullbacked');
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'is_pullbacked')) {
                $table->dropColumn('is_pullbacked');
            }
        });
    }
};

