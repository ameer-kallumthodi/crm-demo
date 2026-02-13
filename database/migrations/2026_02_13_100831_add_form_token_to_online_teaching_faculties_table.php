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
        Schema::table('online_teaching_faculties', function (Blueprint $table) {
            $table->string('form_token', 64)->unique()->nullable()->after('id');
            $table->timestamp('form_filled_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_teaching_faculties', function (Blueprint $table) {
            $table->dropColumn(['form_token', 'form_filled_at']);
        });
    }
};
