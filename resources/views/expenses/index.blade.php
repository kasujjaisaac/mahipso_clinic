@extends('layouts.app')
@section('title', 'Expense Management')
@section('section', 'Finance')
@section('page_title', 'Expense Management')
@section('content')
<div class="panel">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Expense Register</h2>
            <p class="table-meta" style="font-size:0.95rem; color:#888;">Track and manage all clinic operational expenses.</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="primary-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Add New Expense</a>
    </div>

    <!-- Filters Section -->
    <div class="filters-section" style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #e2e8f0;">
        <form method="GET" action="{{ route('expenses.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label for="category" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Category</label>
                <select id="category" name="category" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; background: white;">
                    <option value="">All Categories</option>
                    <option value="utilities" {{ request('category') == 'utilities' ? 'selected' : '' }}>Utilities</option>
                    <option value="rent_lease" {{ request('category') == 'rent_lease' ? 'selected' : '' }}>Rent/Lease</option>
                    <option value="equipment" {{ request('category') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="supplies" {{ request('category') == 'supplies' ? 'selected' : '' }}>Supplies</option>
                    <option value="salaries" {{ request('category') == 'salaries' ? 'selected' : '' }}>Salaries</option>
                    <option value="insurance" {{ request('category') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                    <option value="marketing" {{ request('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                    <option value="professional_fees" {{ request('category') == 'professional_fees' ? 'selected' : '' }}>Professional Fees</option>
                    <option value="taxes" {{ request('category') == 'taxes' ? 'selected' : '' }}>Taxes</option>
                    <option value="loans" {{ request('category') == 'loans' ? 'selected' : '' }}>Loans</option>
                    <option value="maintenance" {{ request('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div>
                <label for="status" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Status</label>
                <select id="status" name="status" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; background: white;">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            @if(auth()->user()->isSuperAdmin())
            <div>
                <label for="branch_id" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Branch</label>
                <select id="branch_id" name="branch_id" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; background: white;">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

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
                <a href="{{ route('expenses.index') }}" class="ghost-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Expenses List -->
    <div class="expenses-grid" style="display: grid; gap: 1rem;">
        @forelse($expenses as $expense)
        <div class="expense-card" style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: #1f2937;">{{ $expense->description }}</h3>
                    <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.85rem;">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 1.1rem; font-weight: 600; color: #1f2937;">${{ number_format($expense->amount, 2) }}</div>
                    <span class="status-pill {{ $expense->status }}" style="font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 12px; font-weight: 500;">{{ ucfirst($expense->status) }}</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.75rem; margin-bottom: 0.75rem;">
                @if($expense->branch)
                <div>
                    <span style="color: #6b7280; font-size: 0.85rem;">Branch:</span>
                    <div style="font-weight: 500; color: #374151; font-size: 0.9rem;">{{ $expense->branch->name }}</div>
                </div>
                @endif

                @if($expense->vendor)
                <div>
                    <span style="color: #6b7280; font-size: 0.85rem;">Vendor:</span>
                    <div style="font-weight: 500; color: #374151; font-size: 0.9rem;">{{ $expense->vendor }}</div>
                </div>
                @endif

                @if($expense->paid_at)
                <div>
                    <span style="color: #6b7280; font-size: 0.85rem;">Paid Date:</span>
                    <div style="font-weight: 500; color: #374151; font-size: 0.9rem;">{{ $expense->paid_at->format('M d, Y') }}</div>
                </div>
                @endif

                @if($expense->due_at)
                <div>
                    <span style="color: #6b7280; font-size: 0.85rem;">Due Date:</span>
                    <div style="font-weight: 500; color: #374151; font-size: 0.9rem;">{{ $expense->due_at->format('M d, Y') }}</div>
                </div>
                @endif
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('expenses.show', $expense) }}" class="ghost-button" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">View</a>
                    <a href="{{ route('expenses.edit', $expense) }}" class="ghost-button" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Edit</a>
                </div>
                @if($expense->recurring)
                <span style="background: #dbeafe; color: #1e40af; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 12px; font-weight: 500;">Recurring</span>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state" style="text-align: center; padding: 3rem; background: white; border: 1px solid #e2e8f0; border-radius: 8px;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">💰</div>
            <h3 style="margin: 0 0 0.5rem 0; color: #374151;">No Expenses Found</h3>
            <p style="margin: 0; color: #6b7280;">Start tracking your clinic's operational expenses by adding your first expense.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($expenses->hasPages())
    <div style="margin-top: 2rem; text-align: center;">
        {{ $expenses->links() }}
    </div>
    @endif
</div>

<style>
    .status-pill.pending { background: #fef3c7; color: #92400e; }
    .status-pill.paid { background: #d1fae5; color: #065f46; }
    .status-pill.overdue { background: #fee2e2; color: #991b1b; }
    .status-pill.cancelled { background: #f3f4f6; color: #374151; }
    .expense-card:hover { box-shadow: 0 4px 6px rgba(0,0,0,0.1); transform: translateY(-2px); }
    .filters-section, .expense-card, .empty-state {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
    }
    .filters-section *, .expense-card *, .empty-state * {
        font-size: 11px !important;
    }
    @media (max-width: 768px) {
        .expenses-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection