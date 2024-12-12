<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Get time-based expense summaries
        $expenseSummaries = [
            'today' => $this->getExpenseSum($user, $today, $today),
            'current_week' => $this->getExpenseSum($user, $today->copy()->startOfWeek(), $today->copy()->endOfWeek()),
            'current_month' => $this->getExpenseSum($user, $today->copy()->startOfMonth(), $today->copy()->endOfMonth()),
            'last_month' => $this->getExpenseSum($user, $today->copy()->subMonth()->startOfMonth(), $today->copy()->subMonth()->endOfMonth()),
            'last_3_months' => $this->getExpenseSum($user, $today->copy()->subMonths(3), $today),
            'last_6_months' => $this->getExpenseSum($user, $today->copy()->subMonths(6), $today),
            'last_year' => $this->getExpenseSum($user, $today->copy()->subYear(), $today),
        ];

        // Get monthly expenses for the chart
        $monthlyExpenses = $this->getMonthlyExpenses($user);

        // Get category-wise expenses for the current month
        $categoryExpenses = $user->expenses()
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->whereBetween('expense_date', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->select(
                'expense_categories.id',
                'expense_categories.name as category',
                DB::raw('SUM(expenses.amount) as total')
            )
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('total')
            ->get();

        // Get active budgets
        $budgets = $user->budgets()
            ->with('category')
            ->where('end_date', '>=', now())
            ->where('is_active', true)
            ->orderBy('start_date')
            ->get();

        return view('dashboard', compact(
            'expenseSummaries',
            'monthlyExpenses',
            'categoryExpenses',
            'budgets'
        ));
    }

    private function getExpenseSum($user, $startDate, $endDate)
    {
        return $user->expenses()
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
    }

    private function getMonthlyExpenses($user)
    {
        return $user->expenses()
            ->select(
                DB::raw('DATE_FORMAT(expense_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('expense_date', '>=', now()->subMonths(11))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                    'total' => $item->total
                ];
            });
    }
}
