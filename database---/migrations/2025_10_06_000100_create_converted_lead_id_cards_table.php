<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('converted_lead_id_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('converted_lead_id');
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamp('generated_at')->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamps();

            $table->foreign('converted_lead_id')->references('id')->on('converted_leads')->onDelete('cascade');
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('converted_lead_id_cards');
    }
};
