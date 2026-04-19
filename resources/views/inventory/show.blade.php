@extends('layouts.app')

@php
    use App\Models\Inventory;
@endphp

@section('title', 'Asset Details')
@section('section', 'Inventory')
@section('page_title', $inventory->item_name)

@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <!-- Header Section -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">📦 {{ $inventory->item_name }}</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">Asset details and lifecycle history from receipt through disposal.</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('inventory.edit', $inventory) }}" style="background: #7c3aed; color: white; padding: 8px 16px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block;">✎ Edit Asset</a>
                <a href="{{ route('inventory.index') }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;">← Back to Assets</a>
            </div>
        </div>
    </div>

    <!-- Asset Details -->
    <div style="background: #fff; border: 1px solid #f0d4d1; margin-bottom: 24px;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;">Asset Information</p>
        </div>
        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
                <div>
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Status</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">
                        @if($inventory->status === Inventory::STATUS_DISPOSED)
                            <span style="display: inline-block; background: #dc3545; color: white; padding: 4px 8px; font-size: 11px; border-radius: 2px;">Disposed</span>
                        @elseif($inventory->status === Inventory::STATUS_ASSIGNED)
                            <span style="display: inline-block; background: #7c3aed; color: white; padding: 4px 8px; font-size: 11px; border-radius: 2px;">Assigned</span>
                        @else
                            <span style="display: inline-block; background: #2f6fed; color: white; padding: 4px 8px; font-size: 11px; border-radius: 2px;">In Store</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Category</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $inventory->category ?: 'Unspecified' }}</p>
                </div>
                <div>
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">SKU</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $inventory->sku ?: 'None' }}</p>
                </div>
                <div>
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Quantity</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $inventory->quantity }}</p>
                </div>
                <div>
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Location</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $inventory->location ?: 'Not set' }}</p>
                </div>
                <div>
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Assigned To</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ optional($inventory->assignedTo)->name ?: 'Not assigned' }}</p>
                </div>
                <div>
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Purchased</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ optional($inventory->purchase_date)->format('Y-m-d') ?: 'Unknown' }}</p>
                </div>
                <div>
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Expiry</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ optional($inventory->expiry_date)->format('Y-m-d') ?: 'N/A' }}</p>
                </div>
                <div>
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Supplier</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $inventory->supplier ?: 'Unknown' }}</p>
                </div>
                <div>
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Unit Price</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $inventory->unit_price ? 'USh ' . number_format($inventory->unit_price, 2) : 'N/A' }}</p>
                </div>
                @if($inventory->status === Inventory::STATUS_DISPOSED)
                    <div>
                        <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Disposed By</p>
                        <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ optional($inventory->disposedBy)->name ?: 'Unknown' }}</p>
                    </div>
                    <div>
                        <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Disposed At</p>
                        <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ optional($inventory->disposed_at)->format('Y-m-d H:i') }}</p>
                    </div>
                @endif
            </div>

            @if($inventory->notes)
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #f0d4d1;">
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Notes</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $inventory->notes }}</p>
                </div>
            @endif

            @if($inventory->status === Inventory::STATUS_DISPOSED && $inventory->disposal_reason)
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #f0d4d1;">
                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Disposal Reason</p>
                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $inventory->disposal_reason }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Active Actions -->
    @if($inventory->status !== Inventory::STATUS_DISPOSED)
        <div style="background: #fff; border: 1px solid #f0d4d1; margin-bottom: 24px;">
            <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;">Active Actions</p>
            </div>
            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                    <!-- Assign to Staff -->
                    <div style="border: 1px solid #f0d4d1; padding: 20px;">
                        <h3 style="color: #222; font-size: 16px; font-weight: 600; margin: 0 0 16px 0;">👤 Assign to Staff</h3>
                        <form method="POST" action="{{ route('inventory.update', $inventory) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="assign">
                            <div style="margin-bottom: 12px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 4px;">Staff Member <span style="color: #b8342b;">*</span></label>
                                <select name="assigned_to" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" required>
                                    <option value="">Select staff member</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 4px;">Location</label>
                                <input type="text" name="location" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ $inventory->location }}">
                            </div>
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 4px;">Notes</label>
                                <textarea name="notes" rows="3" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" placeholder="Optional assignment note"></textarea>
                            </div>
                            <button type="submit" style="background: #7c3aed; color: white; padding: 10px 16px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; width: 100%;">Assign Asset</button>
                        </form>
                    </div>

                    <!-- Return to Store -->
                    @if($inventory->status === Inventory::STATUS_ASSIGNED)
                        <div style="border: 1px solid #f0d4d1; padding: 20px;">
                            <h3 style="color: #222; font-size: 16px; font-weight: 600; margin: 0 0 16px 0;">🏪 Return to Store</h3>
                            <form method="POST" action="{{ route('inventory.update', $inventory) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="return">
                                <div style="margin-bottom: 12px;">
                                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 4px;">Location</label>
                                    <input type="text" name="location" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" value="{{ $inventory->location }}">
                                </div>
                                <div style="margin-bottom: 16px;">
                                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 4px;">Notes</label>
                                    <textarea name="notes" rows="3" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" placeholder="Reason for returning to store"></textarea>
                                </div>
                                <button type="submit" style="background: #2f6fed; color: white; padding: 10px 16px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; width: 100%;">Return Asset</button>
                            </form>
                        </div>
                    @endif

                    <!-- Dispose Asset -->
                    <div style="border: 1px solid #dc3545; padding: 20px;">
                        <h3 style="color: #dc3545; font-size: 16px; font-weight: 600; margin: 0 0 16px 0;">🗑️ Dispose Asset</h3>
                        <form method="POST" action="{{ route('inventory.update', $inventory) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="dispose">
                            <div style="margin-bottom: 12px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 4px;">Disposal Reason <span style="color: #b8342b;">*</span></label>
                                <textarea name="disposal_reason" rows="3" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" required placeholder="Why this asset is being disposed"></textarea>
                            </div>
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 4px;">Notes</label>
                                <textarea name="notes" rows="3" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" placeholder="Optional disposal note"></textarea>
                            </div>
                            <button type="submit" style="background: #dc3545; color: white; padding: 10px 16px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; width: 100%;">Dispose Asset</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div style="background: #fff; border: 1px solid #f0d4d1; margin-bottom: 24px;">
            <div style="padding: 24px; text-align: center;">
                <p style="color: #888; font-size: 14px; margin: 0;">This asset is disposed and no longer active.</p>
            </div>
        </div>
    @endif

    <!-- Movement History -->
    <div style="background: #fff; border: 1px solid #f0d4d1;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;">Movement History</p>
        </div>
        <div class="table-responsive" style="border: none;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;">
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Date</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Action</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Performed By</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Assigned To</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Location</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory->movements as $movement)
                        <tr style="border-bottom: 1px solid #f0d4d1;">
                            <td style="padding: 12px 16px; color: #222; border: none;">{{ optional($movement->performed_at)->format('Y-m-d H:i') }}</td>
                            <td style="padding: 12px 16px; color: #222; border: none;">{{ ucfirst(str_replace('_', ' ', $movement->action)) }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ optional($movement->user)->name ?? 'System' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ optional($movement->assignedTo)->name ?: '—' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ $movement->location ?: '—' }}</td>
                            <td style="padding: 12px 16px; color: #666; border: none;">{{ $movement->notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center; color: #888; border: none;">
                                No movements have been recorded for this asset yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
