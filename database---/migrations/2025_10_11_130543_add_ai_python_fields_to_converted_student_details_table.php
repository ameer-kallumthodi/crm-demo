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
            $table->string('call_status')->nullable()->after('howmany_interview');
            $table->string('class_information')->nullable()->after('call_status');
            $table->string('orientation_class_status')->nullable()->after('class_information');
            $table->date('class_starting_date')->nullable()->after('orientation_class_status');
            $table->date('class_ending_date')->nullable()->after('class_starting_date');
            $table->string('whatsapp_group_status')->nullable()->after('class_ending_date');
            $table->time('class_time')->nullable()->after('whatsapp_group_status');
            $table->string('class_status')->nullable()->after('class_time');
            $table->date('complete_cancel_date')->nullable()->after('class_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_details', function (Blueprint $table) {
            $table->dropColumn([
                'call_status',
                'class_information',
                'orientation_class_status',
                'class_starting_date',
                'class_ending_date',
                'whatsapp_group_status',
                'class_time',
                'class_status',
                'complete_cancel_date',
            ]);
        });
    }
};