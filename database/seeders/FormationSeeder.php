<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Formation;

class FormationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formations = [
            [
                'title' => 'Web Development Fundamentals',
                'description' => 'Learn HTML, CSS, and JavaScript basics',
                'teachers' => ['John Smith', 'David Wilson'],
                'duration' => '12 weeks',
                'level' => 'Beginner',
                'price' => '$299',
                'status' => 'Active',
                'enrolled_students' => 25
            ],
            [
                'title' => 'Advanced React Development',
                'description' => 'Master React hooks, context, and advanced patterns',
                'teachers' => ['Sarah Johnson'],
                'duration' => '8 weeks',
                'level' => 'Advanced',
                'price' => '$399',
                'status' => 'Active',
                'enrolled_students' => 18
            ],
            [
                'title' => 'Database Design & SQL',
                'description' => 'Learn database design principles and SQL queries',
                'teachers' => ['Michael Brown', 'Lisa Anderson'],
                'duration' => '6 weeks',
                'level' => 'Intermediate',
                'price' => '$249',
                'status' => 'Coming Soon',
                'enrolled_students' => 0
            ],
            [
                'title' => 'Mobile App Development',
                'description' => 'Build native mobile apps with React Native',
                'teachers' => ['Emily Davis', 'James Taylor'],
                'duration' => '10 weeks',
                'level' => 'Intermediate',
                'price' => '$349',
                'status' => 'Active',
                'enrolled_students' => 22
            ]
        ];

        foreach ($formations as $formation) {
            Formation::create($formation);
        }
    }
}