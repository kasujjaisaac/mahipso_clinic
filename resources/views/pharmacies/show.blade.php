@extends('layouts.app')

@section('title', 'Pharmacy Details')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <!-- Header Section -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">💊 {{ $pharmacy->branch->name ?? 'Pharmacy' }}</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">Pharmacy #{{ $pharmacy->id }}</p>
            </div>
            <a href="{{ route('pharmacies.index') }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;">← Back to Pharmacies</a>
        </div>

        <!-- Quick Info -->
        <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; margin-bottom: 24px;">
            <p style="color: #888; font-size: 11px; font-weight: 600; margin: 0 0 4px 0; text-transform: uppercase;\">Branch Location</p>
            <p style="color: #222; font-size: 13px; margin: 0; font-weight: 500;\">{{ $pharmacy->branch->location ?? 'Not specified' }}</p>
        </div>
    </div>

    <!-- Main Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;\">
                <a href="{{ route('pharmacies.products.index', $pharmacy) }}" style="background: #b8342b; color: white; padding: 12px 16px; text-align: center; text-decoration: none; font-size: 12px; font-weight: 600; border: none; cursor: pointer;\">💊 Products</a>
                <a href="{{ route('pharmacies.sales.index', $pharmacy) }}" style="background: #b8342b; color: white; padding: 12px 16px; text-align: center; text-decoration: none; font-size: 12px; font-weight: 600; border: none; cursor: pointer;\">📋 Sales</a>
                <a href="{{ route('pharmacies.categories.index', $pharmacy) }}" style="background: #fff; color: #b8342b; border: 1px solid #f0d4d1; padding: 12px 16px; text-align: center; text-decoration: none; font-size: 12px; font-weight: 600; cursor: pointer;\">🏷️ Categories</a>
                <a href="{{ route('pharmacies.analytics', $pharmacy) }}" style="background: #fff; color: #b8342b; border: 1px solid #f0d4d1; padding: 12px 16px; text-align: center; text-decoration: none; font-size: 12px; font-weight: 600; cursor: pointer;\">📊 Analytics</a>
                <a href="{{ route('pharmacies.reports.low-stock', $pharmacy) }}" style="background: #fff; color: #b8342b; border: 1px solid #f0d4d1; padding: 12px 16px; text-align: center; text-decoration: none; font-size: 12px; font-weight: 600; cursor: pointer;\">⚠️ Low Stock</a>
                <a href="{{ route('pharmacies.reports.expiry', $pharmacy) }}" style="background: #fff; color: #b8342b; border: 1px solid #f0d4d1; padding: 12px 16px; text-align: center; text-decoration: none; font-size: 12px; font-weight: 600; cursor: pointer;\">⏰ Expiry</a>
            </div>
        </div>
    </div>

    <!-- Key Stats -->
    <div class="row mb-4" style="margin-left: -8px; margin-right: -8px;">
        <div class="col-lg-3 col-md-6 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; border-left: 3px solid #b8342b; padding: 16px; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;">Total Products</p>
                <h3 style="color: #b8342b; font-size: 28px; font-weight: 700; margin: 0;">{{ $pharmacy->products()->count() }}</h3>
                <p style="color: #aaa; font-size: 11px; margin: 4px 0 0 0;">Active: {{ $pharmacy->products()->active()->count() }}</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; border-left: 3px solid #dc3545; padding: 16px; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;">Low Stock Items</p>
                <h3 style="color: #dc3545; font-size: 28px; font-weight: 700; margin: 0;">{{ $pharmacy->low_stock_products }}</h3>
                <p style="color: #aaa; font-size: 11px; margin: 4px 0 0 0;">Need reorder</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; border-left: 3px solid #c87b16; padding: 16px; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;">Expiring Soon</p>
                <h3 style="color: #c87b16; font-size: 28px; font-weight: 700; margin: 0;">{{ $pharmacy->expiring_products }}</h3>
                <p style="color: #aaa; font-size: 11px; margin: 4px 0 0 0;">Within 30 days</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; border-left: 3px solid #7c3aed; padding: 16px; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;">Expired Products</p>
                <h3 style="color: #7c3aed; font-size: 28px; font-weight: 700; margin: 0;">{{ $pharmacy->expired_products }}</h3>
                <p style="color: #aaa; font-size: 11px; margin: 4px 0 0 0;">Remove from stock</p>
            </div>
        </div>
    </div>

    <!-- Inventory Value -->
    <div class="row mb-4" style="margin-left: -8px; margin-right: -8px;">
        <div class="col-md-6" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; border-left: 3px solid #2f7d57; padding: 16px; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;\">Total Stock Value</p>
                <h2 style="color: #2f7d57; font-size: 28px; font-weight: 700; margin: 0;">USh {{ number_format($pharmacy->inventory_value, 0) }}</h2>
            </div>
        </div>

        <div class="col-md-6" style="padding: 0 8px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; border-left: 3px solid #2f6fed; padding: 16px; height: 100%;">
                <p style="color: #888; font-size: 11px; margin: 0 0 8px 0; text-transform: uppercase; font-weight: 600;\">Categories</p>
                <h2 style="color: #2f6fed; font-size: 28px; font-weight: 700; margin: 0 0 8px 0;">{{ $pharmacy->categories()->count() }}</h2>
                <a href="{{ route('pharmacies.categories.index', $pharmacy) }}" style="display: inline-block; background: #2f6fed; color: white; padding: 6px 12px; font-size: 11px; font-weight: 600; text-decoration: none;\">Manage →</a>
            </div>
        </div>
    </div>
</div>
@endsection
