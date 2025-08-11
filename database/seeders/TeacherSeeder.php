<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        Teacher::truncate();

        $teachers = [
            [
                'name' => 'Ms. Harper',
                'role' => 'Mathematics Head',
                'image' => 'teachers/harper.jpg',
            ],
            [
                'name' => 'Dr. Bennett',
                'role' => 'Science Faculty Lead',
                'image' => 'teachers/bennett.jpg',
            ],
            [
                'name' => 'Mr. Carter',
                'role' => 'English Literature Specialist',
                'image' => 'teachers/carter.jpg',
            ],
        ];

        foreach ($teachers as $t) {
            Teacher::create($t);
        }
    }
}
