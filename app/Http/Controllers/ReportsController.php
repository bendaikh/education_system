<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\EducationalSupportPayment;
use App\Models\EducationalSupportSubscription;
use App\Models\FormationPayment;
use App\Models\FormationSubscription;
use App\Models\ChildhoodPayment;
use App\Models\ChildhoodSubscription;
use Carbon\Carbon;
use App\Models\Teacher;
use App\Models\Student;

class ReportsController extends Controller
{
    public function index()
    {
        $totals = [
            'educational_support' => [
                'payments' => (float) EducationalSupportPayment::sum('paid_amount'),
                'subscriptions' => (int) EducationalSupportSubscription::count(),
            ],
            'formations' => [
                'payments' => (float) FormationPayment::sum('amount'),
                'subscriptions' => (int) FormationSubscription::count(),
            ],
            'childhood' => [
                'payments' => (float) ChildhoodPayment::sum('amount'),
                'subscriptions' => (int) ChildhoodSubscription::count(),
            ],
        ];

        // Build monthly series for overview charts (last 12 months)
        $monthlyPaymentsByCategory = [
            'labels' => [],
            'educational_support' => [],
            'formations' => [],
            'childhood' => [],
        ];
        $monthlySubscriptionsByCategory = [
            'labels' => [],
            'educational_support' => [],
            'formations' => [],
            'childhood' => [],
        ];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $label = $date->format('M');
            $monthlyPaymentsByCategory['labels'][] = $label;
            $monthlySubscriptionsByCategory['labels'][] = $label;

            $monthlyPaymentsByCategory['educational_support'][] = (float) EducationalSupportPayment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('paid_amount');
            $monthlyPaymentsByCategory['formations'][] = (float) FormationPayment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            $monthlyPaymentsByCategory['childhood'][] = (float) ChildhoodPayment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $monthlySubscriptionsByCategory['educational_support'][] = (int) EducationalSupportSubscription::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlySubscriptionsByCategory['formations'][] = (int) FormationSubscription::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlySubscriptionsByCategory['childhood'][] = (int) ChildhoodSubscription::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return Inertia::render('Admin/Reports/Index', [
            'totals' => $totals,
            'monthlyPaymentsByCategory' => $monthlyPaymentsByCategory,
            'monthlySubscriptionsByCategory' => $monthlySubscriptionsByCategory,
        ]);
    }

