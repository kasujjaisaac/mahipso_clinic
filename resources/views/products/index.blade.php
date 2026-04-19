@extends('layouts.app')

@section('title', 'Products')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <!-- Header Section -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">💊 Products</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">{{ $pharmacy->branch->name ?? 'Pharmacy' }} • Manage your inventory</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('pharmacies.products.create', $pharmacy) }}" style="background: #b8342b; color: white; padding: 8px 16px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block;\">+ Add Product</a>
                <a href="{{ route('pharmacies.categories.index', $pharmacy) }}" style="background: #fff; color: #b8342b; border: 1px solid #f0d4d1; padding: 8px 16px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block;\">Categories</a>
                <a href="{{ route('pharmacies.show', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;\">← Back</a>
            </div>
        </div>

        <!-- Filters and Search -->
        <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; margin-bottom: 24px;">
            <form method="GET" action="{{ route('pharmacies.products.index', $pharmacy) }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">Search Product</label>
                    <input type="text" name="search" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;" placeholder="Product name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">Category</label>
                    <select name="category" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">Stock Level</label>
                    <select name="stock_filter" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;">
                        <option value="">All</option>
                        <option value="low" {{ request('stock_filter') == 'low' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out" {{ request('stock_filter') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">Expiry Status</label>
                    <select name="expiry_filter" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;">
                        <option value="">All</option>
                        <option value="expiring" {{ request('expiry_filter') == 'expiring' ? 'selected' : '' }}>Expiring Soon</option>
                        <option value="expired" {{ request('expiry_filter') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">Status</label>
                    <select name="status" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" style="background: #b8342b; color: white; border: none; padding: 6px 12px; font-size: 12px; font-weight: 600; width: 100%; cursor: pointer;">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div style="background: #fff; border: 1px solid #f0d4d1;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;\">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Products List ({{ $products->total() }} items)</p>
        </div>
        <div class="table-responsive" style="border: none;\">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;\">
                <thead>
                    <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;\">
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Name</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Category</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Purchase Date</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Expiry Date</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Price</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Qty</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Min</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Status</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr style="border-bottom: 1px solid #f0d4d1; @if($product->is_expired) background: #fff5f5; @elseif($product->is_low_stock) background: #fffaf0; @endif">
                            <td style="padding: 12px 16px; color: #222; font-weight: 500; border: none;">
                                {{ $product->name }}
                                @if($product->is_expired)
                                    <span style="display: inline-block; background: #dc3545; color: white; padding: 2px 6px; font-size: 10px; margin-left: 6px;\">Expired</span>
                                @elseif($product->expires_in_days && $product->expires_in_days <= 30)
                                    <span style="display: inline-block; background: #c87b16; color: white; padding: 2px 6px; font-size: 10px; margin-left: 6px;\">Exp: {{ $product->expires_in_days }}d</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ $product->category->name ?? '-' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ $product->purchase_date->format('M d, Y') }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ $product->expiry_date ? $product->expiry_date->format('M d, Y') : '-' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none; font-weight: 600;">USh {{ number_format($product->price, 0) }}</td>
                            <td style="padding: 12px 16px; color: #222; border: none; text-align: center; font-weight: 600;\">
                                <span style="background: @if($product->quantity > $product->minimum_stock) #d4edda @else #f8d7da @endif; color: @if($product->quantity > $product->minimum_stock) #2f7d57 @else #b8342b @endif; padding: 4px 8px; display: inline-block; border-radius: 0;\">
                                    {{ $product->quantity }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; color: #666; border: none; text-align: center;\">{{ $product->minimum_stock }}</td>
                            <td style="padding: 12px 16px; color: #222; border: none;\">
                                <span style="background: @if($product->status === 'active') #d4edda @elseif($product->status === 'inactive') #e2e3e5 @else #f5f5f5 @endif; color: @if($product->status === 'active') #2f7d57 @elseif($product->status === 'inactive') #666 @else #222 @endif; padding: 4px 8px; display: inline-block; font-size: 10px; font-weight: 600;\">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; border: none; text-align: center;\">
                                <a href="{{ route('pharmacies.products.show', [$pharmacy, $product]) }}" style="background: #2f6fed; color: white; padding: 4px 8px; font-size: 11px; text-decoration: none; margin-right: 4px; display: inline-block;\">View</a>
                                <a href="{{ route('pharmacies.products.edit', [$pharmacy, $product]) }}" style="background: #7c3aed; color: white; padding: 4px 8px; font-size: 11px; text-decoration: none; display: inline-block;\">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding: 32px 16px; text-align: center; color: #888; border: none;\">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px; border-top: 1px solid #f0d4d1; background: #fafafa; display: flex; justify-content: center;\">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection

