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
        Schema::create('marketing_leads', function (Blueprint $table) {
            $table->id();
            
            // BDE Information
            $table->unsignedBigInteger('marketing_bde_id')->nullable()->comment('Marketing BDE user ID');
            
            // Visit Information
            $table->date('date_of_visit');
            $table->string('location', 255);
            $table->string('house_number', 255)->nullable();
            
            // Lead Information
            $table->string('lead_name', 255);
            $table->string('code', 10)->nullable();
            $table->string('phone', 20);
            $table->string('whatsapp_code', 10)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->text('address')->nullable();
            $table->enum('lead_type', ['Student', 'Parent', 'Working Professional', 'Institution Representative', 'Others']);
            $table->json('interested_courses')->nullable()->comment('Array of interested courses');
            $table->text('remarks')->nullable();
            
            // Assignment Information
            $table->boolean('is_telecaller_assigned')->default(false);
            $table->timestamp('assigned_at')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('marketing_bde_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('marketing_bde_id');
            $table->index('date_of_visit');
            $table->index('is_telecaller_assigned');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_leads');
    }
};
