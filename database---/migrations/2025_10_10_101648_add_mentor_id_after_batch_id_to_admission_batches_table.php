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
        Schema::table('admission_batches', function (Blueprint $table) {
            $table->foreignId('mentor_id')->nullable()->after('batch_id')->constrained('users')->onDelete('set null');
            $table->index('mentor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_batches', function (Blueprint $table) {
            $table->dropForeign(['mentor_id']);
            $table->dropIndex(['mentor_id']);
            $table->dropColumn('mentor_id');
        });
    }
};
