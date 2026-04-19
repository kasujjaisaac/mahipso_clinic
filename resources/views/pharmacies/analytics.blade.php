@extends('layouts.app')

@section('title', 'Pharmacy Analytics')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <!-- Header Section -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">📊 Analytics Dashboard</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">{{ $pharmacy->branch->name ?? 'Pharmacy' }} • Real-time insights</p>
            </div>
            <a href="{{ route('pharmacies.show', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;">← Back to Pharmacy</a>
        </div>

        <!-- Date Range Filter -->
        <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; margin-bottom: 24px;">
            <form method="GET" action="{{ route('pharmacies.analytics', $pharmacy) }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">From Date</label>
                    <input type="date" name="from" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;" value="{{ $from->toDateString() }}">
                </div>
                <div class="col-md-2">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">To Date</label>
                    <input type="date" name="to" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;" value="{{ $to->toDateString() }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" style="background: #b8342b; color: white; border: none; padding: 6px 12px; font-size: 12px; font-weight: 600; width: 100%; cursor: pointer;\">Apply</button>
                </div>
                <div class="col-md-7 text-end">
                    <a href="{{ route('pharmacies.reports.low-stock', $pharmacy) }}" style="display: inline-block; color: #b8342b; border: 1px solid #f0d4d1; padding: 8px 12px; font-size: 11px; margin-right: 8px; background: #fff; text-decoration: none;\">⚠️ Low Stock</a>
                    <a href="{{ route('pharmacies.reports.expiry', $pharmacy) }}" style="display: inline-block; color: #b8342b; border: 1px solid #f0d4d1; padding: 8px 12px; font-size: 11px; margin-right: 8px; background: #fff; text-decoration: none;\">⏰ Expiry</a>
                    <a href="{{ route('pharmacies.reports.revenue', $pharmacy) }}" style="display: inline-block; color: #b8342b; border: 1px solid #f0d4d1; padding: 8px 12px; font-size: 11px; background: #fff; text-decoration: none;\">💰 Revenue</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4" style="margin-left: -8px; margin-right: -8px;">
        <div class="col-lg-3 col-md-6 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; border-left: 3px solid #b8342b; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;\">Total Revenue</p>
                <h3 style="color: #b8342b; font-size: 24px; font-weight: 700; margin: 0 0 4px 0;">USh {{ number_format($metrics['total_revenue'], 0) }}</h3>
                <p style="color: #aaa; font-size: 11px; margin: 0;\">{{ $from->format('M d') }} - {{ $to->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; border-left: 3px solid #2f7d57; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;\">Total Sales</p>
                <h3 style="color: #2f7d57; font-size: 24px; font-weight: 700; margin: 0 0 4px 0;\">{{ $metrics['total_sales'] }}</h3>
                <p style="color: #aaa; font-size: 11px; margin: 0;\">Completed transactions</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; border-left: 3px solid #c87b16; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;\">Inventory Value</p>
                <h3 style="color: #c87b16; font-size: 24px; font-weight: 700; margin: 0 0 4px 0;">USh {{ number_format($metrics['inventory_value'], 0) }}</h3>
                <p style="color: #aaa; font-size: 11px; margin: 0;\">Total stock value</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; border-left: 3px solid #2f6fed; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;\">Total Products</p>
                <h3 style="color: #2f6fed; font-size: 24px; font-weight: 700; margin: 0 0 4px 0;\">{{ $metrics['total_products'] }}</h3>
                <p style="color: #aaa; font-size: 11px; margin: 0;\">{{ $metrics['active_products'] }} active</p>
            </div>
        </div>
    </div>

    <!-- Alerts & Warnings -->
    <div class="row mb-4" style="margin-left: -8px; margin-right: -8px;">
        <div class="col-lg-4 col-md-6 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; border-top: 2px solid #b8342b;">
                <p style="color: #b8342b; font-size: 12px; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase;\">⚠️ LOW STOCK ITEMS</p>
                <h2 style="color: #b8342b; font-size: 32px; font-weight: 700; margin: 0 0 12px 0;\">{{ $metrics['low_stock_count'] }}</h2>
                <a href="{{ route('pharmacies.reports.low-stock', $pharmacy) }}" style="display: inline-block; background: #b8342b; color: white; padding: 6px 12px; font-size: 11px; font-weight: 600; text-decoration: none;\">View Report →</a>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; border-top: 2px solid #c87b16;">
                <p style="color: #c87b16; font-size: 12px; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase;\">⏰ EXPIRING SOON</p>
                <h2 style="color: #c87b16; font-size: 32px; font-weight: 700; margin: 0 0 12px 0;\">{{ $metrics['expiring_count'] }}</h2>
                <a href="{{ route('pharmacies.reports.expiry', $pharmacy) }}" style="display: inline-block; background: #c87b16; color: white; padding: 6px 12px; font-size: 11px; font-weight: 600; text-decoration: none;\">View Report →</a>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; border-top: 2px solid #dc3545;">
                <p style="color: #dc3545; font-size: 12px; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase;\">❌ EXPIRED PRODUCTS</p>
                <h2 style="color: #dc3545; font-size: 32px; font-weight: 700; margin: 0 0 12px 0;\">{{ $metrics['expired_count'] }}</h2>
                <a href="{{ route('pharmacies.reports.expiry', $pharmacy) }}" style="display: inline-block; background: #dc3545; color: white; padding: 6px 12px; font-size: 11px; font-weight: 600; text-decoration: none;\">View Report →</a>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4" style="margin-left: -8px; margin-right: -8px;">
        <!-- Daily Revenue Chart -->
        <div class="col-lg-6 mb-4" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1;">
                <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                    <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">📈 Daily Revenue Trend</p>
                </div>
                <div style="padding: 16px;">
                    <canvas id="dailyRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products Chart -->
        <div class="col-lg-6 mb-4" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1;">
                <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                    <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">🏆 Top 10 Best Selling Products</p>
                </div>
                <div style="padding: 16px;">
                    <canvas id="topProductsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock & Expiring Products Tables -->
    <div class="row mb-4" style="margin-left: -8px; margin-right: -8px;">
        <div class="col-lg-6 mb-4" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1;">
                <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                    <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">🔴 Low Stock Products (Top 10)</p>
                </div>
                <div class="table-responsive" style="border: none;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;">
                                <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Product</th>
                                <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Current</th>
                                <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Minimum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $product)
                                <tr style="border-bottom: 1px solid #f0d4d1; background: #fff5f5;">
                                    <td style="padding: 12px 16px; color: #222; border: none;\">{{ $product->name }}</td>
                                    <td style="padding: 12px 16px; color: #b8342b; font-weight: 700; border: none; text-align: center;\">{{ $product->quantity }}</td>
                                    <td style="padding: 12px 16px; color: #666; border: none; text-align: center;\">{{ $product->minimum_stock }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding: 24px 16px; text-align: center; color: #888; border: none;\">No low stock products</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1;">
                <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                    <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">⏰ Expiring Soon Products (Top 10)</p>
                </div>
                <div class="table-responsive" style="border: none;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;">
                                <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Product</th>
                                <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Expiry Date</th>
                                <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Days Left</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expiringProducts as $product)
                                <tr style="border-bottom: 1px solid #f0d4d1; background: #fffaf0;">
                                    <td style="padding: 12px 16px; color: #222; border: none;\">{{ $product->name }}</td>
                                    <td style="padding: 12px 16px; color: #666; border: none;\">{{ $product->expiry_date->format('M d, Y') }}</td>
                                    <td style="padding: 12px 16px; color: #c87b16; font-weight: 700; border: none; text-align: center;\">{{ $product->expires_in_days }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding: 24px 16px; text-align: center; color: #888; border: none;\">No expiring products</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Daily Revenue Chart
