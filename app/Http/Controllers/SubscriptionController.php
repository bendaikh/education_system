<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Subscription;
use App\Models\SubscriptionType;
use App\Models\Formation;
use App\Models\Student;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); // Default 10 items per page
        $search = $request->get('search');

        $query = Subscription::with(['subscriptionType', 'student'])
            ->orderBy('created_at', 'desc');

        // Add search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function($studentQuery) use ($search) {
                    $studentQuery->where('name', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%');
                })
                ->orWhereHas('subscriptionType', function($typeQuery) use ($search) {
                    $typeQuery->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $subscriptions = $query->paginate($perPage);

        // Transform the data
        $subscriptions->getCollection()->transform(function ($subscription) {
            // Calculate next billing automatically based on plan
            $nextBilling = $subscription->calculateNextBilling();
            
            return [
                'id' => $subscription->id,
                'plan_name' => $subscription->subscriptionType->name,
                'subscriber_name' => $subscription->student->name,
                'student_email' => $subscription->student->email,
                'price' => $subscription->subscriptionType->formatted_price,
                'status' => ucfirst($subscription->status), // This uses the calculated status accessor
                'started_at' => $subscription->started_at->format('Y-m-d'),
                'expires_at' => $subscription->expires_at?->format('Y-m-d') ?? 'N/A',
                'next_billing' => $nextBilling?->format('Y-m-d') ?? 'N/A',
                'duration' => $subscription->subscriptionType->duration
            ];
        });

        // Get active subscription types for the modal
        $subscriptionTypes = SubscriptionType::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'price' => $type->price,
                    'duration' => $type->duration,
                    'formatted_price' => $type->formatted_price,
                    'description' => $type->description
                ];
            });

        // Get students for the modal
        $students = Student::where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'student_id' => $student->student_id
                ];
            });

        return Inertia::render('Admin/Subscriptions', [
            'subscriptions' => $subscriptions->items(),
            'subscriptionTypes' => $subscriptionTypes,
            'students' => $students,
            'pagination' => [
                'current_page' => $subscriptions->currentPage(),
                'last_page' => $subscriptions->lastPage(),
                'per_page' => $subscriptions->perPage(),
                'total' => $subscriptions->total(),
                'from' => $subscriptions->firstItem(),
                'to' => $subscriptions->lastItem(),
                'links' => $subscriptions->links()->elements[0] ?? [],
            ],
            'filters' => [
                'search' => $search,
            ]
        ]);
    }

    public function typesIndex(Request $request)
    {
        $perPage = $request->get('per_page', 10); // Default 10 items per page
        $search = $request->get('search');

        $query = SubscriptionType::orderBy('created_at', 'desc');

        // Add search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $subscriptionTypes = $query->with('formation')->paginate($perPage);

        // Transform the data
        $subscriptionTypes->getCollection()->transform(function ($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'formation_name' => $type->formation?->title ?? null,
                'description' => $type->description,
                'price' => (float) $type->price,
                'duration' => $type->duration,
                'max_students' => $type->max_students,
                'features' => $type->features ?? [],
                'is_active' => (bool) $type->is_active,
                'created_at' => $type->created_at?->format('Y-m-d') ?? null,
            ];
        });

        // Get formations for the dropdown
        $formations = Formation::orderBy('title')->get(['id', 'title']);

        return Inertia::render('Admin/SubscriptionTypes/Index', [
            'subscriptionTypes' => $subscriptionTypes->items(),
            'formations' => $formations,
            'pagination' => [
                'current_page' => $subscriptionTypes->currentPage(),
                'last_page' => $subscriptionTypes->lastPage(),
                'per_page' => $subscriptionTypes->perPage(),
                'total' => $subscriptionTypes->total(),
                'from' => $subscriptionTypes->firstItem(),
                'to' => $subscriptionTypes->lastItem(),
                'links' => $subscriptionTypes->links()->elements[0] ?? [],
            ],
            'filters' => [
                'search' => $search,
            ]
        ]);
    }

    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'formation_id' => 'nullable|exists:formations,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string|in:monthly,yearly',
            'features' => 'nullable|array',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        // Ensure is_active has a default value
        $validated['is_active'] = $validated['is_active'] ?? true;

        // Create the subscription type
        $subscriptionType = SubscriptionType::create($validated);

        return redirect()->route('admin.subscriptions.types.index')
            ->with('success', 'Subscription type created successfully!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subscription_type_id' => 'required|exists:subscription_types,id',
            'student_id' => 'required|exists:students,id',
        ]);

        // Get subscription type to calculate dates
        $subscriptionType = SubscriptionType::findOrFail($validated['subscription_type_id']);
        $startDate = \Carbon\Carbon::now(); // Always start immediately
        
        // Auto-calculate expiry date based on plan duration
        $expiryDate = $subscriptionType->duration === 'monthly' 
            ? $startDate->copy()->addMonth()
            : $startDate->copy()->addYear();

        // Auto-calculate next billing date
        $nextBilling = $subscriptionType->duration === 'monthly' 
            ? $startDate->copy()->addMonth()
            : $startDate->copy()->addYear();

        // Amount paid is the plan price
        $amountPaid = $subscriptionType->price;

        // Create the subscription with auto-calculated values
        $subscription = Subscription::create([
            'subscription_type_id' => $validated['subscription_type_id'],
            'student_id' => $validated['student_id'],
            'started_at' => $startDate,
            'expires_at' => $expiryDate,
            'auto_status' => 'active',
            'next_billing_date' => $nextBilling,
            'amount_paid' => $amountPaid,
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription created successfully! Period: ' . $subscriptionType->duration . ', Expires: ' . $expiryDate->format('Y-m-d'));
    }
}
