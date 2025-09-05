<?php

namespace App\Http\Controllers;

use App\Models\ChildhoodPayment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChildhoodPaymentController extends Controller
{
    public function index()
    {
        $payments = ChildhoodPayment::with(['subscription.student', 'subscription.childhoodSubject'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/ChildhoodPayments', [
            'payments' => $payments
        ]);
    }

    public function markAsPaid(ChildhoodPayment $payment)
    {
        $payment->update([
            'status' => 'Paid',
            'paid_amount' => $payment->amount,
            'payment_date' => now()
        ]);

        return back()->with('success', 'Payment marked as paid successfully');
    }

    public function processPayment(Request $request, ChildhoodPayment $payment)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0|max:' . $payment->amount,
            'payment_date' => 'required|date'
        ]);

        $payment->update([
            'paid_amount' => $request->paid_amount,
            'payment_date' => $request->payment_date,
            'status' => $request->paid_amount >= $payment->amount ? 'Paid' : 'Partial'
        ]);

        return back()->with('success', 'Payment processed successfully');
    }

    public function markAsCancelled(ChildhoodPayment $payment)
    {
        $payment->update([
            'status' => 'Cancelled',
            'paid_amount' => 0
        ]);

        return back()->with('success', 'Payment marked as cancelled successfully');
    }

    public function updateAmount(Request $request, ChildhoodPayment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $payment->update([
            'amount' => $request->amount,
            'status' => $payment->paid_amount >= $request->amount ? 'Paid' : 'Pending'
        ]);

        return back()->with('success', 'Payment amount updated successfully');
    }
}
