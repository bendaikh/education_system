<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\EducationalSubjectController;
use App\Http\Controllers\EducationalSupportController;
use App\Http\Controllers\EducationalSupportPaymentController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SettingsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Inertia\Inertia;

Route::get('/', HomeController::class);

// Language Switch Route
Route::post('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Override dashboard for superadmins
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && $user->role === 'superadmin') {
        return redirect()->route('admin.dashboard');
    }
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
   Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
       Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
   Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
   Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
   Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
   Route::get('/subscriptions/types', [SubscriptionController::class, 'typesIndex'])->name('subscriptions.types.index');
   Route::post('/subscriptions/types', [SubscriptionController::class, 'storeType'])->name('subscriptions.types.store');
   Route::get('/formations', [FormationController::class, 'index'])->name('formations.index');
   Route::post('/formations', [FormationController::class, 'store'])->name('formations.store');
   Route::get('/educational-subjects', [EducationalSubjectController::class, 'index'])->name('educational-subjects.index');
   Route::post('/educational-subjects', [EducationalSubjectController::class, 'store'])->name('educational-subjects.store');
   Route::put('/educational-subjects/{subject}', [EducationalSubjectController::class, 'update'])->name('educational-subjects.update');
   Route::delete('/educational-subjects/{subject}', [EducationalSubjectController::class, 'destroy'])->name('educational-subjects.destroy');
   Route::get('/educational-support', [EducationalSupportController::class, 'index'])->name('educational-support.index');
   Route::post('/educational-support', [EducationalSupportController::class, 'store'])->name('educational-support.store');
   Route::put('/educational-support/{subscription}', [EducationalSupportController::class, 'update'])->name('educational-support.update');
   Route::delete('/educational-support/{subscription}', [EducationalSupportController::class, 'destroy'])->name('educational-support.destroy');
   Route::inertia('/user-management', 'Admin/UserManagement')->name('users.index');
   Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
   Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Educational Support Payments Routes
    Route::get('/educational-support-payments', [EducationalSupportPaymentController::class, 'index'])->name('educational-support-payments.index');
    Route::patch('/educational-support-payments/{payment}/mark-as-paid', [EducationalSupportPaymentController::class, 'markAsPaid'])->name('educational-support-payments.mark-as-paid');
    Route::patch('/educational-support-payments/{payment}/process-payment', [EducationalSupportPaymentController::class, 'processPayment'])->name('educational-support-payments.process-payment');
    Route::patch('/educational-support-payments/{payment}/mark-as-cancelled', [EducationalSupportPaymentController::class, 'markAsCancelled'])->name('educational-support-payments.mark-as-cancelled');
    Route::patch('/educational-support-payments/{payment}/update-amount', [EducationalSupportPaymentController::class, 'updateAmount'])->name('educational-support-payments.update-amount');
});

require __DIR__.'/auth.php';
