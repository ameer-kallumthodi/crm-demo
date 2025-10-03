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
            $table->string('register_number', 50)->nullable()->after('email');
            $table->timestamp('reg_updated_at')->nullable()->after('register_number');
            $table->unsignedBigInteger('reg_updated_by')->nullable()->after('reg_updated_at');
            
            $table->foreign('reg_updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropForeign(['reg_updated_by']);
            $table->dropColumn(['register_number', 'reg_updated_at', 'reg_updated_by']);
        });
    }
};
