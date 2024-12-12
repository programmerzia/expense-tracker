<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_expense_categories()
    {
        $user = User::factory()->create();
        $category = ExpenseCategory::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        $response = $this->actingAs($user)->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Category');
        $response->assertSee('Test Description');
    }

    public function test_user_can_create_expense_category()
    {
        $user = User::factory()->create();
        $categoryData = [
            'name' => 'New Category',
            'description' => 'New Description'
        ];

        $response = $this->actingAs($user)
            ->post(route('categories.store'), $categoryData);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('expense_categories', $categoryData);
    }

    public function test_user_can_update_expense_category()
    {
        $user = User::factory()->create();
        $category = ExpenseCategory::create([
            'name' => 'Old Name',
            'description' => 'Old Description'
        ]);

        $response = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'Updated Name',
            'description' => 'Updated Description'
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description'
        ]);
    }

    public function test_user_can_delete_expense_category()
    {
        $user = User::factory()->create();
        $category = ExpenseCategory::create([
            'name' => 'To Delete',
            'description' => 'Will be deleted'
        ]);

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('expense_categories', ['id' => $category->id]);
    }
}
