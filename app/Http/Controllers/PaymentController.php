<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Formation;
use App\Models\User;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        // Get real payments from database
        $payments = Payment::with(['student', 'formation'])
            ->latest()
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'student_name' => $payment->student->name,
                    'payment_type' => $payment->formation->title,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'due_date' => $payment->due_date->format('Y-m-d'),
                    'payment_date' => $payment->payment_date ? $payment->payment_date->format('Y-m-d') : null
                ];
            });

        // Get formations and students for the modal
        $formations = Formation::select('id', 'title', 'price', 'duration', 'level')
            ->where('status', 'Active')
            ->get();
        
        $students = User::select('id', 'name', 'email')
            ->where('role', 'student')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Payments', [
            'payments' => $payments,
            'total' => $payments->count(),
            'formations' => $formations,
            'students' => $students
        ]);
    }

    /**
     * Store a new payment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'formation_id' => 'required|exists:formations,id',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|string|in:Cash,Bank Transfer,Credit Card,Check,Online,Other',
            'due_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500'
        ]);

        // Get student and formation details
        $student = User::findOrFail($validated['student_id']);
        $formation = Formation::findOrFail($validated['formation_id']);

        // Create the payment in the database
        $payment = Payment::create([
            'student_id' => $validated['student_id'],
            'formation_id' => $validated['formation_id'],
            'amount' => $validated['amount'],
            'payment_type' => $validated['payment_type'],
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'],
            'status' => 'Pending', // Default status
            'created_by' => auth()->id()
        ]);

        // Log for debugging
        \Log::info('Payment created:', ['payment_id' => $payment->id, 'student_id' => $payment->student_id]);

        return redirect()->back()->with('success', 'Payment created successfully!');
    }
}
