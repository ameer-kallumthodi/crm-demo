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
            // Drop the unique constraint on session_id
            $table->dropUnique(['session_id']);
            
            // Add a composite unique constraint on user_id and session_id
            $table->unique(['user_id', 'session_id'], 'telecaller_sessions_user_session_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telecaller_sessions', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('telecaller_sessions_user_session_unique');
            
            // Restore the unique constraint on session_id
            $table->unique('session_id');
        });
    }
};