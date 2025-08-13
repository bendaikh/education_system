<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Formation;

class FormationController extends Controller
{
    /**
     * Display the formations page.
     */
    public function index()
    {
        // Available teachers for selection
        $availableTeachers = [
            ['id' => 1, 'name' => 'John Smith', 'department' => 'Computer Science'],
            ['id' => 2, 'name' => 'Sarah Johnson', 'department' => 'Web Development'],
            ['id' => 3, 'name' => 'Michael Brown', 'department' => 'Database Management'],
            ['id' => 4, 'name' => 'Emily Davis', 'department' => 'Mobile Development'],
            ['id' => 5, 'name' => 'David Wilson', 'department' => 'Frontend Development'],
            ['id' => 6, 'name' => 'Lisa Anderson', 'department' => 'Backend Development'],
            ['id' => 7, 'name' => 'James Taylor', 'department' => 'DevOps'],
            ['id' => 8, 'name' => 'Maria Garcia', 'department' => 'UI/UX Design']
        ];

        // Mock data for formations - replace with real data from database later
        $defaultFormations = [
            [
                'id' => 1,
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
                'id' => 2,
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
                'id' => 3,
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
                'id' => 4,
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

        // Get formations from database, or seed with default data if table is empty
        $formations = Formation::all();
        
        // If no formations exist, seed with default data
        if ($formations->isEmpty()) {
            foreach ($defaultFormations as $formationData) {
                Formation::create($formationData);
            }
            $formations = Formation::all();
        }

        return Inertia::render('Admin/Formations', [
            'formations' => $formations->toArray(),
            'total' => $formations->count(),
            'availableTeachers' => $availableTeachers
        ]);
    }

    /**
     * Store a new formation.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'teachers' => 'required|array|min:1',
            'teachers.*.id' => 'required|integer',
            'teachers.*.name' => 'required|string',
            'duration' => 'nullable|string',
            'level' => 'nullable|string',
            'price' => 'required|string',
            'status' => 'required|string|in:Active,Coming Soon,Completed'
        ]);

        // Create new formation in database
        $formation = Formation::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'teachers' => array_column($validated['teachers'], 'name'),
            'duration' => $validated['duration'],
            'level' => $validated['level'],
            'price' => $validated['price'],
            'status' => $validated['status'],
            'enrolled_students' => 0
        ]);

        return redirect()->route('admin.formations.index')->with('success', 'Formation created successfully!');
    }
}