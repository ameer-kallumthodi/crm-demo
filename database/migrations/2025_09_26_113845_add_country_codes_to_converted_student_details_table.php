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
            $table->string('personal_code')->nullable()->after('personal_number');
            $table->string('parents_code')->nullable()->after('parents_number');
            $table->string('whatsapp_code')->nullable()->after('whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_details', function (Blueprint $table) {
            $table->dropColumn(['personal_code', 'parents_code', 'whatsapp_code']);
        });
    }
};