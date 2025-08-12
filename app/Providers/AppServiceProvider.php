<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

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
        ]);
    }
}