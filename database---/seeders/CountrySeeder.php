<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'title' => 'India',
                'code' => 'IN',
                'phone_code' => '91',
                'is_active' => 1,
            ],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
