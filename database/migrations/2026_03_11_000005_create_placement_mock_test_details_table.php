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
        Schema::create('placement_mock_test_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('converted_lead_id');
            $table->unsignedTinyInteger('speaking_capacity'); // 1-10
            $table->unsignedTinyInteger('presentation_skill');  // 1-10
            $table->unsignedTinyInteger('character');          // 1-10
            $table->unsignedTinyInteger('dedication');          // 1-10
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->foreign('converted_lead_id')->references('id')->on('converted_leads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_mock_test_details');
    }
};
