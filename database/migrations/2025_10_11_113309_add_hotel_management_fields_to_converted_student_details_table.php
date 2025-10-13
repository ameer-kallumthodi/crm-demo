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
            $table->string('app')->nullable()->after('remarks');
            $table->string('group')->nullable()->after('app');
            $table->string('interview')->nullable()->after('group');
            $table->integer('howmany_interview')->nullable()->after('interview');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_details', function (Blueprint $table) {
            $table->dropColumn([
                'app',
                'group',
                'interview',
                'howmany_interview',
            ]);
        });
    }
};
