<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Formation;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\EducationalSupportPayment;
use App\Models\EducationalSupportSubscription;
use App\Models\FormationPayment;
use App\Models\FormationSubscription;
use App\Models\ChildhoodPayment;
use App\Models\ChildhoodSubscription;
use App\Models\SubscriptionType;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Real statistics from database
        $stats = [
            'totalStudents' => Student::count(),
            'totalTeachers' => Teacher::count(),
            'totalFormations' => Formation::count(),
            'totalUsers' => User::count(),
        ];

        // Monthly payments data by category for the line chart (last 12 months)
        $monthlyPayments = [];
        $monthlyPaymentsByCategory = [
            'labels' => [],
            'educational_support' => [],
            'formations' => [],
            'childhood' => [],
        ];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');

            // Aggregate per-category
            $eduSupport = (float) EducationalSupportPayment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('paid_amount');
            $formations = (float) FormationPayment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            $childhood = (float) ChildhoodPayment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            // Backward compatibility total
            $monthlyPayments[] = [
                'month' => $monthName,
                'amount' => $eduSupport + $formations + $childhood,
            ];

            // Fill multi-series structure
            $monthlyPaymentsByCategory['labels'][] = $monthName;
            $monthlyPaymentsByCategory['educational_support'][] = $eduSupport;
            $monthlyPaymentsByCategory['formations'][] = $formations;
            $monthlyPaymentsByCategory['childhood'][] = $childhood;
        }

        // Calculate total revenue
        $totalRevenue = Payment::sum('amount');

        // Subscription distribution for pie chart
        $subscriptionStats = [];
        $subscriptionTypes = SubscriptionType::all();
        $totalActiveSubscriptions = Subscription::where('auto_status', 'active')->count();
        
        foreach ($subscriptionTypes as $type) {
            $count = Subscription::where('subscription_type_id', $type->id)
                                ->where('auto_status', 'active')
                                ->count();
            
            $subscriptionStats[] = [
                'name' => $type->name,
                'count' => $count,
                'percentage' => $totalActiveSubscriptions > 0 ? round(($count / $totalActiveSubscriptions) * 100, 1) : 0
            ];
        }

        // Recent activity - aggregate from all sources
        $recentActivity = collect();

        // Educational Support Payments
        $eduPayments = EducationalSupportPayment::with('educationalSupportSubscription.student')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => 'edu_' . $payment->id,
                    'user' => $payment->educationalSupportSubscription->student->name ?? 'Unknown Student',
                    'activity' => 'Educational Support Payment - ' . number_format($payment->paid_amount, 2) . ' MAD',
                    'date' => $payment->created_at->format('Y-m-d H:i'),
                    'timestamp' => $payment->created_at->timestamp,
                    'status' => ucfirst($payment->status)
                ];
            });

        // Formation Payments
        $formationPayments = FormationPayment::with('formationSubscription.student')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => 'form_' . $payment->id,
                    'user' => $payment->formationSubscription->student->name ?? 'Unknown Student',
                    'activity' => 'Formation Payment - ' . number_format($payment->amount, 2) . ' MAD',
                    'date' => $payment->created_at->format('Y-m-d H:i'),
                    'timestamp' => $payment->created_at->timestamp,
                    'status' => ucfirst($payment->status)
                ];
            });

        // Childhood Payments
        $childhoodPayments = ChildhoodPayment::with('childhoodSubscription.student')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => 'child_' . $payment->id,
                    'user' => $payment->childhoodSubscription->student->name ?? 'Unknown Student',
                    'activity' => 'Childhood Education Payment - ' . number_format($payment->amount, 2) . ' MAD',
                    'date' => $payment->created_at->format('Y-m-d H:i'),
                    'timestamp' => $payment->created_at->timestamp,
                    'status' => ucfirst($payment->status)
                ];
            });

        // Educational Support Subscriptions
        $eduSubscriptions = EducationalSupportSubscription::with('student')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($subscription) {
                return [
                    'id' => 'edu_sub_' . $subscription->id,
                    'user' => $subscription->student->name ?? 'Unknown Student',
                    'activity' => 'Subscribed to Educational Support',
                    'date' => $subscription->created_at->format('Y-m-d H:i'),
                    'timestamp' => $subscription->created_at->timestamp,
                    'status' => 'Active'
                ];
            });

        // Formation Subscriptions
        $formationSubscriptions = FormationSubscription::with('student')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($subscription) {
                return [
                    'id' => 'form_sub_' . $subscription->id,
                    'user' => $subscription->student->name ?? 'Unknown Student',
                    'activity' => 'Subscribed to Formation',
                    'date' => $subscription->created_at->format('Y-m-d H:i'),
                    'timestamp' => $subscription->created_at->timestamp,
                    'status' => 'Active'
                ];
            });

        // Childhood Subscriptions
        $childhoodSubscriptions = ChildhoodSubscription::with('student')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($subscription) {
                return [
                    'id' => 'child_sub_' . $subscription->id,
                    'user' => $subscription->student->name ?? 'Unknown Student',
                    'activity' => 'Subscribed to Childhood Education',
                    'date' => $subscription->created_at->format('Y-m-d H:i'),
                    'timestamp' => $subscription->created_at->timestamp,
                    'status' => 'Active'
                ];
            });

        // Recent Expenses
        $recentExpenses = Expense::with('expenseCategory')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => 'expense_' . $expense->id,
                    'user' => $expense->vendor_name ?? 'School',
                    'activity' => 'Expense: ' . $expense->title . ' - ' . number_format($expense->amount, 2) . ' MAD',
                    'date' => $expense->created_at->format('Y-m-d H:i'),
                    'timestamp' => $expense->created_at->timestamp,
                    'status' => ucfirst($expense->status)
                ];
            });

        // Merge all activities and sort by timestamp
        $recentActivity = $recentActivity
            ->concat($eduPayments)
            ->concat($formationPayments)
            ->concat($childhoodPayments)
            ->concat($eduSubscriptions)
            ->concat($formationSubscriptions)
            ->concat($childhoodSubscriptions)
            ->concat($recentExpenses)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values()
            ->toArray();

        // Category stats: payments total and subscriptions count per domain
        $categoryStats = [
            'educational_support' => [
                'paymentsTotal' => (float) EducationalSupportPayment::sum('paid_amount'),
                'subscriptionsCount' => (int) EducationalSupportSubscription::count(),
            ],
            'formations' => [
                'paymentsTotal' => (float) FormationPayment::sum('amount'),
                'subscriptionsCount' => (int) FormationSubscription::count(),
            ],
            'childhood' => [
                'paymentsTotal' => (float) ChildhoodPayment::sum('amount'),
                'subscriptionsCount' => (int) ChildhoodSubscription::count(),
            ],
        ];

        // Expense statistics
        $expenseStats = [
            'total' => (float) Expense::sum('amount'),
            'paid' => (float) Expense::where('status', 'paid')->sum('amount'),
            'pending' => (float) Expense::where('status', 'pending')->sum('amount'),
            'thisMonth' => (float) Expense::whereYear('expense_date', now()->year)
                                         ->whereMonth('expense_date', now()->month)
                                         ->sum('amount'),
        ];

        // Expenses by category for pie chart
        $expensesByCategory = Expense::with('expenseCategory')
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->get()
            ->groupBy('category_id')
            ->map(function ($expenses, $categoryId) {
                $category = $expenses->first()->expenseCategory;
                return [
                    'name' => $category ? $category->name : 'Unknown',
                    'name_fr' => $category ? $category->name_fr : 'Inconnu',
                    'color' => $category ? $category->color : '#6B7280',
                    'amount' => (float) $expenses->sum('amount'),
                ];
            })
            ->values()
            ->toArray();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'totalRevenue' => $totalRevenue,
            'monthlyPayments' => $monthlyPayments,
            'monthlyPaymentsByCategory' => $monthlyPaymentsByCategory,
            'subscriptionStats' => $subscriptionStats,
            'totalActiveSubscriptions' => $totalActiveSubscriptions,
            'recentActivity' => $recentActivity,
            'categoryStats' => $categoryStats,
            'expenseStats' => $expenseStats,
            'expensesByCategory' => $expensesByCategory,
        ]);
    }
}
