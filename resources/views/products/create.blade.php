@extends('layouts.app')

@section('title', 'Add Product')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">+ Add Product</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">{{ $pharmacy->branch->name ?? 'Pharmacy' }} • Create new product</p>
            </div>
            <a href="{{ route('pharmacies.products.index', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;\">← Back to Products</a>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #f0d4d1; max-width: 800px; margin: 0 auto;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Product Information</p>
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

            <form method="POST" action="{{ route('pharmacies.products.store', $pharmacy) }}" enctype="multipart/form-data">
                @csrf
                
                <div style="margin-bottom: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Category (Optional)</label>
                            <select name="product_category_id" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Product Name <span style="color: #b8342b;">*</span></label>
                            <input type="text" name="name" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px;" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Purchase Date <span style="color: #b8342b;">*</span></label>
                            <input type="date" name="purchase_date" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px;" value="{{ old('purchase_date') }}" required>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Expiry Date (Optional)</label>
                            <input type="date" name="expiry_date" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px;" value="{{ old('expiry_date') }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Price (USh) <span style="color: #b8342b;">*</span></label>
                            <input type="number" name="price" step="0.01" min="0.01" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px;" value="{{ old('price') }}" required>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Initial Quantity <span style="color: #b8342b;">*</span></label>
                            <input type="number" name="quantity" min="0" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px;" value="{{ old('quantity') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Minimum Stock Level <span style="color: #b8342b;">*</span></label>
                            <input type="number" name="minimum_stock" min="0" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px;" value="{{ old('minimum_stock') }}" required>
                            <p style="color: #888; font-size: 11px; margin: 4px 0 0 0;">Alert when stock drops below this level</p>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Product Image (Optional)</label>
                            <input type="file" name="image" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;" accept="image/*">
                            <p style="color: #888; font-size: 11px; margin: 4px 0 0 0;">Max 2MB</p>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; border-top: 1px solid #f0d4d1; padding-top: 16px;">
                    <button type="submit" style="background: #b8342b; color: white; padding: 10px 20px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;">+ Add Product</button>
                    <a href="{{ route('pharmacies.products.index', $pharmacy) }}" style="background: #f5f5f5; color: #222; padding: 10px 20px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1px solid #f0d4d1; display: inline-block;">Cancel</a>
                </div>
            </form>
                </div>
        </div>
    </div>
</div>
@endsection
