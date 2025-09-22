<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\EducationalSupportPayment;
use App\Models\FormationPayment;
use App\Models\ChildhoodPayment;
use App\Models\EducationalSubject;
use App\Models\Formation;
use App\Models\ChildhoodSubject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Setting;

class TeacherPaymentsController extends Controller
{
    /**
     * Display the teacher payments page.
     */
    public function index()
    {
        $teachers = Teacher::with(['educationalSubjects', 'childhoodSubjects'])
            ->orderBy('name')
            ->get();

        // Debug: Get payment counts
        $paymentCounts = [
            'educational_payments' => EducationalSupportPayment::count(),
            'formation_payments' => FormationPayment::count(),
            'childhood_payments' => ChildhoodPayment::count(),
            // Use case-insensitive comparisons to handle mixed-case statuses in DB
            'paid_educational_payments' => EducationalSupportPayment::whereRaw('LOWER(status) = ?', ['paid'])->count(),
            'paid_formation_payments' => FormationPayment::whereRaw('LOWER(status) = ?', ['paid'])->count(),
            'paid_childhood_payments' => ChildhoodPayment::whereRaw('LOWER(status) = ?', ['paid'])->count(),
        ];

        return Inertia::render('Admin/Teachers/Payments', [
            'teachers' => $teachers,
            'paymentCounts' => $paymentCounts,
            'currency' => Setting::get('currency', 'USD ($)'),
        ]);
    }

