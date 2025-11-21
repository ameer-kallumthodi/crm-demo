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
            if (!Schema::hasColumn('batches', 'postpone_batch_id')) {
                $table->unsignedBigInteger('postpone_batch_id')->nullable()->after('amount');
                $table->foreign('postpone_batch_id')
                    ->references('id')
                    ->on('batches')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('batches', 'postpone_start_date')) {
                $table->date('postpone_start_date')->nullable()->after('postpone_batch_id');
            }

            if (!Schema::hasColumn('batches', 'postpone_end_date')) {
                $table->date('postpone_end_date')->nullable()->after('postpone_start_date');
            }

            if (!Schema::hasColumn('batches', 'batch_postpone_amount')) {
                $table->decimal('batch_postpone_amount', 10, 2)->nullable()->after('postpone_end_date');
            }

            if (!Schema::hasColumn('batches', 'is_postpone_active')) {
                $table->boolean('is_postpone_active')->default(0)->after('batch_postpone_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            if (Schema::hasColumn('batches', 'is_postpone_active')) {
                $table->dropColumn('is_postpone_active');
            }

            if (Schema::hasColumn('batches', 'batch_postpone_amount')) {
                $table->dropColumn('batch_postpone_amount');
            }

            if (Schema::hasColumn('batches', 'postpone_end_date')) {
                $table->dropColumn('postpone_end_date');
            }

            if (Schema::hasColumn('batches', 'postpone_start_date')) {
                $table->dropColumn('postpone_start_date');
            }

            if (Schema::hasColumn('batches', 'postpone_batch_id')) {
                $table->dropForeign(['postpone_batch_id']);
                $table->dropColumn('postpone_batch_id');
            }
        });
    }
};

