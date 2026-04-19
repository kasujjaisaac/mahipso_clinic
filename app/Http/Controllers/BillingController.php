<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index()
    {
        $bills = Bill::with(['patient', 'visit'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('billing.index', compact('bills'));
    }

    public function create()
    {
        $patients = Patient::orderBy('full_name')->get();
        return view('billing.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'amount' => 'required|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'status' => 'required|in:unpaid,partial,paid,cancelled',
            'payment_method' => 'nullable|string',
            'billed_at' => 'required|date',
            'due_at' => 'nullable|date',
            'insurance_claim_no' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $bill = Bill::create([
                'patient_id' => $request->patient_id,
                'visit_id' => $request->visit_id,
                'amount' => $request->amount,
                'paid' => $request->paid ?? 0,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'billed_at' => $request->billed_at,
                'due_at' => $request->due_at,
                'insurance_claim_no' => $request->insurance_claim_no,
                'notes' => $request->notes,
            ]);

            DB::commit();
            return redirect()->route('billing.show', $bill)->with('success', 'Bill created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Failed to create bill. Please try again.']);
        }
    }

    public function show($id)
    {
        $bill = Bill::with(['patient', 'visit', 'payments'])->findOrFail($id);
        return view('billing.show', compact('bill'));
    }

    public function edit($id)
    {
        $bill = Bill::with(['patient', 'visit'])->findOrFail($id);
        $patients = Patient::orderBy('full_name')->get();
        return view('billing.edit', compact('bill', 'patients'));
    }

    public function update(Request $request, $id)
    {
        $bill = Bill::findOrFail($id);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'amount' => 'required|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'status' => 'required|in:unpaid,partial,paid,cancelled',
            'payment_method' => 'nullable|string',
            'billed_at' => 'required|date',
            'due_at' => 'nullable|date',
            'insurance_claim_no' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $bill->update([
                'patient_id' => $request->patient_id,
                'visit_id' => $request->visit_id,
                'amount' => $request->amount,
                'paid' => $request->paid ?? 0,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'billed_at' => $request->billed_at,
                'due_at' => $request->due_at,
                'insurance_claim_no' => $request->insurance_claim_no,
                'notes' => $request->notes,
            ]);

            DB::commit();
            return redirect()->route('billing.show', $bill)->with('success', 'Bill updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Failed to update bill. Please try again.']);
        }
    }

    public function destroy($id)
    {
        $bill = Bill::findOrFail($id);

        DB::beginTransaction();
        try {
            $bill->delete();
            DB::commit();
            return redirect()->route('billing.index')->with('success', 'Bill deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete bill. Please try again.']);
        }
    }
}
