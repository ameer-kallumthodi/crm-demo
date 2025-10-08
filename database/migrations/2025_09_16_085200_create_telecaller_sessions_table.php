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
        Schema::create('telecaller_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->unsignedBigInteger('user_id'); // renamed from telecaller_id
            $table->timestamp('login_time');
            $table->timestamp('logout_time')->nullable();
            $table->enum('logout_type', ['manual', 'auto', 'system', 'session_change'])->default('manual');
            $table->integer('total_duration_minutes')->nullable();
            $table->integer('active_duration_minutes')->nullable();
            $table->integer('idle_duration_minutes')->nullable();
            $table->integer('idle_time')->default(0)->comment('Idle time in seconds');
            $table->integer('tasks_completed')->default(0);
            $table->integer('tasks_pending')->default(0);
            $table->boolean('is_auto_logout')->default(false);
            $table->text('logout_reason')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for efficient querying
            $table->index(['user_id', 'login_time']);
            $table->index(['login_time', 'logout_time']);
            $table->index(['user_id', 'created_at']);
            $table->index('is_auto_logout');
            $table->index('session_id');
            $table->index('is_active');
            $table->unique(['user_id', 'session_id'], 'telecaller_sessions_user_session_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telecaller_sessions');
    }
};
