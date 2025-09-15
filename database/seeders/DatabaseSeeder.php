<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserRoleSeeder::class,
            LeadStatusSeeder::class,
            LeadSourceSeeder::class,
        ]);

        // Create a default admin user
        User::create(
            [
                'name' => 'Super Admin User',
                'email' => 'superadmin@crm.com',
                'password' => bcrypt('password'),
                'role_id' => 1, // Admin role
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@crm.com',
                'password' => bcrypt('password'),
                'role_id' => 1, // Admin role
            ],
        );
    }
}