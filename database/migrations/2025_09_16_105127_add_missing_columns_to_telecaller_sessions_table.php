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
            // Add missing columns that our models expect
            $table->string('session_id')->unique()->after('id');
            $table->enum('logout_type', ['manual', 'auto', 'system'])->default('manual')->after('logout_time');
            $table->integer('total_duration_minutes')->nullable()->after('logout_type');
            $table->integer('active_duration_minutes')->nullable()->after('total_duration_minutes');
            $table->integer('idle_duration_minutes')->nullable()->after('active_duration_minutes');
            $table->string('ip_address')->nullable()->after('idle_duration_minutes');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->boolean('is_active')->default(true)->after('user_agent');
            
            // Rename telecaller_id to user_id to match our model
            $table->renameColumn('telecaller_id', 'user_id');
            
            // Add indexes for new columns
            $table->index('session_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telecaller_sessions', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn([
                'session_id',
                'logout_type', 
                'total_duration_minutes',
                'active_duration_minutes',
                'idle_duration_minutes',
                'ip_address',
                'user_agent',
                'is_active'
            ]);
            
            // Rename user_id back to telecaller_id
            $table->renameColumn('user_id', 'telecaller_id');
            
            // Drop indexes
            $table->dropIndex(['session_id']);
            $table->dropIndex(['is_active']);
        });
    }
};
