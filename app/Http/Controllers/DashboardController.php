<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // For now using mock data, but you can replace with real database queries
        $stats = [
            'totalStudents' => 1250,
            'totalTeachers' => 75,
            'totalClasses' => 50,
            'revenue' => '$1.2M'
        ];

        $recentActivity = [
            [
                'id' => 1,
                'user' => 'Clara Bennett',
                'activity' => 'Enrolled in Grade 5',
                'date' => '2023-10-27',
                'status' => 'Completed'
            ],
            [
                'id' => 2,
                'user' => 'Owen Carter',
                'activity' => 'Added a new assignment',
                'date' => '2023-10-26',
                'status' => 'Completed'
            ],
            [
                'id' => 3,
                'user' => 'Emma Foster',
                'activity' => 'Updated school event calendar',
                'date' => '2023-10-25',
                'status' => 'Completed'
            ],
            [
                'id' => 4,
                'user' => 'Lucas Hayes',
                'activity' => 'Fee payment pending',
                'date' => '2023-10-24',
                'status' => 'Pending'
            ]
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity
        ]);
    }
}
