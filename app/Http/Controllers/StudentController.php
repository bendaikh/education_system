<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\Formation;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Import students from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            
            // Store the file temporarily
            $path = $file->store('temp');
            
            // Import the students
            $import = new StudentsImport();
            Excel::import($import, $path);
            
            // Clean up the temporary file
            Storage::delete($path);
            
            // Get import results
            $importedCount = $import->getRowCount();
            $errors = $import->errors();
            
            $message = "Successfully imported {$importedCount} students.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " rows had errors and were skipped.";
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            \Log::error('Validation error importing students:', [
                'message' => $e->getMessage(),
                'failures' => $e->failures()
            ]);
            
            $errorMessage = 'Import failed due to validation errors. Please check your file format and data.';
            if (count($e->failures()) > 0) {
                $errorMessage .= ' First error: ' . $e->failures()[0]->errors()[0];
            }
            
            return redirect()->back()->withErrors(['error' => $errorMessage]);
            
        } catch (\Exception $e) {
            \Log::error('Error importing students:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->withErrors(['error' => 'Failed to import students: ' . $e->getMessage()]);
        }
    }

    /**
     * Download sample Excel template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_template.csv"',
        ];

        $template = "name,email,student_id,phone,parent_phone,address,date_of_birth,notes\n";
        $template .= "John Doe,john.doe@example.com,STU2024001,+1234567890,+1234567890,123 Main St,1995-01-15,Good student\n";
        $template .= "Jane Smith,jane.smith@example.com,STU2024002,+1234567891,+1234567891,456 Oak Ave,1996-03-20,Excellent performance\n";

        return response($template, 200, $headers);
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(Student $student)
    {
        $student->load('formations');
        
        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'student_id' => $student->student_id,
                'phone' => $student->phone,
                'parent_phone' => $student->parent_phone,
                'address' => $student->address,
                'date_of_birth' => $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : null,
                'notes' => $student->notes,
                'status' => $student->status,
                'formations' => $student->formations->pluck('id')->toArray(),
            ]
        ]);
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'student_id' => 'required|string|unique:students,student_id,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'parent_phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'required|date|before:today',
            'formations' => 'nullable|array',
            'formations.*' => 'exists:formations,id',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,suspended'
        ]);

        try {
            $student->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'student_id' => $validated['student_id'],
                'phone' => $validated['phone'],
                'parent_phone' => $validated['parent_phone'],
                'address' => $validated['address'],
                'date_of_birth' => $validated['date_of_birth'],
                'notes' => $validated['notes'],
                'status' => $validated['status'],
            ]);

            // Update formations
            if (isset($validated['formations'])) {
                $student->formations()->sync($validated['formations']);
            }

            return redirect()->back()->with('success', 'Student updated successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Error updating student:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to update student: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified student
     */
    public function destroy(Student $student)
    {
        try {
            // Check if student has any related data
            $hasSubscriptions = $student->educationalSupportSubscriptions()->exists();
            $hasFormations = $student->formations()->exists();
            
            if ($hasSubscriptions || $hasFormations) {
                return redirect()->back()->withErrors(['error' => 'Cannot delete student. They have active subscriptions or formations.']);
            }

            $student->delete();
            return redirect()->back()->with('success', 'Student deleted successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Error deleting student:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to delete student: ' . $e->getMessage()]);
        }
    }
}
