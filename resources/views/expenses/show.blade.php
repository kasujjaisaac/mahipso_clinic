@extends('layouts.app')
@section('title', 'Expense Details')
@section('section', 'Finance')
@section('page_title', 'Expense Details')
@section('content')
<div class="panel">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Expense Details</h2>
            <p class="table-meta" style="font-size:0.95rem; color:#888;">View complete expense information.</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('expenses.edit', $expense) }}" class="primary-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Edit Expense</a>
            <a href="{{ route('expenses.index') }}" class="ghost-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Back to List</a>
        </div>
    </div>

    <div class="expense-details" style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem;">

        <!-- Header Section -->
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0;">
            <div>
                <h3 style="margin: 0; font-size: 1.5rem; font-weight: 600; color: #1f2937;">{{ $expense->description }}</h3>
                <p style="margin: 0.5rem 0 0 0; color: #6b7280; font-size: 1rem;">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}
                    @if($expense->subcategory) • {{ $expense->subcategory }} @endif
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;">${{ number_format($expense->amount, 2) }}</div>
                <span class="status-pill {{ $expense->status }}" style="font-size: 0.9rem; padding: 0.4rem 0.8rem; border-radius: 20px; font-weight: 500;">{{ ucfirst($expense->status) }}</span>
            </div>
        </div>

        <!-- Details Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">

            @if($expense->branch)
            <div class="detail-item">
                <label style="font-weight: 500; color: #374151; margin-bottom: 0.25rem; display: block;">Branch</label>
                <div style="color: #1f2937; font-size: 1rem;">{{ $expense->branch->name }}</div>
            </div>
            @endif

            <div class="detail-item">
                <label style="font-weight: 500; color: #374151; margin-bottom: 0.25rem; display: block;">Category</label>
                <div style="color: #1f2937; font-size: 1rem;">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</div>
            </div>

            @if($expense->subcategory)
            <div class="detail-item">
                <label style="font-weight: 500; color: #374151; margin-bottom: 0.25rem; display: block;">Subcategory</label>
                <div style="color: #1f2937; font-size: 1rem;">{{ $expense->subcategory }}</div>
            </div>
            @endif

            @if($expense->vendor)
            <div class="detail-item">
                <label style="font-weight: 500; color: #374151; margin-bottom: 0.25rem; display: block;">Vendor/Supplier</label>
                <div style="color: #1f2937; font-size: 1rem;">{{ $expense->vendor }}</div>
            </div>
            @endif

            @if($expense->invoice_number)
            <div class="detail-item">
                <label style="font-weight: 500; color: #374151; margin-bottom: 0.25rem; display: block;">Invoice Number</label>
                <div style="color: #1f2937; font-size: 1rem;">{{ $expense->invoice_number }}</div>
            </div>
            @endif

            @if($expense->payment_method)
            <div class="detail-item">
                <label style="font-weight: 500; color: #374151; margin-bottom: 0.25rem; display: block;">Payment Method</label>
                <div style="color: #1f2937; font-size: 1rem;">{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</div>
            </div>
            @endif

            @if($expense->paid_at)
            <div class="detail-item">
                <label style="font-weight: 500; color: #374151; margin-bottom: 0.25rem; display: block;">Payment Date</label>
                <div style="color: #1f2937; font-size: 1rem;">{{ $expense->paid_at->format('F d, Y') }}</div>
            </div>
            @endif

            @if($expense->due_at)
            <div class="detail-item">
                <label style="font-weight: 500; color: #374151; margin-bottom: 0.25rem; display: block;">Due Date</label>
                <div style="color: #1f2937; font-size: 1rem;">{{ $expense->due_at->format('F d, Y') }}</div>
            </div>
            @endif

            <div class="detail-item">
                <label style="font-weight: 500; color: #374151; margin-bottom: 0.25rem; display: block;">Recurring</label>
                <div style="color: #1f2937; font-size: 1rem;">
                    @if($expense->recurring)
                        <span style="color: #059669;">Yes</span> ({{ ucfirst($expense->frequency) }})
                    @else
                        <span style="color: #6b7280;">No</span>
                    @endif
                </div>
            </div>

            <div class="detail-item">
                <label style="font-weight: 500; color: #374151; margin-bottom: 0.25rem; display: block;">Created</label>
                <div style="color: #1f2937; font-size: 1rem;">{{ $expense->created_at->format('F d, Y \a\t g:i A') }}</div>
            </div>

        </div>

        <!-- Description -->
        @if($expense->notes || strlen($expense->description) > 100)
        <div style="margin-bottom: 2rem;">
            <label style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Description</label>
            <div style="color: #1f2937; font-size: 1rem; line-height: 1.5; background: #f8fafc; padding: 1rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                {{ $expense->description }}
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($expense->notes)
        <div style="margin-bottom: 2rem;">
            <label style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Additional Notes</label>
            <div style="color: #1f2937; font-size: 1rem; line-height: 1.5; background: #f8fafc; padding: 1rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                {{ $expense->notes }}
            </div>
        </div>
        @endif

        <!-- Receipt -->
        @if($expense->receipt_path)
        <div style="margin-bottom: 2rem;">
            <label style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Receipt/Invoice</label>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="primary-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View Receipt</a>
                <span style="color: #6b7280; font-size: 0.9rem;">{{ basename($expense->receipt_path) }}</span>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;" onclick="return confirm('Are you sure you want to delete this expense?')">Delete Expense</button>
            </form>
        </div>
    </div>
</div>

<style>
    .status-pill.pending { background: #fef3c7; color: #92400e; }
    .status-pill.paid { background: #d1fae5; color: #065f46; }
    .status-pill.overdue { background: #fee2e2; color: #991b1b; }
    .status-pill.cancelled { background: #f3f4f6; color: #374151; }
    .panel, .expense-details {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
    }
    .panel *, .expense-details * {
        font-size: 11px !important;
    }
    .danger-button {
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    .danger-button:hover {
        background: #b91c1c;
    }
</style>
@endsection