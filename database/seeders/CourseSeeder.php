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
                'title' => 'NIOS Seconday',
                'code' => null,
                'is_active' => 1,
                'amount' => 10000,
                'hod_number' => '+91 8943553164',
            ],
            [
                'title' => 'BOSSE Seconday',
                'code' => null,
                'is_active' => 1,
                'amount' => 20000,
                'hod_number' => '+91 89435 53164',
            ],
            [
                'title' => 'Medical Coding',
                'code' => null,
                'is_active' => 1,
                'amount' => 15000,
                'hod_number' => null,
            ],
            [
                'title' => 'Hospital Administration',
                'code' => null,
                'is_active' => 1,
                'amount' => 20000,
                'hod_number' => null,
            ],
            [
                'title' => 'E-School',
                'code' => null,
                'is_active' => 1,
                'amount' => 15000,
                'hod_number' => null,
            ],
            [
                'title' => 'Eduthanzeel',
                'code' => null,
                'is_active' => 1,
                'amount' => 15000,
                'hod_number' => null,
            ],
            [
                'title' => 'TTC',
                'code' => null,
                'is_active' => 1,
                'amount' => 22000,
                'hod_number' => null,
            ],
            [
                'title' => 'Hotel Management',
                'code' => null,
                'is_active' => 1,
                'amount' => 25000,
                'hod_number' => null,
            ],
            [
                'title' => 'UG/PG',
                'code' => null,
                'is_active' => 1,
                'amount' => 20000,
                'hod_number' => null,
            ],
            [
                'title' => 'Python',
                'code' => null,
                'is_active' => 1,
                'amount' => 30000,
                'hod_number' => null,
            ],
            [
                'title' => 'Digital Marketing',
                'code' => null,
                'is_active' => 1,
                'amount' => 25000,
                'hod_number' => null,
            ],
            [
                'title' => 'AI Automation',
                'code' => null,
                'is_active' => 1,
                'amount' => 35000,
                'hod_number' => null,
            ],
            [
                'title' => 'Web Development& Designing',
                'code' => null,
                'is_active' => 1,
                'amount' => 30000,
                'hod_number' => null,
            ],
            [
                'title' => 'Vibe Coding',
                'code' => null,
                'is_active' => 1,
                'amount' => 25000,
                'hod_number' => null,
            ],
            [
                'title' => 'Graphic Designing',
                'code' => null,
                'is_active' => 1,
                'amount' => 25000,
                'hod_number' => null,
            ],
            [
                'title' => 'GMVSS Seconday',
                'code' => null,
                'is_active' => 1,
                'amount' => 25000,
                'hod_number' => null,
            ],
            [
                'title' => 'NIOS Higher Seconday',
                'code' => null,
                'is_active' => 1,
                'amount' => 10000,
                'hod_number' => null,
            ],
            [
                'title' => 'BOSSE Higher Seconday',
                'code' => null,
                'is_active' => 1,
                'amount' => 25000,
                'hod_number' => '+91 89435 53164',
            ],
            [
                'title' => 'GMVSS Higher Seconday',
                'code' => null,
                'is_active' => 1,
                'amount' => 15000,
                'hod_number' => null,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
