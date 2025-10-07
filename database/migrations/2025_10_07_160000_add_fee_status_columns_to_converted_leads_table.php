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
        Schema::table('converted_leads', function (Blueprint $table) {
            if (!Schema::hasColumn('converted_leads', 'reg_fee')) {
                $table->string('reg_fee')->nullable();
            }
            if (!Schema::hasColumn('converted_leads', 'exam_fee')) {
                $table->string('exam_fee')->nullable();
            }
            if (!Schema::hasColumn('converted_leads', 'id_card')) {
                $table->string('id_card')->nullable();
            }
            if (!Schema::hasColumn('converted_leads', 'tma')) {
                $table->string('tma')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            if (Schema::hasColumn('converted_leads', 'reg_fee')) {
                $table->dropColumn('reg_fee');
            }
            if (Schema::hasColumn('converted_leads', 'exam_fee')) {
                $table->dropColumn('exam_fee');
            }
            if (Schema::hasColumn('converted_leads', 'id_card')) {
                $table->dropColumn('id_card');
            }
            if (Schema::hasColumn('converted_leads', 'tma')) {
                $table->dropColumn('tma');
            }
        });
    }
};


