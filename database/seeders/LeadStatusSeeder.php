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
            ['title' => 'New Lead', 'description' => 'Newly created lead', 'color' => '#28a745'],
            ['title' => 'Contacted', 'description' => 'Lead has been contacted', 'color' => '#17a2b8'],
            ['title' => 'Interested', 'description' => 'Lead is interested', 'color' => '#ffc107'],
            ['title' => 'Not Interested', 'description' => 'Lead is not interested', 'color' => '#dc3545'],
            ['title' => 'Follow Up', 'description' => 'Lead needs follow up', 'color' => '#6f42c1'],
            ['title' => 'Converted', 'description' => 'Lead has been converted', 'color' => '#20c997'],
            ['title' => 'Lost', 'description' => 'Lead is lost', 'color' => '#6c757d'],
        ];

        foreach ($statuses as $status) {
            LeadStatus::create($status);
        }
    }
}
