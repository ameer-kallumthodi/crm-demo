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
        Schema::table('team_details', function (Blueprint $table) {
            // Section 1: Partner Identification Details (Editable)
            $table->string('b2b_partner_id')->nullable();
            $table->string('b2b_code')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->string('partner_status')->nullable(); // Active, Inactive, Suspended

            // Section 2: Assigned Officer Details (Static)
            $table->string('b2b_officer_name')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('designation')->nullable();
            $table->string('official_contact_number')->nullable();
            $table->string('whatsapp_business_number')->nullable();
            $table->string('official_email_id')->nullable();

            // Section 4: Operational Schedule (Static)
            $table->string('working_days')->nullable();
            $table->string('office_hours')->nullable();
            $table->string('break_time')->nullable();
            $table->string('holiday_policy')->nullable();

            // Section 6: Banking & Payment Details (Static)
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_details', function (Blueprint $table) {
            $table->dropColumn([
                'b2b_partner_id', 'b2b_code', 'date_of_joining', 'partner_status',
                'b2b_officer_name', 'employee_id', 'designation', 'official_contact_number', 'whatsapp_business_number', 'official_email_id',
                'working_days', 'office_hours', 'break_time', 'holiday_policy',
                'account_holder_name', 'bank_name', 'account_number', 'ifsc_code'
            ]);
        });
    }
};
