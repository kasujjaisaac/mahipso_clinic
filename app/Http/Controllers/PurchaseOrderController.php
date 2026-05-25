<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('pharmacy'), 403);
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $orders = PurchaseOrder::visibleTo($request->user())->with(['supplier', 'branch', 'requestedBy'])->latest()->paginate(20);
        return view('purchase_orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::when(! $request->user()->isSuperAdmin(), fn ($q) => $q->where(fn ($s) => $s->whereNull('branch_id')->orWhere('branch_id', $request->user()->branch_id)))->orderBy('name')->get();
        return view('purchase_orders.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'total_amount' => 'required|numeric|min:0',
            'expected_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        $data['branch_id'] = $request->user()->branch_id;
        $data['requested_by'] = $request->user()->id;
        $data['status'] = 'submitted';
        PurchaseOrder::create($data);
        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order submitted.');
    }
}
