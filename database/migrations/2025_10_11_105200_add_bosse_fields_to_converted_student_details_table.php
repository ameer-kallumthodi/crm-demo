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
            $table->string('application_number')->nullable()->after('tma');
            $table->string('board_registration_number')->nullable()->after('application_number');
            $table->integer('st')->nullable()->after('board_registration_number');
            $table->integer('phy')->nullable()->after('st');
            $table->integer('che')->nullable()->after('phy');
            $table->integer('bio')->nullable()->after('che');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_details', function (Blueprint $table) {
            $table->dropColumn([
                'application_number',
                'board_registration_number',
                'st',
                'phy',
                'che',
                'bio'
            ]);
        });
    }
};
