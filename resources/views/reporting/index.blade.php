@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Reporting & Analytics Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="panel">
            <h2 class="section-title mb-2">Financial KPIs</h2>
            <ul>
                <li>Total Revenue: <strong>{{ number_format($financial['total_revenue'], 2) }}</strong></li>
                <li>Total Bills: <strong>{{ $financial['total_bills'] }}</strong></li>
                <li>Total Payments: <strong>{{ $financial['total_payments'] }}</strong></li>
            </ul>
        </div>
        <div class="panel">
            <h2 class="section-title mb-2">Clinical KPIs</h2>
            <ul>
                <li>Total Patients: <strong>{{ $clinical['total_patients'] }}</strong></li>
                <li>Total Appointments: <strong>{{ $clinical['total_appointments'] }}</strong></li>
                <li>Total Lab Tests: <strong>{{ $clinical['total_lab_tests'] }}</strong></li>
            </ul>
        </div>
        <div class="panel">
            <h2 class="section-title mb-2">Operational KPIs</h2>
            <ul>
                <li>Total Employees: <strong>{{ $operational['total_employees'] }}</strong></li>
                <li>Total Branches: <strong>{{ $operational['total_branches'] }}</strong></li>
            </ul>
        </div>
    </div>
    <div class="panel">
        <h2 class="section-title mb-2">Reports</h2>
        <ul>
            <li><a href="#" class="text-blue-600 hover:underline">Financial Report</a></li>
            <li><a href="#" class="text-blue-600 hover:underline">Clinical Report</a></li>
            <li><a href="#" class="text-blue-600 hover:underline">Operational Report</a></li>
        </ul>
    </div>
</div>
@endsection
