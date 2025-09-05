<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Formation;
use App\Models\Teacher;

class FormationController extends Controller
{
    /**
     * Display the formations page.
     */
    public function index()
    {
        // Get available teachers from database
        $availableTeachers = Teacher::select('id', 'name', 'role as department')->get()->toArray();

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

    /**
     * Update an existing formation.
     */
    public function update(Request $request, Formation $formation)
    {
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

        $formation->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'teachers' => array_column($validated['teachers'], 'name'),
            'duration' => $validated['duration'],
            'level' => $validated['level'],
            'price' => $validated['price'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.formations.index')->with('success', 'Formation updated successfully!');
    }

    /**
     * Remove the specified formation.
     */
    public function destroy(Formation $formation)
    {
        $formation->delete();
        return redirect()->route('admin.formations.index')->with('success', 'Formation deleted successfully!');
    }
}