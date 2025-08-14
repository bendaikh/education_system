<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionType;

class SubscriptionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionType::create([
            'name' => 'Basic Monthly',
            'description' => 'Perfect for small academies and startups',
            'price' => 49.00,
            'duration' => 'monthly',
            'max_students' => 200,
            'features' => ['Basic Reports', 'Email Support', 'Student Management', 'Course Tracking'],
            'is_active' => true,
        ]);

        SubscriptionType::create([
            'name' => 'Premium Monthly',
            'description' => 'Advanced features for growing institutions',
            'price' => 99.00,
            'duration' => 'monthly',
            'max_students' => 1000,
            'features' => ['Advanced Analytics', 'Priority Support', 'Custom Reports', 'API Access', 'White-label Options'],
            'is_active' => true,
        ]);

        SubscriptionType::create([
            'name' => 'Enterprise Yearly',
            'description' => 'Complete solution for large organizations',
            'price' => 2999.00,
            'duration' => 'yearly',
            'max_students' => null, // unlimited
            'features' => ['Everything in Premium', 'White-label Solution', 'Dedicated Support', 'Custom Integrations', 'Multi-campus Management'],
            'is_active' => true,
        ]);

        SubscriptionType::create([
            'name' => 'Starter Plan',
            'description' => 'Free trial plan for new users',
            'price' => 0.00,
            'duration' => 'monthly',
            'max_students' => 50,
            'features' => ['Limited Features', 'Community Support', 'Basic Student Management'],
            'is_active' => false,
        ]);
    }
}
