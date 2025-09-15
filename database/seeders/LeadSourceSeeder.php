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
            ['title' => 'Google Ad', 'description' => 'Leads from Google Ad'],
            ['title' => 'Facebook Instagram Ad', 'description' => 'Leads from Meta Ad'],
            ['title' => 'Seminar', 'description' => 'Leads from Seminar'],
            ['title' => 'Reference', 'description' => 'Leads from Reference'],
            ['title' => 'Others', 'description' => 'Others'],
        ];

        foreach ($sources as $source) {
            LeadSource::create($source);
        }
    }
}
