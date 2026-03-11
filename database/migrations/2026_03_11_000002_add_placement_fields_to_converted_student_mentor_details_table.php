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
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->boolean('is_placement_passed')->default(0);
            $table->unsignedBigInteger('is_placement_passed_by')->nullable();
            $table->timestamp('is_placement_passed_at')->nullable();
            $table->string('placement_resume')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropColumn([
                'is_placement_passed',
                'is_placement_passed_by',
                'is_placement_passed_at',
                'placement_resume',
            ]);
        });
    }
};
