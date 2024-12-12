<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $today = Carbon::today();
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();

        $expenses = Expense::where('user_id', auth()->id())
            ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select(
                'expense_date',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('expense_date')
            ->orderBy('expense_date', 'desc')
            ->get();

        $totalAmount = $expenses->sum('total_amount');

        return view('reports.index', compact('expenses', 'totalAmount', 'startDate', 'endDate', 'today'));
    }

    public function getDayDetails(Request $request)
    {
        $date = Carbon::parse($request->date)->format('Y-m-d');
        
        $expenses = Expense::where('user_id', auth()->id())
            ->whereDate('expense_date', $date)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'expenses' => $expenses,
            'date' => Carbon::parse($date)->format('d M, Y')
        ]);
    }
}
