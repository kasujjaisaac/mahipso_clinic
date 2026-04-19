@extends('layouts.app')

@section('title', 'Inventory')
@section('section', 'Inventory')
@section('page_title', 'Company Assets')

@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <!-- Header Section -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">📦 Company Assets</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">Track every asset from receipt through assignment and disposal.</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('inventory.create') }}" style="background: #b8342b; color: white; padding: 8px 16px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block;">+ Add Asset</a>
                <a href="{{ route('admin.dashboard') }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;">← Back to Dashboard</a>
            </div>
        </div>

        <!-- Asset Summary Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 20px; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #222; margin-bottom: 8px;">{{ $totals['total'] }}</div>
                <div style="color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase;">Total Assets</div>
            </div>
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 20px; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #2f6fed; margin-bottom: 8px;">{{ $totals['in_store'] }}</div>
                <div style="color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase;">In Store</div>
            </div>
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 20px; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #7c3aed; margin-bottom: 8px;">{{ $totals['assigned'] }}</div>
                <div style="color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase;">Assigned</div>
            </div>
            <div style="background: #fff; border: 1px solid #f0d4d1; padding: 20px; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #dc3545; margin-bottom: 8px;">{{ $totals['disposed'] }}</div>
                <div style="color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase;">Disposed</div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div style="background: #fff; border: 1px solid #f0d4d1; padding: 16px; margin-bottom: 24px;">
            <form method="GET" action="{{ route('inventory.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">Search Assets</label>
                    <input type="text" name="search" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;" placeholder="Asset name, SKU, supplier..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px;">Status</label>
                    <select name="status" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 6px 8px; width: 100%; font-size: 12px;">
                        <option value="">All Statuses</option>
                        <option value="in_store" {{ request('status') === 'in_store' ? 'selected' : '' }}>In Store</option>
                        <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="disposed" {{ request('status') === 'disposed' ? 'selected' : '' }}>Disposed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" style="background: #b8342b; color: white; border: none; padding: 6px 12px; font-size: 12px; font-weight: 600; width: 100%; cursor: pointer;">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assets Table -->
    <div style="background: #fff; border: 1px solid #f0d4d1;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;">Assets List ({{ $assets->total() }} items)</p>
        </div>
        <div class="table-responsive" style="border: none;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;">
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Asset Name</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Category</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">SKU</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Status</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Location</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Assigned To</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;">Qty</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                        <tr style="border-bottom: 1px solid #f0d4d1;">
                            <td style="padding: 12px 16px; color: #222; font-weight: 500; border: none;">
                                {{ $asset->item_name }}
                                @if($asset->status === 'disposed')
                                    <span style="display: inline-block; background: #dc3545; color: white; padding: 2px 6px; font-size: 10px; margin-left: 6px;">Disposed</span>
                                @elseif($asset->status === 'assigned')
                                    <span style="display: inline-block; background: #7c3aed; color: white; padding: 2px 6px; font-size: 10px; margin-left: 6px;">Assigned</span>
                                @else
                                    <span style="display: inline-block; background: #2f6fed; color: white; padding: 2px 6px; font-size: 10px; margin-left: 6px;">In Store</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ $asset->category ?: '-' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ $asset->sku ?: '-' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ ucfirst(str_replace('_', ' ', $asset->status)) }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ $asset->location ?: '-' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ optional($asset->assignedTo)->name ?: '-' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none; text-align: center;">{{ $asset->quantity }}</td>
                            <td style="padding: 12px 16px; border: none; text-align: center;">
                                <a href="{{ route('inventory.show', $asset) }}" style="background: #2f6fed; color: white; padding: 4px 8px; font-size: 11px; text-decoration: none; display: inline-block;">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 40px; text-align: center; color: #888; border: none;">
                                No assets found. <a href="{{ route('inventory.create') }}" style="color: #b8342b;">Add the first asset</a> to begin tracking.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
            <div style="padding: 16px; border-top: 1px solid #f0d4d1; background: #fafafa;">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
