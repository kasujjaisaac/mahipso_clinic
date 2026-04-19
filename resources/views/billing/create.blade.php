@extends('layouts.app')
@section('title', 'Create Patient Bill')
@section('section', 'Financial')
@section('page_title', 'Create Patient Bill')
@section('content')
<div class="panel">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Create Patient Bill</h2>
        <p class="table-meta" style="font-size:0.95rem; color:#888;">Create a new bill for patient services.</p>
    </div>

    <form action="{{ route('billing.store') }}" method="POST" style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
        @csrf

        <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">

            <!-- Patient Selection -->
            <div class="form-group">
                <label for="patient_id" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Patient <span style="color: #ef4444;">*</span></label>
                <select id="patient_id" name="patient_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="">Select Patient</option>
                    @foreach(\App\Models\Patient::all() as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->full_name }} ({{ $patient->mrn }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Visit Selection (Optional) -->
            <div class="form-group">
                <label for="visit_id" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Related Visit</label>
                <select id="visit_id" name="visit_id" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="">Select Visit (Optional)</option>
                    <!-- This would be populated dynamically based on selected patient -->
                </select>
            </div>

            <!-- Amount -->
            <div class="form-group">
                <label for="amount" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Bill Amount ($) <span style="color: #ef4444;">*</span></label>
                <input type="number" id="amount" name="amount" step="0.01" min="0" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Amount Paid -->
            <div class="form-group">
                <label for="paid" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Amount Paid ($)</label>
                <input type="number" id="paid" name="paid" step="0.01" min="0" value="0" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Status <span style="color: #ef4444;">*</span></label>
                <select id="status" name="status" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="unpaid">Unpaid</option>
                    <option value="partial">Partial</option>
                    <option value="paid">Paid</option>
                    <option value="cancelled">Cancelled</option>
                </select>
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
                    <option value="insurance">Insurance</option>
                </select>
            </div>

            <!-- Billed Date -->
            <div class="form-group">
                <label for="billed_at" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Bill Date <span style="color: #ef4444;">*</span></label>
                <input type="date" id="billed_at" name="billed_at" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Due Date -->
            <div class="form-group">
                <label for="due_at" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Due Date</label>
                <input type="date" id="due_at" name="due_at" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Insurance Claim Number -->
            <div class="form-group">
                <label for="insurance_claim_no" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Insurance Claim Number</label>
                <input type="text" id="insurance_claim_no" name="insurance_claim_no" placeholder="e.g., INS-2026-001" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>
        </div>

        <!-- Notes -->
        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="notes" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Notes</label>
            <textarea id="notes" name="notes" rows="3" placeholder="Additional notes about this bill..." style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; resize: vertical;"></textarea>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('billing.index') }}" class="ghost-button" style="padding: 0.75rem 1.5rem;">Cancel</a>
            <button type="submit" class="primary-button" style="padding: 0.75rem 1.5rem;">Create Bill</button>
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
// Auto-populate due date (30 days from bill date)
document.getElementById('billed_at').addEventListener('change', function() {
    const billDate = new Date(this.value);
    if (billDate) {
        const dueDate = new Date(billDate);
        dueDate.setDate(dueDate.getDate() + 30);
        document.getElementById('due_at').value = dueDate.toISOString().split('T')[0];
    }
});

// Update amount paid when status changes to paid
document.getElementById('status').addEventListener('change', function() {
    if (this.value === 'paid') {
        const amount = document.getElementById('amount').value;
        if (amount) {
            document.getElementById('paid').value = amount;
        }
    }
});
</script>
@endsection