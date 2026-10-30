
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">

    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'TolBazar') }}</title>

    {{-- Apply theme before rendering to avoid a flash --}}
    <script>
        (() => {
            try {
                const saved = localStorage.getItem('tolbazar-admin-theme');
                const theme = saved === 'dark' || saved === 'light'
                    ? saved
                    : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

                document.documentElement.dataset.theme = theme;
            } catch (_) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Tailwind v4 + Preline assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            color-scheme: light;
            --accent: #d97706;
            --accent-soft: #fff7e8;
            --sidebar: #111827;
            --sidebar-hover: #202b3d;
            --sidebar-text: #a9b5c7;
            --bg: #f5f7fb;
            --surface: #ffffff;
            --surface-2: #f8fafc;
            --text: #172033;
            --muted: #738096;
            --border: #e5eaf1;
            --success: #059669;
            --danger: #dc2626;
            --topbar-height: 70px;
            --sidebar-width: 270px;
            --sidebar-mini-width: 78px;
            --radius: 14px;
            --shadow: 0 8px 28px rgba(15, 23, 42, .045);
        }

        html[data-theme="dark"] {
            color-scheme: dark;
            --bg: #0b1120;
            --surface: #111a2b;
            --surface-2: #172235;
            --text: #e5eaf2;
            --muted: #98a6ba;
            --border: #263449;
            --accent-soft: #332514;
            --shadow: 0 8px 28px rgba(0, 0, 0, .16);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
            transition: background .2s, color .2s;
        }

        a { text-decoration: none; }
        button, input { font: inherit; }
        button { -webkit-tap-highlight-color: transparent; }

        .admin-shell { min-height: 100vh; }

        /* Sidebar */
        .admin-sidebar {
            position: fixed;
            z-index: 1040;
            inset: 0 auto 0 0;
            width: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            color: var(--sidebar-text);
            background: var(--sidebar);
            border-right: 1px solid rgba(255,255,255,.055);
            transition: width .22s ease, transform .25s ease;
        }

        .brand {
            height: var(--topbar-height);
            padding: 0 21px;
            display: flex;
            align-items: center;
            gap: 11px;
            flex-shrink: 0;
            border-bottom: 1px solid rgba(255,255,255,.07);
            color: #fff;
        }

        .brand:hover { color: #fff; }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 39px;
            height: 39px;
            flex-shrink: 0;
            color: #fff;
            background: linear-gradient(135deg, #f59e0b, #b45309);
            border-radius: 12px;
            font-size: 19px;
            box-shadow: 0 5px 18px rgba(217,119,6,.22);
        }

        .brand-name {
            font-family: "Plus Jakarta Sans", sans-serif;
            font-weight: 800;
            font-size: 17px;
            letter-spacing: -.6px;
            white-space: nowrap;
        }

        .brand-sub {
            display: block;
            margin-top: 2px;
            font-size: 10px;
            color: #94a3b8;
            letter-spacing: 1.7px;
            text-transform: uppercase;
        }

        .sidebar-scroll {
            flex: 1;
            min-height: 0;
            overflow: auto;
            padding: 15px 12px 20px;
            scrollbar-width: thin;
            scrollbar-color: #374151 transparent;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 11px;
            margin: 0 0 18px;
            padding: 13px 11px;
            background: rgba(255,255,255,.045);
            border: 1px solid rgba(255,255,255,.065);
            border-radius: 12px;
            overflow: hidden;
        }

        .admin-avatar {
            width: 39px;
            height: 39px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(135deg, #64748b, #334155);
            font-weight: 800;
        }

        .profile-name {
            font-size: 12px;
            font-weight: 700;
            color: #f8fafc;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .profile-role {
            margin-top: 4px;
            font-size: 10px;
            color: #fbbf24;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .nav-heading {
            padding: 18px 11px 8px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: #64748b;
            white-space: nowrap;
        }

        .side-link,
        .nav-group-toggle {
            position: relative;
            width: 100%;
            min-height: 42px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            margin: 2px 0;
            color: var(--sidebar-text);
            border: 0;
            border-radius: 9px;
            background: transparent;
            text-align: left;
            font-size: 12.5px;
            font-weight: 500;
            transition: background .15s, color .15s;
            cursor: pointer;
        }

        .side-link > i:first-child,
        .nav-group-toggle > i:first-child {
            width: 19px;
            flex-shrink: 0;
            text-align: center;
            font-size: 16px;
        }

        .side-link:hover,
        .nav-group-toggle:hover {
            color: #fff;
            background: var(--sidebar-hover);
        }

        .side-link.active {
            color: #fff;
            font-weight: 700;
            background: linear-gradient(100deg, rgba(245,158,11,.19), rgba(245,158,11,.07));
            box-shadow: inset 0 0 0 1px rgba(245,158,11,.18);
        }

        .side-link.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 10px;
            bottom: 10px;
            width: 3px;
            background: #f59e0b;
            border-radius: 0 4px 4px 0;
        }

        .side-link.active > i:first-child { color: #fbbf24; }

        .side-label {
            flex: 1;
            white-space: nowrap;
        }

        .side-count {
            padding: 3px 7px;
            border-radius: 6px;
            background: rgba(245,158,11,.16);
            color: #fbbf24;
            font-size: 10px;
            font-weight: 800;
        }

        .group-chevron {
            font-size: 11px !important;
            transition: transform .2s;
        }

        .nav-group-toggle[aria-expanded="true"] .group-chevron {
            transform: rotate(90deg);
        }

        .nav-submenu {
            padding: 2px 0 5px 19px;
        }

        .nav-submenu[hidden] { display: none !important; }

        .nav-submenu .side-link {
            min-height: 36px;
            padding: 8px 11px;
            font-size: 11.5px;
        }

        .nav-submenu .side-link > i:first-child { font-size: 14px; }

        .sidebar-footer {
            padding: 12px;
            flex-shrink: 0;
            border-top: 1px solid rgba(255,255,255,.07);
        }

        .sidebar-footer .side-link { margin: 0; }

        /* Main layout */
        .admin-main {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            transition: margin-left .22s ease;
        }

        .admin-topbar {
            position: sticky;
            z-index: 1020;
            top: 0;
            min-height: var(--topbar-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 0 27px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .topbar-left,
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .topbar-title {
            margin: 0;
            color: var(--text);
            font-family: "Plus Jakarta Sans", sans-serif;
            font-weight: 800;
            font-size: 17px;
            letter-spacing: -.5px;
        }

        .breadcrumb {
            margin: 3px 0 0;
            font-size: 10px;
            --bs-breadcrumb-divider: '›';
        }

        .breadcrumb-item a { color: var(--muted); }
        .breadcrumb-item.active { color: var(--text); }

        .icon-button {
            position: relative;
            width: 38px;
            height: 38px;
            display: inline-grid;
            place-items: center;
            flex-shrink: 0;
            color: var(--muted);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 16px;
            transition: .15s;
        }

        .icon-button:hover {
            color: var(--accent);
            border-color: var(--accent);
            background: var(--accent-soft);
        }

        .search-box {
            width: min(260px, 25vw);
            position: relative;
        }

        .search-box > i {
            position: absolute;
            top: 50%;
            left: 13px;
            transform: translateY(-50%);
            color: var(--muted);
        }

        .search-box input {
            width: 100%;
            height: 38px;
            padding: 0 12px 0 36px;
            outline: 0;
            color: var(--text);
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 12px;
        }

        .search-box input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(217,119,6,.1);
        }

        .user-button {
            display: flex;
            align-items: center;
            gap: 9px;
            max-width: 220px;
            padding: 4px 9px 4px 4px;
            color: var(--text);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            text-align: left;
        }

        .user-button .admin-avatar {
            width: 31px;
            height: 31px;
            border-radius: 9px;
            font-size: 12px;
        }

        .user-name {
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            margin-top: 2px;
            color: var(--muted);
            font-size: 10px;
        }

        /* Content and messages */
        .admin-content {
            width: 100%;
            max-width: 1800px;
            margin: 0 auto;
            padding: 25px 27px;
            flex: 1;
        }

        .flash-area { padding: 14px 27px 0; }
        .alert { border-radius: 11px; font-size: 12px; }

        .admin-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 17px 27px;
            color: var(--muted);
            background: var(--surface);
            border-top: 1px solid var(--border);
            font-size: 11px;
        }

        .admin-footer a { color: var(--muted); }
        .admin-footer a:hover { color: var(--accent); }

        /* Existing Bootstrap views compatibility */
        .card {
            color: var(--text);
            background: var(--surface);
            border-color: var(--border);
            border-radius: var(--radius);
        }

        .dropdown-menu {
            padding: 7px;
            color: var(--text);
            background: var(--surface);
            border-color: var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .dropdown-item {
            padding: 9px 11px;
            color: var(--text);
            border-radius: 7px;
            font-size: 12px;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            color: var(--text);
            background: var(--surface-2);
        }

        .dropdown-header { color: var(--muted); }

        html[data-theme="dark"] .table {
            --bs-table-color: var(--text);
            --bs-table-bg: var(--surface);
            --bs-table-border-color: var(--border);
            --bs-table-striped-bg: var(--surface-2);
            --bs-table-hover-bg: var(--surface-2);
        }

        html[data-theme="dark"] .text-dark { color: var(--text) !important; }
        html[data-theme="dark"] .text-muted { color: var(--muted) !important; }
        html[data-theme="dark"] .bg-white { background: var(--surface) !important; }
        html[data-theme="dark"] .bg-light { background: var(--surface-2) !important; }
        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .form-select {
            color: var(--text);
            background-color: var(--surface);
            border-color: var(--border);
        }

        /* Compact sidebar on desktop */
        body.sidebar-mini .admin-sidebar { width: var(--sidebar-mini-width); }
        body.sidebar-mini .admin-main { margin-left: var(--sidebar-mini-width); }
        body.sidebar-mini .brand { padding: 0 19px; }
        body.sidebar-mini .brand-copy,
        body.sidebar-mini .admin-profile .profile-copy,
        body.sidebar-mini .nav-heading,
        body.sidebar-mini .side-label,
        body.sidebar-mini .side-count,
        body.sidebar-mini .group-chevron {
            display: none !important;
        }

        body.sidebar-mini .admin-profile {
            justify-content: center;
            padding: 9px 0;
        }

        body.sidebar-mini .side-link,
        body.sidebar-mini .nav-group-toggle {
            justify-content: center;
            padding: 10px 0;
        }

        body.sidebar-mini .side-link > i:first-child,
        body.sidebar-mini .nav-group-toggle > i:first-child {
            font-size: 18px;
        }

        body.sidebar-mini .nav-submenu { display: none !important; }
        body.sidebar-mini .sidebar-footer .side-link { justify-content: center; }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            z-index: 1035;
            inset: 0;
            background: rgba(2,6,23,.58);
            backdrop-filter: blur(2px);
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                width: min(290px, 86vw);
                transform: translateX(-100%);
            }

            .admin-sidebar.mobile-open { transform: translateX(0); }
            .admin-main,
            body.sidebar-mini .admin-main { margin-left: 0; }

            body.sidebar-mini .admin-sidebar { width: min(290px, 86vw); }
            body.sidebar-mini .admin-sidebar .brand { padding: 0 21px; }

            body.sidebar-mini .brand-copy,
            body.sidebar-mini .admin-profile .profile-copy,
            body.sidebar-mini .nav-heading,
            body.sidebar-mini .side-label,
            body.sidebar-mini .side-count,
            body.sidebar-mini .group-chevron { display: initial !important; }

            body.sidebar-mini .admin-profile { justify-content: flex-start; padding: 13px 11px; }
            body.sidebar-mini .side-link,
            body.sidebar-mini .nav-group-toggle { justify-content: flex-start; padding: 10px 12px; }

            .sidebar-backdrop.visible { display: block; }
            .admin-topbar { padding: 0 16px; }
            .admin-content { padding: 20px 16px; }
            .flash-area { padding: 12px 16px 0; }
            .admin-footer { padding: 15px 16px; }
        }

        @media (max-width: 575.98px) {
            .admin-topbar { gap: 8px; min-height: 62px; }
            .topbar-left, .topbar-actions { gap: 7px; }
            .topbar-title { font-size: 14px; }
            .breadcrumb { display: none; }
            .search-box { display: none; }
            .user-copy { display: none; }
            .icon-button { width: 35px; height: 35px; }
            .admin-content { padding: 15px 12px; }
            .flash-area { padding: 10px 12px 0; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="admin-shell">

    {{-- Sidebar --}}
    <aside class="admin-sidebar" id="adminSidebar" aria-label="Admin navigation">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <span class="brand-mark"><i class="bi bi-layers-half"></i></span>
            <span class="brand-copy">
                <span class="brand-name">{{ config('app.name', 'TolBazar') }}</span>
                <span class="brand-sub">Management Portal</span>
            </span>
        </a>

        <div class="sidebar-scroll">
            @auth
                @php
                    $adminUser = auth()->user();
                    $isSuperAdmin = $adminUser->hasRole('Super Admin');
                @endphp

                <div class="admin-profile">
                    <div class="admin-avatar">
                        {{ strtoupper(substr($adminUser->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="profile-copy min-w-0">
                        <div class="profile-name">{{ $adminUser->name }}</div>
                        <div class="profile-role">
                            {{ $isSuperAdmin ? 'Super Administrator' : 'Administrator' }}
                        </div>
                    </div>
                </div>
            @endauth

            <div class="nav-heading">Workspace</div>

            <a href="{{ route('admin.dashboard') }}"
               class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                <span class="side-label">Overview</span>
            </a>

            {{-- Service management --}}
            <div class="nav-heading">Marketplace</div>

            @php
                $serviceMenuActive = request()->routeIs(
                    'admin.services.*',
                    'admin.service-categories.*',
                    'admin.service-tax-rules.*',
                    'admin.partner-commission-rules.*',
                    'admin.customer-charge-rules.*'
                );
            @endphp

            <button class="nav-group-toggle"
                    type="button"
                    data-nav-group="services"
                    aria-expanded="{{ $serviceMenuActive ? 'true' : 'false' }}">
                <i class="bi bi-grid-3x3-gap"></i>
                <span class="side-label">Service Management</span>
                <i class="bi bi-chevron-right group-chevron"></i>
            </button>

            <div class="nav-submenu" data-nav-submenu="services" @if(!$serviceMenuActive) hidden @endif>
                <a href="{{ route('admin.services.index') }}"
                   class="side-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i><span class="side-label">All Services</span>
                </a>
                <a href="{{ route('admin.service-categories.index') }}"
                   class="side-link {{ request()->routeIs('admin.service-categories.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i><span class="side-label">Categories</span>
                </a>
                <a href="{{ route('admin.service-tax-rules.index') }}"
                   class="side-link {{ request()->routeIs('admin.service-tax-rules.*') ? 'active' : '' }}">
                    <i class="bi bi-percent"></i><span class="side-label">Tax Rules</span>
                </a>
                <a href="{{ route('admin.partner-commission-rules.index') }}"
                   class="side-link {{ request()->routeIs('admin.partner-commission-rules.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i><span class="side-label">Commission Rules</span>
                </a>
                <a href="{{ route('admin.customer-charge-rules.index') }}"
                   class="side-link {{ request()->routeIs('admin.customer-charge-rules.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i><span class="side-label">Customer Charges</span>
                </a>
            </div>

            {{-- Business operations --}}
            <div class="nav-heading">Business Operations</div>

            <button class="nav-group-toggle" type="button"
                    data-nav-group="operations" aria-expanded="false">
                <i class="bi bi-shop"></i>
                <span class="side-label">Partners & Sellers</span>
                <i class="bi bi-chevron-right group-chevron"></i>
            </button>
            <div class="nav-submenu" data-nav-submenu="operations" hidden>
                <a href="#" class="side-link"><i class="bi bi-people"></i><span class="side-label">All Partners</span></a>
                <a href="#" class="side-link"><i class="bi bi-person-check"></i><span class="side-label">Partner Approvals</span></a>
                <a href="#" class="side-link"><i class="bi bi-file-earmark-check"></i><span class="side-label">KYC Verification</span></a>
                <a href="#" class="side-link"><i class="bi bi-person-workspace"></i><span class="side-label">Partner Staff</span></a>
            </div>

            <button class="nav-group-toggle" type="button"
                    data-nav-group="catalog" aria-expanded="false">
                <i class="bi bi-box-seam"></i>
                <span class="side-label">Catalog & Inventory</span>
                <i class="bi bi-chevron-right group-chevron"></i>
            </button>
            <div class="nav-submenu" data-nav-submenu="catalog" hidden>
                <a href="#" class="side-link"><i class="bi bi-box"></i><span class="side-label">Products</span></a>
                <a href="#" class="side-link"><i class="bi bi-tags"></i><span class="side-label">Product Categories</span></a>
                <a href="#" class="side-link"><i class="bi bi-buildings"></i><span class="side-label">Hotels & Rooms</span></a>
                <a href="#" class="side-link"><i class="bi bi-map"></i><span class="side-label">Tours & Activities</span></a>
                <a href="#" class="side-link"><i class="bi bi-car-front"></i><span class="side-label">Vehicle Rentals</span></a>
                <a href="#" class="side-link"><i class="bi bi-cup-hot"></i><span class="side-label">Restaurants & Food</span></a>
            </div>

            <button class="nav-group-toggle" type="button"
                    data-nav-group="orders" aria-expanded="false">
                <i class="bi bi-bag-check"></i>
                <span class="side-label">Bookings & Orders</span>
                <i class="bi bi-chevron-right group-chevron"></i>
            </button>
            <div class="nav-submenu" data-nav-submenu="orders" hidden>
                <a href="#" class="side-link"><i class="bi bi-calendar-check"></i><span class="side-label">All Bookings</span></a>
                <a href="#" class="side-link"><i class="bi bi-cart3"></i><span class="side-label">E-commerce Orders</span></a>
                <a href="#" class="side-link"><i class="bi bi-bus-front"></i><span class="side-label">Bus Tickets</span></a>
                <a href="#" class="side-link"><i class="bi bi-calendar2-week"></i><span class="side-label">Table Reservations</span></a>
                <a href="#" class="side-link"><i class="bi bi-truck"></i><span class="side-label">Deliveries</span></a>
                <a href="#" class="side-link"><i class="bi bi-arrow-counterclockwise"></i><span class="side-label">Cancellations</span></a>
            </div>

            <button class="nav-group-toggle" type="button"
                    data-nav-group="finance" aria-expanded="false">
                <i class="bi bi-wallet2"></i>
                <span class="side-label">Finance</span>
                <i class="bi bi-chevron-right group-chevron"></i>
            </button>
            <div class="nav-submenu" data-nav-submenu="finance" hidden>
                <a href="#" class="side-link"><i class="bi bi-credit-card"></i><span class="side-label">Payments</span></a>
                <a href="#" class="side-link"><i class="bi bi-wallet"></i><span class="side-label">Partner Wallets</span></a>
                <a href="#" class="side-link"><i class="bi bi-bank"></i><span class="side-label">Payouts</span></a>
                <a href="#" class="side-link"><i class="bi bi-receipt-cutoff"></i><span class="side-label">Invoices & VAT</span></a>
                <a href="#" class="side-link"><i class="bi bi-journal-text"></i><span class="side-label">Transactions</span></a>
            </div>

            {{-- Platform administration --}}
            <div class="nav-heading">Platform Administration</div>

            <button class="nav-group-toggle" type="button"
                    data-nav-group="people" aria-expanded="false">
                <i class="bi bi-people"></i>
                <span class="side-label">People & Access</span>
                <i class="bi bi-chevron-right group-chevron"></i>
            </button>
            <div class="nav-submenu" data-nav-submenu="people" hidden>
                <a href="#" class="side-link"><i class="bi bi-person-lines-fill"></i><span class="side-label">Users & Customers</span></a>
                <a href="#" class="side-link"><i class="bi bi-person-badge"></i><span class="side-label">Admin Staff</span></a>
                <a href="#" class="side-link"><i class="bi bi-shield-lock"></i><span class="side-label">Roles & Permissions</span></a>
                <a href="#" class="side-link"><i class="bi bi-person-gear"></i><span class="side-label">Service-wise Access</span></a>
                <a href="#" class="side-link"><i class="bi bi-clock-history"></i><span class="side-label">Activity Logs</span></a>
            </div>

            <button class="nav-group-toggle" type="button"
                    data-nav-group="content" aria-expanded="false">
                <i class="bi bi-window-stack"></i>
                <span class="side-label">Content & Marketing</span>
                <i class="bi bi-chevron-right group-chevron"></i>
            </button>
            <div class="nav-submenu" data-nav-submenu="content" hidden>
                <a href="#" class="side-link"><i class="bi bi-megaphone"></i><span class="side-label">Announcements</span></a>
                <a href="#" class="side-link"><i class="bi bi-images"></i><span class="side-label">Banners</span></a>
                <a href="#" class="side-link"><i class="bi bi-chat-square-text"></i><span class="side-label">Reviews & Moderation</span></a>
                <a href="#" class="side-link"><i class="bi bi-envelope"></i><span class="side-label">Notifications</span></a>
            </div>

            <div class="nav-heading">Insights & System</div>

            <a href="#" class="side-link">
                <i class="bi bi-bar-chart-line"></i><span class="side-label">Reports & Analytics</span>
            </a>
            <a href="#" class="side-link">
                <i class="bi bi-gear"></i><span class="side-label">Platform Settings</span>
            </a>
            <a href="#" class="side-link">
                <i class="bi bi-shield-check"></i><span class="side-label">Security</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <button type="button" class="side-link" id="sidebarCollapse"
                    aria-label="Collapse sidebar">
                <i class="bi bi-layout-sidebar-inset"></i>
                <span class="side-label">Collapse sidebar</span>
            </button>

            @auth
                <form method="POST" action="{{ route('switch.mode', 'customer') }}">
                    @csrf
                    <button class="side-link" type="submit">
                        <i class="bi bi-arrow-left-right"></i>
                        <span class="side-label">Switch to Customer</span>
                    </button>
                </form>
            @endauth
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    {{-- Main content --}}
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="topbar-left">
                <button type="button" class="icon-button" id="sidebarToggle"
                        aria-label="Open navigation" aria-expanded="false">
                    <i class="bi bi-list"></i>
                </button>

                <div class="min-w-0">
                    <h1 class="topbar-title">@yield('title', 'Dashboard')</h1>
                    <nav aria-label="Breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Admin</a>
                            </li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="topbar-actions">
                <form class="search-box" role="search" action="#" method="GET"
                      onsubmit="return false">
                    <i class="bi bi-search"></i>
                    <input id="adminGlobalSearch" type="search"
                           placeholder="Search menu..." autocomplete="off"
                           aria-label="Search navigation">
                </form>

                <button type="button" class="icon-button" id="themeToggle"
                        aria-label="Toggle dark mode" title="Toggle theme">
                    <i class="bi bi-moon-stars" id="themeIcon"></i>
                </button>

                <div class="dropdown">
                    <button type="button" class="icon-button"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside"
                            aria-expanded="false" aria-label="Notifications">
                        <i class="bi bi-bell"></i>
                        <span style="position:absolute;right:8px;top:7px;width:7px;height:7px;border-radius:50%;background:#ef4444;border:1px solid var(--surface)"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="width:min(320px,90vw)">
                        <div class="dropdown-header fw-bold">Notifications</div>
                        <div class="dropdown-item-text small text-muted py-3">
                            Your latest platform notifications will appear here.
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center" href="#">View notifications</a>
                    </div>
                </div>

                @auth
                    <div class="dropdown">
                        <button type="button" class="user-button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="admin-avatar">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </span>
                            <span class="user-copy min-w-0">
                                <span class="user-name d-block">{{ auth()->user()->name }}</span>
                                <span class="user-role d-block">
                                    {{ auth()->user()->hasRole('Super Admin') ? 'Super Admin' : 'Administrator' }}
                                </span>
                            </span>
                            <i class="bi bi-chevron-down small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Account</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="flash-area">
            @foreach (['success', 'error', 'warning', 'info'] as $type)
                @if (session($type))
                    <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show mb-2" role="alert">
                        {{ session($type) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            @endforeach

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                    <strong>Please check the following:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <main class="admin-content" id="mainContent">
            @yield('content')
        </main>

        <footer class="admin-footer">
            <span>
                © {{ date('Y') }}
                <strong>{{ config('app.name', 'TolBazar') }}</strong>
                · All rights reserved.
            </span>
            <div class="d-flex gap-3">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
                <a href="#">Support</a>
                <span>v1.0.0</span>
            </div>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(() => {
    const body = document.body;
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const searchInput = document.getElementById('adminGlobalSearch');

    const isMobile = () => window.matchMedia('(max-width: 991.98px)').matches;

    function closeMobileSidebar() {
        sidebar.classList.remove('mobile-open');
        backdrop.classList.remove('visible');
        sidebarToggle?.setAttribute('aria-expanded', 'false');
        body.style.overflow = '';
    }

    function openMobileSidebar() {
        sidebar.classList.add('mobile-open');
        backdrop.classList.add('visible');
        sidebarToggle?.setAttribute('aria-expanded', 'true');
        body.style.overflow = 'hidden';
    }

    sidebarToggle?.addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.contains('mobile-open')
                ? closeMobileSidebar()
                : openMobileSidebar();
        } else {
            body.classList.toggle('sidebar-mini');
            try {
                localStorage.setItem(
                    'tolbazar-sidebar-mini',
                    body.classList.contains('sidebar-mini') ? '1' : '0'
                );
            } catch (_) {}
        }
    });

    sidebarCollapse?.addEventListener('click', () => {
        if (isMobile()) {
            closeMobileSidebar();
            return;
        }

        body.classList.toggle('sidebar-mini');
        try {
            localStorage.setItem(
                'tolbazar-sidebar-mini',
                body.classList.contains('sidebar-mini') ? '1' : '0'
            );
        } catch (_) {}
    });

    backdrop?.addEventListener('click', closeMobileSidebar);

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeMobileSidebar();
    });

    window.addEventListener('resize', () => {
        if (!isMobile()) {
            closeMobileSidebar();
        }
    });

    // Restore compact sidebar preference on desktop.
    try {
        if (localStorage.getItem('tolbazar-sidebar-mini') === '1' && !isMobile()) {
            body.classList.add('sidebar-mini');
        }
    } catch (_) {}

    // Expand/collapse navigation groups.
    document.querySelectorAll('[data-nav-group]').forEach(button => {
        button.addEventListener('click', () => {
            if (body.classList.contains('sidebar-mini') && !isMobile()) {
                body.classList.remove('sidebar-mini');
            }

            const name = button.dataset.navGroup;
            const submenu = document.querySelector(
                `[data-nav-submenu="${name}"]`
            );

            if (!submenu) return;

            const willOpen = submenu.hidden;
            submenu.hidden = !willOpen;
            button.setAttribute('aria-expanded', String(willOpen));
        });
    });

    // Theme handling.
    function applyTheme(theme, persist = true) {
        const nextTheme = theme === 'dark' ? 'dark' : 'light';
        document.documentElement.dataset.theme = nextTheme;

        if (themeIcon) {
            themeIcon.className = nextTheme === 'dark'
                ? 'bi bi-sun'
                : 'bi bi-moon-stars';
        }

        themeToggle?.setAttribute(
            'aria-label',
            nextTheme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'
        );

        if (persist) {
            try {
                localStorage.setItem('tolbazar-admin-theme', nextTheme);
            } catch (_) {}
        }
    }

    applyTheme(document.documentElement.dataset.theme || 'light', false);

    themeToggle?.addEventListener('click', () => {
        const current = document.documentElement.dataset.theme;
        applyTheme(current === 'dark' ? 'light' : 'dark');
    });

    // Client-side menu filtering; this does not search database records.
    searchInput?.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();

        document.querySelectorAll('.sidebar-scroll .side-link').forEach(link => {
            const matches = link.textContent.toLowerCase().includes(query);
            link.style.display = !query || matches ? '' : 'none';
        });

        document.querySelectorAll('[data-nav-group]').forEach(button => {
            const name = button.dataset.navGroup;
            const submenu = document.querySelector(
                `[data-nav-submenu="${name}"]`
            );

            if (!submenu) return;

            const hasMatch = [...submenu.querySelectorAll('.side-link')]
                .some(link => link.style.display !== 'none');

            if (query && hasMatch) {
                submenu.hidden = false;
                button.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // Automatically close flash messages after a short delay.
    window.setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach(element => {
            if (window.bootstrap?.Alert) {
                bootstrap.Alert.getOrCreateInstance(element).close();
            }
        });
    }, 6000);
})();
</script>

@stack('scripts')
</body>
</html>
