@extends('layouts.app')
@section('title', 'Doctor Dashboard')
@section('page_title', 'Doctor dashboard')
@section('content')
<div class="panel">
    <h2 class="section-title">Consultation queue</h2>
    @include('dashboards.partials.visits', ['visits' => $visits])
</div>
<div class="panel">
    <h2 class="section-title">Recent lab results</h2>
    @include('dashboards.partials.lab_tests', ['labTests' => $labResults])
</div>
@endsection
