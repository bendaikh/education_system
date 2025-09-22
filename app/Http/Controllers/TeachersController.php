<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeachersController extends Controller
{
    /**
     * Display a listing of teachers.
     */
    public function index()
    {
        $teachers = Teacher::with(['educationalSubjects', 'childhoodSubjects'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Teachers/Index', [
            'teachers' => $teachers,
        ]);
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        return Inertia::render('Admin/Teachers/Create');
    }

    /**
     * Store a newly created teacher.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'image' => 'nullable|string|max:255',
        ]);

        Teacher::create($request->all());

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher created successfully!');
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher)
    {
        $teacher->load(['educationalSubjects', 'childhoodSubjects']);

        return Inertia::render('Admin/Teachers/Show', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher)
    {
        return Inertia::render('Admin/Teachers/Edit', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Update the specified teacher.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'image' => 'nullable|string|max:255',
        ]);

        $teacher->update($request->all());

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully!');
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher deleted successfully!');
    }
}