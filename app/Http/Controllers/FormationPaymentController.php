<?php

namespace App\Http\Controllers;

use App\Models\FormationPayment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class FormationPaymentController extends Controller
{
    public function index()
    {
        $payments = FormationPayment::with(['subscription.student', 'subscription.formation'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'student_name' => $p->subscription?->student?->name,
                    'formation_title' => $p->subscription?->formation?->title,
                    'amount' => (float) $p->amount,
                    'paid_amount' => (float) ($p->paid_amount ?? 0),
                    'status' => ucfirst($p->status ?? 'draft'),
                    'payment_date' => optional($p->payment_date)->format('Y-m-d'),
                ];
            });

        return Inertia::render('Admin/FormationPayments', [
            'payments' => $payments,
        ]);
    }

    public function markAsPaid(FormationPayment $payment)
    {
        $payment->update([
            'status' => 'paid',
            'paid_amount' => $payment->amount,
            'payment_date' => Carbon::now()
        ]);
        return back()->with('success', 'Payment marked as paid');
    }

    public function updateAmount(Request $request, FormationPayment $payment)
    {
        $request->validate(['amount' => 'required|numeric|min:0']);
        $payment->update(['amount' => $request->amount]);
        return back()->with('success', 'Amount updated');
    }

    public function processPayment(Request $request, FormationPayment $payment)
    {
        $currentPaid = $payment->paid_amount ?? 0;
        $remaining = max(0, $payment->amount - $currentPaid);

        $request->validate([
            'paid_amount' => 'required|numeric|min:0|max:' . $remaining,
        ]);

        $newPaid = $currentPaid + (float) $request->paid_amount;
        $status = $newPaid >= $payment->amount ? 'paid' : 'partial';

        $payment->update([
            'paid_amount' => $newPaid,
            'status' => $status,
            'payment_date' => Carbon::now(),
        ]);

        return back()->with('success', 'Payment processed');
    }

    public function markAsCancelled(FormationPayment $payment)
    {
        $payment->update(['status' => 'cancelled']);
        return back()->with('success', 'Payment cancelled');
    }
}


