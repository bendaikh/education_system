<?php

namespace App\Http\Controllers;

use App\Models\ChildhoodSubscription;
use App\Models\ChildhoodSubject;
use App\Models\ChildhoodPayment;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ChildhoodSubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = ChildhoodSubscription::with(['student', 'childhoodSubject', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'student_id' => $s->student_id,
                    'childhood_subject_id' => $s->childhood_subject_id,
                    'student_name' => $s->student?->name,
                    'student_email' => $s->student?->email,
                    'childhood_subject_name' => $s->childhoodSubject?->name,
                    'childhood_subject_price' => $s->childhoodSubject?->price,
                    'status' => ucfirst($s->status ?? 'Active'),
                    'payment_status' => ucfirst(optional($s->payment)->status ?? 'Pending'),
                    'start_date' => optional($s->start_date)->format('Y-m-d'),
                    'end_date' => optional($s->end_date)->format('Y-m-d'),
                    'notes' => $s->notes,
                ];
            });

        $students = Student::all();
        $subjects = ChildhoodSubject::where('status', 'Active')->get();

        return Inertia::render('Admin/ChildhoodSubscriptions', [
            'subscriptions' => $subscriptions,
            'students' => $students,
            'subjects' => $subjects
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'childhood_subject_id' => 'required|exists:childhood_subjects,id',
            'start_date' => 'required|date',
            'status' => 'required|string|in:Active,Inactive,Cancelled',
            'notes' => 'nullable|string'
        ]);

        // Get the subject to determine duration
        $subject = ChildhoodSubject::find($request->childhood_subject_id);
        
        // Calculate dates automatically using chosen start date
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = $subject->duration === 'monthly' 
            ? $startDate->copy()->addMonth() 
            : $startDate->copy()->addYear();

        // Create subscription
        $subscription = ChildhoodSubscription::create([
            'student_id' => $request->student_id,
            'childhood_subject_id' => $request->childhood_subject_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        // Create payment record with draft status
        ChildhoodPayment::create([
            'subscription_id' => $subscription->id,
            'amount' => $subject->price, // Use the subject's price
            'status' => 'Pending',
            'notes' => 'Payment pending'
        ]);

        return redirect()->back()->with('success', 'Childhood subscription created successfully.');
    }

    public function update(Request $request, ChildhoodSubscription $subscription)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'childhood_subject_id' => 'required|exists:childhood_subjects,id',
            'start_date' => 'required|date',
            'status' => 'required|string|in:Active,Inactive,Cancelled',
            'notes' => 'nullable|string'
        ]);

        // Get the subject to determine duration
        $subject = ChildhoodSubject::find($request->childhood_subject_id);
        
        // Calculate dates automatically if subject or start date changed
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = $subject->duration === 'monthly' 
            ? $startDate->copy()->addMonth() 
            : $startDate->copy()->addYear();

        $subscription->update([
            'student_id' => $request->student_id,
            'childhood_subject_id' => $request->childhood_subject_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Childhood subscription updated successfully.');
    }

    public function destroy(ChildhoodSubscription $subscription)
    {
        $subscription->delete();
        return redirect()->back()->with('success', 'Childhood subscription deleted successfully.');
    }
}
