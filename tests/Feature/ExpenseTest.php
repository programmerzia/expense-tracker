<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user
        $this->user = User::factory()->create();
        
        // Create a category
        $this->category = ExpenseCategory::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);
    }

    public function test_user_can_view_expense_create_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('expenses.create'));

        $response->assertStatus(200)
            ->assertViewIs('expenses.create')
            ->assertViewHas('categories');
    }

    public function test_user_can_create_expense()
    {
        $expenseData = [
            'category_id' => $this->category->id,
            'amount' => 100.50,
            'expense_date' => now()->format('Y-m-d'),
            'description' => 'Test Expense',
            'notes' => 'Test Notes'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('expenses.store'), $expenseData);

        $response->assertRedirect(route('expenses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 100.50,
            'description' => 'Test Expense'
        ]);
    }

    public function test_user_can_view_expense_list()
    {
        // Create some expenses
        Expense::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('expenses.index'));

        $response->assertStatus(200)
            ->assertViewIs('expenses.index')
            ->assertViewHas('expenses');
    }

    public function test_user_can_edit_expense()
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('expenses.edit', $expense));

        $response->assertStatus(200)
            ->assertViewIs('expenses.edit')
            ->assertViewHas('expense')
            ->assertViewHas('categories');
    }

    public function test_user_can_update_expense()
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $updatedData = [
            'category_id' => $this->category->id,
            'amount' => 200.75,
            'expense_date' => now()->format('Y-m-d'),
            'description' => 'Updated Expense',
            'notes' => 'Updated Notes'
        ];

        $response = $this->actingAs($this->user)
            ->put(route('expenses.update', $expense), $updatedData);

        $response->assertRedirect(route('expenses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 200.75,
            'description' => 'Updated Expense'
        ]);
    }

    public function test_user_can_delete_expense()
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('expenses.destroy', $expense));

        $response->assertRedirect(route('expenses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}
