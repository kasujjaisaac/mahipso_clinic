<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\PrescriptionOrder;
use App\Models\ServiceItem;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessAnyModule(['front_office', 'finance']), 403);

            return $next($request);
        });
    }

    public function index()
    {
        $bills = Bill::with(['patient', 'visit'])
            ->visibleTo(auth()->user())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('billing.index', compact('bills'));
    }

    public function create()
    {
        $patients = Patient::visibleTo(auth()->user())->orderBy('last_name')->get();
        $services = ServiceItem::visibleTo(auth()->user())->where('is_active', true)->orderBy('category')->orderBy('name')->get();
        return view('billing.create', compact('patients', 'services'));
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
            $patient = Patient::visibleTo($request->user())->findOrFail($request->patient_id);

            if ($request->filled('visit_id')) {
                Visit::visibleTo($request->user())
                    ->whereKey($request->visit_id)
                    ->where('patient_id', $patient->id)
                    ->firstOrFail();
            }

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
        $bill = Bill::visibleTo(auth()->user())->with(['patient', 'visit', 'items'])->findOrFail($id);
        return view('billing.show', compact('bill'));
    }

    public function edit($id)
    {
        $bill = Bill::visibleTo(auth()->user())->with(['patient', 'visit'])->findOrFail($id);
        $patients = Patient::visibleTo(auth()->user())->orderBy('last_name')->get();
        return view('billing.edit', compact('bill', 'patients'));
    }

    public function update(Request $request, $id)
    {
        $bill = Bill::visibleTo($request->user())->findOrFail($id);

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
            $patient = Patient::visibleTo($request->user())->findOrFail($request->patient_id);

            if ($request->filled('visit_id')) {
                Visit::visibleTo($request->user())
                    ->whereKey($request->visit_id)
                    ->where('patient_id', $patient->id)
                    ->firstOrFail();
            }

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
        $bill = Bill::visibleTo(auth()->user())->findOrFail($id);

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

    public function generateFromVisit(Request $request, Visit $visit)
    {
        abort_unless($request->user()->canAccessAnyModule(['front_office', 'finance']), 403);
        abort_unless($request->user()->isSuperAdmin() || $request->user()->branch_id === $visit->branch_id, 404);

        $validated = $request->validate([
            'consultation_fee' => 'nullable|numeric|min:0',
        ]);

        $items = collect();

        $consultationFee = (float) ($validated['consultation_fee'] ?? 0);
        if ($consultationFee > 0) {
            $items->push([
                'description' => 'Consultation fee',
                'source_type' => 'visit',
                'source_id' => $visit->id,
                'quantity' => 1,
                'unit_price' => $consultationFee,
                'total' => $consultationFee,
            ]);
        }

        LabTest::where('visit_id', $visit->id)->where('is_billable', true)->where('price', '>', 0)->get()
            ->each(fn ($lab) => $items->push([
                'description' => 'Lab: ' . $lab->test_type,
                'source_type' => 'lab_test',
                'source_id' => $lab->id,
                'quantity' => 1,
                'unit_price' => (float) $lab->price,
                'total' => (float) $lab->price,
            ]));

        PrescriptionOrder::with('items.product')->where('visit_id', $visit->id)->get()
            ->each(function ($order) use ($items) {
                foreach ($order->items as $item) {
                    $items->push([
                        'description' => 'Medicine: ' . ($item->product->name ?? 'Product'),
                        'source_type' => 'prescription_item',
                        'source_id' => $item->id,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'total' => (float) $item->total_price,
                    ]);
                }
            });

        if ($items->isEmpty()) {
            return back()->withErrors(['billing' => 'No billable services found for this visit.']);
        }

        $bill = DB::transaction(function () use ($visit, $items) {
            $bill = Bill::create([
                'patient_id' => $visit->patient_id,
                'visit_id' => $visit->id,
                'amount' => $items->sum('total'),
                'paid' => 0,
                'status' => 'unpaid',
                'billed_at' => now()->toDateString(),
                'notes' => 'Auto-generated from visit services.',
            ]);

            foreach ($items as $item) {
                $bill->items()->create($item);
            }

            $visit->moveToStage(Visit::STAGE_BILLING);

            return $bill;
        });

        return redirect()->route('billing.show', $bill)->with('success', 'Bill generated from visit services.');
    }
}
