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
        Schema::table('converted_student_details', function (Blueprint $table) {
            $table->string('plustwo_certificate')->nullable()->after('signature');
            $table->string('sslc_certificate')->nullable()->after('plustwo_certificate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_details', function (Blueprint $table) {
            $table->dropColumn('plustwo_certificate');
            $table->dropColumn('sslc_certificate');
        });
    }
};