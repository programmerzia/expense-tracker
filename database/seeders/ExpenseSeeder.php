<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $categories = ExpenseCategory::all();
        
        // Create expenses for the last 6 months
        for ($i = 0; $i < 180; $i++) {
            Expense::create([
                'user_id' => $user->id,
                'category_id' => $categories->random()->id,
                'amount' => fake()->randomFloat(2, 10, 500),
                'expense_date' => fake()->dateTimeBetween('-6 months', 'now'),
                'description' => fake()->sentence(),
                'notes' => fake()->paragraph()
            ]);
        }
    }
}
