<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run this migration if we're not doing a fresh migration
        // Check if converted_leads table exists and has data (indicating existing database)
        if (Schema::hasTable('converted_leads') && DB::table('converted_leads')->count() > 0) {
            // This is an existing database, proceed with recreation
            Schema::dropIfExists('converted_student_details');
            
            // Create new table with simplified structure
            Schema::create('converted_student_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('converted_lead_id');
                $table->string('reg_fee')->nullable();
                $table->string('exam_fee')->nullable();
                $table->string('enroll_no')->nullable();
                $table->string('id_card')->nullable();
                $table->string('tma')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
                
                // Foreign key constraints
                $table->foreign('converted_lead_id')
                    ->references('id')
                    ->on('converted_leads')
                    ->onDelete('cascade');
                
                $table->foreign('deleted_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            });

            // Backfill data from converted_leads table
            $convertedLeads = DB::table('converted_leads')->get();
            
            foreach ($convertedLeads as $convertedLead) {
                DB::table('converted_student_details')->insert([
                    'converted_lead_id' => $convertedLead->id,
                    'reg_fee' => null,
                    'exam_fee' => null,
                    'enroll_no' => null,
                    'id_card' => null,
                    'tma' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        // If this is a fresh migration (no data in converted_leads), 
        // skip this migration and let the original create migration handle it
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new table
        Schema::dropIfExists('converted_student_details');
    }
};
