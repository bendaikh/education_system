<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Formation;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionType;
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

        // Monthly payments data for the line chart (last 12 months)
        $monthlyPayments = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $monthlyTotal = Payment::whereYear('created_at', $date->year)
                                  ->whereMonth('created_at', $date->month)
                                  ->sum('amount');
            $monthlyPayments[] = [
                'month' => $monthName,
                'amount' => (float) $monthlyTotal
            ];
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

        // Recent activity (last 10 activities)
        $recentPayments = Payment::with('student')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'user' => $payment->student->name ?? 'Unknown Student',
                    'activity' => 'Payment of ' . $payment->formatted_amount,
                    'date' => $payment->created_at->format('Y-m-d'),
                    'status' => 'Completed'
                ];
            });

        $recentSubscriptions = Subscription::with(['student', 'subscriptionType'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($subscription) {
                return [
                    'id' => 'sub_' . $subscription->id,
                    'user' => $subscription->student->name ?? 'Unknown Student',
                    'activity' => 'Subscribed to ' . ($subscription->subscriptionType->name ?? 'Unknown Plan'),
                    'date' => $subscription->created_at->format('Y-m-d'),
                    'status' => ucfirst($subscription->auto_status)
                ];
            });

        // Merge and sort recent activities
        $recentActivity = $recentPayments->concat($recentSubscriptions)
                                       ->sortByDesc('date')
                                       ->take(8)
                                       ->values()
                                       ->toArray();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'totalRevenue' => $totalRevenue,
            'monthlyPayments' => $monthlyPayments,
            'subscriptionStats' => $subscriptionStats,
            'totalActiveSubscriptions' => $totalActiveSubscriptions,
            'recentActivity' => $recentActivity
        ]);
    }
}
