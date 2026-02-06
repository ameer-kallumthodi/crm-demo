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
        Schema::create('converted_student_activities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('converted_lead_id');
            $table->date('activity_date')->nullable();
            $table->time('activity_time')->nullable();
            $table->string('activity_type')->nullable();
            $table->text('description')->nullable();
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();

            $table->foreign('converted_lead_id')->references('id')->on('converted_leads')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('converted_student_activities');
    }
};

