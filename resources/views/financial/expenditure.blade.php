@extends('layouts.app')
@section('title', 'Expenditure Management')
@section('section', 'Financial')
@section('page_title', 'Expenditure Management')
@section('content')
<div class="panel">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Expenditure Management</h2>
            <p class="table-meta" style="font-size:0.95rem; color:#888;">Track and manage all operational expenses.</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('expenses.create') }}" class="primary-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Add New Expense</a>
            <a href="{{ route('financial.index') }}" class="ghost-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Back to Overview</a>
        </div>
    </div>

    <!-- Expenditure Summary Cards -->
    <div class="expenditure-summary" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="summary-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 1rem; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="font-size: 1.5rem;">💸</div>
                <div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">Total Expenses</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">${{ number_format($expenses->where('status', 'paid')->sum('amount'), 2) }}</div>
                </div>
            </div>
        </div>

        <div class="summary-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 1rem; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="font-size: 1.5rem;">⏰</div>
                <div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">Pending Expenses</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">${{ number_format($expenses->where('status', 'pending')->sum('amount'), 2) }}</div>
                </div>
            </div>
        </div>

        <div class="summary-card" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; padding: 1rem; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="font-size: 1.5rem;">📊</div>
                <div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">This Month</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">${{ number_format($expenses->where('status', 'paid')->where('paid_at', '>=', now()->startOfMonth())->sum('amount'), 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section" style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #e2e8f0;">
        <form method="GET" action="{{ route('financial.expenditure') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
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
                <a href="{{ route('financial.expenditure') }}" class="ghost-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Expenditure Transactions -->
    <div class="expenditure-transactions" style="background: white; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: #1f2937;">Expenditure Transactions</h3>
        </div>

        <div class="transaction-list">
            @forelse($expenses as $expense)
            <div class="transaction-item" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #f0f0f0; transition: background 0.2s;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="transaction-icon" style="width: 40px; height: 40px; border-radius: 50%; background: #ef4444; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                        @switch($expense->category)
                            @case('utilities') 💡 @break
                            @case('rent_lease') 🏢 @break
                            @case('equipment') 🔧 @break
                            @case('supplies') 📦 @break
                            @case('salaries') 👥 @break
                            @case('insurance') 🛡️ @break
                            @case('marketing') 📢 @break
                            @case('professional_fees') 💼 @break
                            @case('taxes') 📋 @break
                            @case('loans') 💳 @break
                            @case('maintenance') 🔨 @break
                            @default 🏷️
                        @endswitch
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">
                            {{ $expense->description }}
                        </div>
                        <div style="font-size: 0.85rem; color: #6b7280;">
                            {{ ucfirst(str_replace('_', ' ', $expense->category)) }}
                            @if($expense->subcategory) • {{ $expense->subcategory }} @endif
                            @if($expense->paid_at) • {{ $expense->paid_at->format('M d, Y') }} @endif
                        </div>
                    </div>
                </div>

                <div style="text-align: right; display: flex; align-items: center; gap: 1rem;">
                    <div>
                        <div style="font-weight: 600; color: #ef4444; font-size: 1.1rem;">
                            ${{ number_format($expense->amount, 2) }}
                        </div>
                        <div style="font-size: 0.8rem; color: #6b7280;">
                            {{ $expense->vendor ? $expense->vendor : 'No vendor' }}
                        </div>
                    </div>

                    <span class="status-pill {{ $expense->status }}" style="font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 20px; font-weight: 500;">
                        {{ ucfirst($expense->status) }}
                    </span>

                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('expenses.show', $expense) }}" class="ghost-button" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">View</a>
                        <a href="{{ route('expenses.edit', $expense) }}" class="ghost-button" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Edit</a>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 3rem; color: #6b7280;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💸</div>
                <h3 style="margin: 0 0 0.5rem 0; color: #374151;">No Expenditure Transactions</h3>
                <p style="margin: 0;">Start recording operational expenses to track your costs.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($expenses->hasPages())
        <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; text-align: center;">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .expenditure-summary, .filters-section, .expenditure-transactions {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
    }
    .transaction-item:hover {
        background: #f8fafc;
    }
    .status-pill.pending { background: #fef3c7; color: #92400e; }
    .status-pill.paid { background: #d1fae5; color: #065f46; }
    .status-pill.overdue { background: #fee2e2; color: #991b1b; }
    .status-pill.cancelled { background: #f3f4f6; color: #374151; }
</style>
@endsection