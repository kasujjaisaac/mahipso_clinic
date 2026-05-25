<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mahipso Clinic')</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#b8342b">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <style>
        :root {
            --panel: #ffffff;
            --sidebar: #ffffff;
            --sidebar-muted: #666666;
            --sidebar-border: #f5e5e5;
            --text: #222222;
            --muted: #888888;
            --line: #f0d4d1;
            --brand: #b8342b;
            --brand-deep: #8d241d;
            --green: #2f7d57;
            --blue: #2f6fed;
            --purple: #7c3aed;
            --amber: #c87b16;
            --danger: #b8342b;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            --radius: 0px;
        }

        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            height: 100%;
            max-width: 100%;
            overflow: hidden;
        }
        body {
            font-family: "Poppins", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            background: #ffffff;
            color: var(--text);
        }

        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }

        .app-shell {
            display: grid;
            grid-template-columns: 250px minmax(0, 1fr);
            height: 100vh;
            width: 100%;
            max-width: 100%;
            overflow: hidden;
        }

        .sidebar {
            background: var(--sidebar);
            color: var(--text);
            padding: 0.7rem 0.6rem;
            border-right: 2px solid var(--brand);
            height: 100vh;
            overflow-y: auto;
            overscroll-behavior: contain;
            box-shadow: 2px 0 4px rgba(0, 0, 0, 0.04);
        }

        .brand {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 0.55rem;
            padding: 0.65rem;
            border-radius: 0;
            background: var(--brand);
            border: 1px solid var(--brand);
            color: #ffffff;
            margin-bottom: 0.65rem;
            box-shadow: 0 1px 4px rgba(138, 29, 27, 0.06);
        }

        .brand-info {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            min-width: 0;
        }

        .brand-info strong {
            font-size: 0.94rem;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: 0.02em;
        }

        .brand-info span {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.82);
            letter-spacing: 0.02em;
        }

        .brand-mark {
            width: 34px;
            height: 34px;
            border-radius: 0;
            background: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.65);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 3px;
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand h1,
        .sidebar-section-title,
        .page-title,
        .section-title,
        .metric-value,
        .auth-title {
            margin: 0;
        }

        .brand h1 { font-size: 0.88rem; line-height: 1.15; }
        .brand p,
        .sidebar-section-title,
        .page-kicker,
        .metric-label,
        .detail-label,
        .auth-note,
        .table-meta,
        .subtle,
        .empty-state {
            color: var(--muted);
        }

        .sidebar-section {
            margin-top: 0.72rem;
            padding: 0;
            border: none;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }
        .sidebar-section:first-of-type {
            margin-top: 0;
        }
        .sidebar-section-title {
            font-size: 0.64rem;
            text-transform: uppercase;
            letter-spacing: 0.13em;
            margin-bottom: 0.25rem;
            padding: 0 0.45rem;
            color: var(--sidebar-muted);
            font-weight: 700;
        }

        .nav-list {
            display: flex;
            flex-direction: column;
            gap: 0.05rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.42rem;
            padding: 0.38rem 0.5rem;
            border-radius: 0;
            color: var(--text);
            background: transparent;
            border: none;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            min-width: 0;
        }

        .nav-item:hover,
        .nav-item.active {
            background: #fff5f4;
            color: var(--brand);
            box-shadow: inset 3px 0 0 var(--brand);
        }

        .nav-icon {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            display: grid;
            place-items: center;
            background: #f0f0f0;
            font-size: 0.9rem;
            flex-shrink: 0;
            color: var(--brand);
        }

        .nav-item span {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .logout-form { margin-top: 0.45rem; }

        .sidebar-logout {
            width: 100%;
            text-align: left;
            border: 1px solid #f0d4d1;
            background: #fff5f5;
            color: var(--brand);
            padding: 0.45rem 0.55rem;
            border-radius: 0;
            cursor: pointer;
            font-weight: 700;
        }

        .main {
            padding: 0.85rem;
            background: #ffffff;
            min-width: 0;
            max-width: 100%;
            height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            background: linear-gradient(135deg, #8a1d1b 0%, #bf312d 100%);
            border: none;
            border-radius: 0;
            padding: 0.75rem 1rem;
            box-shadow: 0 4px 12px rgba(184, 52, 43, 0.15);
            margin-bottom: 0.85rem;
            color: #fff;
            font-size: 0.75rem;
        }

        .topbar-actions,
        .toolbar-form,
        .inline-actions,
        .action-stack {
            display: flex;
            gap: 0.35rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .topbar-left {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            align-items: flex-start;
            min-width: 0;
        }

        .topbar-greeting {
            font-size: 0.75rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.01em;
        }

        .topbar-subtext {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.85);
            letter-spacing: 0.02em;
        }

        .topbar-tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.26rem 0.75rem;
            border-radius: 0;
            background: rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.92);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .topbar-meta {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            min-width: 0;
            max-width: 100%;
        }

        .top-card {
            min-width: auto;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 0;
            padding: 0.35rem 0.6rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .top-card-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            border: none;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #fff;
            background: rgba(255,255,255,0.15);
            margin-right: 0;
        }

        .top-card-value {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.2;
            color: #ffffff;
        }

        .user-menu {
            position: relative;
        }

        .user-menu-trigger {
            cursor: pointer;
            justify-content: space-between;
            width: auto;
            min-width: 160px;
        }

        .user-menu-arrow {
            font-size: 0.85rem;
            margin-left: 0.25rem;
            color: rgba(255,255,255,0.85);
        }

        .user-menu-list {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 0.2rem);
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
            border-radius: 4px;
            padding: 0.2rem 0;
            min-width: 180px;
            z-index: 10;
        }

        .user-menu.open .user-menu-list,
        .user-menu:focus-within .user-menu-list {
            display: block;
        }

        .user-menu-list a,
        .user-menu-list form button {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.5rem 0.75rem;
            background: transparent;
            border: none;
            color: #111;
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .user-menu-list a:hover,
        .user-menu-list form button:hover {
            background: #f6f6f6;
        }

        .ghost-button,
        .primary-button,
        .danger-button,
        .badge-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            padding: 0.42rem 0.62rem;
            border-radius: 4px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--text);
            font-weight: 400;
            cursor: pointer;
        }

        .topbar-actions .ghost-button,
        .topbar-actions .primary-button {
            border-radius: 0;
            background: #c91b1b;
            border-color: #a31212;
            color: #ffffff;
            padding: 0.35rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: auto;
            box-shadow: none;
        }

        .topbar-actions .primary-button {
            background: #c91b1b;
            border-color: #a31212;
            color: #ffffff;
        }

        .topbar-actions .ghost-button:hover,
        .topbar-actions .primary-button:hover {
            background: #a91111;
        }

        .primary-button {
            background: linear-gradient(135deg, var(--brand), var(--brand-deep));
            border-color: transparent;
            color: white;
            font-weight: 400;
        }

        .danger-button {
            background: #ffffff;
            color: #000000;
            border-color: #000000;
        }

        .content-grid,
        .stats-grid,
        .card-grid,
        .detail-grid,
        .chart-grid,
        .form-grid {
            display: grid;
            gap: 0.65rem;
            min-width: 0;
            max-width: 100%;
        }

        .stats-grid { grid-template-columns: repeat(auto-fit, minmax(145px, 1fr)); }
        .card-grid { grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); }
        .detail-grid,
        .form-grid { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
        .chart-grid { grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); }

        .chart-container {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 0;
            box-shadow: var(--shadow);
            padding: 1rem;
            position: relative;
            min-height: 320px;
        }

        .chart-container h3 {
            margin: 0 0 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text);
        }

        .chart-wrapper {
            position: relative;
            height: 280px;
        }

        .panel,
        .metric-card,
        .info-card,
        .list-card,
        .entity-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 0;
            box-shadow: var(--shadow);
            min-width: 0;
            max-width: 100%;
        }

        .content-grid {
            background: #ffffff;
            padding: 0.5rem 0;
            width: 100%;
            overflow-x: hidden;
        }

        .panel { padding: 0.75rem; }

        .panel-header,
        .table-toolbar,
        .split-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.6rem;
            flex-wrap: wrap;
        }

        .section-title { font-size: 0.9rem; font-weight: 400; }

        .metric-card {
            padding: 0.62rem;
            display: grid;
            grid-template-columns: 30px 1fr;
            gap: 0.45rem;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 3px;
            background: var(--accent, var(--brand));
        }

        .metric-icon {
            width: 30px;
            height: 30px;
            border-radius: 0;
            display: grid;
            place-items: center;
            background: color-mix(in srgb, var(--accent, var(--brand)) 14%, white);
            color: var(--accent, var(--brand));
            font-size: 0.74rem;
            font-weight: 400;
        }

        .metric-value {
            font-size: 1.22rem;
            font-weight: 400;
            line-height: 1;
        }

        .metric-label {
            margin-top: 0.08rem;
            font-size: 0.72rem;
        }

        .info-card,
        .entity-card { padding: 0.72rem; }

        .info-card h3,
        .list-card h3,
        .entity-card h3 {
            margin: 0 0 0.3rem;
            font-size: 0.84rem;
        }

        .info-card p,
        .list-card p,
        .entity-card p {
            margin: 0.15rem 0;
            color: var(--muted);
            line-height: 1.45;
        }

        .chip,
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.18rem 0.42rem;
            border-radius: 0;
            font-size: 0.64rem;
            font-weight: 400;
            letter-spacing: 0.02em;
        }

        .chip { background: #ffffff; color: #000000; border: 1px solid #000000; }
        .status-pill.active,
        .status-pill.open,
        .status-pill.scheduled,
        .status-pill.confirmed,
        .status-pill.completed,
        .status-pill.negative { background: #ffffff; color: #000000; border: 1px solid #000000; }
        .status-pill.inactive,
        .status-pill.cancelled,
        .status-pill.canceled,
        .status-pill.no_show,
        .status-pill.positive { background: #ffffff; color: #000000; border: 1px solid #000000; }
        .status-pill.checked_in,
        .status-pill.indeterminate,
        .status-pill.unknown { background: #ffffff; color: #000000; border: 1px solid #000000; }

        .toolbar-form input,
        .toolbar-form select,
        .field input,
        .field select,
        .field textarea {
            width: 100%;
            border: 1px solid #d9e0ea;
            background: #f9fbfd;
            color: var(--text);
            border-radius: 0;
            padding: 0.5rem 0.58rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .toolbar-form input,
        .toolbar-form select { min-width: 170px; }
        .field textarea { min-height: 84px; resize: vertical; }

        .toolbar-form input:focus,
        .toolbar-form select:focus,
        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            outline: none;
            border-color: #e4928d;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(191, 58, 48, 0.08);
        }

        .field label {
            display: block;
            margin-bottom: 0.35rem;
            font-weight: 400;
            font-size: 0.74rem;
        }

        .field-span-2 { grid-column: span 2; }

        .table-wrap {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 0;
        }

        table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            text-align: left;
            padding: 0.55rem 0.6rem;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        th {
            background: #ffffff;
            color: #000000;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        tr:last-child td { border-bottom: none; }

        .detail-item {
            padding: 0.62rem;
            border-radius: 4px;
            border: 1px solid var(--line);
            background: #ffffff;
        }

        .detail-label {
            display: block;
            font-size: 0.62rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.28rem;
        }

        .detail-value {
            font-weight: 400;
            line-height: 1.4;
            white-space: pre-wrap;
        }

        .alert {
            padding: 0.62rem 0.72rem;
            border-radius: 4px;
            border: 1px solid;
            margin-bottom: 0.65rem;
        }

        .alert.success { background: #ffffff; color: #000000; border-color: #000000; }
        .alert.error { background: #ffffff; color: #000000; border-color: #000000; }
        .alert ul { margin: 0; padding-left: 1rem; }
        .empty-state { padding: 0.82rem; text-align: center; }
        .pagination-wrap { margin-top: 0.65rem; }

        .main > *,
        .content-grid > *,
        .panel > *,
        .table-toolbar > *,
        .panel-header > *,
        .split-header > *,
        .container,
        .container-fluid {
            min-width: 0;
            max-width: 100%;
        }

        .main .container,
        .main .container-fluid {
            width: 100%;
        }

        .main .row {
            max-width: 100%;
            margin-left: 0;
            margin-right: 0;
        }

        .main img,
        .main canvas,
        .main svg {
            max-width: 100%;
        }

        .main .d-flex {
            min-width: 0;
            max-width: 100%;
            flex-wrap: wrap;
        }

        @media (max-width: 960px) {
            html, body {
                height: auto;
                overflow-x: hidden;
                overflow-y: auto;
            }
            .app-shell {
                grid-template-columns: 1fr;
                height: auto;
                overflow: visible;
            }
            .sidebar {
                position: relative;
                height: auto;
                overflow-y: visible;
                border-right: none;
                border-bottom: 1px solid var(--sidebar-border);
            }
            .main {
                height: auto;
                overflow-y: visible;
            }
            .field-span-2 { grid-column: span 1; }
        }
    </style>
</head>
<body>
@php
    $user = auth()->user();
    $name = $user->name ?? 'System Admin';
    $name = trim(explode(' ', $name)[0] ?? $name);
    $dashboardHref = $user?->isSuperAdmin() ? route('admin.dashboard') : route('staff.dashboard');
    $isDashboardRoute = request()->routeIs('admin.dashboard')
        || request()->routeIs('staff.dashboard')
        || request()->routeIs('dashboard.role')
        || request()->routeIs('branches.cards')
        || request()->routeIs('branches.dashboard');
    $canSeeNav = function (array $roles) use ($user) {
        if (! $user) {
            return false;
        }

        return in_array('*', $roles, true) || $user->isSuperAdmin() || $user->hasRole($roles);
    };
    $canSeeModule = function (?string $module) use ($user) {
        if (! $module) {
            return true;
        }

        return $user?->canAccessModule($module) ?? false;
    };
    $navSections = [
        [
            'title' => 'Dashboard',
            'items' => [
                ['label' => 'Staff Dashboard', 'href' => $dashboardHref, 'active' => $isDashboardRoute, 'icon' => 'dashboard', 'roles' => ['*']],
            ],
        ],
        [
            'title' => 'My Desk',
            'items' => [
                ['label' => 'Requisitions', 'href' => route('requisitions.mine'), 'active' => request()->routeIs('requisitions.*'), 'icon' => 'requisitions', 'roles' => ['*']],
                ['label' => 'Timesheets', 'href' => route('timesheets.mine'), 'active' => request()->routeIs('timesheets.*'), 'icon' => 'timesheets', 'roles' => ['*']],
            ],
        ],
        [
            'title' => 'Front Office',
            'module' => 'front_office',
            'items' => [
                ['label' => 'Patients', 'href' => route('patients.index'), 'active' => request()->routeIs('patients.*'), 'icon' => 'patients', 'roles' => ['*']],
                ['label' => 'Appointments', 'href' => route('appointments.index'), 'active' => request()->routeIs('appointments.*'), 'icon' => 'appointments', 'roles' => ['*']],
                ['label' => 'Clinic Queue', 'href' => route('clinic-queue.index'), 'active' => request()->routeIs('clinic-queue.*'), 'icon' => 'appointments', 'roles' => ['*']],
                ['label' => 'Visits', 'href' => route('visits.index'), 'active' => request()->routeIs('visits.*'), 'icon' => 'appointments', 'roles' => ['*']],
                ['label' => 'Billing', 'href' => route('billing.index'), 'active' => request()->routeIs('billing.*'), 'icon' => 'billing', 'roles' => ['*']],
            ],
        ],
        [
            'title' => 'Clinic',
            'module' => 'clinic',
            'items' => [
                ['label' => 'Patients', 'href' => route('patients.index'), 'active' => request()->routeIs('patients.*'), 'icon' => 'patients', 'roles' => ['*']],
                ['label' => 'Appointments', 'href' => route('appointments.index'), 'active' => request()->routeIs('appointments.*'), 'icon' => 'appointments', 'roles' => ['*']],
                ['label' => 'Consultation Queue', 'href' => route('clinic-queue.index'), 'active' => request()->routeIs('clinic-queue.*'), 'icon' => 'appointments', 'roles' => ['*']],
                ['label' => 'Visits', 'href' => route('visits.index'), 'active' => request()->routeIs('visits.*'), 'icon' => 'appointments', 'roles' => ['*']],
                ['label' => 'Medical Records', 'href' => route('medical-records.index'), 'active' => request()->routeIs('medical-records.*'), 'icon' => 'patients', 'roles' => ['*']],
                ['label' => 'HIV Records', 'href' => route('hiv-records.index'), 'active' => request()->routeIs('hiv-records.*'), 'icon' => 'lab', 'roles' => ['*']],
                ['label' => 'Lab Results', 'href' => route('laboratory.index'), 'active' => request()->routeIs('laboratory.*'), 'icon' => 'lab', 'roles' => ['*']],
                ['label' => 'Prescriptions', 'href' => route('prescriptions.index'), 'active' => request()->routeIs('prescriptions.*'), 'icon' => 'pharmacy', 'roles' => ['*']],
            ],
        ],
        [
            'title' => 'Nursing',
            'module' => 'nursing',
            'items' => [
                ['label' => 'Triage Queue', 'href' => route('clinic-queue.index'), 'active' => request()->routeIs('clinic-queue.*'), 'icon' => 'appointments', 'roles' => ['*']],
                ['label' => 'Patients', 'href' => route('patients.index'), 'active' => request()->routeIs('patients.*'), 'icon' => 'patients', 'roles' => ['*']],
                ['label' => 'Visits', 'href' => route('visits.index'), 'active' => request()->routeIs('visits.*'), 'icon' => 'appointments', 'roles' => ['*']],
                ['label' => 'Medical Records', 'href' => route('medical-records.index'), 'active' => request()->routeIs('medical-records.*'), 'icon' => 'patients', 'roles' => ['*']],
                ['label' => 'HIV Records', 'href' => route('hiv-records.index'), 'active' => request()->routeIs('hiv-records.*'), 'icon' => 'lab', 'roles' => ['*']],
                ['label' => 'Prescriptions', 'href' => route('prescriptions.index'), 'active' => request()->routeIs('prescriptions.*'), 'icon' => 'pharmacy', 'roles' => ['*']],
            ],
        ],
        [
            'title' => 'Inpatient Ward',
            'module' => 'inpatient_ward',
            'items' => [
                ['label' => 'Admissions', 'href' => route('admissions.index'), 'active' => request()->routeIs('admissions.*'), 'icon' => 'patients', 'roles' => ['*']],
                ['label' => 'Ward Bed Board', 'href' => route('wards.index'), 'active' => request()->routeIs('wards.*') || request()->routeIs('beds.*'), 'icon' => 'inventory', 'roles' => ['*']],
            ],
        ],
        [
            'title' => 'Laboratory',
            'module' => 'laboratory',
            'items' => [
                ['label' => 'Lab Requests', 'href' => route('laboratory.index'), 'active' => request()->routeIs('laboratory.*'), 'icon' => 'lab', 'roles' => ['*']],
                ['label' => 'Lab Catalogue', 'href' => route('lab-catalog.index'), 'active' => request()->routeIs('lab-catalog.*'), 'icon' => 'lab', 'roles' => ['*']],
            ],
        ],
        [
            'title' => 'Pharmacy',
            'module' => 'pharmacy',
            'items' => [
                ['label' => 'Prescriptions', 'href' => route('prescriptions.index'), 'active' => request()->routeIs('prescriptions.*'), 'icon' => 'pharmacy', 'roles' => ['*']],
                ['label' => 'Pharmacy', 'href' => route('pharmacies.index'), 'active' => request()->routeIs('pharmacies.*'), 'icon' => 'pharmacy', 'roles' => ['*']],
                ['label' => 'Inventory', 'href' => route('inventory.index'), 'active' => request()->routeIs('inventory.*'), 'icon' => 'inventory', 'roles' => ['*']],
                ['label' => 'Purchase Orders', 'href' => route('purchase-orders.index'), 'active' => request()->routeIs('purchase-orders.*'), 'icon' => 'requisitions', 'roles' => ['*']],
                ['label' => 'Ward Medication', 'href' => route('admissions.index'), 'active' => request()->routeIs('admissions.*'), 'icon' => 'patients', 'roles' => ['*']],
            ],
        ],
        [
            'title' => 'Finance',
            'module' => 'finance',
            'items' => [
                ['label' => 'Financial Overview', 'href' => route('financial.index'), 'active' => request()->routeIs('financial.index'), 'icon' => 'dashboard', 'roles' => ['*']],
                ['label' => 'Billing', 'href' => route('billing.index'), 'active' => request()->routeIs('billing.*'), 'icon' => 'billing', 'roles' => ['*']],
                ['label' => 'Income', 'href' => route('financial.income'), 'active' => request()->routeIs('financial.income'), 'icon' => 'income', 'roles' => ['*']],
                ['label' => 'Expenditure', 'href' => route('financial.expenditure'), 'active' => request()->routeIs('financial.expenditure'), 'icon' => 'expenses', 'roles' => ['*']],
                ['label' => 'Expenses', 'href' => route('expenses.index'), 'active' => request()->routeIs('expenses.*') && ! request()->routeIs('financial.expenditure'), 'icon' => 'expenses', 'roles' => ['*']],
                ['label' => 'Payroll', 'href' => route('payroll.index'), 'active' => request()->routeIs('payroll.*'), 'icon' => 'timesheets', 'roles' => ['*']],
            ],
        ],
        [
            'title' => 'Human Resources',
            'module' => 'human_resources',
            'items' => [
                ['label' => 'Staff Registry', 'href' => route('employees.index'), 'active' => request()->routeIs('employees.*'), 'icon' => 'admin', 'roles' => ['*']],
                ['label' => 'Departments', 'href' => route('departments.index'), 'active' => request()->routeIs('departments.*'), 'icon' => 'admin', 'roles' => ['*']],
                ['label' => 'Contracts', 'href' => route('contracts.index'), 'active' => request()->routeIs('contracts.*'), 'icon' => 'documents', 'roles' => ['*']],
                ['label' => 'Attendance', 'href' => route('attendance.index'), 'active' => request()->routeIs('attendance.*'), 'icon' => 'timesheets', 'roles' => ['*']],
                ['label' => 'Leave', 'href' => route('leaves.index'), 'active' => request()->routeIs('leaves.*'), 'icon' => 'appointments', 'roles' => ['*']],
                ['label' => 'Appraisals', 'href' => route('appraisals.index'), 'active' => request()->routeIs('appraisals.*'), 'icon' => 'reporting', 'roles' => ['*']],
                ['label' => 'Payroll', 'href' => route('payroll.index'), 'active' => request()->routeIs('payroll.*'), 'icon' => 'timesheets', 'roles' => ['*']],
            ],
        ],
        [
            'title' => 'Programs',
            'module' => 'programs',
            'items' => [
                ['label' => 'Reports', 'href' => route('reporting.index'), 'active' => request()->routeIs('reporting.*'), 'icon' => 'reporting', 'roles' => ['*']],
                ['label' => 'Partners', 'href' => route('partners.index'), 'active' => request()->routeIs('partners.*'), 'icon' => 'admin', 'roles' => ['*']],
                ['label' => 'Documents', 'href' => route('documents.index'), 'active' => request()->routeIs('documents.*'), 'icon' => 'documents', 'roles' => ['*']],
                ['label' => 'Emergencies', 'href' => route('emergencies.index'), 'active' => request()->routeIs('emergencies.*'), 'icon' => 'admin', 'roles' => ['*']],
            ],
        ],
        [
            'title' => 'Administration',
            'module' => 'administration',
            'items' => [
                ['label' => 'Branches', 'href' => route('branches.index'), 'active' => request()->routeIs('branches.*') && ! request()->routeIs('branches.cards'), 'icon' => 'inventory', 'roles' => ['super_admin']],
                ['label' => 'Users', 'href' => route('users.index'), 'active' => request()->routeIs('users.*'), 'icon' => 'admin', 'roles' => ['super_admin']],
                ['label' => 'Roles & Permissions', 'href' => route('roles.index'), 'active' => request()->routeIs('roles.*'), 'icon' => 'roles', 'roles' => ['super_admin']],
                ['label' => 'Audit Logs', 'href' => route('audit-logs.index'), 'active' => request()->routeIs('audit-logs.*'), 'icon' => 'audit', 'roles' => ['*']],
                ['label' => 'Service Catalogue', 'href' => route('service-items.index'), 'active' => request()->routeIs('service-items.*'), 'icon' => 'billing', 'roles' => ['*']],
            ],
        ],
    ];
    
    // Generate time-based greeting
    $hour = now()->hour;
    if ($hour < 12) {
        $greeting = 'Good morning';
    } elseif ($hour < 18) {
        $greeting = 'Good afternoon';
    } else {
        $greeting = 'Good evening';
    }
@endphp

<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">
                <img src="{{ asset('mahipso-logo.png') }}" alt="Mahipso Clinic logo">
            </div>
            <div class="brand-info">
                <strong>Mahipso Clinic</strong>
                <span>{{ $user?->isSuperAdmin() ? 'Administrator Portal' : 'Staff Portal' }}</span>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        @foreach($navSections as $section)
            @php
                $visibleItems = collect($section['items'])->filter(fn ($item) => $canSeeModule($section['module'] ?? null) && $canSeeNav($item['roles']));
            @endphp
            @if($visibleItems->isNotEmpty())
                <div class="sidebar-section">
                    <div class="sidebar-section-title">{{ $section['title'] }}</div>
                    <div class="nav-list">
                        @foreach($visibleItems as $item)
                            <a class="nav-item {{ $item['active'] ? 'active' : '' }}" href="{{ $item['href'] }}">
                                <x-icon :name="$item['icon']" class="nav-icon" /> <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        @if(false)
        <div class="sidebar-section">
            <div class="sidebar-section-title">Main</div>
            <div class="nav-list">
                <a class="nav-item {{ $isDashboardRoute ? 'active' : '' }}" href="{{ $dashboardHref }}">
                    <x-icon name="dashboard" class="nav-icon" /> <span>Dashboard</span>
                </a>
                <a class="nav-item {{ request()->routeIs('branches.*') && ! request()->routeIs('branches.cards') ? 'active' : '' }}" href="{{ route('branches.index') }}">
                    <x-icon name="inventory" class="nav-icon" /> <span>Branches</span>
                </a>
                <a class="nav-item {{ request()->routeIs('reporting.*') ? 'active' : '' }}" href="{{ route('reporting.index') }}">
                    <x-icon name="reporting" class="nav-icon" /> <span>Reports</span>
                </a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Patients</div>
            <div class="nav-list">
                <a class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}">
                    <x-icon name="patients" class="nav-icon" /> <span>Patients</span>
                </a>
                <a class="nav-item {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
                    <x-icon name="appointments" class="nav-icon" /> <span>Appointments</span>
                </a>
                <a class="nav-item {{ request()->routeIs('clinic-queue.*') ? 'active' : '' }}" href="{{ route('clinic-queue.index') }}">
                    <x-icon name="appointments" class="nav-icon" /> <span>Clinic Queue</span>
                </a>
                <a class="nav-item {{ request()->routeIs('visits.*') ? 'active' : '' }}" href="{{ route('visits.index') }}">
                    <x-icon name="appointments" class="nav-icon" /> <span>Visits</span>
                </a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Inpatient</div>
            <div class="nav-list">
                <a class="nav-item {{ request()->routeIs('admissions.*') ? 'active' : '' }}" href="{{ route('admissions.index') }}">
                    <x-icon name="patients" class="nav-icon" /> <span>Admissions</span>
                </a>
                <a class="nav-item {{ request()->routeIs('wards.*') || request()->routeIs('beds.*') ? 'active' : '' }}" href="{{ route('wards.index') }}">
                    <x-icon name="inventory" class="nav-icon" /> <span>Ward Bed Board</span>
                </a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Clinical</div>
            <div class="nav-list">
                <a class="nav-item {{ request()->routeIs('medical-records.*') ? 'active' : '' }}" href="{{ route('medical-records.index') }}">
                    <x-icon name="patients" class="nav-icon" /> <span>Medical Records</span>
                </a>
                <a class="nav-item {{ request()->routeIs('hiv-records.*') ? 'active' : '' }}" href="{{ route('hiv-records.index') }}">
                    <x-icon name="lab" class="nav-icon" /> <span>HIV Records</span>
                </a>
                <a class="nav-item {{ request()->routeIs('laboratory.*') ? 'active' : '' }}" href="{{ route('laboratory.index') }}">
                    <x-icon name="lab" class="nav-icon" /> <span>Laboratory</span>
                </a>
                <a class="nav-item {{ request()->routeIs('lab-catalog.*') ? 'active' : '' }}" href="{{ route('lab-catalog.index') }}">
                    <x-icon name="lab" class="nav-icon" /> <span>Lab Catalogue</span>
                </a>
                <a class="nav-item {{ request()->routeIs('prescriptions.*') ? 'active' : '' }}" href="{{ route('prescriptions.index') }}">
                    <x-icon name="pharmacy" class="nav-icon" /> <span>Prescriptions</span>
                </a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Pharmacy & Stock</div>
            <div class="nav-list">
                <a class="nav-item {{ request()->routeIs('pharmacies.*') ? 'active' : '' }}" href="{{ route('pharmacies.index') }}">
                    <x-icon name="pharmacy" class="nav-icon" /> <span>Pharmacy</span>
                </a>
                <a class="nav-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                    <x-icon name="inventory" class="nav-icon" /> <span>Inventory</span>
                </a>
                <a class="nav-item {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}" href="{{ route('purchase-orders.index') }}">
                    <x-icon name="requisitions" class="nav-icon" /> <span>Purchase Orders</span>
                </a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Finance</div>
            <div class="nav-list">
                <a class="nav-item {{ request()->routeIs('financial.index') ? 'active' : '' }}" href="{{ route('financial.index') }}">
                    <x-icon name="dashboard" class="nav-icon" /> <span>Overview</span>
                </a>
                <a class="nav-item {{ request()->routeIs('financial.income') ? 'active' : '' }}" href="{{ route('financial.income') }}">
                    <x-icon name="income" class="nav-icon" /> <span>Income</span>
                </a>
                <a class="nav-item {{ request()->routeIs('financial.expenditure') ? 'active' : '' }}" href="{{ route('financial.expenditure') }}">
                    <x-icon name="expenses" class="nav-icon" /> <span>Expenditure</span>
                </a>
                <a class="nav-item {{ request()->routeIs('expenses.*') && ! request()->routeIs('financial.expenditure') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                    <x-icon name="expenses" class="nav-icon" /> <span>Expenses</span>
                </a>
                <a class="nav-item {{ request()->routeIs('billing.*') ? 'active' : '' }}" href="{{ route('billing.index') }}">
                    <x-icon name="billing" class="nav-icon" /> <span>Billing</span>
                </a>
                <a class="nav-item {{ request()->routeIs('service-items.*') ? 'active' : '' }}" href="{{ route('service-items.index') }}">
                    <x-icon name="billing" class="nav-icon" /> <span>Service Catalogue</span>
                </a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Staff</div>
            <div class="nav-list">
                <a class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                    <x-icon name="admin" class="nav-icon" /> <span>Staff Registry</span>
                </a>
                <a class="nav-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
                    <x-icon name="timesheets" class="nav-icon" /> <span>Attendance</span>
                </a>
                <a class="nav-item {{ request()->routeIs('leaves.*') ? 'active' : '' }}" href="{{ route('leaves.index') }}">
                    <x-icon name="appointments" class="nav-icon" /> <span>Leave</span>
                </a>
                <a class="nav-item {{ request()->routeIs('requisitions.*') ? 'active' : '' }}" href="{{ route('requisitions.mine') }}">
                    <x-icon name="requisitions" class="nav-icon" /> <span>Requisitions</span>
                </a>
                <a class="nav-item {{ request()->routeIs('timesheets.*') ? 'active' : '' }}" href="{{ route('timesheets.mine') }}">
                    <x-icon name="timesheets" class="nav-icon" /> <span>Timesheets</span>
                </a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Admin</div>
            <div class="nav-list">
                <a class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <x-icon name="admin" class="nav-icon" /> <span>Users</span>
                </a>
                <a class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                    <x-icon name="roles" class="nav-icon" /> <span>Roles & Permissions</span>
                </a>
                <a class="nav-item {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}">
                    <x-icon name="audit" class="nav-icon" /> <span>Audit Logs</span>
                </a>
                <a class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}" href="{{ route('documents.index') }}">
                    <x-icon name="documents" class="nav-icon" /> <span>Documents</span>
                </a>
                <a class="nav-item {{ request()->routeIs('partners.*') ? 'active' : '' }}" href="{{ route('partners.index') }}">
                    <x-icon name="admin" class="nav-icon" /> <span>Partners</span>
                </a>
                <a class="nav-item {{ request()->routeIs('emergencies.*') ? 'active' : '' }}" href="{{ route('emergencies.index') }}">
                    <x-icon name="admin" class="nav-icon" /> <span>Emergencies</span>
                </a>
            </div>
        </div>
        @endif

        <form class="logout-form" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="sidebar-logout" type="submit">Log out</button>
        </form>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="topbar-left">
                <div class="topbar-greeting">{{ $greeting }}, {{ $name }}</div>
            </div>
            <div class="topbar-meta">
                <div class="top-card">
                    <x-icon name="appointments" class="top-card-icon" />
                    <span class="top-card-value">{{ now()->format('M d, Y') }}</span>
                </div>
                <div class="top-card">
                    <x-icon name="notifications" class="top-card-icon" />
                    <span class="top-card-value">0 pending</span>
                </div>
                <div class="topbar-actions">
                    @yield('topbar_actions')
                </div>
                <div class="user-menu">
                    <button class="top-card user-menu-trigger" type="button">
                        <x-icon name="admin" class="top-card-icon" />
                        <span class="top-card-value">{{ $name }}</span>
                        <span class="user-menu-arrow">▾</span>
                    </button>
                    <div class="user-menu-list" aria-label="User menu">
                        <a href="{{ route('profile.show') }}">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.flash')

        <div class="content-grid">
            @yield('content')
        </div>
    </main>
</div>
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => navigator.serviceWorker.register('/service-worker.js').catch(() => {}));
    }

    document.addEventListener('click', function (event) {
        const menu = document.querySelector('.user-menu');
        if (!menu) return;

        const button = menu.querySelector('.user-menu-trigger');
        const isToggleClick = button && button.contains(event.target);
        const isInsideMenu = menu.contains(event.target);

        if (isToggleClick) {
            menu.classList.toggle('open');
            return;
        }

        if (!isInsideMenu && menu.classList.contains('open')) {
            menu.classList.remove('open');
        }
    });
</script>
</body>
</html>

