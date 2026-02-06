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
            UserSeeder::class,
            CountrySeeder::class,
            BoardSeeder::class,
            CourseSeeder::class,
            LeadSourceSeeder::class,
            LeadStatusSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}