<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Budget;
use App\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
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

    public function test_user_can_view_budget_create_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('budgets.create'));

        $response->assertStatus(200)
            ->assertViewIs('budgets.create')
            ->assertViewHas('categories');
    }

    public function test_user_can_create_budget()
    {
        $budgetData = [
            'category_id' => $this->category->id,
            'amount' => 1000.00,
            'period_type' => 'monthly',
            'start_date' => now()->format('Y-m-d'),
            'notes' => 'Test Budget'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('budgets.store'), $budgetData);

        $response->assertRedirect(route('budgets.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('budgets', [
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 1000.00,
            'period_type' => 'monthly'
        ]);
    }

    public function test_user_can_view_budget_list()
    {
        Budget::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('budgets.index'));

        $response->assertStatus(200)
            ->assertViewIs('budgets.index')
            ->assertViewHas('budgets');
    }

    public function test_user_can_edit_budget()
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('budgets.edit', $budget));

        $response->assertStatus(200)
            ->assertViewIs('budgets.edit')
            ->assertViewHas('budget')
            ->assertViewHas('categories');
    }

    public function test_user_can_update_budget()
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $updatedData = [
            'category_id' => $this->category->id,
            'amount' => 2000.00,
            'period_type' => 'yearly',
            'notes' => 'Updated Budget Notes',
            'is_active' => true
        ];

        $response = $this->actingAs($this->user)
            ->put(route('budgets.update', $budget), $updatedData);

        $response->assertRedirect(route('budgets.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'amount' => 2000.00,
            'period_type' => 'yearly'
        ]);
    }

    public function test_user_can_delete_budget()
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('budgets.destroy', $budget));

        $response->assertRedirect(route('budgets.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
    }

    public function test_user_cannot_access_other_users_budgets()
    {
        $otherUser = User::factory()->create();
        $budget = Budget::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('budgets.edit', $budget));

        $response->assertStatus(403);
    }
}
