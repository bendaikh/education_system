<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Rent',
                'name_fr' => 'Loyer',
                'description' => 'Monthly rent payments for school premises',
                'color' => '#3B82F6',
                'is_active' => true,
            ],
            [
                'name' => 'Salary',
                'name_fr' => 'Salaire',
                'description' => 'Staff and teacher salaries',
                'color' => '#EF4444',
                'is_active' => true,
            ],
            [
                'name' => 'Electricity',
                'name_fr' => 'Électricité',
                'description' => 'Electricity bills',
                'color' => '#F59E0B',
                'is_active' => true,
            ],
            [
                'name' => 'Water',
                'name_fr' => 'Eau',
                'description' => 'Water bills',
                'color' => '#14B8A6',
                'is_active' => true,
            ],
            [
                'name' => 'Internet',
                'name_fr' => 'Internet',
                'description' => 'Internet and telecommunications',
                'color' => '#8B5CF6',
                'is_active' => true,
            ],
            [
                'name' => 'Supplies',
                'name_fr' => 'Fournitures',
                'description' => 'School supplies and materials',
                'color' => '#10B981',
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance',
                'name_fr' => 'Maintenance',
                'description' => 'Building and equipment maintenance',
                'color' => '#F97316',
                'is_active' => true,
            ],
            [
                'name' => 'Other',
                'name_fr' => 'Autre',
                'description' => 'Miscellaneous expenses',
                'color' => '#6366F1',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
