<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessAnyModule(['front_office', 'finance']), 403);

            return $next($request);
        });
    }

    public function store(Request $request, Bill $bill)
    {
        $bill = Bill::visibleTo($request->user())->with('patient')->findOrFail($bill->id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,mobile_money,bank_transfer,card,insurance,other',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'paid_at' => 'nullable|date',
        ]);

        $payment = DB::transaction(function () use ($bill, $validated, $request) {
            $payment = PaymentTransaction::create([
                'branch_id' => $bill->patient->branch_id,
                'bill_id' => $bill->id,
                'patient_id' => $bill->patient_id,
                'received_by' => $request->user()->id,
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'paid_at' => $validated['paid_at'] ?? now(),
            ]);

            $paid = $bill->payments()->sum('amount') + (float) $validated['amount'];
            $bill->update([
                'paid' => $paid,
                'status' => $paid >= (float) $bill->amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                'payment_method' => $validated['method'],
            ]);

            return $payment;
        });

        return redirect()->route('payments.receipt', $payment)->with('success', 'Payment recorded.');
    }

    public function receipt(PaymentTransaction $payment)
    {
        $payment = PaymentTransaction::visibleTo(auth()->user())
            ->with(['bill.items', 'patient.branch', 'receivedBy'])
            ->findOrFail($payment->id);

        return view('payments.receipt', compact('payment'));
    }
}
