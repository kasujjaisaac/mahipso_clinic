@extends('layouts.app')

@section('title', $branch->name . ' Dashboard')
@section('section', 'Branch Dashboard')
@section('kicker', 'Clinic Performance')
@section('page_title', $branch->name)
@section('page_subtitle', 'A focused view of patient load, appointments, visits, and medical records for this clinic.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('branches.cards') }}">All clinics</a>
    <a class="primary-button" href="{{ route('patients.index', ['branch' => $branch->id]) }}">Open patient list</a>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="metric-card" style="--accent: var(--brand);">
            <div class="metric-icon">👤</div>
            <div>
                <div class="metric-value">{{ $patientsCount }}</div>
                <div class="metric-label">Patients</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: var(--green);">
            <div class="metric-icon">🗓</div>
            <div>
                <div class="metric-value">{{ $appointmentsCount }}</div>
                <div class="metric-label">Appointments</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: var(--blue);">
            <div class="metric-icon">✚</div>
            <div>
                <div class="metric-value">{{ $visitsCount }}</div>
                <div class="metric-label">Visits</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: var(--purple);">
            <div class="metric-icon">📄</div>
            <div>
                <div class="metric-value">{{ $medicalRecordsCount }}</div>
                <div class="metric-label">Medical records</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: var(--amber);">
            <div class="metric-icon">🧪</div>
            <div>
                <div class="metric-value">{{ $hivRecordsCount }}</div>
                <div class="metric-label">HIV records</div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="padding: 1.25rem; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 8px; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Income</div>
            <div style="font-size: 1.875rem; font-weight: 700;">{{ number_format($totalIncome, 2) }}</div>
        </div>
        <div style="padding: 1.25rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border-radius: 8px; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Billed</div>
            <div style="font-size: 1.875rem; font-weight: 700;">{{ number_format($totalBilled, 2) }}</div>
        </div>
        <div style="padding: 1.25rem; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 8px; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Expenses</div>
            <div style="font-size: 1.875rem; font-weight: 700;">{{ number_format($totalExpenses, 2) }}</div>
        </div>
        <div style="padding: 1.25rem; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); border-radius: 8px; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Pharmacy Sales</div>
            <div style="font-size: 1.875rem; font-weight: 700;">{{ number_format($totalPharmacySales, 2) }}</div>
        </div>
        <div style="padding: 1.25rem; background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-radius: 8px; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Logins This Month</div>
            <div style="font-size: 1.875rem; font-weight: 700;">{{ $logins }}</div>
        </div>
        <div style="padding: 1.25rem; background: linear-gradient(135deg, #f59e42 0%, #d97706 100%); border-radius: 8px; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Failed Logins (30d)</div>
            <div style="font-size: 1.875rem; font-weight: 700;">{{ $failedLogins }}</div>
        </div>
    </div>

    <style>
        #branch-dashboard-calendar { width: 100%; background: #fff; border-radius: 0; padding: 0.25rem; border-top: 1px solid #f0d4d1; }
        .fc-event.status-completed { background: #22c55e !important; border: none; }
        .fc-event.status-canceled, .fc-event.status-no_show { background: #ef4444 !important; border: none; }
        .fc-event.status-scheduled, .fc-event.status-confirmed { background: #3b82f6 !important; border: none; }
        .fc-event.status-checked_in { background: #f59e42 !important; border: none; }
        .chart-container { position: relative; height: 350px; margin-bottom: 2rem; }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>

    <!-- Financial Performance Charts -->
    <div style="margin-bottom: 3rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: #1f2937;">Financial Performance (12 Months)</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
            <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="chart-container">
                    <canvas id="incomeVsExpensesChart"></canvas>
                </div>
            </div>
            <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="chart-container">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>

        @if($expensesByCategory->count() > 0)
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 500px;">
            <div class="chart-container">
                <canvas id="expenseCategoryChart"></canvas>
            </div>
        </div>
        @endif
    </div>

    <script>
        // Income vs Expenses Comparison
        const incomeVsExpensesCtx = document.getElementById('incomeVsExpensesChart').getContext('2d');
        new Chart(incomeVsExpensesCtx, {
            type: 'bar',
            data: {
                labels: @json(collect($monthlyFinancials)->pluck('month')),
                datasets: [
                    {
                        label: 'Income',
                        data: @json(collect($monthlyFinancials)->pluck('income')),
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: '#22c55e',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Expenses',
                        data: @json(collect($monthlyFinancials)->pluck('expenses')),
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: '#ef4444',
                        borderWidth: 1,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Monthly Income vs Expenses', font: { size: 14, weight: 'bold' } },
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function(value) { return '₦' + value.toLocaleString(); } } }
                }
            }
        });

        // Monthly Trend Line Chart
        const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(monthlyTrendCtx, {
            type: 'line',
            data: {
                labels: @json(collect($monthlyFinancials)->pluck('month')),
                datasets: [
                    {
                        label: 'Income Trend',
                        data: @json(collect($monthlyFinancials)->pluck('income')),
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#22c55e',
                    },
                    {
                        label: 'Expense Trend',
                        data: @json(collect($monthlyFinancials)->pluck('expenses')),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#ef4444',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Financial Trend Analysis', font: { size: 14, weight: 'bold' } },
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function(value) { return '₦' + value.toLocaleString(); } } }
                }
            }
        });

        @if($expensesByCategory->count() > 0)
        // Expense Category Distribution
        const expenseCategoryCtx = document.getElementById('expenseCategoryChart').getContext('2d');
        new Chart(expenseCategoryCtx, {
            type: 'doughnut',
            data: {
                labels: @json($expensesByCategory->pluck('category')),
                datasets: [{
                    data: @json($expensesByCategory->pluck('total')),
                    backgroundColor: [
                        '#3b82f6', '#ef4444', '#8b5cf6', '#f59e42', 
                        '#22c55e', '#06b6d4', '#ec4899', '#6366f1',
                        '#14b8a6', '#f97316', '#a855f7', '#d946ef'
                    ],
                    borderColor: 'white',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Expenses by Category', font: { size: 14, weight: 'bold' } },
                    legend: { display: true, position: 'right' }
                }
            }
        });
        @endif
    </script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('branch-dashboard-calendar');
            if (!calendarEl) return;
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'listWeek',
                contentHeight: 220,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: @json($branchCalendarEvents),
                eventClick: function(info) {
                    window.location.href = '/appointments/' + info.event.id;
                },
                eventClassNames: function(info) {
                    return ['status-' + info.event.extendedProps.status];
                }
            });
            calendar.render();
        });
    </script>

    <div class="card-grid">
        <div class="info-card" style="min-height: 220px; padding: 0; overflow: hidden; display: flex; flex-direction: column;">
            <div class="panel-header" style="padding: 0.85rem 1rem;">
                <div>
                    <h3 class="section-title" style="margin:0;">Upcoming appointments</h3>
                    <p class="table-meta" style="margin:0;">Next 14 days</p>
                </div>
            </div>
            <div id="branch-dashboard-calendar" style="flex: 1; min-height: 220px;"></div>
        </div>
        <a class="info-card" href="{{ route('patients.index', ['branch' => $branch->id]) }}">
            <h3>Patients</h3>
            <p>Open the patient register for {{ $branch->name }} and move into registration, profile updates, and lookups.</p>
        </a>
        <a class="info-card" href="{{ route('appointments.index', ['branch' => $branch->id]) }}">
            <h3>Appointments</h3>
            <p>Review scheduled and completed appointments linked to this clinic.</p>
        </a>
        <a class="info-card" href="{{ route('visits.index', ['branch' => $branch->id]) }}">
            <h3>Visits</h3>
            <p>Track active patient visits, clinical encounters, and provider assignments.</p>
        </a>
        <a class="info-card" href="{{ route('medical-records.index', ['branch' => $branch->id]) }}">
            <h3>Medical records</h3>
            <p>Browse clinical notes, treatment plans, and provider-written records.</p>
        </a>
        <a class="info-card" href="{{ route('hiv-records.index', ['branch' => $branch->id]) }}">
            <h3>HIV records</h3>
            <p>Access test records, viral load details, ART status, and adherence notes.</p>
        </a>
    </div>
@endsection
