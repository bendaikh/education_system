<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share the current locale with all Inertia responses
        Inertia::share([
            'locale' => function () {
                $locale = Session::get('locale', config('app.locale'));
                app()->setLocale($locale);
                return $locale;
            },
            'language' => function () {
                $locale = Session::get('locale', config('app.locale'));
                app()->setLocale($locale);
                return trans('messages');
            },
            'settings' => function () {
                return [
                    'app_name' => Setting::get('app_name', config('app.name', 'Admin Panel')),
                    'country' => Setting::get('country', 'United States'),
                    'currency' => Setting::get('currency', 'USD ($)'),
                    'email_notifications' => Setting::get('email_notifications', 'true') === 'true',
                    'push_notifications' => Setting::get('push_notifications', 'false') === 'true',
                    'monthly_reports' => Setting::get('monthly_reports', 'true') === 'true',
                ];
            },
        ]);
    }
}