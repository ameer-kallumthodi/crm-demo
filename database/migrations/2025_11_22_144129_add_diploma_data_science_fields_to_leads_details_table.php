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
        Schema::table('leads_details', function (Blueprint $table) {
            // Personal Details
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->boolean('is_employed')->nullable()->after('gender');
            
            // Communication Details
            $table->string('father_contact_number')->nullable()->after('parents_number');
            $table->string('father_contact_code', 10)->nullable()->after('father_contact_number');
            $table->string('mother_contact_number')->nullable()->after('father_contact_code');
            $table->string('mother_contact_code', 10)->nullable()->after('mother_contact_number');
            
            // Programme Details
            $table->enum('programme_type', ['online', 'offline'])->nullable()->after('course_id');
            $table->string('location')->nullable()->after('programme_type')->comment('Ernakulam or Malappuram for offline');
            $table->unsignedBigInteger('class_time_id')->nullable()->after('location');
            
            // Documents
            $table->string('post_graduation_certificate')->nullable()->after('ug_certificate');
            
            // Foreign key
            $table->foreign('class_time_id')->references('id')->on('class_times')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->dropForeign(['class_time_id']);
            $table->dropColumn([
                'gender',
                'is_employed',
                'father_contact_number',
                'father_contact_code',
                'mother_contact_number',
                'mother_contact_code',
                'programme_type',
                'location',
                'class_time_id',
                'post_graduation_certificate',
            ]);
        });
    }
};
