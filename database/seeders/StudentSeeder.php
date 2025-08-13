<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'name' => 'Ahmed Benali',
                'email' => 'ahmed.benali@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Fatima Zahra',
                'email' => 'fatima.zahra@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Youssef Alami',
                'email' => 'youssef.alami@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Khadija Mouhsine',
                'email' => 'khadija.mouhsine@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Omar Benjelloun',
                'email' => 'omar.benjelloun@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Aicha Berrada',
                'email' => 'aicha.berrada@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        ];

        foreach ($students as $student) {
            User::updateOrCreate(
                ['email' => $student['email']],
                $student
            );
        }
    }
}
