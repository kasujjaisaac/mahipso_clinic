<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\Pharmacy;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index(Pharmacy $pharmacy)
    {
        $this->authorize('view', $pharmacy);
        $categories = $pharmacy->categories()->get();
        return view('product-categories.index', compact('pharmacy', 'categories'));
    }

    public function create(Pharmacy $pharmacy)
    {
        $this->authorize('update', $pharmacy);
        return view('product-categories.create', compact('pharmacy'));
    }

    public function store(Request $request, Pharmacy $pharmacy)
    {
        $this->authorize('update', $pharmacy);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,NULL,id,pharmacy_id,' . $pharmacy->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['pharmacy_id'] = $pharmacy->id;
        ProductCategory::create($validated);

        return redirect()->route('pharmacies.categories.index', $pharmacy)
            ->with('success', 'Category created successfully.');
    }

    public function edit(Pharmacy $pharmacy, ProductCategory $category)
    {
        $this->authorize('update', $pharmacy);
        
        if ($category->pharmacy_id !== $pharmacy->id) {
            abort(403);
        }

        return view('product-categories.edit', compact('pharmacy', 'category'));
    }

    public function update(Request $request, Pharmacy $pharmacy, ProductCategory $category)
    {
        $this->authorize('update', $pharmacy);

        if ($category->pharmacy_id !== $pharmacy->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $category->id . ',id,pharmacy_id,' . $pharmacy->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($validated);

        return redirect()->route('pharmacies.categories.index', $pharmacy)
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Pharmacy $pharmacy, ProductCategory $category)
    {
        $this->authorize('update', $pharmacy);

        if ($category->pharmacy_id !== $pharmacy->id) {
            abort(403);
        }

        // Remove category from products
        $category->products()->update(['product_category_id' => null]);
        $category->delete();

        return redirect()->route('pharmacies.categories.index', $pharmacy)
            ->with('success', 'Category deleted successfully.');
    }
}