const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyRevenue->keys()) !!},
        datasets: [{
            label: 'Daily Revenue (USh)',
            data: {!! json_encode($dailyRevenue->values()) !!},
            borderColor: '#b8342b',
            backgroundColor: 'rgba(184, 52, 43, 0.08)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#b8342b',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: true, labels: { font: { size: 11 }, boxWidth: 12 } },
            tooltip: { backgroundColor: 'rgba(0,0,0,0.8)', padding: 10, titleFont: { size: 12 }, bodyFont: { size: 11 } }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f0d4d1' }, ticks: { font: { size: 11 }, color: '#666' } },
            x: { grid: { color: '#f0d4d1' }, ticks: { font: { size: 11 }, color: '#666' } }
        }
    }
});

// Top Products Chart
const topCtx = document.getElementById('topProductsChart').getContext('2d');
new Chart(topCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($topProducts->pluck('product')) !!},
        datasets: [{
            label: 'Revenue (USh)',
            data: {!! json_encode($topProducts->pluck('revenue')) !!},
            backgroundColor: '#b8342b',
            borderColor: '#8d241d',
            borderWidth: 1,
            borderRadius: 0
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: true, labels: { font: { size: 11 }, boxWidth: 12 } },
            tooltip: { backgroundColor: 'rgba(0,0,0,0.8)', padding: 10, titleFont: { size: 12 }, bodyFont: { size: 11 } }
        },
        scales: {
            x: { beginAtZero: true, grid: { color: '#f0d4d1' }, ticks: { font: { size: 11 }, color: '#666' } },
            y: { grid: { color: '#f0d4d1' }, ticks: { font: { size: 11 }, color: '#666' } }
        }
    }
});
</script>
@endsection