    public function educationalSupport()
    {
        $payments = EducationalSupportPayment::latest()
            ->paginate(15)
            ->through(function ($p) {
                return [
                    'id' => $p->id,
                    'amount' => (float) ($p->paid_amount ?? $p->amount ?? 0),
                    'status' => $p->status ?? 'N/A',
                    'date' => optional($p->created_at)->format('Y-m-d'),
                ];
            });

        $subscriptions = EducationalSupportSubscription::latest()
            ->paginate(15)
            ->through(function ($s) {
                return [
                    'id' => $s->id,
                    'status' => $s->status ?? 'N/A',
                    'date' => optional($s->created_at)->format('Y-m-d'),
                ];
            });

        // Monthly series (last 12 months)
        $monthlyPayments = [];
        $monthlySubscriptions = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyPayments[] = [
                'month' => $date->format('M'),
                'amount' => (float) EducationalSupportPayment::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('paid_amount'),
            ];
            $monthlySubscriptions[] = [
                'month' => $date->format('M'),
                'count' => (int) EducationalSupportSubscription::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        $summary = [
            'paymentsTotal' => (float) EducationalSupportPayment::sum('paid_amount'),
            'subscriptionsCount' => (int) EducationalSupportSubscription::count(),
        ];

        return Inertia::render('Admin/Reports/EducationalSupport', [
            'payments' => $payments,
            'subscriptions' => $subscriptions,
            'summary' => $summary,
            'monthlyPayments' => $monthlyPayments,
            'monthlySubscriptions' => $monthlySubscriptions,
        ]);
    }

    public function formations()
    {
        $payments = FormationPayment::latest()
            ->paginate(15)
            ->through(function ($p) {
                return [
                    'id' => $p->id,
                    'amount' => (float) ($p->amount ?? 0),
                    'status' => $p->status ?? 'N/A',
                    'date' => optional($p->created_at)->format('Y-m-d'),
                ];
            });

        $subscriptions = FormationSubscription::latest()
            ->paginate(15)
            ->through(function ($s) {
                return [
                    'id' => $s->id,
                    'status' => $s->status ?? 'N/A',
                    'date' => optional($s->created_at)->format('Y-m-d'),
                ];
            });

        $monthlyPayments = [];
        $monthlySubscriptions = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyPayments[] = [
                'month' => $date->format('M'),
                'amount' => (float) FormationPayment::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount'),
            ];
            $monthlySubscriptions[] = [
                'month' => $date->format('M'),
                'count' => (int) FormationSubscription::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        $summary = [
            'paymentsTotal' => (float) FormationPayment::sum('amount'),
            'subscriptionsCount' => (int) FormationSubscription::count(),
        ];

        return Inertia::render('Admin/Reports/Formations', [
            'payments' => $payments,
            'subscriptions' => $subscriptions,
            'summary' => $summary,
            'monthlyPayments' => $monthlyPayments,
            'monthlySubscriptions' => $monthlySubscriptions,
        ]);
    }

    public function childhood()
    {
        $payments = ChildhoodPayment::latest()
            ->paginate(15)
            ->through(function ($p) {
                return [
                    'id' => $p->id,
                    'amount' => (float) ($p->amount ?? 0),
                    'status' => $p->status ?? 'N/A',
                    'date' => optional($p->created_at)->format('Y-m-d'),
                ];
            });

        $subscriptions = ChildhoodSubscription::latest()
            ->paginate(15)
            ->through(function ($s) {
                return [
                    'id' => $s->id,
                    'status' => $s->status ?? 'N/A',
                    'date' => optional($s->created_at)->format('Y-m-d'),
                ];
            });

        $monthlyPayments = [];
        $monthlySubscriptions = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyPayments[] = [
                'month' => $date->format('M'),
                'amount' => (float) ChildhoodPayment::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount'),
            ];
            $monthlySubscriptions[] = [
                'month' => $date->format('M'),
                'count' => (int) ChildhoodSubscription::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        $summary = [
            'paymentsTotal' => (float) ChildhoodPayment::sum('amount'),
            'subscriptionsCount' => (int) ChildhoodSubscription::count(),
        ];

        return Inertia::render('Admin/Reports/Childhood', [
            'payments' => $payments,
            'subscriptions' => $subscriptions,
            'summary' => $summary,
            'monthlyPayments' => $monthlyPayments,
            'monthlySubscriptions' => $monthlySubscriptions,
        ]);
    }

    public function teachers()
    {
        $recent = Teacher::latest()
            ->paginate(15)
            ->through(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->name ?? 'Unknown',
                    'created' => optional($t->created_at)->format('Y-m-d'),
                ];
            });

        $monthlyCounts = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyCounts[] = [
                'month' => $date->format('M'),
                'count' => (int) Teacher::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return Inertia::render('Admin/Reports/Teachers', [
            'summary' => [ 'total' => (int) Teacher::count() ],
            'recent' => $recent,
            'monthlyCounts' => $monthlyCounts,
        ]);
    }

    public function students()
    {
        $recent = Student::latest()
            ->paginate(15)
            ->through(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name ?? 'Unknown',
                    'created' => optional($s->created_at)->format('Y-m-d'),
                ];
            });

        $monthlyCounts = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyCounts[] = [
                'month' => $date->format('M'),
                'count' => (int) Student::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return Inertia::render('Admin/Reports/Students', [
            'summary' => [ 'total' => (int) Student::count() ],
            'recent' => $recent,
            'monthlyCounts' => $monthlyCounts,
        ]);
    }
}


