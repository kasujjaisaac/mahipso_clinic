@extends('layouts.app')

@section('title', 'Low Stock Report')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">⚠️ Low Stock Report</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;\">{{ $pharmacy->branch->name ?? 'Pharmacy' }} • Products below minimum level</p>
            </div>
            <a href="{{ route('pharmacies.analytics', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;\">← Back to Analytics</a>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #f0d4d1;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">🔴 Products Below Minimum Stock Level ({{ $products->total() }} items)</p>
        </div>

        @if($products->count() > 0)
            <div class="table-responsive" style="border: none;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead>
                        <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;">
                            <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Product Name</th>
                            <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Category</th>
                            <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Current</th>
                            <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Minimum</th>
                            <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Deficit</th>
                            <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #666; border: none;\">Unit Price</th>
                            <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr style="border-bottom: 1px solid #f0d4d1; background: #fff5f5;">
                                <td style="padding: 12px 16px; color: #222; font-weight: 500; border: none;\">{{ $product->name }}</td>
                                <td style="padding: 12px 16px; color: #666; border: none;\">{{ $product->category->name ?? '-' }}</td>
                                <td style="padding: 12px 16px; color: #b8342b; font-weight: 700; border: none; text-align: center;\">{{ $product->quantity }}</td>
                                <td style="padding: 12px 16px; color: #666; border: none; text-align: center;\">{{ $product->minimum_stock }}</td>
                                <td style="padding: 12px 16px; color: #dc3545; font-weight: 700; border: none; text-align: center;\">{{ $product->minimum_stock - $product->quantity }}</td>
                                <td style="padding: 12px 16px; color: #666; border: none; text-align: right;">USh {{ number_format($product->price, 0) }}</td>
                                <td style="padding: 12px 16px; border: none; text-align: center;">
                                    <a href="{{ route('pharmacies.products.edit', [$pharmacy, $product]) }}" style="background: #7c3aed; color: white; padding: 6px 12px; font-size: 11px; text-decoration: none; display: inline-block;\">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding: 16px; border-top: 1px solid #f0d4d1; background: #fafafa; display: flex; justify-content: center;">
                {{ $products->links() }}
            </div>
        @else
            <div style="padding: 32px; text-align: center;">
                <p style="color: #2f7d57; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;\">✓ All products are healthy!</p>
                <p style="color: #888; font-size: 12px; margin: 0;\">No products below minimum stock level</p>
            </div>
        @endif
    </div>
</div>
@endsection
