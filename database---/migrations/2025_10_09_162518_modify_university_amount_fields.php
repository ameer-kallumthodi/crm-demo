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
        Schema::table('universities', function (Blueprint $table) {
            // Rename amount column to ug_amount
            $table->renameColumn('amount', 'ug_amount');
            // Add new pg_amount column
            $table->decimal('pg_amount', 10, 2)->nullable()->after('ug_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            // Drop pg_amount column
            $table->dropColumn('pg_amount');
            // Rename ug_amount back to amount
            $table->renameColumn('ug_amount', 'amount');
        });
    }
};
