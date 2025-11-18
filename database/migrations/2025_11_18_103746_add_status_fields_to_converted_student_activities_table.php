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
        Schema::table('converted_student_activities', function (Blueprint $table) {
            $table->string('status')->nullable()->after('converted_lead_id'); // paid, unpaid, cancel, pending, followup
            $table->string('paid_status')->nullable()->after('status'); // Fully paid, Registration Paid, Certificate Paid, Halticket Paid, Exam fee Paid
            $table->string('call_status')->nullable()->after('paid_status'); // RNR, Switch off, Completed
            $table->date('followup_date')->nullable()->after('activity_time');
            $table->time('followup_time')->nullable()->after('followup_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_activities', function (Blueprint $table) {
            $table->dropColumn(['status', 'paid_status', 'call_status', 'followup_date', 'followup_time']);
        });
    }
};
