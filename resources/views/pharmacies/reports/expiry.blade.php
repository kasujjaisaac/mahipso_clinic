@extends('layouts.app')

@section('title', 'Expiry Report')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">⏰ Expiry Status Report</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;\">{{ $pharmacy->branch->name ?? 'Pharmacy' }} • Track expiring and expired products</p>
            </div>
            <a href="{{ route('pharmacies.analytics', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;\">← Back to Analytics</a>
        </div>
    </div>

    <!-- Expired Products -->
    <div class="mb-4">
        <div style="background: #fff; border: 1px solid #f0d4d1;">
            <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">❌ Expired Products ({{ $expired->count() }} items)</p>
            </div>

            @if($expired->count() > 0)
                <div class="table-responsive" style="border: none;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;">
                                <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Product Name</th>
                                <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Category</th>
                                <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Expiry Date</th>
                                <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Days Expired</th>
                                <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expired as $product)
                                <tr style="border-bottom: 1px solid #f0d4d1; background: #fff5f5;">
                                    <td style="padding: 12px 16px; color: #222; font-weight: 500; border: none;\">{{ $product->name }}</td>
                                    <td style="padding: 12px 16px; color: #666; border: none;\">{{ $product->category->name ?? '-' }}</td>
                                    <td style="padding: 12px 16px; color: #666; border: none;\">{{ $product->expiry_date->format('M d, Y') }}</td>
                                    <td style="padding: 12px 16px; color: #dc3545; font-weight: 700; border: none; text-align: center;\">{{ abs($product->expires_in_days) }} days</td>
                                    <td style="padding: 12px 16px; color: #666; border: none; text-align: center;\">{{ $product->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 32px; text-align: center; color: #2f7d57;\">
                    <p style="font-size: 14px; font-weight: 600; margin: 0 0 4px 0;\">✓ No expired products</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Expiring Soon -->
    <div>
        <div style="background: #fff; border: 1px solid #f0d4d1;">
            <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">⏰ Expiring Soon - Next 30 Days ({{ $expiringSoon->count() }} items)</p>
            </div>

            @if($expiringSoon->count() > 0)
                <div class="table-responsive" style="border: none;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;">
                                <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Product Name</th>
                                <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Category</th>
                                <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Expiry Date</th>
                                <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Days Left</th>
                                <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiringSoon as $product)
                                <tr style="border-bottom: 1px solid #f0d4d1; background: #fffaf0;">
                                    <td style="padding: 12px 16px; color: #222; font-weight: 500; border: none;\">{{ $product->name }}</td>
                                    <td style="padding: 12px 16px; color: #666; border: none;\">{{ $product->category->name ?? '-' }}</td>
                                    <td style="padding: 12px 16px; color: #666; border: none;\">{{ $product->expiry_date->format('M d, Y') }}</td>
                                    <td style="padding: 12px 16px; color: #c87b16; font-weight: 700; border: none; text-align: center;\">{{ $product->expires_in_days }} days</td>
                                    <td style="padding: 12px 16px; color: #666; border: none; text-align: center;\">{{ $product->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 32px; text-align: center; color: #2f7d57;">
                    <p style="font-size: 14px; font-weight: 600; margin: 0 0 4px 0;\">✓ No products expiring soon</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
