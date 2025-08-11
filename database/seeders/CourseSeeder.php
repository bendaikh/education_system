<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::truncate();

        $courses = [
            [
                'title' => 'Advanced Mathematics',
                'stream' => 'Science',
                'description' => 'Explore complex mathematical concepts and problem-solving techniques.',
                'image' => 'courses/math.jpg',
            ],
            [
                'title' => 'Experimental Science',
                'stream' => 'Science',
                'description' => 'Engage in hands-on experiments and scientific inquiry.',
                'image' => 'courses/science.jpg',
            ],
            [
                'title' => 'Literature and Composition',
                'stream' => 'Arts',
                'description' => 'Analyze classic and contemporary literature, enhancing writing skills.',
                'image' => 'courses/lit.jpg',
            ],
        ];

        foreach ($courses as $c) {
            Course::create($c);
        }
    }
}
