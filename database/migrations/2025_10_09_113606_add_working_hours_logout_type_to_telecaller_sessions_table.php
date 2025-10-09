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
            // Modify the logout_type enum to include 'working_hours'
            $table->enum('logout_type', ['manual', 'auto', 'system', 'session_change', 'working_hours'])->default('manual')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telecaller_sessions', function (Blueprint $table) {
            // Revert the logout_type enum to original values
            $table->enum('logout_type', ['manual', 'auto', 'system', 'session_change'])->default('manual')->change();
        });
    }
};
