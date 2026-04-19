@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('section', 'Admin command center')
@section('kicker', 'Clinic operations insight')
@section('page_title', 'Administrator dashboard')
@section('page_subtitle', 'Real-time clinic performance across branches, patients, appointments, visits, and records.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('admin.dashboard') }}">Dashboard</a>
    <a class="ghost-button" href="{{ route('branches.index') }}">Manage branches</a>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="panel">
                <div class="panel-header">
                    <span class="section-title">Recent Pharmacy Sales</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Pharmacy</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPharmacySales as $sale)
                                <tr>
                                    <td>{{ $sale->product->name ?? '-' }}</td>
                                    <td>{{ $sale->pharmacy->branch->name ?? '-' }}</td>
                                    <td>{{ $sale->quantity }}</td>
                                    <td>UGX {{ $sale->total_price }}</td>
                                    <td>{{ $sale->sale_date }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No recent sales</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel">
                <div class="panel-header">
                    <span class="section-title">Expiring Products (Next 30 Days)</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Pharmacy</th>
                                <th>Expiry Date</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expiringProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->pharmacy->branch->name ?? '-' }}</td>
                                    <td>{{ $product->expiry_date }}</td>
                                    <td>{{ $product->quantity }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No expiring products</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('content')

    <div class="stats-grid">
        <div class="metric-card" style="--accent: var(--brand);">
            <div class="metric-icon">⌂</div>
            <div>
                <div class="metric-value">{{ $totals['branches'] }}</div>
                <div class="metric-label">Active branches</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: var(--blue);">
            <div class="metric-icon">👥</div>
            <div>
                <div class="metric-value">{{ \App\Models\User::count() }}</div>
                <div class="metric-label">Users</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: #b8342b;">
            <div class="metric-icon">🏥</div>
            <div>
                <div class="metric-value">{{ $totals['pharmacies'] }}</div>
                <div class="metric-label">Pharmacies</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: #b8342b;">
            <div class="metric-icon">💊</div>
            <div>
                <div class="metric-value">{{ $totals['products'] }}</div>
                <div class="metric-label">Products</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: #b8342b;">
            <div class="metric-icon">🛒</div>
            <div>
                <div class="metric-value">{{ $totals['sales'] }}</div>
                <div class="metric-label">Sales</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: #b8342b;">
            <div class="metric-icon">💰</div>
            <div>
                <div class="metric-value">UGX {{ $totals['sales_revenue'] }}</div>
                <div class="metric-label">Total Revenue</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: #b8342b;">
            <div class="metric-icon">⚠️</div>
            <div>
                <div class="metric-value">{{ $totals['low_stock'] }}</div>
                <div class="metric-label">Low Stock Alerts</div>
            </div>
        </div>
    </div>


@endsection
