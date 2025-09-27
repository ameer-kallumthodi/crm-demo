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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null')->after('student_id');
            $table->foreignId('board_id')->nullable()->constrained('boards')->onDelete('set null')->after('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropForeign(['board_id']);
            $table->dropColumn(['batch_id', 'board_id']);
        });
    }
};
