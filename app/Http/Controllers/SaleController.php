<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Pharmacy;
use App\Models\Product;
use App\Models\ProductAuditLog;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\User;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function index(Pharmacy $pharmacy, Request $request)
    {
        $this->authorize('view', $pharmacy);
        
        $query = $pharmacy->sales()->with('product', 'soldBy');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show active (non-voided) sales
            $query->active();
        }

        // Filter by date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->betweenDates($request->date_from, $request->date_to);
        }

        // Search by product name
        if ($request->filled('search')) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . request('search') . '%');
            });
        }

        $sales = $query->latest('sale_date')->paginate(20);

        return view('sales.index', compact('pharmacy', 'sales'));
    }

    public function create(Pharmacy $pharmacy, Request $request)
    {
        $this->authorize('update', $pharmacy);
        
        // Show only active products with available stock
        $products = $pharmacy->products()
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->get();

        $patient = null;
        $visit = null;

        if ($request->filled('patient_id')) {
            $patient = Patient::find($request->patient_id);
        }

        if ($request->filled('visit_id')) {
            $visit = Visit::find($request->visit_id);
        }

        return view('sales.create', compact('pharmacy', 'products', 'patient', 'visit'));
    }

    public function store(Request $request, Pharmacy $pharmacy)
    {
        $this->authorize('update', $pharmacy);
        
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'patient_id' => 'nullable|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'provider_id' => 'nullable|exists:users,id',
            'prescription_note' => 'nullable|string|max:1000',
        ]);

        $product = $pharmacy->products()->findOrFail($validated['product_id']);

        // Validation checks
        if ($product->status !== 'active') {
            return back()->withErrors(['product_id' => 'Product is not active.']);
        }

        if ($product->is_expired) {
            return back()->withErrors(['product_id' => 'Product has expired.']);
        }

        if ($product->quantity < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Insufficient stock. Available: ' . $product->quantity]);
        }

        $patient = null;
        $visit = null;
        $provider = null;

        if (! empty($validated['patient_id'])) {
            $patient = Patient::findOrFail($validated['patient_id']);

            if ($patient->branch_id !== $pharmacy->branch_id) {
                return back()->withErrors(['patient_id' => 'The selected patient does not belong to this pharmacy branch.']);
            }
        }

        if (! empty($validated['visit_id'])) {
            $visit = Visit::findOrFail($validated['visit_id']);

            if ($patient && $visit->patient_id !== $patient->id) {
                return back()->withErrors(['visit_id' => 'The selected visit does not belong to the selected patient.']);
            }

            if ($visit->branch_id !== $pharmacy->branch_id) {
                return back()->withErrors(['visit_id' => 'The selected visit is not associated with this pharmacy branch.']);
            }
        }

        if (! empty($validated['provider_id'])) {
            $provider = User::findOrFail($validated['provider_id']);

            if ($provider->branch_id !== $pharmacy->branch_id) {
                return back()->withErrors(['provider_id' => 'The selected provider does not belong to this pharmacy branch.']);
            }
        }

        $total_price = $product->price * $validated['quantity'];
        
        $sale = Sale::create([
            'pharmacy_id' => $pharmacy->id,
            'product_id' => $product->id,
            'patient_id' => $patient?->id,
            'visit_id' => $visit?->id,
            'provider_id' => $provider?->id,
            'quantity' => $validated['quantity'],
            'total_price' => $total_price,
            'sold_by' => Auth::id(),
            'sale_date' => now(),
            'status' => 'completed',
            'prescription_note' => $validated['prescription_note'] ?? null,
        ]);

        // Decrement product quantity
        $oldQuantity = $product->quantity;
        $product->decrement('quantity', $validated['quantity']);

        // Log audit
        ProductAuditLog::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'action' => 'quantity_adjusted',
            'old_values' => ['quantity' => $oldQuantity],
            'new_values' => ['quantity' => $product->quantity],
            'reason' => "Sale #" . $sale->id,
        ]);

        return redirect()->route('pharmacies.sales.show', [$pharmacy, $sale])
            ->with('success', 'Sale registered. Click "Print Receipt" to generate receipt.');
    }

    public function show(Pharmacy $pharmacy, Sale $sale)
    {
        $this->authorize('view', $pharmacy);

        if ($sale->pharmacy_id !== $pharmacy->id) {
            abort(403);
        }

        $receiptData = $this->receiptService->generateReceiptData($sale);

        return view('sales.show', compact('pharmacy', 'sale', 'receiptData'));
    }

    public function printReceipt(Pharmacy $pharmacy, Sale $sale)
    {
        $this->authorize('view', $pharmacy);

        if ($sale->pharmacy_id !== $pharmacy->id) {
            abort(403);
        }

        $receiptData = $this->receiptService->generateReceiptData($sale);

        return view('receipts.print', compact('receiptData', 'sale'));
    }

    public function void(Request $request, Pharmacy $pharmacy, Sale $sale)
    {
        $this->authorize('update', $pharmacy);

        if ($sale->pharmacy_id !== $pharmacy->id) {
            abort(403);
        }

        if ($sale->status !== 'completed') {
            return back()->withErrors(['message' => 'Only completed sales can be voided.']);
        }

        $request->validate([
            'void_reason' => 'required|string|max:255',
        ]);

        $sale->void($request->void_reason, Auth::id());

        return redirect()->route('pharmacies.sales.show', [$pharmacy, $sale])
            ->with('success', 'Sale voided successfully. Inventory has been reversed.');
    }

    public function refund(Request $request, Pharmacy $pharmacy, Sale $sale)
    {
        $this->authorize('update', $pharmacy);

        if ($sale->pharmacy_id !== $pharmacy->id) {
            abort(403);
        }

        if ($sale->status !== 'completed') {
            return back()->withErrors(['message' => 'Only completed sales can be refunded.']);
        }

        $request->validate([
            'refund_reason' => 'required|string|max:255',
        ]);

        // Mark as refunded instead of voiding
        $sale->update([
            'status' => 'refunded',
            'void_reason' => $request->refund_reason,
            'voided_by' => Auth::id(),
            'voided_at' => now(),
        ]);

        // Reverse inventory
        $sale->product->increment('quantity', $sale->quantity);
        
        ProductAuditLog::create([
            'product_id' => $sale->product_id,
            'user_id' => Auth::id(),
            'action' => 'quantity_adjusted',
            'old_values' => ['quantity' => $sale->product->quantity - $sale->quantity],
            'new_values' => ['quantity' => $sale->product->quantity],
            'reason' => "Sale refund - {$request->refund_reason}",
        ]);

        return redirect()->route('pharmacies.sales.show', [$pharmacy, $sale])
            ->with('success', 'Sale refunded successfully. Inventory has been reversed.');
    }
}
