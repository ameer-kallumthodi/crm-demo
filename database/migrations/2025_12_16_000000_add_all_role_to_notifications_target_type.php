<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'all_role'
        // For MySQL, we need to use raw SQL to alter the enum
        DB::statement("ALTER TABLE notifications MODIFY COLUMN target_type ENUM('all', 'all_role', 'role', 'user') DEFAULT 'all'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE notifications MODIFY COLUMN target_type ENUM('all', 'role', 'user') DEFAULT 'all'");
    }
};

