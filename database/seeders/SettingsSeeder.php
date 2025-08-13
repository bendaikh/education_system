<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default application settings
        $defaultSettings = [
            'app_name' => 'Academy Admin',
            'currency' => 'USD ($)',
            'country' => 'United States',
            'email_notifications' => 'true',
            'push_notifications' => 'false',
            'monthly_reports' => 'true',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
