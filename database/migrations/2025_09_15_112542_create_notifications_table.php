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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'success', 'warning', 'error'])->default('info');
            $table->enum('target_type', ['all', 'role', 'user'])->default('all');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id')->nullable(); // null = all users, specific ID = single user
            $table->unsignedBigInteger('created_by');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('role_id')->references('id')->on('user_roles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
