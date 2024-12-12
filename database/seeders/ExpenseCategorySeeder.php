<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Food & Dining',
            'Transportation',
            'Housing',
            'Utilities',
            'Healthcare',
            'Entertainment',
            'Shopping',
            'Education',
            'Personal Care',
            'Insurance',
            'Savings & Investments',
            'Debt Payments',
            'Gifts & Donations',
            'Travel',
            'Miscellaneous'
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create([
                'name' => $category,
                'description' => 'Expenses related to ' . strtolower($category)
            ]);
        }
    }
}
