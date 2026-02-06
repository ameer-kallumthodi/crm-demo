<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Board;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user ID or use 1 as fallback
        $userId = \App\Models\User::first()?->id ?? 1;
        
        $boards = [
            [
                'title' => 'Central Board of Secondary Education',
                'code' => 'CBSE',
                'description' => 'Central Board of Secondary Education - National level board',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'title' => 'Indian Certificate of Secondary Education',
                'code' => 'ICSE',
                'description' => 'Indian Certificate of Secondary Education - Private board',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'title' => 'State Board of Education',
                'code' => 'STATE',
                'description' => 'State Board of Education - State level board',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'title' => 'International Baccalaureate',
                'code' => 'IB',
                'description' => 'International Baccalaureate - International board',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'title' => 'Cambridge International',
                'code' => 'CIE',
                'description' => 'Cambridge International Examinations',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
        ];

        foreach ($boards as $board) {
            Board::updateOrCreate(
                ['code' => $board['code']],
                $board
            );
        }
    }
}