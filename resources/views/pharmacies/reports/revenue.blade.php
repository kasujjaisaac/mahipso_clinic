@extends('layouts.app')

@section('title', 'Revenue Report')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">💰 Revenue Report</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;\">{{ $pharmacy->branch->name ?? 'Pharmacy' }} • Transaction analysis</p>
            </div>
            <a href="{{ route('pharmacies.analytics', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;\">← Back to Analytics</a>
        </div>

        <!-- Date Filter -->
        <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; margin-bottom: 24px;">
            <form method="GET" action="{{ route('pharmacies.reports.revenue', $pharmacy) }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;\">From Date</label>
                    <input type="date" name="from" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;" value="{{ $from->toDateString() }}">
                </div>
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;\">To Date</label>
                    <input type="date" name="to" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;" value="{{ $to->toDateString() }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" style="background: #b8342b; color: white; border: none; padding: 6px 12px; font-size: 12px; font-weight: 600; width: 100%; cursor: pointer;\">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="row mb-4" style="margin-left: -8px; margin-right: -8px;">
        <div class="col-lg-4 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; border-left: 3px solid #b8342b; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;\">Total Revenue</p>
                <h2 style="color: #b8342b; font-size: 28px; font-weight: 700; margin: 0 0 4px 0;">USh {{ number_format($totalRevenue, 0) }}</h2>
                <p style="color: #aaa; font-size: 11px; margin: 0;\">{{ $from->format('M d, Y') }} - {{ $to->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="col-lg-4 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; border-left: 3px solid #2f6fed; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;\">Total Transactions</p>
                <h2 style="color: #2f6fed; font-size: 28px; font-weight: 700; margin: 0 0 4px 0;\">{{ $sales->count() }}</h2>
                <p style="color: #aaa; font-size: 11px; margin: 0;\">Completed sales</p>
            </div>
        </div>
        <div class="col-lg-4 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; border-left: 3px solid #2f7d57; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;\">Average Transaction</p>
                <h2 style="color: #2f7d57; font-size: 28px; font-weight: 700; margin: 0 0 4px 0;\">₱{{ $sales->count() > 0 ? number_format($totalRevenue / $sales->count(), 2) : '0.00' }}</h2>
                <p style="color: #aaa; font-size: 11px; margin: 0;\">Per transaction</p>
            </div>
        </div>
    </div>

    <!-- Sales Details Table -->
    <div style="background: #fff; border: 1px solid #f0d4d1;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Sale Transactions ({{ $sales->count() }} items)</p>
        </div>
        <div class="table-responsive" style="border: none;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;">
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Receipt #</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Product</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Quantity</th>
                        <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #666; border: none;\">Unit Price</th>
                        <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #666; border: none;\">Total</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Sold By</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Sale Date</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr style="border-bottom: 1px solid #f0d4d1;">
                            <td style="padding: 12px 16px; color: #222; font-weight: 600; border: none; font-family: monospace;\">RCP-{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td style="padding: 12px 16px; color: #222; border: none;\">{{ $sale->product->name ?? 'Deleted' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none; text-align: center;\">{{ $sale->quantity }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none; text-align: right;\">₱{{ $sale->product ? number_format($sale->product->price, 2) : '0.00' }}</td>
                            <td style="padding: 12px 16px; color: #b8342b; font-weight: 700; border: none; text-align: right;\">₱{{ number_format($sale->total_price, 2) }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;\">{{ $sale->soldBy->name ?? 'N/A' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;\">{{ $sale->sale_date->format('M d, Y H:i') }}</td>
                            <td style="padding: 12px 16px; border: none; text-align: center;">
                                <a href="{{ route('pharmacies.sales.show', [$pharmacy, $sale]) }}" style="background: #2f6fed; color: white; padding: 6px 12px; font-size: 11px; text-decoration: none; display: inline-block;\">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 32px 16px; text-align: center; color: #888; border: none;\">No sales found for the selected period</td>
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
