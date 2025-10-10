<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            // Remove the fields that are now stored in converted_student_details
            if (Schema::hasColumn('converted_leads', 'reg_fee')) {
                $table->dropColumn('reg_fee');
            }
            if (Schema::hasColumn('converted_leads', 'exam_fee')) {
                $table->dropColumn('exam_fee');
            }
            if (Schema::hasColumn('converted_leads', 'enroll_no')) {
                $table->dropColumn('enroll_no');
            }
            if (Schema::hasColumn('converted_leads', 'id_card')) {
                $table->dropColumn('id_card');
            }
            if (Schema::hasColumn('converted_leads', 'tma')) {
                $table->dropColumn('tma');
            }
        });
    }

    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            // Add the fields back if rolling back
            $table->string('reg_fee')->nullable();
            $table->string('exam_fee')->nullable();
            $table->string('enroll_no')->nullable();
            $table->string('id_card')->nullable();
            $table->string('tma')->nullable();
        });
    }
};
