@extends('layouts.app')

@section('title', 'Sales')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">📋 Sales</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">{{ $pharmacy->branch->name ?? 'Pharmacy' }} • Transaction history</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('pharmacies.sales.create', $pharmacy) }}" style="background: #b8342b; color: white; padding: 8px 16px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block;\">+ New Sale</a>
                <a href="{{ route('pharmacies.show', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;\">← Back</a>
            </div>
        </div>

        @if (session('success'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #2f7d57; padding: 12px 16px; margin-bottom: 16px; border-radius: 0;\">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filters -->
        <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; margin-bottom: 24px;">
            <form method="GET" action="{{ route('pharmacies.sales.index', $pharmacy) }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">Search Product</label>
                    <input type="text" name="search" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;" placeholder="Product name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">From Date</label>
                    <input type="date" name="date_from" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;\">To Date</label>
                    <input type="date" name="date_to" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;\">Status</label>
                    <select name="status" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;">
                        <option value="">All Sales</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="voided" {{ request('status') === 'voided' ? 'selected' : '' }}>Voided</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" style="background: #b8342b; color: white; border: none; padding: 6px 12px; font-size: 12px; font-weight: 600; width: 100%; cursor: pointer;">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div style="background: #fff; border: 1px solid #f0d4d1;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;">Transactions ({{ $sales->total() }} items)</p>
        </div>
        <div class="table-responsive" style="border: none;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;">
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Receipt #</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Product</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;">Quantity</th>
                        <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #666; border: none;">Total Price</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Sold By</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Sale Date</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Status</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr style="border-bottom: 1px solid #f0d4d1; @if($sale->status === 'voided') background: #fff5f5; @elseif($sale->status === 'refunded') background: #fffaf0; @endif">
                            <td style="padding: 12px 16px; color: #222; font-weight: 600; border: none; font-family: monospace;">RCP-{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td style="padding: 12px 16px; color: #222; border: none;">{{ $sale->product->name ?? 'Deleted Product' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none; text-align: center;">{{ $sale->quantity }}</td>
                            <td style="padding: 12px 16px; color: #b8342b; font-weight: 700; border: none; text-align: right;">USh {{ number_format($sale->total_price, 0) }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ $sale->soldBy->name ?? 'N/A' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ $sale->sale_date->format('M d, Y H:i') }}</td>
                            <td style="padding: 12px 16px; border: none;">
                                <span style="background: @if($sale->status === 'completed') #d4edda @elseif($sale->status === 'voided') #f8d7da @else #fff3cd @endif; color: @if($sale->status === 'completed') #2f7d57 @elseif($sale->status === 'voided') #b8342b @else #c87b16 @endif; padding: 4px 8px; display: inline-block; font-size: 10px; font-weight: 600;\">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; border: none; text-align: center;">
                                <a href="{{ route('pharmacies.sales.show', [$pharmacy, $sale]) }}" style="background: #2f6fed; color: white; padding: 4px 8px; font-size: 11px; text-decoration: none; display: inline-block;\">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 32px 16px; text-align: center; color: #888; border: none;">No sales found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px; border-top: 1px solid #f0d4d1; background: #fafafa; display: flex; justify-content: center;">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
            </div>
        </div>
    </div>
</div>
@endsection

