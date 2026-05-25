@extends('layouts.app')

@section('title', 'Reporting')
@section('page_title', 'Reporting and analytics')

@section('content')
<div class="stats-grid">
    <div class="metric-card"><div class="metric-icon">B</div><div><div class="metric-value">{{ number_format($financial['total_billed'], 0) }}</div><div class="metric-label">Total billed</div></div></div>
    <div class="metric-card"><div class="metric-icon">P</div><div><div class="metric-value">{{ number_format($financial['total_collected'], 0) }}</div><div class="metric-label">Collected</div></div></div>
    <div class="metric-card"><div class="metric-icon">O</div><div><div class="metric-value">{{ number_format($financial['outstanding'], 0) }}</div><div class="metric-label">Outstanding</div></div></div>
    <div class="metric-card"><div class="metric-icon">Pt</div><div><div class="metric-value">{{ $clinical['total_patients'] }}</div><div class="metric-label">Patients</div></div></div>
    <div class="metric-card"><div class="metric-icon">V</div><div><div class="metric-value">{{ $clinical['open_visits'] }}</div><div class="metric-label">Open visits</div></div></div>
    <div class="metric-card"><div class="metric-icon">L</div><div><div class="metric-value">{{ $clinical['pending_lab_tests'] }}</div><div class="metric-label">Pending labs</div></div></div>
    <div class="metric-card"><div class="metric-icon">Rx</div><div><div class="metric-value">{{ $clinical['pending_prescriptions'] }}</div><div class="metric-label">Pending prescriptions</div></div></div>
    <div class="metric-card"><div class="metric-icon">HR</div><div><div class="metric-value">{{ $operational['attendance_today'] }}</div><div class="metric-label">Attendance today</div></div></div>
</div>

<div class="panel">
    <h2 class="section-title">Operational snapshot</h2>
    <div class="detail-grid">
        <div class="detail-item"><span class="detail-label">Employees</span><div class="detail-value">{{ $operational['total_employees'] }}</div></div>
        <div class="detail-item"><span class="detail-label">Branches</span><div class="detail-value">{{ $operational['total_branches'] }}</div></div>
        <div class="detail-item"><span class="detail-label">Inventory items</span><div class="detail-value">{{ $operational['inventory_items'] }}</div></div>
        <div class="detail-item"><span class="detail-label">Open purchase orders</span><div class="detail-value">{{ $operational['purchase_orders_open'] }}</div></div>
    </div>
</div>
@endsection
