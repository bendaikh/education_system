<?php

namespace App\Http\Controllers;

use App\Models\FormationSubscription;
use App\Models\FormationPayment;
use App\Models\Formation;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class FormationSubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = FormationSubscription::with(['student', 'formation', 'payment'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'student_id' => $s->student_id,
                    'formation_id' => $s->formation_id,
                    'student_name' => $s->student?->name,
                    'student_email' => $s->student?->email,
                    'formation_title' => $s->formation?->title,
                    'status' => ucfirst($s->status ?? 'active'),
                    'payment_status' => ucfirst(optional($s->payment)->status ?? 'draft'),
                    'start_date' => optional($s->start_date)->format('Y-m-d'),
                    'end_date' => optional($s->end_date)->format('Y-m-d'),
                    'price' => (float) ($s->formation->price ?? 0),
                    'notes' => $s->notes,
                ];
            });

        $students = Student::orderBy('name')->get(['id','name','email']);
        $formations = Formation::orderBy('title')->get(['id','title','price','duration','level']);

        return Inertia::render('Admin/FormationSubscriptions', [
            'subscriptions' => $subscriptions,
            'students' => $students,
            'formations' => $formations,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'formation_id' => 'required|exists:formations,id',
            'start_date' => 'required|date',
            'status' => 'required|in:active,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = $startDate->copy()->addMonth();

        $subscription = FormationSubscription::create([
            'student_id' => $validated['student_id'],
            'formation_id' => $validated['formation_id'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $formation = Formation::find($validated['formation_id']);
        FormationPayment::create([
            'subscription_id' => $subscription->id,
            'amount' => $formation?->price ?? 0,
            'status' => 'draft',
            'notes' => 'Payment pending'
        ]);

        return redirect()->back()->with('success', 'Formation subscription created successfully.');
    }

    public function update(Request $request, FormationSubscription $subscription)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'formation_id' => 'required|exists:formations,id',
            'start_date' => 'required|date',
            'status' => 'required|in:active,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = $startDate->copy()->addMonth();

        $subscription->update([
            'student_id' => $validated['student_id'],
            'formation_id' => $validated['formation_id'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Formation subscription updated successfully.');
    }

    public function destroy(FormationSubscription $subscription)
    {
        $subscription->delete();
        return redirect()->back()->with('success', 'Formation subscription deleted successfully.');
    }
}


