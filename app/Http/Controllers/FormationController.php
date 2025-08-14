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



        // Get formations from database
        $formations = Formation::all();

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