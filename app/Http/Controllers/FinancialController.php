<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the financial dashboard.
     */
    public function index(Request $request)
    {
        // Get current month and year
        $currentMonth = $request->get('month', now()->month);
        $currentYear = $request->get('year', now()->year);

        // Income Statistics
        $totalPatientBills = Bill::whereYear('billed_at', $currentYear)
            ->whereMonth('billed_at', $currentMonth)
            ->sum('amount');

        $paidPatientBills = Bill::whereYear('billed_at', $currentYear)
            ->whereMonth('billed_at', $currentMonth)
            ->where('status', 'paid')
            ->sum('paid');

        $totalPharmacySales = Sale::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('total_price');

        $totalIncome = $paidPatientBills + $totalPharmacySales;

        // Expense Statistics
        $totalExpenses = Expense::whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', $currentMonth)
            ->where('status', 'paid')
            ->sum('amount');

        $pendingExpenses = Expense::whereYear('due_at', $currentYear)
            ->whereMonth('due_at', $currentMonth)
            ->where('status', 'pending')
            ->sum('amount');

        // Category-wise expenses
        $expensesByCategory = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', $currentMonth)
            ->where('status', 'paid')
            ->groupBy('category')
            ->get();

        // Recent transactions
        $recentBills = Bill::with('patient')
            ->whereYear('billed_at', $currentYear)
            ->whereMonth('billed_at', $currentMonth)
            ->orderBy('billed_at', 'desc')
            ->limit(5)
            ->get();

        $recentExpenses = Expense::whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', $currentMonth)
            ->orderBy('paid_at', 'desc')
            ->limit(5)
            ->get();

        $recentSales = Sale::with(['pharmacy', 'patient'])
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly trend data for charts (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'income' => Bill::whereYear('billed_at', $date->year)
                    ->whereMonth('billed_at', $date->month)
                    ->where('status', 'paid')
                    ->sum('paid') +
                    Sale::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_amount'),
                'expenses' => Expense::whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->where('status', 'paid')
                    ->sum('amount')
            ];
        }

        return view('financial.index', compact(
            'totalIncome',
            'totalExpenses',
            'pendingExpenses',
            'totalPatientBills',
            'paidPatientBills',
            'totalPharmacySales',
            'expensesByCategory',
            'recentBills',
            'recentExpenses',
            'recentSales',
            'monthlyData',
            'currentMonth',
            'currentYear'
        ));
    }

    /**
     * Show income management page.
     */
    public function income(Request $request)
    {
        $query = Bill::with(['patient', 'visit']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('billed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('billed_at', '<=', $request->date_to);
        }

        $bills = $query->orderBy('billed_at', 'desc')->paginate(15);

        return view('financial.income', compact('bills'));
    }

    /**
     * Show expenditure management page.
     */
    public function expenditure(Request $request)
    {
        $query = Expense::with('branch');

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('paid_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('paid_at', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('paid_at', 'desc')->paginate(15);

        return view('financial.expenditure', compact('expenses'));
    }
}
