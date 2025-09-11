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
        // Clear existing data and insert only the three required roles
        DB::table('user_roles')->truncate();
        
        DB::table('user_roles')->insert([
            [
                'title' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Admin',
                'description' => 'Administrative access with user management',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Telecaller',
                'description' => 'Telecaller access for lead management',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration doesn't need to be reversed as it's just data seeding
    }
};