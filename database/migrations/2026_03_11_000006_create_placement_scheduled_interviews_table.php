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
        Schema::create('placement_scheduled_interviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('converted_lead_id');
            $table->string('company_name');
            $table->string('place');
            $table->date('interview_date');
            $table->string('status', 20)->default('pending'); // pending, placed, not_placed
            $table->timestamps();

            $table->foreign('converted_lead_id')->references('id')->on('converted_leads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_scheduled_interviews');
    }
};
