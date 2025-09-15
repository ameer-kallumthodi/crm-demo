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
        Schema::create('voxbay_call_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['incoming', 'outgoing', 'missedcall'])->nullable();
            $table->string('call_uuid', 100)->nullable();
            $table->string('calledNumber', 60)->nullable();
            $table->string('callerNumber', 60)->nullable();
            $table->string('AgentNumber', 100)->nullable();
            $table->string('extensionNumber', 100)->nullable();
            $table->string('destinationNumber', 60)->nullable();
            $table->string('callerid', 100)->nullable();
            $table->string('duration', 60)->nullable();
            $table->string('status', 60)->nullable();
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('recording_URL', 260)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraints
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
        Schema::dropIfExists('voxbay_call_logs');
    }
};
