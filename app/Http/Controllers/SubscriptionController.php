<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        // Mock data for subscriptions - replace with real database queries later
        $subscriptions = [
            [
                'id' => 1,
                'plan_name' => 'Premium Monthly',
                'subscriber_name' => 'Evergreen Academy',
                'price' => '$99.00/month',
                'status' => 'Active',
                'start_date' => '2024-01-01',
                'next_billing' => '2024-02-01',
                'features' => 'Unlimited Students, Advanced Analytics'
            ],
            [
                'id' => 2,
                'plan_name' => 'Basic Annual',
                'subscriber_name' => 'Lincoln High School',
                'price' => '$599.00/year',
                'status' => 'Active',
                'start_date' => '2023-09-01',
                'next_billing' => '2024-09-01',
                'features' => 'Up to 500 Students, Basic Reports'
            ],
            [
                'id' => 3,
                'plan_name' => 'Enterprise',
                'subscriber_name' => 'Metro School District',
                'price' => '$299.00/month',
                'status' => 'Trial',
                'start_date' => '2024-01-15',
                'next_billing' => '2024-02-15',
                'features' => 'Unlimited Everything, Priority Support'
            ],
            [
                'id' => 4,
                'plan_name' => 'Standard Monthly',
                'subscriber_name' => 'Riverside Elementary',
                'price' => '$49.00/month',
                'status' => 'Expired',
                'start_date' => '2023-12-01',
                'next_billing' => '2024-01-01',
                'features' => 'Up to 200 Students, Standard Features'
            ]
        ];

        return Inertia::render('Admin/Subscriptions', [
            'subscriptions' => $subscriptions,
            'total' => 42
        ]);
    }
}
