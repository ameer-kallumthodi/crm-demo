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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->integer('age')->nullable();
            $table->string('phone')->nullable();
            $table->string('code')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('whatsapp_code')->nullable();
            $table->string('email')->nullable();
            $table->text('qualification')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('interest_status')->nullable();
            $table->tinyInteger('rating')->nullable()->comment('Rating from 1-10');
            $table->unsignedBigInteger('lead_status_id')->nullable();
            $table->unsignedBigInteger('lead_source_id')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('telecaller_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('place')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->boolean('by_meta')->default(false);
            $table->string('meta_lead_id')->nullable();
            $table->boolean('is_converted')->default(false);
            $table->date('followup_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('lead_status_id')->references('id')->on('lead_statuses')->onDelete('set null');
            $table->foreign('lead_source_id')->references('id')->on('lead_sources')->onDelete('set null');
            $table->foreign('telecaller_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
