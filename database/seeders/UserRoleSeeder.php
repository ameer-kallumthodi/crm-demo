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
        $roles = [
            [
                'id' => 1,
                'title' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'title' => 'Admin',
                'description' => 'Administrative access with user management',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'title' => 'Telecaller',
                'description' => 'Telecaller access for lead management',
                'is_active' => true,
            ],
            [
                'id' => 4,
                'title' => 'Admission Counsellor',
                'description' => 'Admission Counsellor access for Converted lead management',
                'is_active' => true,
            ],
            [
                'id' => 5,
                'title' => 'Academic Assistant',
                'description' => 'Academic Assistant access for Converted lead management',
                'is_active' => true,
            ],
            [
                'id' => 6,
                'title' => 'Finance',
                'description' => 'Finance department access for financial management',
                'is_active' => true,
            ],
            [
                'id' => 7,
                'title' => 'Post-sales',
                'description' => 'Post-sales department access for customer support',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            UserRole::updateOrCreate(
                ['id' => $role['id']],
                $role
            );
        }
    }
}
