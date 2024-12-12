<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = auth()->user()->budgets()
            ->with('category')
            ->where('end_date', '>=', now())
            ->where('is_active', true)
            ->orderBy('start_date')
            ->get();

        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'period_type' => 'required|in:monthly,yearly',
            'start_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string'
        ]);

        $start_date = Carbon::parse($validated['start_date']);
        $end_date = $validated['period_type'] === 'monthly' 
            ? $start_date->copy()->endOfMonth() 
            : $start_date->copy()->addYear()->subDay();

        $budget = new Budget([
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'period_type' => $validated['period_type'],
            'start_date' => $start_date,
            'end_date' => $end_date,
            'notes' => $validated['notes'],
            'is_active' => true
        ]);

        auth()->user()->budgets()->save($budget);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget created successfully.');
    }

    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $validated = $request->validate([
            'category_id' => 'nullable|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'period_type' => 'required|in:monthly,yearly',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if (isset($validated['period_type']) && $validated['period_type'] !== $budget->period_type) {
            $start_date = $budget->start_date;
            $end_date = $validated['period_type'] === 'monthly' 
                ? $start_date->copy()->endOfMonth() 
                : $start_date->copy()->addYear()->subDay();
            $validated['end_date'] = $end_date;
        }

        $budget->update($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        
        $budget->delete();

        return redirect()->route('budgets.index')
            ->with('success', 'Budget deleted successfully.');
    }
}
