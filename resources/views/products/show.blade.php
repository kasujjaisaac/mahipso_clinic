@extends('layouts.app')

@section('title', 'Product Details')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">{{ $product->name }}</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">Product ID: {{ $product->id }} • Category: {{ $product->category->name ?? 'Uncategorized' }}</p>
            </div>
            <a href="{{ route('pharmacies.products.index', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;\">← Back to Products</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Product Details -->
        <div>
            <div style="background: #fff; border: 1px solid #f0d4d1;">
                <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                    <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Product Information</p>
                </div>
                <div style="padding: 24px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Category</p>
                            <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;\">{{ $product->category->name ?? 'Uncategorized' }}</p>
                        </div>
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Status</p>
                            <span style="background: @if($product->status === 'active') #d4edda @elseif($product->status === 'inactive') #e2e3e5 @else #f5f5f5 @endif; color: @if($product->status === 'active') #2f7d57 @elseif($product->status === 'inactive') #666 @else #222 @endif; padding: 4px 8px; display: inline-block; font-size: 11px; font-weight: 600;\">{{ ucfirst($product->status) }}</span>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #f0d4d1; padding-top: 24px; margin-top: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Price</p>
                            <p style="color: #b8342b; font-size: 20px; font-weight: 700; margin: 0;">USh {{ number_format($product->price, 0) }}</p>
                        </div>
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Current Stock</p>
                            <span style="background: @if($product->is_low_stock) #f8d7da @else #d4edda @endif; color: @if($product->is_low_stock) #b8342b @else #2f7d57 @endif; padding: 8px 12px; display: inline-block; font-weight: 700;\">{{ $product->quantity }}</span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Minimum Stock</p>
                            <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;\">{{ $product->minimum_stock }}</p>
                        </div>
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Purchase Date</p>
                            <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;\">{{ $product->purchase_date->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Expiry Date</p>
                            @if($product->expiry_date)
                                <span style="background: @if($product->is_expired) #f8d7da @elseif($product->expires_in_days <= 30) #fff3cd @else #d1ecf1 @endif; color: @if($product->is_expired) #b8342b @elseif($product->expires_in_days <= 30) #c87b16 @else #2f6fed @endif; padding: 4px 8px; display: inline-block; font-size: 12px; font-weight: 600;\">{{ $product->expiry_date->format('M d, Y') }}</span>
                            @else
                                <p style="color: #888; font-size: 13px; margin: 0;\">No expiry</p>
                            @endif
                        </div>
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Added By</p>
                            <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;\">{{ $product->addedBy->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Image -->
            @if($product->image_path)
                <div style="background: #fff; border: 1px solid #f0d4d1; margin-top: 24px;">
                    <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                        <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Product Image</p>
                    </div>
                    <div style="padding: 24px; text-align: center;">
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 400px; border: 1px solid #f0d4d1;">
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Actions -->
            <div style="background: #fff; border: 1px solid #f0d4d1; margin-bottom: 24px;">
                <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                    <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Actions</p>
                </div>
                <div style="padding: 16px; display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('pharmacies.products.edit', [$pharmacy, $product]) }}" style="background: #7c3aed; color: white; padding: 10px 16px; font-size: 12px; font-weight: 600; text-decoration: none; text-align: center;\">✎ Edit Product</a>
                    <form action="{{ route('pharmacies.products.destroy', [$pharmacy, $product]) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: #dc3545; color: white; padding: 10px 16px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; width: 100%; text-align: center;\">🗑 Delete Product</button>
                    </form>
                </div>
            </div>

            <!-- Stock Status -->
            <div style="background: #fff; border: 1px solid #f0d4d1; margin-bottom: 24px;">
                <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                    <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Stock Status</p>
                </div>
                <div style="padding: 16px;">
                    @if($product->is_low_stock)
                        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #b8342b; padding: 12px; margin-bottom: 8px; border-radius: 0;">
                            <p style="font-size: 12px; font-weight: 600; margin: 0 0 4px 0;\">⚠️ Low Stock</p>
                            <p style="font-size: 11px; margin: 0;\">Below minimum level ({{ $product->minimum_stock }})</p>
                        </div>
                    @elseif($product->is_expired)
                        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #b8342b; padding: 12px; margin-bottom: 8px; border-radius: 0;">
                            <p style="font-size: 12px; font-weight: 600; margin: 0 0 4px 0;\">❌ Expired</p>
                            <p style="font-size: 11px; margin: 0;\">This product has expired</p>
                        </div>
                    @elseif($product->expires_in_days && $product->expires_in_days <= 30)
                        <div style="background: #fff3cd; border: 1px solid #ffeeba; color: #c87b16; padding: 12px; margin-bottom: 8px; border-radius: 0;">
                            <p style="font-size: 12px; font-weight: 600; margin: 0 0 4px 0;\">⏰ Expiring Soon</p>
                            <p style="font-size: 11px; margin: 0;\">{{ $product->expires_in_days }} days remaining</p>
                        </div>
                    @else
                        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #2f7d57; padding: 12px; border-radius: 0;">
                            <p style="font-size: 12px; font-weight: 600; margin: 0;\">✓ Healthy Stock</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
                    @if($auditLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Action</th>
                                        <th>User</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($auditLogs as $log)
                                        <tr>
                                            <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                            </td>
                                            <td>{{ $log->user->name ?? 'N/A' }}</td>
                                            <td>
                                                <small class="text-muted">{{ $log->reason }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $auditLogs->links() }}
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No audit logs available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
