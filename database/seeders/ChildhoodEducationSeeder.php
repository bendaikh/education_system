<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChildhoodSubject;
use App\Models\Teacher;

class ChildhoodEducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some teachers to assign to subjects
        $teachers = Teacher::take(3)->get();
        
        if ($teachers->isEmpty()) {
            $this->command->info('No teachers found. Please run TeacherSeeder first.');
            return;
        }

        // Create sample childhood subjects
        $subjects = [
            [
                'name' => 'Early Reading & Writing',
                'description' => 'Introduction to basic reading and writing skills for young children',
                'duration' => '6 months',
                'price' => 299.99,
                'status' => 'Active'
            ],
            [
                'name' => 'Basic Mathematics',
                'description' => 'Fun introduction to numbers, counting, and basic math concepts',
                'duration' => '6 months',
                'price' => 299.99,
                'status' => 'Active'
            ],
            [
                'name' => 'Creative Arts & Crafts',
                'description' => 'Exploring creativity through drawing, painting, and crafts',
                'duration' => '3 months',
                'price' => 199.99,
                'status' => 'Active'
            ],
            [
                'name' => 'Music & Movement',
                'description' => 'Introduction to music, rhythm, and physical movement',
                'duration' => '3 months',
                'price' => 179.99,
                'status' => 'Active'
            ],
            [
                'name' => 'Science Discovery',
                'description' => 'Simple science experiments and nature exploration',
                'duration' => '4 months',
                'price' => 249.99,
                'status' => 'Active'
            ]
        ];

        foreach ($subjects as $subjectData) {
            $subject = ChildhoodSubject::create($subjectData);
            
            // Assign 1-2 teachers to each subject
            $randomTeachers = $teachers->random(rand(1, 2));
            $subject->teachers()->attach($randomTeachers->pluck('id')->toArray());
        }

        $this->command->info('Childhood education subjects seeded successfully!');
    }
}
