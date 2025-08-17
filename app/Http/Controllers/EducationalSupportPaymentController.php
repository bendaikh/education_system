<?php

namespace App\Http\Controllers;

use App\Models\EducationalSupportPayment;
use App\Models\EducationalSupportSubscription;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class EducationalSupportPaymentController extends Controller
{
    public function index()
    {
        $payments = EducationalSupportPayment::with(['subscription.student', 'subscription.subject'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return Inertia::render('Admin/EducationalSupportPayments', [
            'payments' => $payments,
            'language' => $this->getLanguageData()
        ]);
    }

    public function markAsPaid(EducationalSupportPayment $payment)
    {
        $payment->update([
            'status' => 'paid',
            'paid_amount' => $payment->amount, // Full payment
            'payment_date' => Carbon::now()
        ]);
        return redirect()->back()->with('success', 'Payment marked as paid successfully.');
    }

    public function processPayment(Request $request, EducationalSupportPayment $payment)
    {
        $currentPaidAmount = $payment->paid_amount ?? 0;
        $remainingAmount = $payment->amount - $currentPaidAmount;
        
        $request->validate([
            'paid_amount' => 'required|numeric|min:0|max:' . $remainingAmount,
        ]);

        $newPaidAmount = $request->paid_amount;
        $totalPaidAmount = $currentPaidAmount + $newPaidAmount;
        $totalAmount = $payment->amount;
        $newRemainingAmount = $totalAmount - $totalPaidAmount;

        // Determine status based on total payment amount
        $status = $totalPaidAmount >= $totalAmount ? 'paid' : 'partial';

        $payment->update([
            'paid_amount' => $totalPaidAmount,
            'status' => $status,
            'payment_date' => Carbon::now(),
            'notes' => $newRemainingAmount > 0 ? "Remaining balance: $" . number_format($newRemainingAmount, 2) : 'Payment completed'
        ]);

        return redirect()->back()->with('success', 'Payment processed successfully.');
    }

    public function markAsCancelled(EducationalSupportPayment $payment)
    {
        $payment->update([
            'status' => 'cancelled'
        ]);

        return redirect()->back()->with('success', 'Payment marked as cancelled successfully.');
    }

    public function updateAmount(Request $request, EducationalSupportPayment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $payment->update([
            'amount' => $request->amount
        ]);

        return redirect()->back()->with('success', 'Payment amount updated successfully.');
    }

    private function getLanguageData()
    {
        return [
            'educational_support_payments' => 'Educational Support Payments',
            'add_new' => __('messages.add_new'),
            'edit' => __('messages.edit'),
            'delete' => __('messages.delete'),
            'save' => __('messages.save'),
            'cancel' => __('messages.cancel'),
            'student_name' => __('messages.student_name'),
            'subject' => 'Subject',
            'amount' => __('messages.amount'),
            'payment_status' => 'Payment Status',
            'payment_date' => __('messages.payment_date'),
            'actions' => __('messages.actions'),
            'draft' => 'Draft',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            'partial' => 'Partial',
            'mark_as_paid' => 'Mark as Paid',
            'mark_as_cancelled' => 'Mark as Cancelled',
            'update_amount' => 'Update Amount',
            'process_payment' => 'Process Payment',
            'payment_details' => 'Payment Details',
            'total_amount' => 'Total Amount',
            'paid_amount' => 'Paid Amount',
            'remaining_balance' => 'Remaining Balance',
            'max_amount' => 'Maximum Amount',
            'notes' => 'Notes',
            'subscription' => 'Subscription',
            'duration' => __('messages.duration'),
            'monthly' => __('messages.monthly'),
            'yearly' => __('messages.yearly'),
        ];
    }
}
