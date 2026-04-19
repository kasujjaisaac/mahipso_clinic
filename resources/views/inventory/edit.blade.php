@extends('layouts.app')

@section('title', 'Update Asset')
@section('section', 'Inventory')
@section('page_title', 'Update Asset')

@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">✎ Update Asset</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">Edit asset metadata, adjust location, and update stock tracking details.</p>
            </div>
            <a href="{{ route('inventory.show', $inventory) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;">← Back to Asset</a>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #f0d4d1; max-width: 800px; margin: 0 auto;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;">Asset Information</p>
        </div>
        <div style="padding: 24px;">
            @if ($errors->any())
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 16px; margin-bottom: 16px; border-radius: 0;">
                    <strong>Validation Error:</strong>
                    <ul style="margin: 4px 0 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('inventory.update', $inventory) }}">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Asset Name <span style="color: #b8342b;">*</span></label>
                        <input type="text" name="item_name" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ old('item_name', $inventory->item_name) }}" required>
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Category</label>
                        <input type="text" name="category" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ old('category', $inventory->category) }}" placeholder="Equipment, Furniture, Supplies...">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">SKU / Asset Tag</label>
                        <input type="text" name="sku" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ old('sku', $inventory->sku) }}">
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Quantity <span style="color: #b8342b;">*</span></label>
                        <input type="number" name="quantity" min="0" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ old('quantity', $inventory->quantity) }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Unit</label>
                        <input type="text" name="unit" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ old('unit', $inventory->unit) }}" placeholder="pcs, set, box">
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Unit Price</label>
                        <input type="number" name="unit_price" step="0.01" min="0" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ old('unit_price', $inventory->unit_price) }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Supplier</label>
                        <input type="text" name="supplier" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ old('supplier', $inventory->supplier) }}">
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Purchase Date</label>
                        <input type="date" name="purchase_date" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ old('purchase_date', optional($inventory->purchase_date)->format('Y-m-d')) }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Expiry Date</label>
                        <input type="date" name="expiry_date" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ old('expiry_date', optional($inventory->expiry_date)->format('Y-m-d')) }}">
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Location</label>
                        <input type="text" name="location" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ old('location', $inventory->location) }}" placeholder="Store, warehouse, office">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Reorder Level</label>
                        <input type="number" name="reorder_level" min="0" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ old('reorder_level', $inventory->reorder_level) }}">
                        <p style="color: #888; font-size: 11px; margin: 4px 0 0 0;">Alert when stock drops below this level</p>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Notes</label>
                    <textarea name="notes" rows="3" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" placeholder="Additional details about this asset">{{ old('notes', $inventory->notes) }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; border-top: 1px solid #f0d4d1; padding-top: 16px;">
                    <button type="submit" style="background: #b8342b; color: white; padding: 10px 20px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;">Update Asset</button>
                    <a href="{{ route('inventory.show', $inventory) }}" style="background: #f5f5f5; color: #222; padding: 10px 20px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1px solid #f0d4d1; display: inline-block;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
