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
        // Add rating to leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('interest_status')->comment('Rating from 1-10');
        });
        
        // Add rating to lead_activities table
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('reason')->comment('Rating from 1-10');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
        
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
};