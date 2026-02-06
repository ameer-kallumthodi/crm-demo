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
            if (!Schema::hasColumn('converted_leads', 'name_updated_at')) {
                $table->timestamp('name_updated_at')->nullable()->after('reg_updated_by');
            }
            if (!Schema::hasColumn('converted_leads', 'name_updated_by')) {
                $table->unsignedBigInteger('name_updated_by')->nullable()->after('name_updated_at');
            }
        });

        Schema::table('converted_leads', function (Blueprint $table) {
            // Add foreign key only if column exists and FK not present
            if (Schema::hasColumn('converted_leads', 'name_updated_by')) {
                try {
                    $table->foreign('name_updated_by')->references('id')->on('users')->onDelete('set null');
                } catch (\Throwable $e) {
                    // Ignore if FK already exists (or DB driver doesn't support checking here)
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            // Drop FK first if it exists
            try {
                $table->dropForeign(['name_updated_by']);
            } catch (\Throwable $e) {
                // Ignore if not present
            }

            if (Schema::hasColumn('converted_leads', 'name_updated_by')) {
                $table->dropColumn('name_updated_by');
            }
            if (Schema::hasColumn('converted_leads', 'name_updated_at')) {
                $table->dropColumn('name_updated_at');
            }
        });
    }
};

