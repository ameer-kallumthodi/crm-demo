<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Fee breakdown (used for course_id = 23)
            $table->decimal('fee_pg_amount', 10, 2)->nullable()->after('total_amount');
            $table->decimal('fee_ug_amount', 10, 2)->nullable()->after('fee_pg_amount');
            $table->decimal('fee_plustwo_amount', 10, 2)->nullable()->after('fee_ug_amount');
            $table->decimal('fee_sslc_amount', 10, 2)->nullable()->after('fee_plustwo_amount');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'fee_pg_amount',
                'fee_ug_amount',
                'fee_plustwo_amount',
                'fee_sslc_amount',
            ]);
        });
    }
};

