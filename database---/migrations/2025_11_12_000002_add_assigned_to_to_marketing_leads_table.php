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
        Schema::table('marketing_leads', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable()->after('assigned_by')->comment('Telecaller user ID to whom the lead is assigned');
            
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_leads', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropIndex(['assigned_to']);
            $table->dropColumn('assigned_to');
        });
    }
};

