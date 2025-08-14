<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Teacher;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); // Default 10 items per page
        $search = $request->get('search');

        $query = Teacher::orderBy('created_at', 'desc');

        // Add search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('department', 'like', '%' . $search . '%')
                  ->orWhere('contact_email', 'like', '%' . $search . '%')
                  ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        $teachers = $query->paginate($perPage);

        // Transform the data
        $teachers->getCollection()->transform(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'department' => $teacher->department ?? 'Not specified',
                'role' => $teacher->role ?? 'Teacher',
                'contact_phone' => $teacher->contact_phone ?? 'N/A',
                'contact_email' => $teacher->contact_email ?? 'N/A',
                'image' => $teacher->image,
                'created_at' => $teacher->created_at?->format('Y-m-d') ?? null,
            ];
        });

        return Inertia::render('Admin/Teachers', [
            'teachers' => $teachers->items(),
            'pagination' => [
                'current_page' => $teachers->currentPage(),
                'last_page' => $teachers->lastPage(),
                'per_page' => $teachers->perPage(),
                'total' => $teachers->total(),
                'from' => $teachers->firstItem(),
                'to' => $teachers->lastItem(),
                'links' => $teachers->links()->elements[0] ?? [],
            ],
            'filters' => [
                'search' => $search,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'role' => 'nullable|string|max:255',
        ]);

        // Set default role if not provided
        $validated['role'] = $validated['role'] ?? 'Teacher';

        // Create the teacher
        $teacher = Teacher::create($validated);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher created successfully!');
    }
}
