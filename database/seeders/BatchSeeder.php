<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Batch;

class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active courses
        $courses = \App\Models\Course::where('is_active', true)->get();
        
        if ($courses->isEmpty()) {
            // If no courses exist, create some sample batches without course_id
            $batches = [
                [
                    'title' => 'General Batch 2024-25 A',
                    'description' => 'First batch for academic year 2024-25',
                    'is_active' => true,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'title' => 'General Batch 2024-25 B',
                    'description' => 'Second batch for academic year 2024-25',
                    'is_active' => true,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
            ];
        } else {
            // Create batches for each course
            $batches = [];
            foreach ($courses as $course) {
                $batches[] = [
                    'title' => $course->title . ' - Batch 2024-25 A',
                    'course_id' => $course->id,
                    'description' => 'First batch for ' . $course->title . ' - 2024-25',
                    'is_active' => true,
                    'created_by' => 1,
                    'updated_by' => 1,
                ];
                $batches[] = [
                    'title' => $course->title . ' - Batch 2024-25 B',
                    'course_id' => $course->id,
                    'description' => 'Second batch for ' . $course->title . ' - 2024-25',
                    'is_active' => true,
                    'created_by' => 1,
                    'updated_by' => 1,
                ];
                $batches[] = [
                    'title' => $course->title . ' - Weekend Batch',
                    'course_id' => $course->id,
                    'description' => 'Weekend classes for ' . $course->title,
                    'is_active' => true,
                    'created_by' => 1,
                    'updated_by' => 1,
                ];
            }
        }

        foreach ($batches as $batch) {
            Batch::create($batch);
        }
    }
}