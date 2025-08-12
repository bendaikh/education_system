<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        // Mock data for payments - replace with real database queries later
        $payments = [
            [
                'id' => 1,
                'student_name' => 'Liam Smith',
                'payment_type' => 'Tuition Fee',
                'amount' => '$1,250.00',
                'status' => 'Paid',
                'due_date' => '2024-01-15',
                'payment_date' => '2024-01-12'
            ],
            [
                'id' => 2,
                'student_name' => 'Olivia Johnson',
                'payment_type' => 'Lab Fee',
                'amount' => '$350.00',
                'status' => 'Pending',
                'due_date' => '2024-01-20',
                'payment_date' => null
            ],
            [
                'id' => 3,
                'student_name' => 'Noah Williams',
                'payment_type' => 'Library Fee',
                'amount' => '$75.00',
                'status' => 'Paid',
                'due_date' => '2024-01-10',
                'payment_date' => '2024-01-08'
            ],
            [
                'id' => 4,
                'student_name' => 'Emma Brown',
                'payment_type' => 'Activity Fee',
                'amount' => '$200.00',
                'status' => 'Overdue',
                'due_date' => '2024-01-05',
                'payment_date' => null
            ]
        ];

        return Inertia::render('Admin/Payments', [
            'payments' => $payments,
            'total' => 156
        ]);
    }
}
