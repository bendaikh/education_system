<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::with('expenseCategory');

        // Filter by category
        if ($request->has('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->where('expense_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('expense_date', '<=', $request->end_date);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('vendor_name', 'like', '%' . $request->search . '%');
            });
        }

        $expenses = $query->orderBy('expense_date', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        // Calculate statistics
        $totalExpenses = Expense::sum('amount');
        $totalPaid = Expense::where('status', 'paid')->sum('amount');
        $totalPending = Expense::where('status', 'pending')->sum('amount');
        
        // Get expenses by category for the current month
        $currentMonth = now()->format('Y-m');
        $categoryStats = Expense::whereYear('expense_date', now()->year)
                                ->whereMonth('expense_date', now()->month)
                                ->selectRaw('category_id, SUM(amount) as total')
                                ->groupBy('category_id')
                                ->get()
                                ->pluck('total', 'category_id');

        // Get all active categories
        $categories = \App\Models\ExpenseCategory::active()->orderBy('name')->get();

        return Inertia::render('Admin/Expenses', [
            'expenses' => $expenses,
            'categories' => $categories,
            'filters' => $request->only(['category_id', 'status', 'search', 'start_date', 'end_date']),
            'statistics' => [
                'total' => $totalExpenses,
                'paid' => $totalPaid,
                'pending' => $totalPending,
                'categoryStats' => $categoryStats,
            ],
        ]);
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:expense_categories,id',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'status' => 'required|in:pending,paid,cancelled',
        ]);

        $expense = Expense::create($validated);

        return redirect()->back()->with('success', 'Expense created successfully.');
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:expense_categories,id',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'status' => 'required|in:pending,paid,cancelled',
        ]);

        $expense->update($validated);

        return redirect()->back()->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }

    /**
     * Mark expense as paid.
     */
    public function markAsPaid(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'payment_method' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
        ]);

        $expense->update([
            'status' => 'paid',
            'payment_method' => $validated['payment_method'] ?? $expense->payment_method,
            'receipt_number' => $validated['receipt_number'] ?? $expense->receipt_number,
        ]);

        return redirect()->back()->with('success', 'Expense marked as paid.');
    }

    /**
     * Mark expense as cancelled.
     */
    public function markAsCancelled(Expense $expense)
    {
        $expense->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Expense marked as cancelled.');
    }
}
