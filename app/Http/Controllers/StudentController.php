<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\Formation;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        // Get real students from dedicated students table
        $students = Student::with(['formations']) // Eager load formations
            ->latest()
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'student_id' => $student->student_id,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'parent_phone' => $student->parent_phone,
                    'address' => $student->address,
                    'date_of_birth' => $student->date_of_birth,
                    'grade' => $student->formations->first()->title ?? 'No formation assigned',
                    'contact_parent' => $student->parent_phone ?? $student->phone ?? 'Not provided'
                ];
            });

        // Get formations for the modal
        $formations = Formation::select('id', 'title', 'price', 'duration', 'level')
            ->where('status', 'Active')
            ->get();

        return Inertia::render('Admin/Students', [
            'students' => $students,
            'total' => $students->count(),
            'formations' => $formations
        ]);
    }

    /**
     * Store a new student
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Student creation request received', $request->all());
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'student_id' => 'required|string|unique:users,student_id',
                'phone' => 'nullable|string|max:20',
                'parent_phone' => 'required|string|max:20',
                'address' => 'nullable|string|max:500',
                'date_of_birth' => 'required|date|before:today',
                'formations' => 'nullable|array',
                'formations.*' => 'exists:formations,id',
                'notes' => 'nullable|string|max:1000'
            ]);
            
            \Log::info('Validation passed', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', $e->errors());
            throw $e;
        }

        try {
            // Create the student in the students table
            $student = Student::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'student_id' => $validated['student_id'],
                'phone' => $validated['phone'],
                'parent_phone' => $validated['parent_phone'],
                'address' => $validated['address'],
                'date_of_birth' => $validated['date_of_birth'],
                'notes' => $validated['notes'],
                'password' => Hash::make('password123'), // Default password
                'status' => 'active',
                'email_verified_at' => now(), // Auto-verify email for admin-created students
            ]);

            \Log::info('Student created in students table:', ['student_id' => $student->id, 'name' => $student->name]);

            // Attach formations if selected
            if (!empty($validated['formations'])) {
                $student->formations()->attach($validated['formations']);
                \Log::info('Formations attached:', $validated['formations']);
            }

            \Log::info('Student creation completed successfully');
            return redirect()->back()->with('success', 'Student created successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Error creating student:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to create student: ' . $e->getMessage()]);
        }
    }
}
