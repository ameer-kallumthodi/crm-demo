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
            if (!Schema::hasColumn('leads_details', 'plustwo_subject')) {
                $table->string('plustwo_subject')->nullable()->after('plustwo_back_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            if (Schema::hasColumn('leads_details', 'plustwo_subject')) {
                $table->dropColumn('plustwo_subject');
            }
        });
    }
};
