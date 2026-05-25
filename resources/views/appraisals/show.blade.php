@extends('layouts.app')

@section('title', 'Appraisal')
@section('page_title', 'Staff appraisal')
@section('page_subtitle', $appraisal->employee->first_name . ' ' . $appraisal->employee->last_name)

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('appraisals.index') }}">Back to appraisals</a>
    <a class="primary-button" href="{{ route('appraisals.edit', $appraisal) }}">Edit</a>
@endsection

@section('content')
<div class="panel">
    <div class="detail-grid">
        <div class="detail-item"><span class="detail-label">Period</span><div class="detail-value">{{ $appraisal->period_start?->format('Y-m-d') }} to {{ $appraisal->period_end?->format('Y-m-d') }}</div></div>
        <div class="detail-item"><span class="detail-label">Reviewer</span><div class="detail-value">{{ $appraisal->reviewer->name ?? '-' }}</div></div>
        <div class="detail-item"><span class="detail-label">Score</span><div class="detail-value">{{ $appraisal->score !== null ? number_format($appraisal->score, 2) : '-' }}</div></div>
        <div class="detail-item"><span class="detail-label">Rating</span><div class="detail-value">{{ $appraisal->rating ?? '-' }}</div></div>
        <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value">{{ ucfirst($appraisal->status) }}</div></div>
        <div class="detail-item"><span class="detail-label">Reviewed</span><div class="detail-value">{{ $appraisal->reviewed_at?->format('Y-m-d') ?? '-' }}</div></div>
    </div>
</div>
<div class="panel">
    <div class="detail-grid">
        <div class="detail-item"><span class="detail-label">Strengths</span><div class="detail-value">{{ $appraisal->strengths ?? '-' }}</div></div>
        <div class="detail-item"><span class="detail-label">Improvement Areas</span><div class="detail-value">{{ $appraisal->improvement_areas ?? '-' }}</div></div>
        <div class="detail-item"><span class="detail-label">Goals</span><div class="detail-value">{{ $appraisal->goals ?? '-' }}</div></div>
    </div>
</div>
@endsection
