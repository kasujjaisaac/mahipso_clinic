@extends('layouts.app')
@section('title', 'Add New Expense')
@section('section', 'Finance')
@section('page_title', 'Add New Expense')
@section('content')
<div class="panel">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Add New Expense</h2>
        <p class="table-meta" style="font-size:0.95rem; color:#888;">Record a new operational expense for the clinic.</p>
    </div>

    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
        @csrf

        <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">

            <!-- Branch Selection (Super Admin Only) -->
            @if(auth()->user()->isSuperAdmin())
            <div class="form-group">
                <label for="branch_id" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Branch <span style="color: #ef4444;">*</span></label>
                <select id="branch_id" name="branch_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
                <input type="hidden" name="branch_id" value="{{ auth()->user()->currentBranch()->id }}">
            @endif

            <!-- Category -->
            <div class="form-group">
                <label for="category" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Category <span style="color: #ef4444;">*</span></label>
                <select id="category" name="category" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="">Select Category</option>
                    <option value="utilities">Utilities</option>
                    <option value="rent_lease">Rent/Lease</option>
                    <option value="equipment">Equipment</option>
                    <option value="supplies">Supplies</option>
                    <option value="salaries">Salaries</option>
                    <option value="insurance">Insurance</option>
                    <option value="marketing">Marketing</option>
                    <option value="professional_fees">Professional Fees</option>
                    <option value="taxes">Taxes</option>
                    <option value="loans">Loans</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <!-- Subcategory -->
            <div class="form-group">
                <label for="subcategory" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Subcategory</label>
                <input type="text" id="subcategory" name="subcategory" placeholder="e.g., Electricity, Water, Internet" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Amount -->
            <div class="form-group">
                <label for="amount" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Amount ($) <span style="color: #ef4444;">*</span></label>
                <input type="number" id="amount" name="amount" step="0.01" min="0" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Vendor -->
            <div class="form-group">
                <label for="vendor" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Vendor/Supplier</label>
                <input type="text" id="vendor" name="vendor" placeholder="e.g., Kenya Power, KPLC" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Invoice Number -->
            <div class="form-group">
                <label for="invoice_number" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Invoice/Receipt Number</label>
                <input type="text" id="invoice_number" name="invoice_number" placeholder="e.g., INV-2026-001" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Payment Method -->
            <div class="form-group">
                <label for="payment_method" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Payment Method</label>
                <select id="payment_method" name="payment_method" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="">Select Payment Method</option>
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="check">Check</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="debit_card">Debit Card</option>
                </select>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Status <span style="color: #ef4444;">*</span></label>
                <select id="status" name="status" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Paid Date -->
            <div class="form-group">
                <label for="paid_at" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Payment Date</label>
                <input type="date" id="paid_at" name="paid_at" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Due Date -->
            <div class="form-group">
                <label for="due_at" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Due Date</label>
                <input type="date" id="due_at" name="due_at" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>
        </div>

        <!-- Description (Full Width) -->
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label for="description" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Description <span style="color: #ef4444;">*</span></label>
            <textarea id="description" name="description" rows="3" required placeholder="Detailed description of the expense..." style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; resize: vertical;"></textarea>
        </div>

        <!-- Recurring Expense -->
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500; color: #374151;">
                <input type="checkbox" id="recurring" name="recurring" value="1" style="width: auto;">
                This is a recurring expense
            </label>
        </div>

        <!-- Frequency (shown when recurring is checked) -->
        <div class="form-group" id="frequency-group" style="margin-bottom: 1.5rem; display: none;">
            <label for="frequency" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Frequency</label>
            <select id="frequency" name="frequency" style="width: 100%; max-width: 250px; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>

        <!-- Receipt Upload -->
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label for="receipt" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Receipt/Invoice (Optional)</label>
            <input type="file" id="receipt" name="receipt" accept=".pdf,.jpg,.jpeg,.png" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            <small style="color: #6b7280; font-size: 0.8rem;">Accepted formats: PDF, JPG, PNG. Max size: 5MB</small>
        </div>

        <!-- Notes -->
        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="notes" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Additional Notes</label>
            <textarea id="notes" name="notes" rows="2" placeholder="Any additional notes or comments..." style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; resize: vertical;"></textarea>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('expenses.index') }}" class="ghost-button" style="padding: 0.75rem 1.5rem;">Cancel</a>
            <button type="submit" class="primary-button" style="padding: 0.75rem 1.5rem;">Save Expense</button>
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
</script>
@endsection