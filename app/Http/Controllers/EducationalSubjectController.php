<?php

namespace App\Http\Controllers;

use App\Models\EducationalSubject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EducationalSubjectController extends Controller
{
    public function index()
    {
        $subjects = EducationalSubject::with('teachers')->orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();
        
        return Inertia::render('Admin/EducationalSubjects', [
            'subjects' => $subjects,
            'teachers' => $teachers,
            'language' => $this->getLanguageData()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|in:monthly,yearly',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:teachers,id'
        ]);

        $subject = EducationalSubject::create($request->except('teacher_ids'));
        
        if ($request->has('teacher_ids')) {
            $subject->teachers()->attach($request->teacher_ids);
        }

        return redirect()->back()->with('success', 'Educational subject created successfully.');
    }

    public function update(Request $request, EducationalSubject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|in:monthly,yearly',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:teachers,id'
        ]);

        $subject->update($request->except('teacher_ids'));
        
        // Sync teachers (this will remove old relationships and add new ones)
        $subject->teachers()->sync($request->teacher_ids ?? []);

        return redirect()->back()->with('success', 'Educational subject updated successfully.');
    }

    public function destroy(EducationalSubject $subject)
    {
        $subject->delete();

        return redirect()->back()->with('success', 'Educational subject deleted successfully.');
    }

    private function getLanguageData()
    {
        return [
            'educational_subjects' => __('messages.educational_subjects'),
            'add_new' => __('messages.add_new'),
            'edit' => __('messages.edit'),
            'delete' => __('messages.delete'),
            'save' => __('messages.save'),
            'cancel' => __('messages.cancel'),
            'name' => __('messages.name'),
            'description' => __('messages.description'),
            'duration' => __('messages.duration'),
            'monthly' => __('messages.monthly'),
            'yearly' => __('messages.yearly'),
            'price' => __('messages.price'),
            'select_duration' => 'Select Duration',
            'status' => __('messages.status'),
            'actions' => __('messages.actions'),
            'teachers' => __('messages.formation_teachers'),
            'select_teachers' => __('messages.select_teachers'),
            'active' => 'Active',
            'inactive' => 'Inactive',
            'close' => __('messages.close'),
        ];
    }
}
