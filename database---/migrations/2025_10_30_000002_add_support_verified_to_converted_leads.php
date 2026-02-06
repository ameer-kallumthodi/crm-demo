<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->boolean('is_support_verified')->default(false);
            $table->unsignedBigInteger('support_verified_by')->nullable();
            $table->timestamp('support_verified_at')->nullable();

            $table->foreign('support_verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropForeign(['support_verified_by']);
            $table->dropColumn(['is_support_verified', 'support_verified_by', 'support_verified_at']);
        });
    }
};


