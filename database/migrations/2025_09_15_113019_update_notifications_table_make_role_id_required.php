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
        Schema::table('notifications', function (Blueprint $table) {
            // First, drop the existing foreign key constraint
            $table->dropForeign(['role_id']);
            
            // Make role_id not nullable
            $table->unsignedBigInteger('role_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('role_id')->references('id')->on('user_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['role_id']);
            
            // Make role_id nullable again
            $table->unsignedBigInteger('role_id')->nullable(true)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('role_id')->references('id')->on('user_roles')->onDelete('cascade');
        });
    }
};
