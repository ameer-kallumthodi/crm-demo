<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@crm.com',
                'password' => bcrypt('Superadmin@2025'),
                'role_id' => 1,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@crm.com',
                'password' => bcrypt('admin@skillpark'),
                'role_id' => 2,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
