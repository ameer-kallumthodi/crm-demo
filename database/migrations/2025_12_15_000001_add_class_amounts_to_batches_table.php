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
        Schema::table('batches', function (Blueprint $table) {
            if (!Schema::hasColumn('batches', 'sslc_amount')) {
                $table->decimal('sslc_amount', 10, 2)->nullable()->after('amount');
            }

            if (!Schema::hasColumn('batches', 'plustwo_amount')) {
                $table->decimal('plustwo_amount', 10, 2)->nullable()->after('sslc_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            if (Schema::hasColumn('batches', 'plustwo_amount')) {
                $table->dropColumn('plustwo_amount');
            }

            if (Schema::hasColumn('batches', 'sslc_amount')) {
                $table->dropColumn('sslc_amount');
            }
        });
    }
};
