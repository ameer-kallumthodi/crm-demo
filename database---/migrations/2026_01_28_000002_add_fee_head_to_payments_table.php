<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // For course_id = 23 payments (PG/UG/PLUS_TWO/SSLC)
            $table->string('fee_head', 50)->nullable()->after('amount_paid');
            $table->index(['invoice_id', 'fee_head']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['invoice_id', 'fee_head']);
            $table->dropColumn('fee_head');
        });
    }
};

