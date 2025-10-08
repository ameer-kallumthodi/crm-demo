<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeadSource;

class LeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            ['title' => 'Google Ad', 'description' => 'Leads from Google Ad', 'is_active' => 1],
            ['title' => 'Facebook Instagram Ad', 'description' => 'Leads from Meta Ad', 'is_active' => 1],
            ['title' => 'Seminar', 'description' => 'Leads from Seminar', 'is_active' => 1],
            ['title' => 'Reference', 'description' => 'Leads from Reference', 'is_active' => 1],
            ['title' => 'Others', 'description' => 'Other Sources', 'is_active' => 1],
        ];

        foreach ($sources as $source) {
            LeadSource::create($source);
        }
    }
}
