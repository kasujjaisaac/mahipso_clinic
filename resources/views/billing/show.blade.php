@extends('layouts.app')
@section('title', 'Bill Details')
@section('section', 'Financial')
@section('page_title', 'Bill Details')
@section('content')
<div class="panel">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Bill Details</h2>
        <p class="table-meta" style="font-size:0.95rem; color:#888;">Bill #{{ $bill->id }} - {{ $bill->patient->full_name }}</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem;">

        <!-- Main Content -->
        <div style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">

            <!-- Bill Information -->
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: #374151;">Bill Information</h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <label style="font-weight: 500; color: #6b7280; font-size: 0.9rem;">Patient</label>
                        <p style="margin: 0.25rem 0; font-weight: 500;">{{ $bill->patient->full_name }}</p>
                        <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">MRN: {{ $bill->patient->mrn }}</p>
                    </div>

                    @if($bill->visit)
                    <div>
                        <label style="font-weight: 500; color: #6b7280; font-size: 0.9rem;">Related Visit</label>
                        <p style="margin: 0.25rem 0; font-weight: 500;">Visit #{{ $bill->visit->id }}</p>
                        <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">{{ $bill->visit->visit_date->format('M d, Y') }}</p>
                    </div>
                    @endif

                    <div>
                        <label style="font-weight: 500; color: #6b7280; font-size: 0.9rem;">Bill Amount</label>
                        <p style="margin: 0.25rem 0; font-weight: 500; font-size: 1.1rem; color: #059669;">${{ number_format($bill->amount, 2) }}</p>
                    </div>

                    <div>
                        <label style="font-weight: 500; color: #6b7280; font-size: 0.9rem;">Amount Paid</label>
                        <p style="margin: 0.25rem 0; font-weight: 500; font-size: 1.1rem; color: #059669;">${{ number_format($bill->paid, 2) }}</p>
                    </div>

                    <div>
                        <label style="font-weight: 500; color: #6b7280; font-size: 0.9rem;">Balance</label>
                        <p style="margin: 0.25rem 0; font-weight: 500; font-size: 1.1rem; color: {{ $bill->balance > 0 ? '#dc2626' : '#059669' }};">
                            ${{ number_format($bill->balance, 2) }}
                        </p>
                    </div>

                    <div>
                        <label style="font-weight: 500; color: #6b7280; font-size: 0.9rem;">Status</label>
                        <p style="margin: 0.25rem 0;">
                            <span class="status-badge status-{{ $bill->status }}" style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 500; text-transform: uppercase;">
                                {{ $bill->status }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label style="font-weight: 500; color: #6b7280; font-size: 0.9rem;">Bill Date</label>
                        <p style="margin: 0.25rem 0; font-weight: 500;">{{ $bill->billed_at->format('M d, Y') }}</p>
                    </div>

                    @if($bill->due_at)
                    <div>
                        <label style="font-weight: 500; color: #6b7280; font-size: 0.9rem;">Due Date</label>
                        <p style="margin: 0.25rem 0; font-weight: 500;">{{ $bill->due_at->format('M d, Y') }}</p>
                    </div>
                    @endif

                    @if($bill->payment_method)
                    <div>
                        <label style="font-weight: 500; color: #6b7280; font-size: 0.9rem;">Payment Method</label>
                        <p style="margin: 0.25rem 0; font-weight: 500;">{{ ucwords(str_replace('_', ' ', $bill->payment_method)) }}</p>
                    </div>
                    @endif

                    @if($bill->insurance_claim_no)
                    <div>
                        <label style="font-weight: 500; color: #6b7280; font-size: 0.9rem;">Insurance Claim</label>
                        <p style="margin: 0.25rem 0; font-weight: 500;">{{ $bill->insurance_claim_no }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            @if($bill->notes)
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: #374151;">Notes</h3>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 6px; border: 1px solid #e5e7eb;">
                    <p style="margin: 0; color: #374151; line-height: 1.5;">{{ $bill->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                <a href="{{ route('billing.edit', $bill) }}" class="primary-button" style="padding: 0.5rem 1rem;">Edit Bill</a>
                <a href="{{ route('billing.index') }}" class="ghost-button" style="padding: 0.5rem 1rem;">Back to Bills</a>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Actions -->
            <div style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: #374151;">Quick Actions</h3>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @if($bill->status !== 'paid')
                    <button class="primary-button" style="padding: 0.75rem; text-align: center;" onclick="recordPayment()">
                        Record Payment
                    </button>
                    @endif

                    <button class="ghost-button" style="padding: 0.75rem; text-align: center;" onclick="printBill()">
                        Print Bill
                    </button>

                    <button class="ghost-button" style="padding: 0.75rem; text-align: center;" onclick="sendReminder()">
                        Send Reminder
                    </button>
                </div>
            </div>
        </div>
    </div>
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
    }

    .ghost-button:hover {
        background-color: #f9fafb;
        border-color: #9ca3af;
    }
</style>

<script>
function recordPayment() {
    // This would open a modal or redirect to payment recording
    alert('Payment recording functionality would be implemented here');
}

function printBill() {
    window.print();
}

function sendReminder() {
    if (confirm('Send payment reminder to patient?')) {
        // This would make an AJAX call to send reminder
        alert('Reminder sent successfully');
    }
}
</script>
@endsection