@extends('layouts.app')
@section('title', 'Lab Dashboard')
@section('page_title', 'Lab dashboard')
@section('content')
<div class="panel">
    <h2 class="section-title">Pending lab orders</h2>
    @include('dashboards.partials.lab_tests', ['labTests' => $labTests])
</div>
@endsection
