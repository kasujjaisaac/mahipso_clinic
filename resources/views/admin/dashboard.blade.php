@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('section', 'Administration')
@section('kicker', 'Network Overview')
@section('page_title', 'Super admin dashboard')
@section('page_subtitle', 'Monitor branch activity and move quickly into branch setup, users, appointments, and patient operations.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('users.index') }}">Manage users</a>
    <a class="primary-button" href="{{ route('branches.index') }}">Manage branches</a>
@endsection

@section('content')
    @php
        $financialSummary = collect($monthlyFinancials);
        $rollingIncome = $financialSummary->sum('income');
        $rollingExpenses = $financialSummary->sum('expenses');
        $rollingNet = $rollingIncome - $rollingExpenses;
        $latestFinancialMonth = $financialSummary->last();
    @endphp

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">System totals</h2>
                <p class="subtle">Core entity counts for every major item in your clinic management system.</p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="metric-card" style="--accent: var(--brand);">
                <div class="metric-icon">⌂</div>
                <div>
                    <div class="metric-value">{{ $branchesCount }}</div>
                    <div class="metric-label">Branches</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: var(--purple);">
                <div class="metric-icon">👤</div>
                <div>
                    <div class="metric-value">{{ $usersCount }}</div>
                    <div class="metric-label">Users</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: var(--teal);">
                <div class="metric-icon">🛡</div>
                <div>
                    <div class="metric-value">{{ $rolesCount }}</div>
                    <div class="metric-label">Roles</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: var(--green);">
                <div class="metric-icon">👥</div>
                <div>
                    <div class="metric-value">{{ $patientsCount }}</div>
                    <div class="metric-label">Patients</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: var(--blue);">
                <div class="metric-icon">📅</div>
                <div>
                    <div class="metric-value">{{ $appointmentsCount }}</div>
                    <div class="metric-label">Appointments</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: var(--orange);">
                <div class="metric-icon">✅</div>
                <div>
                    <div class="metric-value">{{ $visitsCount }}</div>
                    <div class="metric-label">Visits</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #5f5fd9;">
                <div class="metric-icon">📄</div>
                <div>
                    <div class="metric-value">{{ $medicalRecordsCount }}</div>
                    <div class="metric-label">Medical records</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #204e8d;">
                <div class="metric-icon">🧬</div>
                <div>
                    <div class="metric-value">{{ $hivRecordsCount }}</div>
                    <div class="metric-label">HIV records</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #008b8b;">
                <div class="metric-icon">💊</div>
                <div>
                    <div class="metric-value">{{ $pharmaciesCount }}</div>
                    <div class="metric-label">Pharmacies</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #614b8a;">
                <div class="metric-icon">📦</div>
                <div>
                    <div class="metric-value">{{ $productsCount }}</div>
                    <div class="metric-label">Products</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #4a4a4a;">
                <div class="metric-icon">🗃</div>
                <div>
                    <div class="metric-value">{{ $documentsCount }}</div>
                    <div class="metric-label">Documents</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #9f5555;">
                <div class="metric-icon">✉️</div>
                <div>
                    <div class="metric-value">{{ $messagesCount }}</div>
                    <div class="metric-label">Messages</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #ff6b35;">
                <div class="metric-icon">📦</div>
                <div>
                    <div class="metric-value">{{ $inventoryReceivedCount }}</div>
                    <div class="metric-label">Assets Received</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #4caf50;">
                <div class="metric-icon">🏪</div>
                <div>
                    <div class="metric-value">{{ $inventoryInStoreCount }}</div>
                    <div class="metric-label">Assets in Store</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #2196f3;">
                <div class="metric-icon">🔧</div>
                <div>
                    <div class="metric-value">{{ $inventoryInUseCount }}</div>
                    <div class="metric-label">Assets in Use</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #ff9800;">
                <div class="metric-icon">⚠️</div>
                <div>
                    <div class="metric-value">{{ $inventoryNearDisposalCount }}</div>
                    <div class="metric-label">Assets Near Disposal</div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Financial Overview</h2>
                <p class="subtle">Collected income, expenses, and overall financial position across the clinic network.</p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="metric-card" style="--accent: #22c55e;">
                <div class="metric-icon">💰</div>
                <div>
                    <div class="metric-value">{{ number_format($totalIncome, 2) }}</div>
                    <div class="metric-label">Total Income</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #3b82f6;">
                <div class="metric-icon">📊</div>
                <div>
                    <div class="metric-value">{{ number_format($patientCollections, 2) }}</div>
                    <div class="metric-label">Bill Collections</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #2563eb;">
                <div class="metric-icon">🧾</div>
                <div>
                    <div class="metric-value">{{ number_format($totalBilled, 2) }}</div>
                    <div class="metric-label">Total Billed</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #f59e42;">
                <div class="metric-icon">⏳</div>
                <div>
                    <div class="metric-value">{{ number_format($totalOutstanding, 2) }}</div>
                    <div class="metric-label">Outstanding Balance</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #ef4444;">
                <div class="metric-icon">💸</div>
                <div>
                    <div class="metric-value">{{ number_format($totalExpenses, 2) }}</div>
                    <div class="metric-label">Total Expenses</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #8b5cf6;">
                <div class="metric-icon">💊</div>
                <div>
                    <div class="metric-value">{{ number_format($totalPharmacySales, 2) }}</div>
                    <div class="metric-label">Pharmacy Sales</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: {{ $netIncome >= 0 ? '#0f766e' : '#dc2626' }};">
                <div class="metric-icon">{{ $netIncome >= 0 ? '📈' : '📉' }}</div>
                <div>
                    <div class="metric-value">{{ number_format($netIncome, 2) }}</div>
                    <div class="metric-label">Net Position</div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Security & Access</h2>
                <p class="subtle">User authentication and system access metrics.</p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="metric-card" style="--accent: #06b6d4;">
                <div class="metric-icon">🔐</div>
                <div>
                    <div class="metric-value">{{ $totalLogins }}</div>
                    <div class="metric-label">Total Logins</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #22c55e;">
                <div class="metric-icon">✅</div>
                <div>
                    <div class="metric-value">{{ $successfulLogins }}</div>
                    <div class="metric-label">Successful Logins</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #ef4444;">
                <div class="metric-icon">❌</div>
                <div>
                    <div class="metric-value">{{ $failedLogins }}</div>
                    <div class="metric-label">Failed Logins (30d)</div>
                </div>
            </div>
            <div class="metric-card" style="--accent: #f59e42;">
                <div class="metric-icon">🟢</div>
                <div>
                    <div class="metric-value">{{ $activeSessions }}</div>
                    <div class="metric-label">Active Sessions</div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Today's operations</h2>
                <p class="subtle">Live queue pressure, patient movement, and same-day service activity.</p>
            </div>
        </div>
        <div class="stats-grid">
            <div class="metric-card" style="--accent: #2f6fed;"><div class="metric-icon">IN</div><div><div class="metric-value">{{ $appointmentsToday }}</div><div class="metric-label">Appointments today</div></div></div>
            <div class="metric-card" style="--accent: #b8342b;"><div class="metric-icon">Q</div><div><div class="metric-value">{{ $openVisitsCount }}</div><div class="metric-label">Open visits</div></div></div>
            <div class="metric-card" style="--accent: #c87b16;"><div class="metric-icon">V</div><div><div class="metric-value">{{ $todayVisitsCount }}</div><div class="metric-label">Visits today</div></div></div>
            <div class="metric-card" style="--accent: #2f7d57;"><div class="metric-icon">OK</div><div><div class="metric-value">{{ $completedVisitsToday }}</div><div class="metric-label">Completed today</div></div></div>
            <div class="metric-card" style="--accent: #5f5fd9;"><div class="metric-icon">7D</div><div><div class="metric-value">{{ $appointmentsNext7Days }}</div><div class="metric-label">Appointments next 7 days</div></div></div>
        </div>
        <div class="chart-grid" style="margin-top: 1rem;">
            <div class="chart-container">
                <h3>Patient queue by stage</h3>
                <div class="chart-wrapper"><canvas id="queueStageChart"></canvas></div>
            </div>
            <div class="chart-container">
                <h3>Branch workload</h3>
                <div class="chart-wrapper"><canvas id="branchWorkloadChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Inpatient command center</h2>
                <p class="subtle">Admissions, discharge readiness, clearance blockers, and bed occupancy.</p>
            </div>
        </div>
        <div class="stats-grid">
            <div class="metric-card" style="--accent: #b8342b;"><div class="metric-icon">IP</div><div><div class="metric-value">{{ $activeAdmissionsCount }}</div><div class="metric-label">Active admissions</div></div></div>
            <div class="metric-card" style="--accent: #2f6fed;"><div class="metric-icon">AD</div><div><div class="metric-value">{{ $admissionsToday }}</div><div class="metric-label">Admissions today</div></div></div>
            <div class="metric-card" style="--accent: #2f7d57;"><div class="metric-icon">DC</div><div><div class="metric-value">{{ $dischargesToday }}</div><div class="metric-label">Discharges today</div></div></div>
            <div class="metric-card" style="--accent: #c87b16;"><div class="metric-icon">RD</div><div><div class="metric-value">{{ $readyForDischarge }}</div><div class="metric-label">Ready for discharge</div></div></div>
            <div class="metric-card" style="--accent: #ef4444;"><div class="metric-icon">!</div><div><div class="metric-value">{{ $pendingDischargeClearance }}</div><div class="metric-label">Pending clearance</div></div></div>
            <div class="metric-card" style="--accent: #7c3aed;"><div class="metric-icon">%</div><div><div class="metric-value">{{ $bedOccupancyRate }}%</div><div class="metric-label">Bed occupancy</div></div></div>
        </div>
        <div class="progress-row">
            <div>
                <div class="progress-label"><span>Bed occupancy progress</span><strong>{{ $occupiedBeds }} / {{ $totalBeds }} beds</strong></div>
                <div class="progress-track"><div class="progress-fill" style="width: {{ min($bedOccupancyRate, 100) }}%;"></div></div>
            </div>
        </div>
        <div class="chart-grid" style="margin-top: 1rem;">
            <div class="chart-container"><h3>Bed status</h3><div class="chart-wrapper"><canvas id="bedStatusChart"></canvas></div></div>
            <div class="chart-container"><h3>Admissions by ward</h3><div class="chart-wrapper"><canvas id="admissionsByWardChart"></canvas></div></div>
            <div class="chart-container"><h3>Admissions vs discharges</h3><div class="chart-wrapper"><canvas id="admissionFlowChart"></canvas></div></div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Clinical workload</h2>
                <p class="subtle">Lab, prescription, and pharmacy stock alerts requiring operational attention.</p>
            </div>
        </div>
        <div class="stats-grid">
            <div class="metric-card" style="--accent: #204e8d;"><div class="metric-icon">LB</div><div><div class="metric-value">{{ $pendingLabTests }}</div><div class="metric-label">Pending lab tests</div></div></div>
            <div class="metric-card" style="--accent: #2f7d57;"><div class="metric-icon">LR</div><div><div class="metric-value">{{ $completedLabTestsToday }}</div><div class="metric-label">Lab results today</div></div></div>
            <div class="metric-card" style="--accent: #8b5cf6;"><div class="metric-icon">RX</div><div><div class="metric-value">{{ $pendingPrescriptions }}</div><div class="metric-label">Pending prescriptions</div></div></div>
            <div class="metric-card" style="--accent: #2f7d57;"><div class="metric-icon">DS</div><div><div class="metric-value">{{ $dispensedToday }}</div><div class="metric-label">Dispensed today</div></div></div>
            <div class="metric-card" style="--accent: #f59e42;"><div class="metric-icon">LS</div><div><div class="metric-value">{{ $lowStockProducts }}</div><div class="metric-label">Low stock items</div></div></div>
            <div class="metric-card" style="--accent: #ef4444;"><div class="metric-icon">OS</div><div><div class="metric-value">{{ $outOfStockProducts }}</div><div class="metric-label">Out of stock</div></div></div>
            <div class="metric-card" style="--accent: #c87b16;"><div class="metric-icon">EX</div><div><div class="metric-value">{{ $expiringProducts }}</div><div class="metric-label">Expiring soon</div></div></div>
        </div>
        <div class="chart-grid" style="margin-top: 1rem;">
            <div class="chart-container"><h3>Lab status</h3><div class="chart-wrapper"><canvas id="labStatusChart"></canvas></div></div>
            <div class="chart-container"><h3>Top lab tests</h3><div class="chart-wrapper"><canvas id="topLabTestsChart"></canvas></div></div>
            <div class="chart-container"><h3>Prescription status</h3><div class="chart-wrapper"><canvas id="prescriptionStatusChart"></canvas></div></div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Revenue cycle and collections</h2>
                <p class="subtle">Bill status, discharge blockers, and aging outstanding balances.</p>
            </div>
        </div>
        <div class="stats-grid">
            <div class="metric-card" style="--accent: #ef4444;"><div class="metric-icon">UB</div><div><div class="metric-value">{{ $billStatusCounts->get('unpaid', 0) }}</div><div class="metric-label">Unpaid bills</div></div></div>
            <div class="metric-card" style="--accent: #f59e42;"><div class="metric-icon">PB</div><div><div class="metric-value">{{ $billStatusCounts->get('partial', 0) }}</div><div class="metric-label">Partial bills</div></div></div>
            <div class="metric-card" style="--accent: #22c55e;"><div class="metric-icon">PD</div><div><div class="metric-value">{{ $billStatusCounts->get('paid', 0) }}</div><div class="metric-label">Paid bills</div></div></div>
            <div class="metric-card" style="--accent: #b8342b;"><div class="metric-icon">DC</div><div><div class="metric-value">{{ $billingBlockedDischarges }}</div><div class="metric-label">Discharges blocked by billing</div></div></div>
        </div>
        <div class="chart-grid" style="margin-top: 1rem;">
            <div class="chart-container"><h3>Bill status</h3><div class="chart-wrapper"><canvas id="billStatusChart"></canvas></div></div>
            <div class="chart-container"><h3>Outstanding aging</h3><div class="chart-wrapper"><canvas id="agingChart"></canvas></div></div>
            <div class="chart-container"><h3>Revenue by branch</h3><div class="chart-wrapper"><canvas id="branchRevenueChart"></canvas></div></div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Staff and procurement</h2>
                <p class="subtle">People operations, payroll readiness, requisitions, and purchase order flow.</p>
            </div>
        </div>
        <div class="stats-grid">
            <div class="metric-card" style="--accent: #2f7d57;"><div class="metric-icon">ST</div><div><div class="metric-value">{{ $activeStaffCount }}</div><div class="metric-label">Active staff</div></div></div>
            <div class="metric-card" style="--accent: #c87b16;"><div class="metric-icon">LV</div><div><div class="metric-value">{{ $staffOnLeaveCount }}</div><div class="metric-label">Staff on leave</div></div></div>
            <div class="metric-card" style="--accent: #2f6fed;"><div class="metric-icon">AT</div><div><div class="metric-value">{{ $attendanceTodayCount }}</div><div class="metric-label">Attendance today</div></div></div>
            <div class="metric-card" style="--accent: #ef4444;"><div class="metric-icon">LT</div><div><div class="metric-value">{{ $lateAttendanceToday }}</div><div class="metric-label">Late today</div></div></div>
            <div class="metric-card" style="--accent: #7c3aed;"><div class="metric-icon">AP</div><div><div class="metric-value">{{ $pendingAppraisals }}</div><div class="metric-label">Pending appraisals</div></div></div>
            <div class="metric-card" style="--accent: #b8342b;"><div class="metric-icon">RQ</div><div><div class="metric-value">{{ $pendingRequisitions }}</div><div class="metric-label">Pending requisitions</div></div></div>
            <div class="metric-card" style="--accent: #c87b16;"><div class="metric-icon">PO</div><div><div class="metric-value">{{ $pendingPurchaseOrders }}</div><div class="metric-label">Pending purchase orders</div></div></div>
        </div>
        <div class="chart-grid" style="margin-top: 1rem;">
            <div class="chart-container"><h3>Staff by role</h3><div class="chart-wrapper"><canvas id="staffRoleChart"></canvas></div></div>
            <div class="chart-container"><h3>Staff by branch</h3><div class="chart-wrapper"><canvas id="staffBranchChart"></canvas></div></div>
            <div class="chart-container"><h3>Requisition status</h3><div class="chart-wrapper"><canvas id="requisitionStatusChart"></canvas></div></div>
            <div class="chart-container"><h3>Purchase order status</h3><div class="chart-wrapper"><canvas id="purchaseOrderStatusChart"></canvas></div></div>
            <div class="chart-container"><h3>Payroll status</h3><div class="chart-wrapper"><canvas id="payrollStatusChart"></canvas></div></div>
            <div class="chart-container"><h3>Failed login trend</h3><div class="chart-wrapper"><canvas id="failedLoginTrendChart"></canvas></div></div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Quick access</h2>
                <p class="subtle">Navigate directly to the sections you review most often as a super admin.</p>
            </div>
        </div>

        <div class="card-grid">
            <a class="info-card" href="{{ route('branches.index') }}">
                <h3>Branches</h3>
                <p>Open location management and clinic settings.</p>
            </a>
            <a class="info-card" href="{{ route('users.index') }}">
                <h3>Users</h3>
                <p>Manage staff access and roles.</p>
            </a>
            <a class="info-card" href="{{ route('patients.index') }}">
                <h3>Patients</h3>
                <p>Browse the full patient registry.</p>
            </a>
            <a class="info-card" href="{{ route('appointments.index') }}">
                <h3>Appointments</h3>
                <p>Review scheduled visits across branches.</p>
            </a>
            <a class="info-card" href="{{ route('roles.index') }}">
                <h3>Roles</h3>
                <p>Adjust permissions and security groups.</p>
            </a>
            <a class="info-card" href="{{ route('audit-logs.index') }}">
                <h3>Audit logs</h3>
                <p>Inspect recent system activity and access events.</p>
            </a>
        </div>
    </div>

    <style>
        #admin-dashboard-calendar { width: 100%; background: #fff; border-radius: 0; padding: 0.25rem; border: 1px solid #f0d4d1; }
        .fc-event.status-completed { background: #22c55e !important; border: none; }
        .fc-event.status-canceled, .fc-event.status-no_show { background: #ef4444 !important; border: none; }
        .fc-event.status-scheduled, .fc-event.status-confirmed { background: #3b82f6 !important; border: none; }
        .fc-event.status-checked_in { background: #f59e42 !important; border: none; }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('admin-dashboard-calendar');
            if (!calendarEl) return;
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'listWeek',
                contentHeight: 220,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: @json($adminCalendarEvents),
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

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Financial Performance</h2>
                <p class="subtle">12-month income versus expense comparison for the full clinic network.</p>
            </div>
        </div>

        <div class="financial-performance-shell">
            <div class="financial-performance-summary">
                <div class="financial-summary-card">
                    <span class="financial-summary-label">12-Month Income</span>
                    <strong>{{ number_format($rollingIncome, 2) }}</strong>
                </div>
                <div class="financial-summary-card">
                    <span class="financial-summary-label">12-Month Expenses</span>
                    <strong>{{ number_format($rollingExpenses, 2) }}</strong>
                </div>
                <div class="financial-summary-card {{ $rollingNet >= 0 ? 'is-positive' : 'is-negative' }}">
                    <span class="financial-summary-label">12-Month Net</span>
                    <strong>{{ number_format($rollingNet, 2) }}</strong>
                </div>
                <div class="financial-summary-card">
                    <span class="financial-summary-label">Latest Month</span>
                    <strong>{{ $latestFinancialMonth['month'] ?? now()->format('M Y') }}</strong>
                </div>
            </div>

            <div class="financial-chart-card">
                <div class="financial-chart-copy">
                    <h3>Income vs expense bar graph</h3>
                    <p class="subtle">Income combines bill collections and completed pharmacy sales. Expenses include paid expense records only.</p>
                </div>
                <div class="financial-chart-canvas">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">System analytics</h2>
                <p class="subtle">Visual breakdown of distribution and activity across the clinic network.</p>
            </div>
        </div>
        <div class="chart-grid">
            <div class="chart-container" style="min-height: 220px;">
                <h3>Upcoming appointments</h3>
                <p class="subtle">Appointments for the next 14 days.</p>
                <div id="admin-dashboard-calendar" style="height: 220px; margin-top: 0.8rem;"></div>
            </div>
            <div class="chart-container">
                <h3>Entity Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="entityChart"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <h3>Clinical Activity</h3>
                <div class="chart-wrapper">
                    <canvas id="clinicalChart"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <h3>Records Breakdown</h3>
                <div class="chart-wrapper">
                    <canvas id="recordsChart"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <h3>System Resources</h3>
                <div class="chart-wrapper">
                    <canvas id="resourcesChart"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <h3>Inventory Status</h3>
                <div class="chart-wrapper">
                    <canvas id="inventoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const amountFormatter = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        // Entity Distribution Pie Chart
        const entityCtx = document.getElementById('entityChart').getContext('2d');
        new Chart(entityCtx, {
            type: 'doughnut',
            data: {
                labels: ['Patients', 'Users', 'Branches', 'Roles'],
                datasets: [{
                    data: [{{ $patientsCount }}, {{ $usersCount }}, {{ $branchesCount }}, {{ $rolesCount }}],
                    backgroundColor: ['#2f7d57', '#7c3aed', '#b8342b', '#2f6fed'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11, family: 'Poppins' },
                            padding: 10,
                            color: '#222222'
                        }
                    }
                }
            }
        });

        // Clinical Activity Bar Chart
        const clinicalCtx = document.getElementById('clinicalChart').getContext('2d');
        new Chart(clinicalCtx, {
            type: 'bar',
            data: {
                labels: ['Appointments', 'Visits', 'Medical Records', 'HIV Records'],
                datasets: [{
                    label: 'Count',
                    data: [{{ $appointmentsCount }}, {{ $visitsCount }}, {{ $medicalRecordsCount }}, {{ $hivRecordsCount }}],
                    backgroundColor: ['#2f6fed', '#c87b16', '#5f5fd9', '#204e8d'],
                    borderRadius: 0,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' },
                        ticks: { font: { size: 10 } }
                    },
                    y: {
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });

        // Records Breakdown Pie Chart
        const recordsCtx = document.getElementById('recordsChart').getContext('2d');
        new Chart(recordsCtx, {
            type: 'pie',
            data: {
                labels: ['Medical Records', 'HIV Records', 'Documents'],
                datasets: [{
                    data: [{{ $medicalRecordsCount }}, {{ $hivRecordsCount }}, {{ $documentsCount }}],
                    backgroundColor: ['#5f5fd9', '#204e8d', '#4a4a4a'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11, family: 'Poppins' },
                            padding: 10,
                            color: '#222222'
                        }
                    }
                }
            }
        });

        // System Resources Bar Chart
        const resourcesCtx = document.getElementById('resourcesChart').getContext('2d');
        new Chart(resourcesCtx, {
            type: 'bar',
            data: {
                labels: ['Pharmacies', 'Products', 'Messages'],
                datasets: [{
                    label: 'Count',
                    data: [{{ $pharmaciesCount }}, {{ $productsCount }}, {{ $messagesCount }}],
                    backgroundColor: ['#008b8b', '#614b8a', '#9f5555'],
                    borderRadius: 0,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });

        // Inventory Status Pie Chart
        const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
        new Chart(inventoryCtx, {
            type: 'pie',
            data: {
                labels: ['In Store', 'In Use', 'Near Disposal'],
                datasets: [{
                    data: [{{ $inventoryInStoreCount }}, {{ $inventoryInUseCount }}, {{ $inventoryNearDisposalCount }}],
                    backgroundColor: ['#4caf50', '#2196f3', '#ff9800'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11, family: 'Poppins' },
                            padding: 10,
                            color: '#222222'
                        }
                    }
                }
            }
        });

        const palette = ['#b8342b', '#2f6fed', '#2f7d57', '#c87b16', '#7c3aed', '#008b8b', '#ef4444', '#64748b'];
        const makeChart = (id, type, labels, data, label, options = {}) => {
            const element = document.getElementById(id);
            if (!element) return;
            const ctx = element.getContext('2d');
            new Chart(ctx, {
                type,
                data: {
                    labels,
                    datasets: [{
                        label,
                        data,
                        backgroundColor: type === 'line' ? 'rgba(184, 52, 43, 0.12)' : palette,
                        borderColor: type === 'line' ? '#b8342b' : '#ffffff',
                        borderWidth: type === 'line' ? 2 : 1,
                        tension: 0.35,
                        fill: type === 'line',
                        borderRadius: type === 'bar' ? 0 : undefined,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: ['pie', 'doughnut', 'line'].includes(type), position: 'bottom' }
                    },
                    scales: ['pie', 'doughnut'].includes(type) ? {} : {
                        y: { beginAtZero: true, grid: { color: '#f0f0f0' }, ticks: { precision: 0 } },
                        x: { ticks: { font: { size: 10 } } }
                    },
                    ...options
                }
            });
        };

        makeChart('queueStageChart', 'bar', @json($queueStageCounts->keys()->values()), @json($queueStageCounts->values()->values()), 'Open visits', { indexAxis: 'y' });
        makeChart('branchWorkloadChart', 'bar', @json($branchPerformance->pluck('branch')), @json($branchPerformance->pluck('visits')), 'Visits');
        makeChart('bedStatusChart', 'doughnut', ['Available', 'Occupied', 'Cleaning', 'Maintenance'], [{{ $availableBeds }}, {{ $occupiedBeds }}, {{ $cleaningBeds }}, {{ $maintenanceBeds }}], 'Beds');
        makeChart('admissionsByWardChart', 'bar', @json($admissionsByWard->pluck('name')), @json($admissionsByWard->pluck('total')), 'Admissions', { indexAxis: 'y' });

        const admissionFlowEl = document.getElementById('admissionFlowChart');
        if (admissionFlowEl) {
            new Chart(admissionFlowEl.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json(collect($monthlyAdmissionsFlow)->pluck('month')),
                    datasets: [
                        { label: 'Admissions', data: @json(collect($monthlyAdmissionsFlow)->pluck('admissions')), borderColor: '#b8342b', backgroundColor: 'rgba(184, 52, 43, 0.12)', tension: 0.35, fill: true },
                        { label: 'Discharges', data: @json(collect($monthlyAdmissionsFlow)->pluck('discharges')), borderColor: '#2f7d57', backgroundColor: 'rgba(47, 125, 87, 0.12)', tension: 0.35, fill: true }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
            });
        }

        makeChart('labStatusChart', 'doughnut', @json($labStatusCounts->keys()->values()), @json($labStatusCounts->values()->values()), 'Lab tests');
        makeChart('topLabTestsChart', 'bar', @json($topLabTests->pluck('test_type')), @json($topLabTests->pluck('total')), 'Orders', { indexAxis: 'y' });
        makeChart('prescriptionStatusChart', 'pie', @json($prescriptionStatusCounts->keys()->values()), @json($prescriptionStatusCounts->values()->values()), 'Prescriptions');
        makeChart('billStatusChart', 'doughnut', @json($billStatusCounts->keys()->values()), @json($billStatusCounts->values()->values()), 'Bills');
        makeChart('agingChart', 'bar', @json(array_keys($outstandingAging)), @json(array_values($outstandingAging)), 'Outstanding');
        makeChart('branchRevenueChart', 'bar', @json($branchPerformance->pluck('branch')), @json($branchPerformance->pluck('revenue')), 'Revenue');
        makeChart('staffRoleChart', 'doughnut', @json($staffByRole->pluck('name')), @json($staffByRole->pluck('users_count')), 'Users');
        makeChart('staffBranchChart', 'bar', @json($staffByBranch->pluck('name')), @json($staffByBranch->pluck('users_count')), 'Staff');
        makeChart('requisitionStatusChart', 'pie', @json($requisitionStatusCounts->keys()->values()), @json($requisitionStatusCounts->values()->values()), 'Requisitions');
        makeChart('purchaseOrderStatusChart', 'pie', @json($purchaseOrderStatusCounts->keys()->values()), @json($purchaseOrderStatusCounts->values()->values()), 'Purchase orders');
        makeChart('payrollStatusChart', 'doughnut', @json($payrollStatusCounts->keys()->values()), @json($payrollStatusCounts->values()->values()), 'Payroll');
        makeChart('failedLoginTrendChart', 'line', @json(collect($failedLoginTrend)->pluck('day')), @json(collect($failedLoginTrend)->pluck('failed')), 'Failed logins');

        // Financial Performance Charts
        const incomeVsExpensesCtx = document.getElementById('financialChart')?.getContext('2d');
        if (incomeVsExpensesCtx) {
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
                        title: { display: true, text: 'Income vs Expenses by Month', font: { size: 14, weight: 'bold' } },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#222222',
                                font: { family: 'Poppins', size: 11 },
                                usePointStyle: true,
                                pointStyle: 'rectRounded'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + amountFormatter.format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#555555',
                                font: { size: 10 }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f0f0f0' },
                            ticks: {
                                color: '#555555',
                                callback: function(value) {
                                    return amountFormatter.format(value);
                                }
                            },
                            title: {
                                display: true,
                                text: 'Amount'
                            }
                        }
                    }
                }
            });
        }
    </script>

    <style>
        .financial-performance-shell {
            display: grid;
            gap: 1.25rem;
            margin-top: 1.5rem;
        }
        .financial-performance-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }
        .financial-summary-card {
            border: 1px solid #f0d4d1;
            background: #fffaf9;
            padding: 1rem 1.1rem;
        }
        .financial-summary-card strong {
            display: block;
            margin-top: 0.35rem;
            font-size: 1.1rem;
            color: #1f2937;
        }
        .financial-summary-card.is-positive strong {
            color: #0f766e;
        }
        .financial-summary-card.is-negative strong {
            color: #b91c1c;
        }
        .financial-summary-label {
            display: block;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7f615d;
        }
        .financial-chart-card {
            background: white;
            padding: 1.5rem;
            border: 1px solid #f0d4d1;
        }
        .financial-chart-copy {
            margin-bottom: 1rem;
        }
        .financial-chart-copy h3 {
            margin: 0 0 0.35rem;
            font-size: 1.05rem;
            color: #1f2937;
        }
        .financial-chart-canvas {
            position: relative;
            min-height: 360px;
        }
        .progress-row {
            margin-top: 1rem;
            border: 1px solid #f0d4d1;
            background: #fffaf9;
            padding: 0.9rem 1rem;
        }
        .progress-label {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.45rem;
            color: #374151;
            font-weight: 700;
        }
        .progress-track {
            height: 12px;
            background: #f3e6e4;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #b8342b;
        }
    </style>
@endsection
