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
        // Modify the gender enum to remove 'other'
        \DB::statement("ALTER TABLE leads_details MODIFY COLUMN gender ENUM('male', 'female') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the gender enum with 'other'
        \DB::statement("ALTER TABLE leads_details MODIFY COLUMN gender ENUM('male', 'female', 'other') NULL");
    }
};
