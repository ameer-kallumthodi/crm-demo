<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserRole;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data and insert only the three required roles
        UserRole::truncate();
        
        $roles = [
            [
                'title' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'is_active' => true,
            ],
            [
                'title' => 'Admin',
                'description' => 'Administrative access with user management',
                'is_active' => true,
            ],
            [
                'title' => 'Telecaller',
                'description' => 'Telecaller access for lead management',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            UserRole::create($role);
        }
    }
}
