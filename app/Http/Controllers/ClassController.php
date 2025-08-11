<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassController extends Controller
{
    public function index()
    {
        // Mock data for now - replace with real database queries later
        $classes = [
            [
                'id' => 1,
                'name' => 'Grade 5 - Section A',
                'teacher' => 'Mr. Owen Carter',
                'students' => 35,
                'schedule' => 'Mon, Wed, Fri (10:00 - 11:30)'
            ],
            [
                'id' => 2,
                'name' => 'Physics 101',
                'teacher' => 'Ms. Clara Bennett',
                'students' => 28,
                'schedule' => 'Tue, Thu (13:00 - 14:30)'
            ],
            [
                'id' => 3,
                'name' => 'History - Grade 8',
                'teacher' => 'Dr. Emma Foster',
                'students' => 32,
                'schedule' => 'Mon, Wed (08:30 - 10:00)'
            ],
            [
                'id' => 4,
                'name' => 'Art & Craft',
                'teacher' => 'Ms. Sophia Hayes',
                'students' => 25,
                'schedule' => 'Friday (14:00 - 16:00)'
            ]
        ];

        return Inertia::render('Admin/Classes', [
            'classes' => $classes,
            'total' => 50
        ]);
    }
}
