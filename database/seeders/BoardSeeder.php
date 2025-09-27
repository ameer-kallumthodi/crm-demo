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
        $boards = [
            [
                'title' => 'Central Board of Secondary Education',
                'code' => 'CBSE',
                'description' => 'Central Board of Secondary Education - National level board',
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'title' => 'Indian Certificate of Secondary Education',
                'code' => 'ICSE',
                'description' => 'Indian Certificate of Secondary Education - Private board',
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'title' => 'State Board of Education',
                'code' => 'STATE',
                'description' => 'State Board of Education - State level board',
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'title' => 'International Baccalaureate',
                'code' => 'IB',
                'description' => 'International Baccalaureate - International board',
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'title' => 'Cambridge International',
                'code' => 'CIE',
                'description' => 'Cambridge International Examinations',
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($boards as $board) {
            Board::create($board);
        }
    }
}