<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'id' => 1,
                'title' => 'NIOS',
                'code' => '9992',
                'amount' => 10000,
                'hod_number' => '+91 8943553164',
                'is_active' => 1,
            ],
            [
                'id' => 2,
                'title' => 'BOSSE',
                'code' => '9992',
                'amount' => 20000,
                'hod_number' => '+91 89435 53164',
                'is_active' => 1,
            ],
            [
                'id' => 3,
                'title' => 'Medical Coding',
                'code' => '9992',
                'amount' => 15000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 4,
                'title' => 'Hospital Administration',
                'code' => '9992',
                'amount' => 20000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 5,
                'title' => 'E-School',
                'code' => '9992',
                'amount' => 15000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 6,
                'title' => 'Eduthanzeel',
                'code' => '9992',
                'amount' => 15000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 7,
                'title' => 'TTC',
                'code' => '9992',
                'amount' => 22000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 8,
                'title' => 'Hotel Management',
                'code' => '9992',
                'amount' => 25000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 9,
                'title' => 'UG/PG',
                'code' => '9992',
                'amount' => 20000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 10,
                'title' => 'Python',
                'code' => '9992',
                'amount' => 30000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 11,
                'title' => 'Digital Marketing',
                'code' => '9992',
                'amount' => 25000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 12,
                'title' => 'AI Automation',
                'code' => '9992',
                'amount' => 35000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 13,
                'title' => 'Web Development& Designing',
                'code' => '9992',
                'amount' => 30000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 14,
                'title' => 'Vibe Coding',
                'code' => '9992',
                'amount' => 25000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 15,
                'title' => 'Graphic Designing',
                'code' => '9992',
                'amount' => 25000,
                'hod_number' => null,
                'is_active' => 1,
            ],
            [
                'id' => 16,
                'title' => 'GMVSS',
                'code' => '9992',
                'amount' => 15000,
                'hod_number' => null,
                'is_active' => 1,
            ],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['id' => $course['id']],
                $course
            );
        }
    }
}
