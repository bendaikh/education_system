<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index()
    {
        // Mock data for now - replace with real database queries later
        $students = [
            [
                'id' => 1,
                'name' => 'Liam Smith',
                'student_id' => '#ST5A28',
                'grade' => 'Grade 5 - Section A',
                'contact_parent' => '+1234567890'
            ],
            [
                'id' => 2,
                'name' => 'Olivia Johnson',
                'student_id' => '#STP101',
                'grade' => 'Physics 101',
                'contact_parent' => '+1987654321'
            ],
            [
                'id' => 3,
                'name' => 'Noah Williams',
                'student_id' => '#STH832',
                'grade' => 'History - Grade 8',
                'contact_parent' => '+1122334455'
            ],
            [
                'id' => 4,
                'name' => 'Emma Brown',
                'student_id' => '#STAC25',
                'grade' => 'Art & Craft',
                'contact_parent' => '+1554433221'
            ]
        ];

        return Inertia::render('Admin/Students', [
            'students' => $students,
            'total' => 1200
        ]);
    }
}
