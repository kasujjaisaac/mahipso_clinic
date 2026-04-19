@extends('layouts.app')
@section('title', 'Edit Patient Bill')
@section('section', 'Financial')
@section('page_title', 'Edit Patient Bill')
@section('content')
<div class="panel">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Edit Patient Bill</h2>
        <p class="table-meta" style="font-size:0.95rem; color:#888;">Bill #{{ $bill->id }} - {{ $bill->patient->full_name }}</p>
    </div>

    <form action="{{ route('billing.update', $bill) }}" method="POST" style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
        @csrf
        @method('PUT')

        <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">

            <!-- Patient Selection -->
            <div class="form-group">
                <label for="patient_id" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Patient <span style="color: #ef4444;">*</span></label>
                <select id="patient_id" name="patient_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="">Select Patient</option>
                    @foreach(\App\Models\Patient::all() as $patient)
                        <option value="{{ $patient->id }}" {{ $bill->patient_id == $patient->id ? 'selected' : '' }}>
                            {{ $patient->full_name }} ({{ $patient->mrn }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Visit Selection (Optional) -->
            <div class="form-group">
                <label for="visit_id" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Related Visit</label>
                <select id="visit_id" name="visit_id" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="">Select Visit (Optional)</option>
                    @if($bill->patient)
                        @foreach($bill->patient->visits as $visit)
                            <option value="{{ $visit->id }}" {{ $bill->visit_id == $visit->id ? 'selected' : '' }}>
                                Visit #{{ $visit->id }} - {{ $visit->visit_date->format('M d, Y') }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Amount -->
            <div class="form-group">
                <label for="amount" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Bill Amount ($) <span style="color: #ef4444;">*</span></label>
                <input type="number" id="amount" name="amount" step="0.01" min="0" value="{{ $bill->amount }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Amount Paid -->
            <div class="form-group">
                <label for="paid" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Amount Paid ($)</label>
                <input type="number" id="paid" name="paid" step="0.01" min="0" value="{{ $bill->paid }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Status <span style="color: #ef4444;">*</span></label>
                <select id="status" name="status" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="unpaid" {{ $bill->status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ $bill->status == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ $bill->status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="cancelled" {{ $bill->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Payment Method -->
            <div class="form-group">
                <label for="payment_method" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Payment Method</label>
                <select id="payment_method" name="payment_method" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    <option value="">Select Payment Method</option>
                    <option value="cash" {{ $bill->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank_transfer" {{ $bill->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="check" {{ $bill->payment_method == 'check' ? 'selected' : '' }}>Check</option>
                    <option value="credit_card" {{ $bill->payment_method == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                    <option value="debit_card" {{ $bill->payment_method == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                    <option value="insurance" {{ $bill->payment_method == 'insurance' ? 'selected' : '' }}>Insurance</option>
                </select>
            </div>

            <!-- Billed Date -->
            <div class="form-group">
                <label for="billed_at" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Bill Date <span style="color: #ef4444;">*</span></label>
                <input type="date" id="billed_at" name="billed_at" value="{{ $bill->billed_at->format('Y-m-d') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Due Date -->
            <div class="form-group">
                <label for="due_at" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Due Date</label>
                <input type="date" id="due_at" name="due_at" value="{{ $bill->due_at ? $bill->due_at->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Insurance Claim Number -->
            <div class="form-group">
                <label for="insurance_claim_no" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Insurance Claim Number</label>
                <input type="text" id="insurance_claim_no" name="insurance_claim_no" value="{{ $bill->insurance_claim_no }}" placeholder="e.g., INS-2026-001" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>
        </div>

        <!-- Notes -->
        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="notes" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Notes</label>
            <textarea id="notes" name="notes" rows="3" placeholder="Additional notes about this bill..." style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; resize: vertical;">{{ $bill->notes }}</textarea>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('billing.show', $bill) }}" class="ghost-button" style="padding: 0.75rem 1.5rem;">Cancel</a>
            <button type="submit" class="primary-button" style="padding: 0.75rem 1.5rem;">Update Bill</button>
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

// Update visit options when patient changes
document.getElementById('patient_id').addEventListener('change', function() {
    const patientId = this.value;
    const visitSelect = document.getElementById('visit_id');

    if (!patientId) {
        visitSelect.innerHTML = '<option value="">Select Visit (Optional)</option>';
        return;
    }

    // This would typically make an AJAX call to get visits for the selected patient
    // For now, we'll just clear the selection
    visitSelect.innerHTML = '<option value="">Select Visit (Optional)</option>';
});
</script>
@endsection