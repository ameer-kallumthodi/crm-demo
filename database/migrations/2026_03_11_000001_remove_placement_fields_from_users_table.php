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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_placement_passed')) {
                $table->dropColumn([
                    'is_placement_passed',
                    'is_placement_passed_by',
                    'is_placement_passed_at',
                    'placement_resume',
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_placement_passed')->default(0)->after('department_id');
            $table->unsignedBigInteger('is_placement_passed_by')->nullable()->after('is_placement_passed');
            $table->timestamp('is_placement_passed_at')->nullable()->after('is_placement_passed_by');
            $table->string('placement_resume')->nullable()->after('is_placement_passed_at');
        });
    }
};
