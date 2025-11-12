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
            if (!Schema::hasColumn('converted_leads', 'is_course_changed')) {
                $table->boolean('is_course_changed')
                    ->default(false)
                    ->after('batch_id');
            }

            if (!Schema::hasColumn('converted_leads', 'course_changed_at')) {
                $table->timestamp('course_changed_at')
                    ->nullable()
                    ->after('is_course_changed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            if (Schema::hasColumn('converted_leads', 'course_changed_at')) {
                $table->dropColumn('course_changed_at');
            }

            if (Schema::hasColumn('converted_leads', 'is_course_changed')) {
                $table->dropColumn('is_course_changed');
            }
        });
    }
};