    /**
     * Generate monthly invoices for teachers and school.
     */
    public function generateMonthlyInvoices(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $month = Carbon::createFromFormat('Y-m', $request->month);
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        // Use payment_date and pre-filter to paid (case-insensitive) for processing
        $educationalPayments = EducationalSupportPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->whereRaw('LOWER(status) = ?', ['paid'])
            ->with(['subscription.subject', 'subscription.student'])
            ->get();

        $formationPayments = FormationPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->whereRaw('LOWER(status) = ?', ['paid'])
            ->with(['subscription.formation', 'subscription.student'])
            ->get();

        $childhoodPayments = ChildhoodPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->whereRaw('LOWER(status) = ?', ['paid'])
            ->with(['subscription.childhoodSubject', 'subscription.student'])
            ->get();

        // Debug: Log the counts
        \Log::info('Payment counts for month ' . $month->format('Y-m'), [
            'educational_payments' => $educationalPayments->count(),
            'formation_payments' => $formationPayments->count(),
            'childhood_payments' => $childhoodPayments->count(),
            'start_of_month' => $startOfMonth->format('Y-m-d H:i:s'),
            'end_of_month' => $endOfMonth->format('Y-m-d H:i:s')
        ]);

        // Debug: Check what months actually have payments
        $educationalMonths = EducationalSupportPayment::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        \Log::info('Educational payments by month', $educationalMonths->toArray());

        // Debug: Check total payments regardless of status
        $totalEducationalPayments = EducationalSupportPayment::count();
        $totalFormationPayments = FormationPayment::count();
        $totalChildhoodPayments = ChildhoodPayment::count();
        
        \Log::info('Total payments in database', [
            'total_educational_payments' => $totalEducationalPayments,
            'total_formation_payments' => $totalFormationPayments,
            'total_childhood_payments' => $totalChildhoodPayments
        ]);

        // Calculate teacher and school payments
        $teacherInvoices = [];
        $schoolInvoices = [];

        // Process Educational Support payments
        foreach ($educationalPayments as $payment) {
            
            $subject = $payment->subscription->subject;
            $teacherAmount = $payment->paid_amount * ($subject->teacher_percent / 100);
            $schoolAmount = $payment->paid_amount * ($subject->school_percent / 100);

            // Get teachers for this subject
            $teachers = $subject->teachers;
            $teacherCount = $teachers->count();
            $amountPerTeacher = $teacherCount > 0 ? $teacherAmount / $teacherCount : 0;

            foreach ($teachers as $teacher) {
                if (!isset($teacherInvoices[$teacher->id])) {
                    $teacherInvoices[$teacher->id] = [
                        'teacher' => $teacher,
                        'total_amount' => 0,
                        'subjects' => []
                    ];
                }

                $teacherInvoices[$teacher->id]['total_amount'] += $amountPerTeacher;
                $teacherInvoices[$teacher->id]['subjects'][] = [
                    'subject_name' => $subject->name,
                    'student_name' => $payment->subscription->student->name,
                    'amount' => $amountPerTeacher,
                    'payment_date' => $payment->created_at->format('Y-m-d')
                ];
            }

            // School invoice
            if (!isset($schoolInvoices['educational_support'])) {
                $schoolInvoices['educational_support'] = [
                    'key' => 'educational_support',
                    'category' => 'Educational Support',
                    'total_amount' => 0,
                    'subjects' => []
                ];
            }

            $schoolInvoices['educational_support']['total_amount'] += $schoolAmount;
            $schoolInvoices['educational_support']['subjects'][] = [
                'subject_name' => $subject->name,
                'student_name' => $payment->subscription->student->name,
                'amount' => $schoolAmount,
                'payment_date' => $payment->created_at->format('Y-m-d')
            ];
        }

        // Process Formation payments
        foreach ($formationPayments as $payment) {
            
            $formation = $payment->subscription->formation;
            $teacherAmount = $payment->paid_amount * ($formation->teacher_percent / 100);
            $schoolAmount = $payment->paid_amount * ($formation->school_percent / 100);

            // Get teachers for this formation (stored as JSON array)
            $teacherIds = $formation->teachers ?? [];
            $teachers = Teacher::whereIn('id', $teacherIds)->get();
            $teacherCount = $teachers->count();
            $amountPerTeacher = $teacherCount > 0 ? $teacherAmount / $teacherCount : 0;

            foreach ($teachers as $teacher) {
                if (!isset($teacherInvoices[$teacher->id])) {
                    $teacherInvoices[$teacher->id] = [
                        'teacher' => $teacher,
                        'total_amount' => 0,
                        'subjects' => []
                    ];
                }

                $teacherInvoices[$teacher->id]['total_amount'] += $amountPerTeacher;
                $teacherInvoices[$teacher->id]['subjects'][] = [
                    'subject_name' => $formation->title,
                    'student_name' => $payment->subscription->student->name,
                    'amount' => $amountPerTeacher,
                    'payment_date' => $payment->created_at->format('Y-m-d')
                ];
            }

            // School invoice
            if (!isset($schoolInvoices['formations'])) {
                $schoolInvoices['formations'] = [
                    'key' => 'formations',
                    'category' => 'Formations',
                    'total_amount' => 0,
                    'subjects' => []
                ];
            }

            $schoolInvoices['formations']['total_amount'] += $schoolAmount;
            $schoolInvoices['formations']['subjects'][] = [
                'subject_name' => $formation->title,
                'student_name' => $payment->subscription->student->name,
                'amount' => $schoolAmount,
                'payment_date' => $payment->created_at->format('Y-m-d')
            ];
        }

        // Process Childhood Education payments
        foreach ($childhoodPayments as $payment) {
            
            $subject = $payment->subscription->childhoodSubject;
            $teacherAmount = $payment->paid_amount * ($subject->teacher_percent / 100);
            $schoolAmount = $payment->paid_amount * ($subject->school_percent / 100);

            // Get teachers for this subject
            $teachers = $subject->teachers;
            $teacherCount = $teachers->count();
            $amountPerTeacher = $teacherCount > 0 ? $teacherAmount / $teacherCount : 0;

            foreach ($teachers as $teacher) {
                if (!isset($teacherInvoices[$teacher->id])) {
                    $teacherInvoices[$teacher->id] = [
                        'teacher' => $teacher,
                        'total_amount' => 0,
                        'subjects' => []
                    ];
                }

                $teacherInvoices[$teacher->id]['total_amount'] += $amountPerTeacher;
                $teacherInvoices[$teacher->id]['subjects'][] = [
                    'subject_name' => $subject->name,
                    'student_name' => $payment->subscription->student->name,
                    'amount' => $amountPerTeacher,
                    'payment_date' => $payment->created_at->format('Y-m-d')
                ];
            }

            // School invoice
            if (!isset($schoolInvoices['childhood_education'])) {
                $schoolInvoices['childhood_education'] = [
                    'key' => 'childhood_education',
                    'category' => 'Childhood Education',
                    'total_amount' => 0,
                    'subjects' => []
                ];
            }

            $schoolInvoices['childhood_education']['total_amount'] += $schoolAmount;
            $schoolInvoices['childhood_education']['subjects'][] = [
                'subject_name' => $subject->name,
                'student_name' => $payment->subscription->student->name,
                'amount' => $schoolAmount,
                'payment_date' => $payment->created_at->format('Y-m-d')
            ];
        }

        // Compute debug counts based on payment_date window
        $educationalPaymentsAllInMonth = EducationalSupportPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->get();
        $formationPaymentsAllInMonth = FormationPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->get();
        $childhoodPaymentsAllInMonth = ChildhoodPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->get();

        return response()->json([
            'month' => $month->format('F Y'),
            'teacher_invoices' => array_values($teacherInvoices),
            'school_invoices' => array_values($schoolInvoices),
            'currency' => Setting::get('currency', 'USD ($)'),
            'debug' => [
                'educational_payments_count' => $educationalPaymentsAllInMonth->count(),
                'formation_payments_count' => $formationPaymentsAllInMonth->count(),
                'childhood_payments_count' => $childhoodPaymentsAllInMonth->count(),
                // Count paid using case-insensitive filter
                'paid_educational_payments' => $educationalPaymentsAllInMonth->filter(function ($p) { return strtolower((string) $p->status) === 'paid'; })->count(),
                'paid_formation_payments' => $formationPaymentsAllInMonth->filter(function ($p) { return strtolower((string) $p->status) === 'paid'; })->count(),
                'paid_childhood_payments' => $childhoodPaymentsAllInMonth->filter(function ($p) { return strtolower((string) $p->status) === 'paid'; })->count(),
            ]
        ]);
    }

