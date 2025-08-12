<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index()
    {
        // Mock data for teachers - replace with real database queries later
        $teachers = [
            [
                'id' => 1,
                'name' => 'Mr. Owen Carter',
                'department' => 'Science',
                'classes_assigned' => 3,
                'contact' => 'owen.carter@example.com'
            ],
            [
                'id' => 2,
                'name' => 'Ms. Clara Bennett',
                'department' => 'Physics',
                'classes_assigned' => 2,
                'contact' => 'clara.bennett@example.com'
            ],
            [
                'id' => 3,
                'name' => 'Dr. Emma Foster',
                'department' => 'History',
                'classes_assigned' => 4,
                'contact' => 'emma.foster@example.com'
            ],
            [
                'id' => 4,
                'name' => 'Ms. Sophia Hayes',
                'department' => 'Arts',
                'classes_assigned' => 1,
                'contact' => 'sophia.hayes@example.com'
            ]
        ];

        return Inertia::render('Admin/Teachers', [
            'teachers' => $teachers,
            'total' => 20
        ]);
    }
}
