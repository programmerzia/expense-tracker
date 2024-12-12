<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ExpenseCategorySeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\ExpenseSeeder;
use Database\Seeders\BudgetSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ExpenseCategorySeeder::class,
            ExpenseSeeder::class,
            BudgetSeeder::class,
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
