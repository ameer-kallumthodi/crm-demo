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
        // Add is_b2b to teams table
        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('is_b2b')->default(false)->after('marketing_team');
        });

        // Add is_b2b to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_b2b')->default(false)->after('is_active');
        });

        // Add is_b2b to leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('is_b2b')->default(false)->after('is_converted');
        });

        // Add is_b2b to converted_leads table
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->boolean('is_b2b')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('is_b2b');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_b2b');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('is_b2b');
        });

        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropColumn('is_b2b');
        });
    }
};
