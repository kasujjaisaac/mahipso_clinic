<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mahipso Clinic')</title>
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
        html, body { margin: 0; min-height: 100%; }
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
            grid-template-columns: 240px minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            background: var(--sidebar);
            color: var(--text);
            padding: 1rem 0.75rem 1rem 0.75rem;
            border-right: 2px solid var(--brand);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 4px rgba(0, 0, 0, 0.04);
        }

        .brand {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 0.9rem;
            border-radius: 0;
            background: linear-gradient(135deg, #b8342b 0%, #8d241d 100%);
            color: #ffffff;
            margin-bottom: 1.2rem;
            box-shadow: 0 4px 12px rgba(184, 52, 43, 0.15);
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
            color: rgba(255,255,255,0.82);
            letter-spacing: 0.02em;
        }

        .brand-mark {
            width: 46px;
            height: 46px;
            border-radius: 0;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
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
            margin-top: 1.2rem;
            padding: 1rem 0.6rem;
            border: 1px solid #f0d4d1;
            border-radius: 0;
            background: #ffffff;
        }
        .sidebar-section:first-of-type {
            margin-top: 0;
        }
        .sidebar-section-title {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            margin-bottom: 0.5rem;
            padding: 0 0.2rem;
            color: var(--sidebar-muted);
            font-weight: 700;
        }

        .nav-list {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.52rem 0.55rem;
            border-radius: 0;
            color: var(--text);
            background: transparent;
            transition: background 0.2s, color 0.2s;
        }

        .nav-item:hover,
        .nav-item.active {
            background: #f0f0f0;
            color: var(--brand);
            border-left: 4px solid var(--brand);
            padding-left: 0.5rem;
        }

        .nav-icon {
            width: 22px;
            height: 22px;
            border-radius: 0;
            display: grid;
            place-items: center;
            background: #f0f0f0;
            font-size: 1.05rem;
            flex-shrink: 0;
            color: var(--brand);
        }

        .nav-submenu {
            margin-bottom: 0.25rem;
        }

        .nav-submenu-header {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.52rem 0.55rem;
            border-radius: 0;
            color: var(--text);
            background: transparent;
            transition: background 0.2s, color 0.2s;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.78rem;
        }

        .nav-submenu-header:hover {
            background: #f0f0f0;
            color: var(--brand);
        }

        .submenu-arrow {
            margin-left: auto;
            font-size: 0.7rem;
            transition: transform 0.2s;
            color: var(--muted);
        }

        .nav-submenu-items {
            margin-left: 1rem;
            border-left: 2px solid #f0d4d1;
            padding-left: 0.5rem;
            margin-top: 0.25rem;
        }

        .nav-submenu-items .nav-item {
            padding: 0.4rem 0.5rem;
            margin-bottom: 0.1rem;
            font-size: 0.75rem;
        }

        .nav-submenu-items .nav-item:hover,
        .nav-submenu-items .nav-item.active {
            background: #f8f9fa;
            border-left: 4px solid var(--brand);
            padding-left: 0.4rem;
        }

        .logout-form { margin-top: 0.9rem; }

        .sidebar-logout {
            width: 100%;
            text-align: left;
            border: 1px solid #f0d4d1;
            background: #fff5f5;
            color: var(--brand);
            padding: 0.65rem 0.75rem;
            border-radius: 0;
            cursor: pointer;
            font-weight: 700;
        }

        .main {
            padding: 0.85rem;
            background: #ffffff;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        }

        .content-grid {
            background: #ffffff;
            padding: 0.5rem 0;
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
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            text-align: left;
            padding: 0.55rem 0.6rem;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
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

        @media (max-width: 960px) {
            .app-shell { grid-template-columns: 1fr; }
            .sidebar {
                position: relative;
                height: auto;
                border-right: none;
                border-bottom: 1px solid var(--sidebar-border);
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
    $dashboardHref = $user?->isSuperAdmin() ? route('admin.dashboard') : route('branches.cards');
    $isDashboardRoute = request()->routeIs('admin.dashboard')
        || request()->routeIs('branches.cards')
        || request()->routeIs('branches.dashboard');
    
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
                <span>Administrator Portal</span>
            </div>
        </div>

        <!-- Professional Sidebar Navigation -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">Main</div>
            <div class="nav-list">
                <a class="nav-item {{ $isDashboardRoute ? 'active' : '' }}" href="{{ $dashboardHref }}">
                    <x-icon name="dashboard" class="nav-icon" /> <span>Dashboard</span>
                </a>
                <a class="nav-item {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
                    <x-icon name="appointments" class="nav-icon" /> <span>Appointments</span>
                </a>
                <a class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}">
                    <x-icon name="patients" class="nav-icon" /> <span>Patients</span>
                </a>
                <a class="nav-item {{ request()->routeIs('messages.*') ? 'active' : '' }}" href="{{ route('messages.index') }}">
                    <x-icon name="messages" class="nav-icon" /> <span>Messages</span>
                </a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Clinical</div>
            <div class="nav-list">
                <a class="nav-item {{ request()->routeIs('visits.*') ? 'active' : '' }}" href="{{ route('visits.index') }}">
                    <x-icon name="appointments" class="nav-icon" /> <span>Visits</span>
                </a>
                <a class="nav-item {{ request()->routeIs('medical-records.*') ? 'active' : '' }}" href="{{ route('medical-records.index') }}">
                    <x-icon name="patients" class="nav-icon" /> <span>Medical Records</span>
                </a>
                <a class="nav-item {{ request()->routeIs('hiv-records.*') ? 'active' : '' }}" href="{{ route('hiv-records.index') }}">
                    <x-icon name="lab" class="nav-icon" /> <span>HIV Records</span>
                </a>
                <a class="nav-item {{ request()->routeIs('laboratory.*') ? 'active' : '' }}" href="{{ route('laboratory.index') }}">
                    <x-icon name="lab" class="nav-icon" /> <span>Laboratory</span>
                </a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Pharmacy & Inventory</div>
            <div class="nav-list">
                <a class="nav-item {{ request()->routeIs('pharmacies.*') ? 'active' : '' }}" href="{{ route('pharmacies.index') }}">
                    <x-icon name="pharmacy" class="nav-icon" /> <span>Pharmacy</span>
                </a>
                <a class="nav-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                    <x-icon name="inventory" class="nav-icon" /> <span>Inventory</span>
                </a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Financial</div>
            <div class="nav-list">
                <!-- Income Submenu -->
                <div class="nav-submenu">
                    <div class="nav-submenu-header" onclick="toggleSubmenu('income-submenu')">
                        <x-icon name="income" class="nav-icon" /> 
                        <span>Income</span>
                        <span class="submenu-arrow" id="income-arrow">▶</span>
                    </div>
                    <div class="nav-submenu-items" id="income-submenu" style="display: none;">
                        <a class="nav-item {{ request()->routeIs('financial.income') ? 'active' : '' }}" href="{{ route('financial.income') }}">
                            <x-icon name="billing" class="nav-icon" /> <span>Patient Billing</span>
                        </a>
                        <a class="nav-item {{ request()->routeIs('pharmacies.sales.*') ? 'active' : '' }}" href="{{ route('pharmacies.index') }}#sales">
                            <x-icon name="sales" class="nav-icon" /> <span>Pharmacy Sales</span>
                        </a>
                    </div>
                </div>

                <!-- Expenditure Submenu -->
                <div class="nav-submenu">
                    <div class="nav-submenu-header" onclick="toggleSubmenu('expenditure-submenu')">
                        <x-icon name="expenses" class="nav-icon" /> 
                        <span>Expenditure</span>
                        <span class="submenu-arrow" id="expenditure-arrow">▶</span>
                    </div>
                    <div class="nav-submenu-items" id="expenditure-submenu" style="display: none;">
                        <a class="nav-item {{ request()->routeIs('financial.expenditure') ? 'active' : '' }}" href="{{ route('financial.expenditure') }}">
                            <x-icon name="expenses" class="nav-icon" /> <span>Operational Expenses</span>
                        </a>
                        <a class="nav-item {{ request()->routeIs('expenses.*') && !request()->routeIs('financial.expenditure') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                            <x-icon name="expenses" class="nav-icon" /> <span>Manage Expenses</span>
                        </a>
                        <a class="nav-item" href="#" onclick="alert('Utilities tracking coming soon!')">
                            <x-icon name="utilities" class="nav-icon" /> <span>Utilities</span>
                        </a>
                        <a class="nav-item" href="#" onclick="alert('Payroll management coming soon!')">
                            <x-icon name="payroll" class="nav-icon" /> <span>Payroll</span>
                        </a>
                    </div>
                </div>

                <!-- Financial Overview -->
                <a class="nav-item {{ request()->routeIs('financial.index') ? 'active' : '' }}" href="{{ route('financial.index') }}">
                    <x-icon name="dashboard" class="nav-icon" /> <span>Financial Overview</span>
                </a>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Notifications</div>
            <div class="nav-list">
                <a class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                    <x-icon name="notifications" class="nav-icon" /> <span>Notifications</span>
                </a>
            </div>
        </div>

        <!-- Admin & Security at the bottom -->
        <div class="sidebar-section" style="margin-top:2rem;">
            <div class="sidebar-section-title">Admin & Security</div>
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
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Other</div>
            <div class="nav-list">
                <a class="nav-item {{ request()->routeIs('partners.*') ? 'active' : '' }}" href="{{ route('partners.index') }}">
                    <x-icon name="admin" class="nav-icon" /> <span>Partners</span>
                </a>
                <a class="nav-item {{ request()->routeIs('emergencies.*') ? 'active' : '' }}" href="{{ route('emergencies.index') }}">
                    <x-icon name="admin" class="nav-icon" /> <span>Emergencies</span>
                </a>
                <a class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}" href="{{ route('documents.index') }}">
                    <x-icon name="admin" class="nav-icon" /> <span>Documents</span>
                </a>
            </div>
        </div>

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
                        <a href="{{ route('users.show', auth()->user()) }}">Profile</a>
                        <a href="{{ route('users.edit', auth()->user()) }}">Change password</a>
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
    function toggleSubmenu(submenuId) {
        const submenu = document.getElementById(submenuId);
        const arrow = document.getElementById(submenuId.replace('-submenu', '-arrow'));
        
        if (submenu.style.display === 'none' || submenu.style.display === '') {
            submenu.style.display = 'block';
            arrow.textContent = '▼';
            arrow.style.transform = 'rotate(0deg)';
        } else {
            submenu.style.display = 'none';
            arrow.textContent = '▶';
            arrow.style.transform = 'rotate(0deg)';
        }
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
