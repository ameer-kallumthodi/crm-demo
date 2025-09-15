<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeadStatus;

class LeadStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['title' => 'Un Touched Leads', 'description' => 'Un Touched Leads', 'color' => ''],
            ['title' => 'Follow-up', 'description' => 'Lead Follow-up', 'color' => ''],
            ['title' => 'Not-interested IN FULL COURSE', 'description' => 'Not-interested IN FULL COURSE', 'color' => ''],
            ['title' => 'Disqualified', 'description' => 'Lead is Disqualified', 'color' => ''],
            ['title' => 'DNP', 'description' => 'DNP', 'color' => ''],
            ['title' => 'Demo', 'description' => 'Demo', 'color' => ''],
            ['title' => 'Interested to Buy', 'description' => 'Interested to Buy', 'color' => ''],
            ['title' => 'Positive', 'description' => 'Positive', 'color' => ''],
            ['title' => 'May Buy Later', 'description' => 'May Buy Later', 'color' => ''],
        ];

        foreach ($statuses as $status) {
            LeadStatus::create($status);
        }
    }
}
