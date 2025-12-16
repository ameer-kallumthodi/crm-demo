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
            // Add selected courses field (JSON to store array of selected courses: SSLC, Plus two, UG, PG)
            if (!Schema::hasColumn('leads_details', 'selected_courses')) {
                $table->json('selected_courses')->nullable()->after('course_type');
            }
            
            // Add back year fields
            if (!Schema::hasColumn('leads_details', 'sslc_back_year')) {
                $table->integer('sslc_back_year')->nullable()->after('selected_courses');
            }
            
            if (!Schema::hasColumn('leads_details', 'plustwo_back_year')) {
                $table->integer('plustwo_back_year')->nullable()->after('sslc_back_year');
            }
            
            // Add residential address field
            if (!Schema::hasColumn('leads_details', 'residential_address')) {
                $table->text('residential_address')->nullable()->after('mother_contact_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $columnsToDrop = [];
            
            if (Schema::hasColumn('leads_details', 'residential_address')) {
                $columnsToDrop[] = 'residential_address';
            }
            
            if (Schema::hasColumn('leads_details', 'plustwo_back_year')) {
                $columnsToDrop[] = 'plustwo_back_year';
            }
            
            if (Schema::hasColumn('leads_details', 'sslc_back_year')) {
                $columnsToDrop[] = 'sslc_back_year';
            }
            
            if (Schema::hasColumn('leads_details', 'selected_courses')) {
                $columnsToDrop[] = 'selected_courses';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
