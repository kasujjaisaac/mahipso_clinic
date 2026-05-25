<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
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
        $assets = Inventory::search($request->search)
            ->visibleTo($request->user())
            ->status($request->status)
            ->orderBy('updated_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totals = [
            'total' => Inventory::visibleTo($request->user())->count(),
            'in_store' => Inventory::visibleTo($request->user())->where('status', Inventory::STATUS_IN_STORE)->count(),
            'assigned' => Inventory::visibleTo($request->user())->where('status', Inventory::STATUS_ASSIGNED)->count(),
            'disposed' => Inventory::visibleTo($request->user())->where('status', Inventory::STATUS_DISPOSED)->count(),
        ];

        return view('inventory.index', compact('assets', 'totals'));
    }

    public function create()
    {
        $branches = auth()->user()->isSuperAdmin()
            ? Branch::active()->orderBy('name')->get()
            : Branch::whereKey(auth()->user()->branch_id)->get();

        return view('inventory.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'category' => 'nullable|string|max:100',
            'sku' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'reorder_level' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        $validated['branch_id'] = $this->resolvedBranchId($request, $validated['branch_id'] ?? null);

        $asset = Inventory::create(array_merge($validated, [
            'status' => Inventory::STATUS_IN_STORE,
            'location' => $validated['location'] ?? 'Main store',
        ]));

        $asset->movements()->create([
            'action' => 'received',
            'user_id' => Auth::id(),
            'quantity' => $asset->quantity,
            'location' => $asset->location,
            'notes' => 'Asset recorded and received into store.',
            'performed_at' => now(),
        ]);

        return redirect()->route('inventory.show', $asset)
            ->with('success', 'Asset recorded and available in inventory.');
    }

    public function show(Inventory $inventory)
    {
        $this->authorizeBranchAccess($inventory);

        $inventory->load(['assignedTo', 'disposedBy', 'movements.user', 'movements.assignedTo']);
        $users = User::when(! auth()->user()->isSuperAdmin(), fn ($query) => $query->where('branch_id', auth()->user()->branch_id))
            ->orderBy('name')
            ->get();

        return view('inventory.show', compact('inventory', 'users'));
    }

    public function edit(Inventory $inventory)
    {
        $this->authorizeBranchAccess($inventory);

        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $this->authorizeBranchAccess($inventory);

        if ($request->filled('action')) {
            return $this->handleAction($request, $inventory);
        }

        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'category' => 'nullable|string|max:100',
            'sku' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'reorder_level' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        $validated['branch_id'] = $this->resolvedBranchId($request, $validated['branch_id'] ?? $inventory->branch_id);

        $inventory->update($validated);

        $inventory->movements()->create([
            'action' => 'updated',
            'user_id' => Auth::id(),
            'quantity' => $inventory->quantity,
            'location' => $inventory->location,
            'notes' => 'Asset details updated.',
            'performed_at' => now(),
        ]);

        return redirect()->route('inventory.show', $inventory)
            ->with('success', 'Asset details updated.');
    }

    public function destroy(Inventory $inventory)
    {
        $this->authorizeBranchAccess($inventory);

        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Asset removed from inventory.');
    }

    protected function handleAction(Request $request, Inventory $inventory)
    {
        $action = $request->input('action');

        if ($action === 'assign') {
            $validated = $request->validate([
                'assigned_to' => 'required|exists:users,id',
                'location' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:2000',
            ]);

            $inventory->update([
                'status' => Inventory::STATUS_ASSIGNED,
                'assigned_to' => $validated['assigned_to'],
                'assigned_at' => now(),
                'location' => $validated['location'] ?? $inventory->location,
            ]);

            $inventory->movements()->create([
                'action' => 'assigned',
                'user_id' => Auth::id(),
                'assigned_to' => $validated['assigned_to'],
                'location' => $inventory->location,
                'notes' => $validated['notes'] ?? 'Asset assigned to staff.',
                'performed_at' => now(),
            ]);

            return redirect()->route('inventory.show', $inventory)
                ->with('success', 'Asset assigned to staff successfully.');
        }

        if ($action === 'return') {
            $validated = $request->validate([
                'notes' => 'nullable|string|max:2000',
                'location' => 'nullable|string|max:255',
            ]);

            $inventory->update([
                'status' => Inventory::STATUS_IN_STORE,
                'assigned_to' => null,
                'assigned_at' => null,
                'location' => $validated['location'] ?? $inventory->location,
            ]);

            $inventory->movements()->create([
                'action' => 'returned',
                'user_id' => Auth::id(),
                'location' => $inventory->location,
                'notes' => $validated['notes'] ?? 'Asset returned to store.',
                'performed_at' => now(),
            ]);

            return redirect()->route('inventory.show', $inventory)
                ->with('success', 'Asset returned to store.');
        }

        if ($action === 'dispose') {
            $validated = $request->validate([
                'disposal_reason' => 'required|string|max:2000',
                'notes' => 'nullable|string|max:2000',
            ]);

            $inventory->update([
                'status' => Inventory::STATUS_DISPOSED,
                'disposed_by' => Auth::id(),
                'disposed_at' => now(),
                'disposal_reason' => $validated['disposal_reason'],
                'assigned_to' => null,
                'assigned_at' => null,
            ]);

            $inventory->movements()->create([
                'action' => 'disposed',
                'user_id' => Auth::id(),
                'location' => $inventory->location,
                'notes' => $validated['notes'] ?? $validated['disposal_reason'],
                'performed_at' => now(),
            ]);

            return redirect()->route('inventory.show', $inventory)
                ->with('success', 'Asset disposed and removed from active inventory.');
        }

        return redirect()->route('inventory.show', $inventory)
            ->withErrors(['action' => 'Unrecognized action.']);
    }

    protected function authorizeBranchAccess(Inventory $inventory): void
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->branch_id === $inventory->branch_id, 404);
    }

    protected function resolvedBranchId(Request $request, ?int $branchId): ?int
    {
        if ($request->user()->isSuperAdmin()) {
            return $branchId;
        }

        return $request->user()->branch_id;
    }
}
