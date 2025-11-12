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
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('marketing_leads_id')->nullable()->after('meta_lead_id')->comment('ID from marketing_leads table');
            $table->text('marketing_remarks')->nullable()->after('marketing_leads_id')->comment('Remarks from marketing lead');
            
            $table->foreign('marketing_leads_id')->references('id')->on('marketing_leads')->onDelete('set null');
            $table->index('marketing_leads_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['marketing_leads_id']);
            $table->dropIndex(['marketing_leads_id']);
            $table->dropColumn(['marketing_leads_id', 'marketing_remarks']);
        });
    }
};

