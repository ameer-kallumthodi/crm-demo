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
            $table->boolean('is_resume_verified')->default(0);
            $table->timestamp('resume_verified_at')->nullable();
            $table->unsignedBigInteger('resume_verified_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropColumn([
                'is_resume_verified',
                'resume_verified_at',
                'resume_verified_by',
            ]);
        });
    }
};
