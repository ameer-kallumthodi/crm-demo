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
            // Drop the global unique constraint on session_id
            $table->dropUnique(['session_id']);
            
            // The combination unique constraint already exists, so we don't need to add it again
            // $table->unique(['user_id', 'session_id'], 'telecaller_sessions_user_session_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telecaller_sessions', function (Blueprint $table) {
            // Restore the global unique constraint on session_id
            $table->unique('session_id');
        });
    }
};
