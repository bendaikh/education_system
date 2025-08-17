<?php

namespace App\Http\Controllers;

use App\Models\EducationalSupportSubscription;
use App\Models\EducationalSupportPayment;
use App\Models\Student;
use App\Models\EducationalSubject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class EducationalSupportController extends Controller
{
    public function index()
    {
        $subscriptions = EducationalSupportSubscription::with(['student', 'subject', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $students = Student::orderBy('name')->get();
        $subjects = EducationalSubject::where('status', 'active')->orderBy('name')->get();
        
        return Inertia::render('Admin/ManageEducationalSupport', [
            'subscriptions' => $subscriptions,
            'students' => $students,
            'subjects' => $subjects,
            'language' => $this->getLanguageData()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'educational_subject_id' => 'required|exists:educational_subjects,id',
            'status' => 'required|in:active,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        // Get the subject to determine duration
        $subject = EducationalSubject::find($request->educational_subject_id);
        
        // Calculate dates automatically
        $startDate = Carbon::now()->startOfDay();
        $endDate = $subject->duration === 'monthly' 
            ? $startDate->copy()->addMonth() 
            : $startDate->copy()->addYear();

        // Create subscription
        $subscription = EducationalSupportSubscription::create([
            'student_id' => $request->student_id,
            'educational_subject_id' => $request->educational_subject_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        // Create payment record with draft status
        EducationalSupportPayment::create([
            'subscription_id' => $subscription->id,
            'amount' => $subject->price, // Use the subject's price
            'status' => 'draft',
            'notes' => 'Payment pending'
        ]);

        return redirect()->back()->with('success', 'Educational support subscription created successfully.');
    }

    public function update(Request $request, EducationalSupportSubscription $subscription)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'educational_subject_id' => 'required|exists:educational_subjects,id',
            'status' => 'required|in:active,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        // Get the subject to determine duration
        $subject = EducationalSubject::find($request->educational_subject_id);
        
        // Calculate dates automatically (only if subject changed)
        $startDate = $subscription->start_date;
        $endDate = $subscription->end_date;
        
        if ($request->educational_subject_id !== $subscription->educational_subject_id) {
            // Subject changed, recalculate dates
            $startDate = Carbon::now()->startOfDay();
            $endDate = $subject->duration === 'monthly' 
                ? $startDate->copy()->addMonth() 
                : $startDate->copy()->addYear();
        }

        $subscription->update([
            'student_id' => $request->student_id,
            'educational_subject_id' => $request->educational_subject_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Educational support subscription updated successfully.');
    }

    public function destroy(EducationalSupportSubscription $subscription)
    {
        $subscription->delete();

        return redirect()->back()->with('success', 'Educational support subscription deleted successfully.');
    }

    private function getLanguageData()
    {
        return [
            'manage_educational_support' => __('messages.manage_educational_support'),
            'add_new' => __('messages.add_new'),
            'edit' => __('messages.edit'),
            'delete' => __('messages.delete'),
            'save' => __('messages.save'),
            'cancel' => __('messages.cancel'),
            'student_name' => __('messages.student_name'),
            'subject' => 'Subject',
            'select_student' => __('messages.select_student'),
            'select_subject' => __('messages.select_subject'),
            'start_date' => __('messages.start_date'),
            'end_date' => __('messages.end_date'),
            'status' => __('messages.status'),
            'notes' => 'Notes',
            'actions' => __('messages.actions'),
            'active' => 'Active',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'close' => __('messages.close'),
            'duration' => __('messages.duration'),
            'monthly' => __('messages.monthly'),
            'yearly' => __('messages.yearly'),
            'subscription_duration' => 'Subscription Duration',
            'price' => __('messages.price'),
            'calculated_dates' => 'Calculated Dates',
        ];
    }
}
