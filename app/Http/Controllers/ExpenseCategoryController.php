<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the expense categories.
     */
    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/ExpenseCategories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created expense category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_fr' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    /**
     * Update the specified expense category in storage.
     */
    public function update(Request $request, ExpenseCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_fr' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified expense category from storage.
     */
    public function destroy(ExpenseCategory $category)
    {
        // Check if category has expenses
        if ($category->expenses()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with existing expenses.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    /**
     * Toggle the active status of the category.
     */
    public function toggleActive(ExpenseCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return redirect()->back()->with('success', 'Category status updated.');
    }
}
