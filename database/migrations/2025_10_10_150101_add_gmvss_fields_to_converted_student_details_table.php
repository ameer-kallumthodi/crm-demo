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
        Schema::table('converted_student_details', function (Blueprint $table) {
            $table->string('registration_number')->nullable()->after('tma');
            $table->date('converted_date')->nullable()->after('registration_number');
            $table->string('enrollment_number')->nullable()->after('converted_date');
            $table->unsignedBigInteger('registration_link_id')->nullable()->after('enrollment_number');
            $table->enum('certificate_status', [
                'In Progress', 
                'Online Result Not Arrived', 
                'One Result Arrived', 
                'Certificate Arrived', 
                'Not Received', 
                'No Admission'
            ])->nullable()->after('registration_link_id');
            $table->date('certificate_received_date')->nullable()->after('certificate_status');
            $table->date('certificate_issued_date')->nullable()->after('certificate_received_date');
            $table->text('remarks')->nullable()->after('certificate_issued_date');
            
            // Foreign key for registration link
            $table->foreign('registration_link_id')->references('id')->on('registration_links')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_details', function (Blueprint $table) {
            $table->dropForeign(['registration_link_id']);
            $table->dropColumn([
                'registration_number',
                'converted_date', 
                'enrollment_number',
                'registration_link_id',
                'certificate_status',
                'certificate_received_date',
                'certificate_issued_date',
                'remarks'
            ]);
        });
    }
};
