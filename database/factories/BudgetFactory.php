<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Budget;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 month');
        return [
            'user_id' => User::factory(),
            'category_id' => ExpenseCategory::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'period_type' => $this->faker->randomElement(['monthly', 'yearly']),
            'start_date' => $startDate,
            'end_date' => $this->faker->dateTimeBetween($startDate, '+1 year'),
            'notes' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
