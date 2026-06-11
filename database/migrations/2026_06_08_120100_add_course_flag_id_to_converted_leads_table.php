<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->foreignId('course_flag_id')
                ->nullable()
                ->after('support_flag_id')
                ->constrained('course_flags')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropForeign(['course_flag_id']);
            $table->dropColumn('course_flag_id');
        });
    }
};
