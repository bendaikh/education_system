<?php

namespace App\Http\Controllers;

use App\Models\ChildhoodSubject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChildhoodSubjectController extends Controller
{
    public function index()
    {
        $subjects = ChildhoodSubject::with('teachers')->get();
        $teachers = Teacher::all();

        return Inertia::render('Admin/ChildhoodSubjects', [
            'subjects' => $subjects,
            'teachers' => $teachers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Inactive',
            'teachers' => 'nullable|array'
        ]);

        $subject = ChildhoodSubject::create($request->except('teachers'));

        if ($request->has('teachers')) {
            $subject->teachers()->attach($request->teachers);
        }

        return redirect()->back()->with('success', 'Childhood subject created successfully.');
    }

    public function update(Request $request, ChildhoodSubject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Inactive',
            'teachers' => 'nullable|array'
        ]);

        $subject->update($request->except('teachers'));

        if ($request->has('teachers')) {
            $subject->teachers()->sync($request->teachers);
        }

        return redirect()->back()->with('success', 'Childhood subject updated successfully.');
    }

    public function destroy(ChildhoodSubject $subject)
    {
        $subject->delete();
        return redirect()->back()->with('success', 'Childhood subject deleted successfully.');
    }
}
