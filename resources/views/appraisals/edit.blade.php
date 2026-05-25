@extends('layouts.app')

@section('title', 'Edit Appraisal')
@section('page_title', 'Edit staff appraisal')
@section('page_subtitle', $appraisal->employee->first_name . ' ' . $appraisal->employee->last_name)

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('appraisals.show', $appraisal) }}">Back to appraisal</a>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('appraisals.update', $appraisal) }}">
        @method('PUT')
        @include('appraisals.form')
    </form>
</div>
@endsection
