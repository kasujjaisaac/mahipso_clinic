@extends('layouts.app')
@section('title', 'Billing & Invoicing')
@section('section', 'Financial')
@section('page_title', 'Billing & Invoicing')
@section('content')
<div class="panel">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Patient Bills</h2>
        <p class="table-meta" style="font-size:0.95rem; color:#888;">Manage patient billing and invoicing.</p>
    </div>

    <!-- Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 1rem; align-items: center;">
            <!-- Search -->
            <div style="position: relative;">
                <input type="text" placeholder="Search bills..." style="padding: 0.5rem 2.5rem 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; width: 250px;">
                <i class="fas fa-search" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
            </div>

            <!-- Filter -->
            <select style="padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                <option value="">All Status</option>
                <option value="unpaid">Unpaid</option>
                <option value="partial">Partial</option>
                <option value="paid">Paid</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <a href="{{ route('billing.create') }}" class="primary-button" style="padding: 0.5rem 1rem;">
            <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>
            Create Bill
        </a>
    </div>

    <!-- Bills Table -->
    <div style="background: white; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                <tr>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.9rem;">Bill #</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.9rem;">Patient</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.9rem;">Amount</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.9rem;">Paid</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.9rem;">Balance</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.9rem;">Status</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.9rem;">Date</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.9rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr style="border-bottom: 1px solid #f3f4f6; hover: background-color: #f9fafb;">
                    <td style="padding: 1rem; font-size: 0.9rem; color: #374151;">#{{ $bill->id }}</td>
                    <td style="padding: 1rem; font-size: 0.9rem; color: #374151;">
                        <div>
                            <div style="font-weight: 500;">{{ $bill->patient->full_name }}</div>
                            <div style="color: #6b7280; font-size: 0.8rem;">MRN: {{ $bill->patient->mrn }}</div>
                        </div>
                    </td>
                    <td style="padding: 1rem; font-size: 0.9rem; color: #374151; font-weight: 500;">${{ number_format($bill->amount, 2) }}</td>
                    <td style="padding: 1rem; font-size: 0.9rem; color: #059669; font-weight: 500;">${{ number_format($bill->paid, 2) }}</td>
                    <td style="padding: 1rem; font-size: 0.9rem; color: {{ $bill->balance > 0 ? '#dc2626' : '#059669' }}; font-weight: 500;">
                        ${{ number_format($bill->balance, 2) }}
                    </td>
                    <td style="padding: 1rem;">
                        <span class="status-badge status-{{ $bill->status }}" style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 500; text-transform: uppercase;">
                            {{ $bill->status }}
                        </span>
                    </td>
                    <td style="padding: 1rem; font-size: 0.9rem; color: #6b7280;">
                        {{ $bill->billed_at->format('M d, Y') }}
                    </td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('billing.show', $bill) }}" class="ghost-button" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">View</a>
                            <a href="{{ route('billing.edit', $bill) }}" class="ghost-button" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 3rem; text-align: center; color: #6b7280;">
                        <div style="margin-bottom: 1rem;">
                            <i class="fas fa-receipt" style="font-size: 3rem; color: #d1d5db;"></i>
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">No bills found</div>
                        <div style="font-size: 0.9rem;">Get started by creating your first patient bill.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($bills->hasPages())
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $bills->links() }}
    </div>
    @endif
</div>

<style>
    .panel, .form-section, .section-header {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
    }
    .panel *, .form-section *, .section-header * {
        font-size: 11px !important;
    }

    .status-badge {
        font-size: 0.75rem;
    }

    .status-unpaid { background-color: #fef2f2; color: #dc2626; }
    .status-partial { background-color: #fef3c7; color: #d97706; }
    .status-paid { background-color: #d1fae5; color: #059669; }
    .status-cancelled { background-color: #f3f4f6; color: #6b7280; }

    .primary-button {
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .primary-button:hover {
        background-color: #2563eb;
    }

    .ghost-button {
        background-color: transparent;
        color: #6b7280;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }

    .ghost-button:hover {
        background-color: #f9fafb;
        border-color: #9ca3af;
    }

    tr:hover {
        background-color: #f9fafb !important;
    }
</style>

<script>
// Add Font Awesome if not already included
if (!document.querySelector('link[href*="font-awesome"]')) {
    const faLink = document.createElement('link');
    faLink.rel = 'stylesheet';
    faLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
    document.head.appendChild(faLink);
}
</script>
@endsection
