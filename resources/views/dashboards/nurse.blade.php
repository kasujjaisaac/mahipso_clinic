@extends('layouts.app')
@section('title', 'Nurse Dashboard')
@section('page_title', 'Nurse dashboard')
@section('content')
<div class="panel">
    <h2 class="section-title">Triage queue</h2>
    @include('dashboards.partials.visits', ['visits' => $visits])
</div>
@endsection
