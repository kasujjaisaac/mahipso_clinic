@extends('layouts.app')
@section('title', 'Edit Expense')
@section('section', 'Finance')
@section('page_title', 'Edit Expense')
@section('content')
<div class="panel">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Edit Expense</h2>
        <p class="table-meta" style="font-size:0.95rem; color:#888;">Update expense information.</p>
    </div>

    <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data" style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
        @csrf
        @method('PUT')

        <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">

            <!-- Branch Selection (Super Admin Only) -->
            @if(auth()->user()->isSuperAdmin())
            <div class="form-group">
                <label for="branch_id" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Branch <span style="color: #ef4444;">*</span></label>
                <select id="branch_id" name="branch_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $expense->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
                <input type="hidden" name="branch_id" value="{{ $expense->branch_id ?: auth()->user()->currentBranch()->id }}">
            @endif

            <!-- Category -->
            <div class="form-group">
                <label for="category" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Category <span style="color: #ef4444;">*</span></label>
                <select id="category" name="category" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="">Select Category</option>
                    <option value="utilities" {{ $expense->category == 'utilities' ? 'selected' : '' }}>Utilities</option>
                    <option value="rent_lease" {{ $expense->category == 'rent_lease' ? 'selected' : '' }}>Rent/Lease</option>
                    <option value="equipment" {{ $expense->category == 'equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="supplies" {{ $expense->category == 'supplies' ? 'selected' : '' }}>Supplies</option>
                    <option value="salaries" {{ $expense->category == 'salaries' ? 'selected' : '' }}>Salaries</option>
                    <option value="insurance" {{ $expense->category == 'insurance' ? 'selected' : '' }}>Insurance</option>
                    <option value="marketing" {{ $expense->category == 'marketing' ? 'selected' : '' }}>Marketing</option>
                    <option value="professional_fees" {{ $expense->category == 'professional_fees' ? 'selected' : '' }}>Professional Fees</option>
                    <option value="taxes" {{ $expense->category == 'taxes' ? 'selected' : '' }}>Taxes</option>
                    <option value="loans" {{ $expense->category == 'loans' ? 'selected' : '' }}>Loans</option>
                    <option value="maintenance" {{ $expense->category == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="other" {{ $expense->category == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Subcategory -->
            <div class="form-group">
                <label for="subcategory" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Subcategory</label>
                <input type="text" id="subcategory" name="subcategory" value="{{ $expense->subcategory }}" placeholder="e.g., Electricity, Water, Internet" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Amount -->
            <div class="form-group">
                <label for="amount" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Amount ($) <span style="color: #ef4444;">*</span></label>
                <input type="number" id="amount" name="amount" step="0.01" min="0" value="{{ $expense->amount }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Vendor -->
            <div class="form-group">
                <label for="vendor" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Vendor/Supplier</label>
                <input type="text" id="vendor" name="vendor" value="{{ $expense->vendor }}" placeholder="e.g., Kenya Power, KPLC" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Invoice Number -->
            <div class="form-group">
                <label for="invoice_number" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Invoice/Receipt Number</label>
                <input type="text" id="invoice_number" name="invoice_number" value="{{ $expense->invoice_number }}" placeholder="e.g., INV-2026-001" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Payment Method -->
            <div class="form-group">
                <label for="payment_method" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Payment Method</label>
                <select id="payment_method" name="payment_method" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="">Select Payment Method</option>
                    <option value="cash" {{ $expense->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank_transfer" {{ $expense->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="check" {{ $expense->payment_method == 'check' ? 'selected' : '' }}>Check</option>
                    <option value="credit_card" {{ $expense->payment_method == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                    <option value="debit_card" {{ $expense->payment_method == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                </select>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Status <span style="color: #ef4444;">*</span></label>
                <select id="status" name="status" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="pending" {{ $expense->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $expense->status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ $expense->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="cancelled" {{ $expense->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Paid Date -->
            <div class="form-group">
                <label for="paid_at" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Payment Date</label>
                <input type="date" id="paid_at" name="paid_at" value="{{ $expense->paid_at ? $expense->paid_at->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Due Date -->
            <div class="form-group">
                <label for="due_at" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Due Date</label>
                <input type="date" id="due_at" name="due_at" value="{{ $expense->due_at ? $expense->due_at->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>
        </div>

        <!-- Description (Full Width) -->
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label for="description" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Description <span style="color: #ef4444;">*</span></label>
            <textarea id="description" name="description" rows="3" required placeholder="Detailed description of the expense..." style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; resize: vertical;">{{ $expense->description }}</textarea>
        </div>

        <!-- Recurring Expense -->
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500; color: #374151;">
                <input type="checkbox" id="recurring" name="recurring" value="1" {{ $expense->recurring ? 'checked' : '' }} style="width: auto;">
                This is a recurring expense
            </label>
        </div>

        <!-- Frequency (shown when recurring is checked) -->
        <div class="form-group" id="frequency-group" style="margin-bottom: 1.5rem; {{ $expense->recurring ? 'display: block;' : 'display: none;' }}">
            <label for="frequency" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Frequency</label>
            <select id="frequency" name="frequency" style="width: 100%; max-width: 250px; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                <option value="daily" {{ $expense->frequency == 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="weekly" {{ $expense->frequency == 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ $expense->frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="quarterly" {{ $expense->frequency == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                <option value="yearly" {{ $expense->frequency == 'yearly' ? 'selected' : '' }}>Yearly</option>
            </select>
        </div>

        <!-- Receipt Upload -->
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label for="receipt" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Receipt/Invoice (Optional)</label>
            @if($expense->receipt_path)
                <div style="margin-bottom: 0.5rem;">
                    <small style="color: #059669;">Current file: {{ basename($expense->receipt_path) }}</small>
                </div>
            @endif
            <input type="file" id="receipt" name="receipt" accept=".pdf,.jpg,.jpeg,.png" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            <small style="color: #6b7280; font-size: 0.8rem;">Leave empty to keep current file. Accepted formats: PDF, JPG, PNG. Max size: 5MB</small>
        </div>

        <!-- Notes -->
        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="notes" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Additional Notes</label>
            <textarea id="notes" name="notes" rows="2" placeholder="Any additional notes or comments..." style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; resize: vertical;">{{ $expense->notes }}</textarea>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('expenses.show', $expense) }}" class="ghost-button" style="padding: 0.75rem 1.5rem;">Cancel</a>
            <button type="submit" class="primary-button" style="padding: 0.75rem 1.5rem;">Update Expense</button>
        </div>
    </form>
</div>

<style>
    .panel, .form-section, .section-header {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
    }
    .panel *, .form-section *, .section-header * {
        font-size: 11px !important;
    }
</style>

<script>
document.getElementById('recurring').addEventListener('change', function() {
    document.getElementById('frequency-group').style.display = this.checked ? 'block' : 'none';
    document.getElementById('frequency').required = this.checked;
});

// Initialize frequency field requirement based on current state
document.addEventListener('DOMContentLoaded', function() {
    const recurringCheckbox = document.getElementById('recurring');
    const frequencyField = document.getElementById('frequency');
    frequencyField.required = recurringCheckbox.checked;
});
</script>
@endsection