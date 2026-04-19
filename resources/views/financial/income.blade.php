@extends('layouts.app')
@section('title', 'Income Management')
@section('section', 'Financial')
@section('page_title', 'Income Management')
@section('content')
<div class="panel">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Income Management</h2>
            <p class="table-meta" style="font-size:0.95rem; color:#888;">Track and manage all income sources.</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('billing.create') }}" class="primary-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Add Patient Bill</a>
            <a href="{{ route('financial.index') }}" class="ghost-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Back to Overview</a>
        </div>
    </div>

    <!-- Income Summary Cards -->
    <div class="income-summary" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="summary-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="font-size: 1.5rem;">🏥</div>
                <div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">Patient Billing</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">${{ number_format($bills->where('status', 'paid')->sum('paid'), 2) }}</div>
                </div>
            </div>
        </div>

        <div class="summary-card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 1rem; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="font-size: 1.5rem;">💊</div>
                <div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">Pharmacy Sales</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">$0.00</div>
                </div>
            </div>
        </div>

        <div class="summary-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; padding: 1rem; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="font-size: 1.5rem;">💰</div>
                <div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">Total Income</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">${{ number_format($bills->where('status', 'paid')->sum('paid'), 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section" style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #e2e8f0;">
        <form method="GET" action="{{ route('financial.income') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label for="status" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Status</label>
                <select id="status" name="status" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; background: white;">
                    <option value="">All Status</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label for="date_from" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">From Date</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <div>
                <label for="date_to" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">To Date</label>
                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="primary-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Apply Filters</button>
                <a href="{{ route('financial.income') }}" class="ghost-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Income Transactions -->
    <div class="income-transactions" style="background: white; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: #1f2937;">Income Transactions</h3>
        </div>

        <div class="transaction-list">
            @forelse($bills as $bill)
            <div class="transaction-item" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #f0f0f0; transition: background 0.2s;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="transaction-icon" style="width: 40px; height: 40px; border-radius: 50%; background: #10b981; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">🏥</div>
                    <div>
                        <div style="font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">
                            {{ $bill->patient->full_name ?? 'Patient' }}
                        </div>
                        <div style="font-size: 0.85rem; color: #6b7280;">
                            Bill #{{ $bill->id }} • {{ $bill->billed_at->format('M d, Y') }}
                            @if($bill->visit)
                                • Visit #{{ $bill->visit->id }}
                            @endif
                        </div>
                    </div>
                </div>

                <div style="text-align: right; display: flex; align-items: center; gap: 1rem;">
                    <div>
                        <div style="font-weight: 600; color: #10b981; font-size: 1.1rem;">
                            ${{ number_format($bill->paid, 2) }}
                        </div>
                        <div style="font-size: 0.8rem; color: #6b7280;">
                            of ${{ number_format($bill->amount, 2) }}
                        </div>
                    </div>

                    <span class="status-pill {{ $bill->status }}" style="font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 20px; font-weight: 500;">
                        {{ ucfirst($bill->status) }}
                    </span>

                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('billing.show', $bill) }}" class="ghost-button" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">View</a>
                        <a href="{{ route('billing.edit', $bill) }}" class="ghost-button" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Edit</a>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 3rem; color: #6b7280;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💰</div>
                <h3 style="margin: 0 0 0.5rem 0; color: #374151;">No Income Transactions</h3>
                <p style="margin: 0;">Start recording patient bills to track your income.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($bills->hasPages())
        <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; text-align: center;">
            {{ $bills->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .income-summary, .filters-section, .income-transactions {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
    }
    .transaction-item:hover {
        background: #f8fafc;
    }
    .status-pill.unpaid { background: #fef3c7; color: #92400e; }
    .status-pill.partial { background: #dbeafe; color: #1e40af; }
    .status-pill.paid { background: #d1fae5; color: #065f46; }
    .status-pill.cancelled { background: #f3f4f6; color: #374151; }
</style>
@endsection