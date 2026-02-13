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
        Schema::table('online_teaching_faculties', function (Blueprint $table) {
            // Make full_name nullable
            $table->string('full_name')->nullable()->change();

            // Make primary_mobile_number required (NOT NULL)
            $table->string('primary_mobile_number', 30)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_teaching_faculties', function (Blueprint $table) {
            // Reverse: make full_name required again
            $table->string('full_name')->nullable(false)->change();

            // Reverse: make primary_mobile_number nullable again
            $table->string('primary_mobile_number', 30)->nullable()->change();
        });
    }
};
