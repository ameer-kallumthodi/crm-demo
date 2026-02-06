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
            if (!Schema::hasColumn('converted_leads', 'is_postpond_batch')) {
                $table->boolean('is_postpond_batch')->default(0)->after('admission_batch_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            if (Schema::hasColumn('converted_leads', 'is_postpond_batch')) {
                $table->dropColumn('is_postpond_batch');
            }
        });
    }
};
