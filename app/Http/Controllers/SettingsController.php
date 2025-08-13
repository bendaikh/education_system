<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(): Response
    {
        // Load current settings from database or use defaults
        $settings = [
            'app_name' => Setting::get('app_name', config('app.name', 'Admin Panel')),
            'country' => Setting::get('country', 'United States'),
            'currency' => Setting::get('currency', 'USD ($)'),
            'email_notifications' => Setting::get('email_notifications', 'true') === 'true',
            'push_notifications' => Setting::get('push_notifications', 'false') === 'true',
            'monthly_reports' => Setting::get('monthly_reports', 'true') === 'true',
        ];

        return Inertia::render('Admin/Settings', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'currency' => 'required|string|max:255',
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'monthly_reports' => 'boolean',
            'logo' => 'nullable|image|max:2048', // 2MB max
        ]);

        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            Setting::set('logo', $logoPath);
        }

        // Save settings to database
        Setting::set('app_name', $request->app_name);
        Setting::set('country', $request->country);
        Setting::set('currency', $request->currency);
        Setting::set('email_notifications', $request->email_notifications ? 'true' : 'false');
        Setting::set('push_notifications', $request->push_notifications ? 'true' : 'false');
        Setting::set('monthly_reports', $request->monthly_reports ? 'true' : 'false');
        
        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
