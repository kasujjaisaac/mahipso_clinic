@extends('layouts.app')

@section('title', 'Contract')
@section('page_title', 'Contract ' . $contract->contract_no)
@section('page_subtitle', trim(($contract->employee->first_name ?? '') . ' ' . ($contract->employee->last_name ?? '')))

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('contracts.index') }}">Back to contracts</a>
    <a class="primary-button" href="{{ route('contracts.edit', $contract) }}">Edit</a>
@endsection

@section('content')
<div class="panel">
    <div class="detail-grid">
        <div class="detail-item"><span class="detail-label">Employee</span><div class="detail-value">{{ $contract->employee->first_name }} {{ $contract->employee->last_name }}</div></div>
        <div class="detail-item"><span class="detail-label">Type</span><div class="detail-value">{{ ucfirst(str_replace('_', ' ', $contract->contract_type)) }}</div></div>
        <div class="detail-item"><span class="detail-label">Job Title</span><div class="detail-value">{{ $contract->job_title ?? '-' }}</div></div>
        <div class="detail-item"><span class="detail-label">Start Date</span><div class="detail-value">{{ $contract->start_date?->format('Y-m-d') }}</div></div>
        <div class="detail-item"><span class="detail-label">End Date</span><div class="detail-value">{{ $contract->end_date?->format('Y-m-d') ?? '-' }}</div></div>
        <div class="detail-item"><span class="detail-label">Salary</span><div class="detail-value">{{ number_format($contract->salary_amount, 2) }}</div></div>
        <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value">{{ ucfirst($contract->status) }}</div></div>
        <div class="detail-item"><span class="detail-label">Signed</span><div class="detail-value">{{ $contract->signed_at?->format('Y-m-d') ?? '-' }}</div></div>
    </div>
</div>
@if($contract->terms)
<div class="panel">
    <h2 class="section-title">Terms</h2>
    <div class="detail-value" style="margin-top: 0.6rem;">{{ $contract->terms }}</div>
</div>
@endif
@endsection
