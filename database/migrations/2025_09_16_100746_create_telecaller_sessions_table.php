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
            $table->unsignedBigInteger('telecaller_id');
            $table->timestamp('login_time');
            $table->timestamp('logout_time')->nullable();
            $table->integer('idle_time')->default(0)->comment('Idle time in seconds');
            $table->integer('tasks_completed')->default(0);
            $table->integer('tasks_pending')->default(0);
            $table->boolean('is_auto_logout')->default(false);
            $table->text('logout_reason')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('telecaller_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for efficient querying
            $table->index(['telecaller_id', 'login_time']);
            $table->index(['login_time', 'logout_time']);
            $table->index(['telecaller_id', 'created_at']);
            $table->index('is_auto_logout');
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
