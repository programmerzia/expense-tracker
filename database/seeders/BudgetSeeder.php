<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\User;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $categories = ExpenseCategory::all();
        
        // Create monthly budgets for some categories
        $selectedCategories = $categories->random(5);
        foreach ($selectedCategories as $category) {
            Budget::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'amount' => fake()->randomFloat(2, 500, 2000),
                'period_type' => 'monthly',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'notes' => 'Monthly budget for ' . $category->name,
                'is_active' => true
            ]);
        }

        // Create one yearly overall budget
        Budget::create([
            'user_id' => $user->id,
            'amount' => 24000.00,
            'period_type' => 'yearly',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'notes' => 'Yearly overall budget',
            'is_active' => true
        ]);
    }
}
