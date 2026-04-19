<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::with('branch');

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('date_from')) {
            $query->where('paid_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('paid_at', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('paid_at', 'desc')->paginate(15);
        $branches = Branch::all();

        return view('expenses.index', compact('expenses', 'branches'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $branches = Branch::all();
        return view('expenses.create', compact('branches'));
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category' => 'required|in:utilities,rent_lease,equipment,supplies,salaries,insurance,marketing,professional_fees,taxes,loans,maintenance,other',
            'subcategory' => 'nullable|string|max:255',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'vendor' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:cash,bank_transfer,check,credit_card,debit_card',
            'paid_at' => 'nullable|date',
            'due_at' => 'nullable|date|after_or_equal:today',
            'status' => 'required|in:pending,paid,overdue,cancelled',
            'recurring' => 'boolean',
            'frequency' => 'nullable|required_if:recurring,true|in:daily,weekly,monthly,quarterly,yearly',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->all();

        // Handle file upload
        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the expense.
     */
    public function edit(Expense $expense)
    {
        $branches = Branch::all();
        return view('expenses.edit', compact('expense', 'branches'));
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category' => 'required|in:utilities,rent_lease,equipment,supplies,salaries,insurance,marketing,professional_fees,taxes,loans,maintenance,other',
            'subcategory' => 'nullable|string|max:255',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'vendor' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:cash,bank_transfer,check,credit_card,debit_card',
            'paid_at' => 'nullable|date',
            'due_at' => 'nullable|date',
            'status' => 'required|in:pending,paid,overdue,cancelled',
            'recurring' => 'boolean',
            'frequency' => 'nullable|required_if:recurring,true|in:daily,weekly,monthly,quarterly,yearly',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->all();

        // Handle file upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Expense $expense)
    {
        // Delete receipt file if exists
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
