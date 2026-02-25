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
        Schema::table('leads_details', function (Blueprint $table) {
            $table->string('medium_of_study', 50)->nullable()->after('second_language');
            $table->string('previous_qualification', 50)->nullable()->after('medium_of_study');
            $table->string('technology_performance_category', 50)->nullable()->after('previous_qualification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->dropColumn([
                'medium_of_study',
                'previous_qualification',
                'technology_performance_category',
            ]);
        });
    }
};
