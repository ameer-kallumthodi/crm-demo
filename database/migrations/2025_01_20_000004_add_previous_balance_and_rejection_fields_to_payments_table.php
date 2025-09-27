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
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('previous_balance', 10, 2)->default(0)->after('amount_paid');
            $table->timestamp('rejected_date')->nullable()->after('approved_date');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null')->after('rejected_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropColumn(['previous_balance', 'rejected_date', 'rejected_by']);
        });
    }
};