    /**
     * Download a teacher invoice for a specific month.
     */
    public function downloadTeacherInvoice(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'teacher_id' => 'required|integer',
        ]);

        $month = Carbon::createFromFormat('Y-m', $request->month);
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        $teacher = Teacher::findOrFail($request->teacher_id);
        $currency = Setting::get('currency', 'USD ($)');

        // Build entries from paid payments within month
        $entries = [];

        $eduPayments = EducationalSupportPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->whereRaw('LOWER(status) = ?', ['paid'])
            ->with(['subscription.subject', 'subscription.student'])
            ->get();
        foreach ($eduPayments as $p) {
            $subject = $p->subscription->subject;
            if (!$subject || !$subject->teachers->contains('id', $teacher->id)) { continue; }
            $teacherAmount = $p->paid_amount * ($subject->teacher_percent / 100);
            $share = $subject->teachers->count() > 0 ? $teacherAmount / $subject->teachers->count() : 0;
            $entries[] = [
                'subject' => $subject->name,
                'student' => $p->subscription->student->name,
                'amount' => $share,
                'date' => optional($p->payment_date ?? $p->created_at)->format('Y-m-d'),
                'teacher_percent' => (float) $subject->teacher_percent,
            ];
        }

        $formationPayments = FormationPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->whereRaw('LOWER(status) = ?', ['paid'])
            ->with(['subscription.formation', 'subscription.student'])
            ->get();
        foreach ($formationPayments as $p) {
            $formation = $p->subscription->formation;
            $teacherIds = $formation->teachers ?? [];
            if (!in_array($teacher->id, $teacherIds, true)) { continue; }
            $teacherAmount = $p->paid_amount * ($formation->teacher_percent / 100);
            $share = count($teacherIds) > 0 ? $teacherAmount / count($teacherIds) : 0;
            $entries[] = [
                'subject' => $formation->title,
                'student' => $p->subscription->student->name,
                'amount' => $share,
                'date' => optional($p->payment_date ?? $p->created_at)->format('Y-m-d'),
                'teacher_percent' => (float) $formation->teacher_percent,
            ];
        }

        $childPayments = ChildhoodPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->whereRaw('LOWER(status) = ?', ['paid'])
            ->with(['subscription.childhoodSubject', 'subscription.student'])
            ->get();
        foreach ($childPayments as $p) {
            $subject = $p->subscription->childhoodSubject;
            if (!$subject || !$subject->teachers->contains('id', $teacher->id)) { continue; }
            $teacherAmount = $p->paid_amount * ($subject->teacher_percent / 100);
            $share = $subject->teachers->count() > 0 ? $teacherAmount / $subject->teachers->count() : 0;
            $entries[] = [
                'subject' => $subject->name,
                'student' => $p->subscription->student->name,
                'amount' => $share,
                'date' => optional($p->payment_date ?? $p->created_at)->format('Y-m-d'),
                'teacher_percent' => (float) $subject->teacher_percent,
            ];
        }

        $total = collect($entries)->sum('amount');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.teacher', [
            'teacher' => $teacher,
            'entries' => $entries,
            'total' => $total,
            'currency' => $currency,
            'month' => $month->format('F Y'),
        ])->setPaper('a4');

        return $pdf->download('teacher-invoice-'.$teacher->id.'-'.$month->format('Y-m').'.pdf');
    }

    /**
     * Download a school invoice by category for a specific month.
     */
    public function downloadSchoolInvoice(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'category' => 'required|string|in:educational_support,formations,childhood_education',
        ]);

        $month = Carbon::createFromFormat('Y-m', $request->month);
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        $currency = Setting::get('currency', 'USD ($)');

        $entries = [];
        $categoryLabel = '';

        if ($request->category === 'educational_support') {
            $categoryLabel = 'Educational Support';
            $payments = EducationalSupportPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->whereRaw('LOWER(status) = ?', ['paid'])
                ->with(['subscription.subject', 'subscription.student'])
                ->get();
            foreach ($payments as $p) {
                $subject = $p->subscription->subject;
                $amount = $p->paid_amount * ($subject->school_percent / 100);
                $entries[] = [
                    'subject' => $subject->name,
                    'student' => $p->subscription->student->name,
                    'amount' => $amount,
                    'date' => optional($p->payment_date ?? $p->created_at)->format('Y-m-d'),
                    'school_percent' => (float) $subject->school_percent,
                ];
            }
        } elseif ($request->category === 'formations') {
            $categoryLabel = 'Formations';
            $payments = FormationPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->whereRaw('LOWER(status) = ?', ['paid'])
                ->with(['subscription.formation', 'subscription.student'])
                ->get();
            foreach ($payments as $p) {
                $formation = $p->subscription->formation;
                $amount = $p->paid_amount * ($formation->school_percent / 100);
                $entries[] = [
                    'subject' => $formation->title,
                    'student' => $p->subscription->student->name,
                    'amount' => $amount,
                    'date' => optional($p->payment_date ?? $p->created_at)->format('Y-m-d'),
                    'school_percent' => (float) $formation->school_percent,
                ];
            }
        } else {
            $categoryLabel = 'Childhood Education';
            $payments = ChildhoodPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->whereRaw('LOWER(status) = ?', ['paid'])
                ->with(['subscription.childhoodSubject', 'subscription.student'])
                ->get();
            foreach ($payments as $p) {
                $subject = $p->subscription->childhoodSubject;
                $amount = $p->paid_amount * ($subject->school_percent / 100);
                $entries[] = [
                    'subject' => $subject->name,
                    'student' => $p->subscription->student->name,
                    'amount' => $amount,
                    'date' => optional($p->payment_date ?? $p->created_at)->format('Y-m-d'),
                    'school_percent' => (float) $subject->school_percent,
                ];
            }
        }

        $total = collect($entries)->sum('amount');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.school', [
            'category' => $categoryLabel,
            'entries' => $entries,
            'total' => $total,
            'currency' => $currency,
            'month' => $month->format('F Y'),
        ])->setPaper('a4');

        return $pdf->download('school-invoice-'.$request->category.'-'.$month->format('Y-m').'.pdf');
    }
}