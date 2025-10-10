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
        // Drop the existing table if it exists
        Schema::dropIfExists('converted_student_details');
        
        // Create new table with only the required fields
        Schema::create('converted_student_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('converted_lead_id');
            $table->string('reg_fee')->nullable();
            $table->string('exam_fee')->nullable();
            $table->string('enroll_no')->nullable();
            $table->string('id_card')->nullable();
            $table->string('tma')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes support
            
            // Foreign key constraint
            $table->foreign('converted_lead_id')
                ->references('id')
                ->on('converted_leads')
                ->onDelete('cascade');
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new table
        Schema::dropIfExists('converted_student_details');
    }
};
