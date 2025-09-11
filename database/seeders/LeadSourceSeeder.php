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
            ['title' => 'Website', 'description' => 'Leads from website'],
            ['title' => 'Social Media', 'description' => 'Leads from social media'],
            ['title' => 'Referral', 'description' => 'Leads from referrals'],
            ['title' => 'Cold Call', 'description' => 'Leads from cold calling'],
            ['title' => 'Email Campaign', 'description' => 'Leads from email campaigns'],
            ['title' => 'Advertisement', 'description' => 'Leads from advertisements'],
            ['title' => 'Walk-in', 'description' => 'Walk-in leads'],
            ['title' => 'Other', 'description' => 'Other sources'],
        ];

        foreach ($sources as $source) {
            LeadSource::create($source);
        }
    }
}
