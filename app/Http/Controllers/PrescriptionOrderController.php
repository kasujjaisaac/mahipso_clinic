<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Models\PrescriptionOrder;
use App\Models\DrugInteraction;
use App\Models\Product;
use App\Models\ProductAuditLog;
use App\Models\Sale;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessAnyModule(['clinic', 'nursing', 'pharmacy']), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $orders = PrescriptionOrder::visibleTo($request->user())
            ->with(['patient', 'visit', 'provider', 'items.product'])
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest('ordered_at')
            ->paginate(20);

        return view('prescriptions.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $visit = Visit::visibleTo($request->user())->with('patient')->findOrFail($request->query('visit_id'));
        $pharmacy = Pharmacy::where('branch_id', $visit->branch_id)->firstOrFail();
        $products = $pharmacy->products()->active()->where('quantity', '>', 0)->orderBy('name')->get();

        return view('prescriptions.create', compact('visit', 'products'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->canAccessAnyModule(['clinic', 'nursing']), 403);

        $request->merge([
            'items' => collect($request->input('items', []))
                ->filter(fn ($item) => ! empty($item['product_id']))
                ->values()
                ->all(),
        ]);

        $validated = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'notes' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.dosage' => 'nullable|string|max:255',
            'items.*.frequency' => 'nullable|string|max:255',
            'items.*.duration' => 'nullable|string|max:255',
            'items.*.instructions' => 'nullable|string|max:1000',
        ]);

        $visit = Visit::visibleTo($request->user())->with('patient')->findOrFail($validated['visit_id']);
        $pharmacy = Pharmacy::where('branch_id', $visit->branch_id)->firstOrFail();
        $products = $pharmacy->products()->whereIn('id', collect($validated['items'])->pluck('product_id'))->get()->keyBy('id');

        $allergyWarnings = $visit->patient->allergies()
            ->get()
            ->flatMap(function ($allergy) use ($products) {
                return $products->filter(fn ($product) => str_contains(strtolower($product->name), strtolower($allergy->substance)))
                    ->map(fn ($product) => "{$product->name} matches allergy {$allergy->substance}");
            });

        if ($allergyWarnings->isNotEmpty()) {
            return back()->withInput()->withErrors(['allergy' => $allergyWarnings->implode('; ')]);
        }

        $productNames = $products->pluck('name')->values();
        $interaction = DrugInteraction::where('is_active', true)
            ->where(function ($query) use ($productNames) {
                foreach ($productNames as $a) {
                    foreach ($productNames as $b) {
                        if ($a === $b) {
                            continue;
                        }
                        $query->orWhere(fn ($q) => $q
                            ->where('drug_a', 'like', "%{$a}%")
                            ->where('drug_b', 'like', "%{$b}%"));
                    }
                }
            })
            ->first();

        if ($interaction) {
            return back()->withInput()->withErrors(['interaction' => "Drug interaction warning: {$interaction->drug_a} + {$interaction->drug_b}. {$interaction->warning}"]);
        }

        $order = DB::transaction(function () use ($validated, $visit, $pharmacy, $request) {
            $order = PrescriptionOrder::create([
                'branch_id' => $visit->branch_id,
                'visit_id' => $visit->id,
                'patient_id' => $visit->patient_id,
                'provider_id' => $request->user()->id,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'ordered_at' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                $product = $pharmacy->products()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'dosage' => $item['dosage'] ?? null,
                    'frequency' => $item['frequency'] ?? null,
                    'duration' => $item['duration'] ?? null,
                    'instructions' => $item['instructions'] ?? null,
                    'unit_price' => $product->price,
                    'total_price' => $product->price * $quantity,
                ]);
            }

            $visit->moveToStage(Visit::STAGE_PHARMACY);

            return $order;
        });

        return redirect()->route('prescriptions.show', $order)->with('success', 'Prescription sent to pharmacy.');
    }

    public function show(PrescriptionOrder $prescription)
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->branch_id === $prescription->branch_id, 404);

        $prescription->load(['patient', 'visit', 'provider', 'dispensedBy', 'items.product']);

        return view('prescriptions.show', ['order' => $prescription]);
    }

    public function dispense(Request $request, PrescriptionOrder $prescription)
    {
        abort_unless($request->user()->canAccessModule('pharmacy'), 403);
        abort_unless($request->user()->isSuperAdmin() || $request->user()->branch_id === $prescription->branch_id, 404);

        if (! in_array($prescription->status, ['pending', 'partially_dispensed'], true)) {
            return back()->withErrors(['prescription' => 'Only pending prescriptions can be dispensed.']);
        }

        DB::transaction(function () use ($prescription, $request) {
            $pharmacy = Pharmacy::where('branch_id', $prescription->branch_id)->firstOrFail();

            foreach ($prescription->items()->with('product')->get() as $item) {
                $remaining = $item->quantity - $item->dispensed_quantity;
                if ($remaining <= 0) {
                    continue;
                }

                $product = $pharmacy->products()->whereKey($item->product_id)->lockForUpdate()->firstOrFail();
                if ($product->quantity < $remaining) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'stock' => "{$product->name} has insufficient stock. Available: {$product->quantity}",
                    ]);
                }

                $sale = Sale::create([
                    'pharmacy_id' => $pharmacy->id,
                    'product_id' => $product->id,
                    'patient_id' => $prescription->patient_id,
                    'visit_id' => $prescription->visit_id,
                    'provider_id' => $prescription->provider_id,
                    'quantity' => $remaining,
                    'total_price' => $product->price * $remaining,
                    'sold_by' => $request->user()->id,
                    'sale_date' => now(),
                    'status' => 'completed',
                    'prescription_note' => $item->instructions,
                ]);

                $oldQuantity = $product->quantity;
                $product->decrement('quantity', $remaining);
                $item->update(['dispensed_quantity' => $item->quantity]);

                ProductAuditLog::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id,
                    'action' => 'quantity_adjusted',
                    'old_values' => ['quantity' => $oldQuantity],
                    'new_values' => ['quantity' => $product->fresh()->quantity],
                    'reason' => "Prescription #{$prescription->id}, sale #{$sale->id}",
                ]);
            }

            $prescription->update([
                'status' => 'dispensed',
                'dispensed_by' => $request->user()->id,
                'dispensed_at' => now(),
            ]);

            $prescription->visit->moveToStage(Visit::STAGE_BILLING);
        });

        return back()->with('success', 'Prescription dispensed and patient moved to billing.');
    }
}
