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
        Schema::table('registration_links', function (Blueprint $table) {
            $table->string('color_code', 7)->default('#0d6efd')->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_links', function (Blueprint $table) {
            $table->dropColumn('color_code');
        });
    }
};

