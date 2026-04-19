<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAuditLog;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Pharmacy $pharmacy, Request $request)
    {
        $this->authorize('view', $pharmacy);
        
        $query = $pharmacy->products()->with('category', 'addedBy');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by stock level
        if ($request->filled('stock_filter')) {
            if ($request->stock_filter === 'low') {
                $query->lowStock();
            } elseif ($request->stock_filter === 'out') {
                $query->where('quantity', 0);
            }
        }

        // Filter by expiry
        if ($request->filled('expiry_filter')) {
            if ($request->expiry_filter === 'expired') {
                $query->expired();
            } elseif ($request->expiry_filter === 'expiring') {
                $query->expiringSoon();
            }
        }

        $products = $query->paginate(20);
        $categories = $pharmacy->categories;

        return view('products.index', compact('pharmacy', 'products', 'categories'));
    }

    public function create(Pharmacy $pharmacy)
    {
        $this->authorize('update', $pharmacy);
        $categories = $pharmacy->categories;
        return view('products.create', compact('pharmacy', 'categories'));
    }

    public function store(Request $request, Pharmacy $pharmacy)
    {
        $this->authorize('update', $pharmacy);
        $validated = $request->validate([
            'product_category_id' => 'nullable|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:purchase_date',
            'price' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|max:2048',
            'quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
        ]);

        $validated['added_by'] = Auth::id();
        $validated['pharmacy_id'] = $pharmacy->id;
        $validated['status'] = 'active';

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // Log audit
        ProductAuditLog::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'new_values' => $product->toArray(),
            'reason' => 'Product created',
        ]);

        return redirect()->route('pharmacies.products.index', $pharmacy)
            ->with('success', 'Product added successfully.');
    }

    public function show(Pharmacy $pharmacy, Product $product)
    {
        $this->authorize('view', $pharmacy);

        if ($product->pharmacy_id !== $pharmacy->id) {
            abort(403);
        }

        $auditLogs = $product->auditLogs()->latest()->paginate(10);

        return view('products.show', compact('pharmacy', 'product', 'auditLogs'));
    }

    public function edit(Pharmacy $pharmacy, Product $product)
    {
        $this->authorize('update', $pharmacy);

        if ($product->pharmacy_id !== $pharmacy->id) {
            abort(403);
        }

        $categories = $pharmacy->categories;
        return view('products.edit', compact('pharmacy', 'product', 'categories'));
    }

    public function update(Request $request, Pharmacy $pharmacy, Product $product)
    {
        $this->authorize('update', $pharmacy);

        if ($product->pharmacy_id !== $pharmacy->id) {
            abort(403);
        }

        $validated = $request->validate([
            'product_category_id' => 'nullable|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:purchase_date',
            'price' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,discontinued',
            'image' => 'nullable|image|max:2048',
        ]);

        // Store old values for audit
        $oldValues = $product->toArray();

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        // Log audit
        ProductAuditLog::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'old_values' => $oldValues,
            'new_values' => $product->toArray(),
            'reason' => 'Product updated',
        ]);

        return redirect()->route('pharmacies.products.show', [$pharmacy, $product])
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Pharmacy $pharmacy, Product $product)
    {
        $this->authorize('update', $pharmacy);

        if ($product->pharmacy_id !== $pharmacy->id) {
            abort(403);
        }

        // Check if product has sales
        if ($product->sales()->exists()) {
            return back()->withErrors(['message' => 'Cannot delete product with sales history.']);
        }

        // Delete image
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        // Log audit
        ProductAuditLog::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'old_values' => $product->toArray(),
            'reason' => 'Product deleted',
        ]);

        $product->delete();

        return redirect()->route('pharmacies.products.index', $pharmacy)
            ->with('success', 'Product deleted successfully.');
    }

    public function bulkImport(Request $request, Pharmacy $pharmacy)
    {
        $this->authorize('update', $pharmacy);

        $request->validate([
            'import_file' => 'required|file|mimes:csv,xlsx',
        ]);

        // TODO: Implement CSV/Excel import logic
        return back()->with('success', 'Bulk import feature coming soon.');
    }

    public function bulkExport(Pharmacy $pharmacy)
    {
        $this->authorize('view', $pharmacy);

        // TODO: Implement CSV export logic
        return response()->json(['message' => 'Bulk export feature coming soon.']);
    }
}
