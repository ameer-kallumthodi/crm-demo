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
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('lead_status_id')->nullable()->constrained('lead_statuses')->onDelete('set null');
            $table->string('activity_type');
            $table->text('description')->nullable();
            $table->date('followup_date')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropForeign(['lead_status_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn([
                'lead_id',
                'lead_status_id', 
                'activity_type',
                'description',
                'followup_date',
                'remarks',
                'created_by',
                'updated_by'
            ]);
        });
    }
};
