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
            ['title' => 'Un Touched Leads', 'description' => 'Un Touched Leads', 'color' => '#28a745', 'interest_status' => 3, 'is_active' => 1],
            ['title' => 'Follow-up', 'description' => 'Lead Follow-up', 'color' => '#17a2b8', 'interest_status' => 1, 'is_active' => 1],
            ['title' => 'Not-interested IN FULL COURSE', 'description' => 'Not-interested IN FULL COURSE', 'color' => '#ffc107', 'interest_status' => 3, 'is_active' => 1],
            ['title' => 'Disqualified', 'description' => 'Lead is Disqualified', 'color' => '#dc3545', 'interest_status' => 3, 'is_active' => 1],
            ['title' => 'DNP', 'description' => 'DNP', 'color' => '#6f42c1', 'interest_status' => 3, 'is_active' => 1],
            ['title' => 'Demo', 'description' => 'Demo', 'color' => '#20c997', 'interest_status' => 2, 'is_active' => 1],
            ['title' => 'Interested to Buy', 'description' => 'Interested to Buy', 'color' => '#6c757d', 'interest_status' => 1, 'is_active' => 1],
            ['title' => 'Positive', 'description' => 'Positive', 'color' => null, 'interest_status' => null, 'is_active' => 1],
            ['title' => 'May Buy Later', 'description' => 'May Buy Later', 'color' => null, 'interest_status' => null, 'is_active' => 1],
        ];

        foreach ($statuses as $status) {
            LeadStatus::create($status);
        }
    }
}
