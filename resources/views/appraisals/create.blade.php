@extends('layouts.app')

@section('title', 'New Appraisal')
@section('page_title', 'New staff appraisal')
@section('page_subtitle', 'Capture performance review notes, scores, and goals.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('appraisals.index') }}">Back to appraisals</a>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('appraisals.store') }}">
        @include('appraisals.form')
    </form>
</div>
@endsection
