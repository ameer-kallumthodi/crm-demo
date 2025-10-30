<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->boolean('is_academic_verified')->default(false);
            $table->unsignedBigInteger('academic_verified_by')->nullable();
            $table->timestamp('academic_verified_at')->nullable();

            $table->foreign('academic_verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropForeign(['academic_verified_by']);
            $table->dropColumn(['is_academic_verified', 'academic_verified_by', 'academic_verified_at']);
        });
    }
};


