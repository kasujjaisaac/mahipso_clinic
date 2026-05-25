@extends('layouts.app')

@section('title', 'Payment Receipt')
@section('section', 'Financial')
@section('page_title', 'Payment receipt #' . $payment->id)

@section('topbar_actions')
    <button class="ghost-button" onclick="window.print()">Print</button>
    <a class="ghost-button" href="{{ route('billing.show', $payment->bill) }}">Back to bill</a>
@endsection

@section('content')
<div class="panel" style="max-width: 760px;">
    <div class="detail-grid">
        <div class="detail-item"><span class="detail-label">Patient</span><div class="detail-value">{{ $payment->patient->full_name }}</div></div>
        <div class="detail-item"><span class="detail-label">Branch</span><div class="detail-value">{{ $payment->branch->name ?? $payment->patient->branch->name ?? 'N/A' }}</div></div>
        <div class="detail-item"><span class="detail-label">Bill</span><div class="detail-value">#{{ $payment->bill_id }}</div></div>
        <div class="detail-item"><span class="detail-label">Amount paid</span><div class="detail-value">USh {{ number_format($payment->amount, 0) }}</div></div>
        <div class="detail-item"><span class="detail-label">Method</span><div class="detail-value">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</div></div>
        <div class="detail-item"><span class="detail-label">Received by</span><div class="detail-value">{{ $payment->receivedBy->name ?? 'N/A' }}</div></div>
        <div class="detail-item"><span class="detail-label">Reference</span><div class="detail-value">{{ $payment->reference ?: '-' }}</div></div>
        <div class="detail-item"><span class="detail-label">Paid at</span><div class="detail-value">{{ $payment->paid_at->format('Y-m-d H:i') }}</div></div>
    </div>
</div>
@endsection
