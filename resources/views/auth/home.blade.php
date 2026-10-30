@php
    $user = auth()->user();

    $hasCustomer = $user ? $user->hasRole('Customer') : false;
    $hasSellerRole = $user ? $user->hasRole('Seller') : false;
    $hasDelivery = $user ? $user->hasRole('Delivery Boy') : false;

    $isOwner = $user ? $user->isSellerOwner() : false;
    $isBranchStaff = $user ? $user->isBranchStaff() : false;

    $branchStaff = ($user && $isBranchStaff) ? $user->branchStaff : null;
    $staffBranch = $branchStaff?->branch;
    $staffRole = $branchStaff?->role;
    $staffRoleLabel = $staffRole ? ucfirst($staffRole) : null;

    $sellerProfile = $user?->sellerProfile;

    $ownerBranches = ($user && $isOwner && $sellerProfile)
        ? $sellerProfile->branches()->where('status', 1)->orderByDesc('is_default')->get()
        : collect();

    $routeExists = fn ($name) => \Illuminate\Support\Facades\Route::has($name);

    /* ── SERVICES ───────────────────────────────────────────── */
    $serviceLinks = [
        ['name' => 'Hotels', 'route' => 'hotels.index', 'icon' => 'fa-hotel',
            'description' => 'Find a stay that suits you', 'color' => '#e8f1ff', 'iconColor' => '#3265bc'],
        ['name' => 'Tours & Packages', 'route' => 'tours.index', 'icon' => 'fa-mountain-sun',
            'description' => 'Explore Nepal and beyond', 'color' => '#e5f6ed', 'iconColor' => '#218253'],
        ['name' => 'Activities', 'route' => 'activities.index', 'icon' => 'fa-person-hiking',
            'description' => 'Discover memorable experiences', 'color' => '#fff0df', 'iconColor' => '#bd6a19'],
        ['name' => 'Shop', 'route' => 'services.index', 'icon' => 'fa-bag-shopping',
            'description' => 'Browse all services & products', 'color' => '#f3eaff', 'iconColor' => '#8654bb'],
        ['name' => 'Bus Tickets', 'route' => 'buses.index', 'icon' => 'fa-bus',
            'description' => 'Plan your next journey', 'color' => '#e4f6f8', 'iconColor' => '#168392'],
        ['name' => 'Vehicle Rental', 'route' => 'rentals.index', 'icon' => 'fa-car',
            'description' => 'Find vehicles for your trip', 'color' => '#ffe9ed', 'iconColor' => '#c34b65'],
        ['name' => 'Restaurants', 'route' => 'restaurants.index', 'icon' => 'fa-utensils',
            'description' => 'Discover food and dining', 'color' => '#fff4d8', 'iconColor' => '#a87912'],
    ];

    /* ── ACCOUNT MENU SECTIONS (smart grouping) ────────────── */
    $accountMenuSections = [
        [
            'title' => 'My Activity',
            'items' => [
                ['label' => 'My Profile',      'icon' => 'fa-user',              'route' => 'profile.me'],
                ['label' => 'Edit Profile',    'icon' => 'fa-pen',               'route' => 'profile.edit'],
                ['label' => 'My Bookings',     'icon' => 'fa-calendar-check',    'route' => 'my-bookings.index'],
                ['label' => 'My Cart',         'icon' => 'fa-cart-shopping',     'route' => 'cart.index'],
            ],
        ],
        [
            'title' => 'My Wallet',
            'items' => [
                ['label' => 'Wallet Dashboard',  'icon' => 'fa-wallet',            'route' => 'wallet.dashboard'],
                ['label' => 'Transactions',      'icon' => 'fa-arrow-right-arrow-left', 'route' => 'wallet.transactions'],
                ['label' => 'Add Money',         'icon' => 'fa-circle-plus',       'route' => 'wallet.add-money'],
                ['label' => 'Load Requests',     'icon' => 'fa-clock-rotate-left', 'route' => 'wallet.load-requests'],
                ['label' => 'Withdraw',          'icon' => 'fa-money-bill-transfer', 'route' => 'wallet.withdraw'],
                ['label' => 'Withdraw Requests', 'icon' => 'fa-hourglass-half',    'route' => 'wallet.withdraw-requests'],
                ['label' => 'Transfer',          'icon' => 'fa-paper-plane',       'route' => 'wallet.transfer'],
                ['label' => 'Bank Accounts',     'icon' => 'fa-building-columns',  'route' => 'wallet.bank-accounts'],
                ['label' => 'Wallet Security',   'icon' => 'fa-shield-halved',     'route' => 'wallet.security'],
                ['label' => 'Rewards',           'icon' => 'fa-gift',              'route' => 'wallet.rewards'],
            ],
        ],
        [
            'title' => 'Account Settings',
            'items' => [
                ['label' => 'Settings Hub',    'icon' => 'fa-gear',              'route' => 'account.settings'],
                ['label' => 'Login Activity',  'icon' => 'fa-clock-rotate-left', 'route' => 'account.login-activity'],
                ['label' => 'Active Sessions', 'icon' => 'fa-laptop',            'route' => 'account.sessions'],
                ['label' => 'Switch Account',  'icon' => 'fa-repeat',            'route' => 'account.switcher'],
                ['label' => 'Verification',    'icon' => 'fa-circle-check',      'route' => 'verification.center'],
            ],
        ],
        [
            'title' => 'Profile & Security',
            'items' => [
                ['label' => 'Change Username', 'icon' => 'fa-at',           'route' => 'account.change-username'],
                ['label' => 'Change Email',    'icon' => 'fa-envelope',     'route' => 'account.change-email'],
                ['label' => 'Change Name',     'icon' => 'fa-id-badge',     'route' => 'account.change-name'],
                ['label' => 'Change Mobile',   'icon' => 'fa-mobile-screen','route' => 'account.change-mobile'],
            ],
        ],
    ];
@endphp

@extends('layouts.guest')

@section('title', 'Home — ' . config('app.name', 'TolBazar'))

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
    :root {
        color-scheme: light;
        --brand: #625bf6;
        --brand-hover: #5148e5;
        --brand-soft: #f1f0ff;
        --brand-glow: rgba(98,91,246,.18);
        --page: #ffffff;
        --surface: #ffffff;
        --surface-soft: #f7f8fc;
        --text: #1a1f2e;
        --muted: #6f7689;
        --border: #e8eaf1;
        --success: #15966b;
        --warning: #d97706;
        --danger: #dc4545;
        --radius: 16px;
        --radius-lg: 22px;
        --shadow-sm: 0 2px 8px rgba(25,33,60,.05);
        --shadow: 0 8px 28px rgba(25,33,60,.08);
        --shadow-lg: 0 20px 50px rgba(25,33,60,.12);
        --header-h: 74px;
    }

    html[data-theme="dark"] {
        color-scheme: dark;
        --page: #0f1320;
        --surface: #191e2e;
        --surface-soft: #22283a;
        --text: #edf0f7;
        --muted: #a0aac0;
        --border: #30364a;
        --brand-soft: #302d55;
        --brand-glow: rgba(98,91,246,.28);
        --shadow: 0 8px 28px rgba(0,0,0,.25);
        --shadow-lg: 0 20px 50px rgba(0,0,0,.35);
    }

    .tb-home { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: var(--page); color: var(--text); overflow-x: hidden; }
    .tb-home * { box-sizing: border-box; }
    .tb-home a { text-decoration: none; color: inherit; }

    /* ── ANNOUNCEMENT ─────────────────────────────────────── */
    .tb-announce {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 9px 18px; color: #fff; text-align: center;
        background: linear-gradient(100deg, #5148e5, #7968f8);
        font-size: 12px; font-weight: 600;
    }
    .tb-announce i { opacity: .9; }

    /* ── HEADER ───────────────────────────────────────────── */
    .tb-header {
        position: sticky; top: 0; z-index: 1000;
        background: var(--surface); border-bottom: 1px solid var(--border);
        backdrop-filter: saturate(180%) blur(12px);
        background: color-mix(in srgb, var(--surface) 92%, transparent);
    }
    .tb-header-inner {
        max-width: 1400px; margin: auto; min-height: var(--header-h);
        padding: 0 28px; display: flex; align-items: center; gap: 22px;
    }

    .tb-brand {
        display: inline-flex; align-items: center; gap: 10px; flex-shrink: 0;
    }
    .tb-brand-mark {
        width: 42px; height: 42px; display: grid; place-items: center;
        border-radius: 13px; font-size: 21px; color: #fff;
        background: linear-gradient(135deg, #7d73ff, #5148e5);
        box-shadow: 0 6px 16px var(--brand-glow);
        transition: transform .2s;
    }
    .tb-brand:hover .tb-brand-mark { transform: translateY(-2px) rotate(-3deg); }
    .tb-brand-copy { line-height: 1; }
    .tb-brand-name { font-size: 19px; font-weight: 900; letter-spacing: -.7px; color: var(--text); }
    .tb-brand-tag { font-size: 9px; color: var(--muted); font-weight: 700; letter-spacing: 1.1px; margin-top: 3px; }

    /* Nav */
    .tb-nav { display: flex; align-items: center; gap: 3px; margin-left: auto; }
    .tb-nav-link {
        padding: 9px 12px; border-radius: 9px; color: var(--muted);
        font-size: 12px; font-weight: 600; white-space: nowrap;
        display: inline-flex; align-items: center; gap: 7px;
        transition: .15s ease;
    }
    .tb-nav-link:hover, .tb-nav-link.active { color: var(--brand); background: var(--brand-soft); }
    .tb-nav-link i { font-size: 13px; }

    .tb-header-actions { display: flex; align-items: center; gap: 9px; flex-shrink: 0; }

    .tb-icon-btn {
        width: 40px; height: 40px; display: inline-grid; place-items: center;
        border: 1px solid var(--border); border-radius: 11px;
        background: var(--surface); color: var(--text);
        font-size: 16px; cursor: pointer; transition: .15s;
        position: relative;
    }
    .tb-icon-btn:hover { color: var(--brand); background: var(--surface-soft); border-color: var(--brand); }
    .tb-icon-btn .tb-badge {
        position: absolute; top: -4px; right: -4px; min-width: 17px; height: 17px;
        padding: 0 4px; border-radius: 9px; background: var(--danger); color: #fff;
        font-size: 9px; font-weight: 800; display: grid; place-items: center;
    }

    .tb-btn {
        min-height: 40px; display: inline-flex; align-items: center; justify-content: center;
        gap: 8px; padding: 0 18px; border-radius: 11px; border: 1px solid transparent;
        font-size: 12px; font-weight: 700; white-space: nowrap; cursor: pointer;
        transition: transform .15s, background .15s, box-shadow .15s;
    }
    .tb-btn:hover { transform: translateY(-1px); }
    .tb-btn-primary {
        background: var(--brand); color: #fff;
        box-shadow: 0 6px 16px var(--brand-glow);
    }
    .tb-btn-primary:hover { background: var(--brand-hover); color: #fff; box-shadow: 0 8px 22px var(--brand-glow); }
    .tb-btn-outline { border-color: var(--border); color: var(--text); background: var(--surface); }
    .tb-btn-outline:hover { color: var(--brand); border-color: var(--brand); }

    /* Dropdown */
    .tb-dropdown-wrap { position: relative; }
    .tb-dropdown {
        display: none; position: absolute; top: calc(100% + 10px); right: 0;
        z-index: 1100; width: 300px; max-height: 82vh; overflow-y: auto;
        padding: 8px; background: var(--surface);
        border: 1px solid var(--border); border-radius: 16px;
        box-shadow: var(--shadow-lg);
        scrollbar-width: thin; scrollbar-color: var(--border) transparent;
    }
    .tb-dropdown.open { display: block; animation: tbDropIn .18s ease-out; }
    @keyframes tbDropIn {
        from { opacity: 0; transform: translateY(-6px) scale(.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .tb-dd-head {
        padding: 12px 12px; border-bottom: 1px solid var(--border);
        margin-bottom: 6px;
    }
    .tb-dd-head-name { font-size: 13px; font-weight: 800; color: var(--text); }
    .tb-dd-head-meta { font-size: 11px; color: var(--muted); margin-top: 4px; overflow-wrap: anywhere; }
    .tb-dd-section {
        padding: 11px 12px 5px; font-size: 9px; font-weight: 800;
        letter-spacing: 1.1px; text-transform: uppercase; color: var(--muted);
    }
    .tb-dd-link {
        display: flex; align-items: center; gap: 11px;
        width: 100%; padding: 10px 12px; border: 0; border-radius: 9px;
        background: transparent; color: var(--text); text-align: left;
        font-size: 12px; font-weight: 500; cursor: pointer;
        transition: .12s ease;
    }
    .tb-dd-link i { width: 18px; font-size: 14px; color: var(--muted); transition: .12s; }
    .tb-dd-link:hover { color: var(--brand); background: var(--surface-soft); }
    .tb-dd-link:hover i { color: var(--brand); }
    .tb-dd-divider { height: 1px; background: var(--border); margin: 6px 0; }

    .tb-avatar {
        width: 34px; height: 34px; flex: 0 0 34px; display: grid; place-items: center;
        border-radius: 11px; background: var(--brand-soft); color: var(--brand);
        font-weight: 800; font-size: 13px;
    }

    /* Search panel */
    .tb-search-panel {
        display: none; padding: 16px 28px; background: var(--surface);
        border-top: 1px solid var(--border);
    }
    .tb-search-panel.open { display: block; animation: tbDropIn .2s; }
    .tb-search-form { max-width: 900px; margin: auto; display: flex; gap: 10px; }
    .tb-search-input {
        flex: 1; min-width: 0; height: 46px; padding: 0 16px;
        color: var(--text); background: var(--surface-soft);
        border: 1px solid var(--border); border-radius: 12px;
        font-size: 13px; outline: none;
    }
    .tb-search-input:focus { border-color: var(--brand); box-shadow: 0 0 0 4px var(--brand-glow); }

    /* Mobile */
    .tb-mobile-toggle { display: none; }
    .tb-mobile-nav {
        display: none; padding: 12px 18px 18px;
        background: var(--surface); border-top: 1px solid var(--border);
        max-height: 78vh; overflow-y: auto;
    }
    .tb-mobile-nav.open { display: block; }
    .tb-mobile-section {
        padding: 14px 12px 6px; font-size: 9px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 1.1px; color: var(--muted);
    }
    .tb-mobile-link {
        display: flex; align-items: center; gap: 11px;
        padding: 11px 12px; border-radius: 10px;
        font-size: 13px; font-weight: 500; color: var(--text);
        transition: .12s;
    }
    .tb-mobile-link:hover { background: var(--brand-soft); color: var(--brand); }
    .tb-mobile-link i { width: 20px; font-size: 15px; color: var(--muted); }
    .tb-mobile-link:hover i { color: var(--brand); }

    /* ── HERO ─────────────────────────────────────────────── */
    .tb-hero {
        position: relative; padding: 72px 28px 120px;
        background:
            radial-gradient(circle at 15% 20%, rgba(121,104,248,.28), transparent 45%),
            radial-gradient(circle at 85% 0%, rgba(21,150,107,.18), transparent 40%),
            linear-gradient(135deg, #1a1f3a 0%, #252656 50%, #1c3055 100%);
        color: #fff; overflow: hidden;
    }
    html[data-theme="dark"] .tb-hero {
        background:
            radial-gradient(circle at 15% 20%, rgba(121,104,248,.35), transparent 45%),
            radial-gradient(circle at 85% 0%, rgba(21,150,107,.22), transparent 40%),
            linear-gradient(135deg, #0c1024 0%, #161a3a 50%, #111f3b 100%);
    }
    .tb-hero::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.08) 1px, transparent 1px);
        background-size: 32px 32px; opacity: .35; pointer-events: none;
    }
    .tb-hero-inner { max-width: 1240px; margin: auto; position: relative; z-index: 1; }
    .tb-hero-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 7px 14px; border-radius: 999px;
        background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18);
        font-size: 10px; font-weight: 800; letter-spacing: 1.3px;
        color: #b8b0ff; backdrop-filter: blur(8px);
    }
    .tb-hero h1 {
        font-size: clamp(28px, 4.8vw, 56px); font-weight: 900;
        letter-spacing: -1.6px; line-height: 1.05; margin: 20px 0 18px;
        max-width: 780px;
    }
    .tb-hero h1 span {
        background: linear-gradient(120deg, #b8b0ff, #7ce3c2);
        -webkit-background-clip: text; background-clip: text; color: transparent;
    }
    .tb-hero p {
        font-size: clamp(14px, 1.5vw, 17px); line-height: 1.7;
        color: rgba(255,255,255,.82); max-width: 620px; margin-bottom: 32px;
    }
    .tb-hero-ctas { display: flex; flex-wrap: wrap; gap: 12px; }
    .tb-hero-btn {
        min-height: 52px; padding: 0 26px; border-radius: 13px;
        font-size: 14px; font-weight: 700;
        display: inline-flex; align-items: center; gap: 10px;
        transition: transform .15s, box-shadow .15s;
    }
    .tb-hero-btn:hover { transform: translateY(-2px); }
    .tb-hero-btn-primary { background: #fff; color: #1a1f3a; box-shadow: 0 10px 28px rgba(0,0,0,.2); }
    .tb-hero-btn-primary:hover { color: #1a1f3a; }
    .tb-hero-btn-outline {
        border: 1px solid rgba(255,255,255,.3); color: #fff;
        background: rgba(255,255,255,.06); backdrop-filter: blur(8px);
    }
    .tb-hero-btn-outline:hover { color: #fff; background: rgba(255,255,255,.12); }

    .tb-hero-stats {
        display: flex; flex-wrap: wrap; gap: 40px; margin-top: 48px;
        padding-top: 32px; border-top: 1px solid rgba(255,255,255,.12);
    }
    .tb-hero-stat-num { font-size: 26px; font-weight: 900; letter-spacing: -.8px; }
    .tb-hero-stat-lbl { font-size: 11px; color: rgba(255,255,255,.65); font-weight: 600; margin-top: 4px; }

    /* ── SEARCH BAR (overlapping) ─────────────────────────── */
    .tb-search-wrap { max-width: 1160px; margin: -58px auto 0; padding: 0 28px; position: relative; z-index: 3; }
    .tb-search-card {
        background: var(--surface); border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg); border: 1px solid var(--border);
        padding: 22px; display: grid;
        grid-template-columns: 2fr 1fr auto; gap: 14px;
    }
    .tb-search-field { position: relative; }
    .tb-search-field i {
        position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
        color: var(--brand); font-size: 15px;
    }
    .tb-search-field input, .tb-search-field select {
        width: 100%; height: 52px; padding: 0 16px 0 44px;
        background: var(--surface-soft); border: 1px solid var(--border);
        border-radius: 12px; color: var(--text); font-size: 13px; font-weight: 500;
        outline: none; appearance: none;
    }
    .tb-search-field input:focus, .tb-search-field select:focus {
        border-color: var(--brand); box-shadow: 0 0 0 4px var(--brand-glow);
    }
    .tb-search-submit {
        height: 52px; padding: 0 30px; border-radius: 12px;
        background: var(--brand); color: #fff; border: 0;
        font-size: 13px; font-weight: 800; cursor: pointer;
        display: inline-flex; align-items: center; gap: 9px;
        box-shadow: 0 8px 20px var(--brand-glow);
        transition: .15s;
    }
    .tb-search-submit:hover { background: var(--brand-hover); transform: translateY(-1px); }

    /* ── SECTIONS ─────────────────────────────────────────── */
    .tb-section { max-width: 1400px; margin: auto; padding: 76px 28px; }
    .tb-section-sm { padding: 46px 28px; }
    .tb-sec-head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 16px; margin-bottom: 38px; }
    .tb-sec-eyebrow {
        display: inline-block; font-size: 10px; font-weight: 800; letter-spacing: 1.4px;
        text-transform: uppercase; color: var(--brand); margin-bottom: 10px;
    }
    .tb-sec-title { font-size: clamp(22px, 3vw, 34px); font-weight: 900; letter-spacing: -.9px; margin: 0; }
    .tb-sec-desc { font-size: 14px; color: var(--muted); margin: 8px 0 0; max-width: 620px; }
    .tb-sec-link {
        font-size: 12px; font-weight: 800; color: var(--brand);
        display: inline-flex; align-items: center; gap: 8px;
    }
    .tb-sec-link:hover { gap: 12px; }

    /* ── SERVICE CARDS ────────────────────────────────────── */
    .tb-services { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
    .tb-service-card {
        display: block; padding: 26px 22px; border-radius: var(--radius);
        background: var(--surface); border: 1px solid var(--border);
        box-shadow: var(--shadow-sm); transition: .2s ease;
        position: relative; overflow: hidden;
    }
    .tb-service-card::after {
        content: ''; position: absolute; inset: 0;
        background: radial-gradient(circle at 100% 100%, var(--brand-glow), transparent 60%);
        opacity: 0; transition: opacity .25s;
    }
    .tb-service-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--brand); }
    .tb-service-card:hover::after { opacity: 1; }
    .tb-service-card > * { position: relative; z-index: 1; }
    .tb-service-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: grid; place-items: center; font-size: 22px;
        margin-bottom: 18px; transition: transform .2s;
    }
    .tb-service-card:hover .tb-service-icon { transform: scale(1.08) rotate(-4deg); }
    .tb-service-card h3 { font-size: 15px; font-weight: 800; margin: 0 0 6px; color: var(--text); letter-spacing: -.2px; }
    .tb-service-card p { font-size: 12px; color: var(--muted); margin: 0; line-height: 1.55; }

    /* ── DESTINATIONS ─────────────────────────────────────── */
    .tb-dest-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .tb-destination {
        position: relative; border-radius: var(--radius); overflow: hidden;
        aspect-ratio: 3/4; display: block; transition: transform .3s;
    }
    .tb-destination img {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform .5s ease;
    }
    .tb-destination::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,.78) 100%);
        transition: opacity .3s;
    }
    .tb-destination:hover { transform: translateY(-5px); }
    .tb-destination:hover img { transform: scale(1.08); }
    .tb-dest-caption {
        position: absolute; left: 18px; right: 18px; bottom: 18px;
        z-index: 2; color: #fff;
    }
    .tb-dest-caption h3 { font-size: 17px; font-weight: 800; margin: 0 0 4px; letter-spacing: -.3px; }
    .tb-dest-caption span { font-size: 11px; opacity: .85; }

    /* ── PROMO / CTA ──────────────────────────────────────── */
    .tb-promo {
        border-radius: var(--radius-lg); padding: 56px 48px;
        background:
            radial-gradient(circle at 85% 20%, rgba(121,104,248,.25), transparent 50%),
            linear-gradient(135deg, #1e2456, #0f1d3d);
        color: #fff; overflow: hidden; position: relative;
    }
    .tb-promo::after {
        content: ''; position: absolute; right: -60px; top: -60px;
        width: 300px; height: 300px; border-radius: 50%;
        background: radial-gradient(circle, rgba(121,104,248,.4), transparent 70%);
    }
    .tb-promo-inner { position: relative; z-index: 1; max-width: 720px; }
    .tb-promo-badge {
        display: inline-block; padding: 7px 14px; border-radius: 999px;
        background: rgba(255,193,7,.15); border: 1px solid rgba(255,193,7,.35);
        color: #ffc107; font-size: 10px; font-weight: 800; letter-spacing: 1.2px;
        margin-bottom: 20px;
    }
    .tb-promo h2 { font-size: clamp(24px, 3.4vw, 40px); font-weight: 900; letter-spacing: -1.1px; margin: 0 0 16px; }
    .tb-promo p { color: rgba(255,255,255,.78); font-size: 15px; line-height: 1.7; margin: 0 0 30px; max-width: 560px; }
    .tb-promo-btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 0 26px; min-height: 52px; border-radius: 13px;
        background: #fff; color: #1e2456; font-size: 14px; font-weight: 800;
        box-shadow: 0 10px 28px rgba(0,0,0,.22); transition: transform .15s;
    }
    .tb-promo-btn:hover { transform: translateY(-2px); color: #1e2456; }

    /* ── FEATURES ─────────────────────────────────────────── */
    .tb-features { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
    .tb-feature {
        display: flex; gap: 16px; align-items: flex-start;
        padding: 24px; border-radius: var(--radius);
        background: var(--surface); border: 1px solid var(--border);
        box-shadow: var(--shadow-sm); transition: .2s;
    }
    .tb-feature:hover { box-shadow: var(--shadow); transform: translateY(-2px); }
    .tb-feature-icon {
        width: 46px; height: 46px; flex: 0 0 46px; border-radius: 12px;
        background: var(--brand-soft); color: var(--brand);
        display: grid; place-items: center; font-size: 18px;
    }
    .tb-feature h3 { font-size: 14px; font-weight: 800; margin: 0 0 6px; letter-spacing: -.2px; }
    .tb-feature p { font-size: 12px; color: var(--muted); margin: 0; line-height: 1.6; }

    /* ── FOOTER ───────────────────────────────────────────── */
    .tb-footer { background: var(--surface); border-top: 1px solid var(--border); margin-top: 40px; }
    .tb-footer-grid {
        max-width: 1400px; margin: auto; padding: 64px 28px 40px;
        display: grid; grid-template-columns: 1.6fr repeat(3, 1fr); gap: 40px;
    }
    .tb-footer-desc { font-size: 12px; color: var(--muted); line-height: 1.9; margin: 16px 0 20px; max-width: 340px; }
    .tb-footer-col h4 {
        font-size: 11px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 1.1px; color: var(--text); margin: 0 0 18px;
    }
    .tb-footer-col a {
        display: block; margin: 10px 0; font-size: 12px;
        color: var(--muted); transition: color .15s;
    }
    .tb-footer-col a:hover { color: var(--brand); }
    .tb-socials { display: flex; gap: 9px; }
    .tb-social {
        width: 36px; height: 36px; border-radius: 10px;
        border: 1px solid var(--border); display: grid; place-items: center;
        color: var(--muted); font-size: 14px; transition: .15s;
    }
    .tb-social:hover { color: var(--brand); background: var(--brand-soft); border-color: var(--brand); }
    .tb-footer-bottom {
        max-width: 1400px; margin: auto; padding: 22px 28px;
        border-top: 1px solid var(--border);
        display: flex; flex-wrap: wrap; justify-content: space-between;
        gap: 12px; font-size: 11px; color: var(--muted);
    }

    /* ── BACK TO TOP ──────────────────────────────────────── */
    .tb-back-top {
        position: fixed; right: 22px; bottom: 22px; z-index: 900;
        width: 46px; height: 46px; border-radius: 13px;
        background: var(--brand); color: #fff; border: 0; cursor: pointer;
        display: grid; place-items: center; font-size: 16px;
        box-shadow: 0 10px 28px var(--brand-glow);
        opacity: 0; pointer-events: none; transform: translateY(10px);
        transition: .25s;
    }
    .tb-back-top.visible { opacity: 1; pointer-events: auto; transform: translateY(0); }

    /* Flash */
    .tb-flash {
        padding: 13px 16px; border-radius: 11px; margin-bottom: 12px;
        font-size: 13px; border: 1px solid var(--border);
    }
    .tb-flash-success { background: #eafaf3; color: #08784f; border-color: #c9f0dd; }
    .tb-flash-error { background: #fff0f1; color: #b42332; border-color: #ffd4d9; }
    html[data-theme="dark"] .tb-flash-success { background: #14382a; color: #7ce3c2; border-color: #1e5c43; }
    html[data-theme="dark"] .tb-flash-error { background: #3a1c22; color: #ffb4bd; border-color: #5c2530; }

    /* Responsive */
    @media (max-width: 1200px) {
        .tb-header-inner { padding: 0 22px; gap: 14px; }
        .tb-nav-link { padding: 8px 9px; font-size: 11px; }
        .tb-section { padding: 60px 22px; }
        .tb-search-wrap { padding: 0 22px; }
        .tb-footer-grid { padding-inline: 22px; gap: 28px; }
    }

    @media (max-width: 991.98px) {
        :root { --header-h: 66px; }
        .tb-nav { display: none; }
        .tb-mobile-toggle { display: inline-grid; }
        .tb-header-actions { margin-left: auto; }
        .tb-search-card { grid-template-columns: 1fr; }
        .tb-dest-grid { grid-template-columns: repeat(2, 1fr); }
        .tb-footer-grid { grid-template-columns: repeat(2, 1fr); }
        .tb-hero { padding: 52px 22px 110px; }
        .tb-hero-stats { gap: 24px; }
        .tb-promo { padding: 40px 30px; }
        .tb-search-wrap { padding: 0 18px; }
    }

    @media (max-width: 575.98px) {
        .tb-header-inner { padding: 0 14px; gap: 8px; }
        .tb-header-actions { gap: 6px; }
        .tb-icon-btn { width: 36px; height: 36px; font-size: 15px; }
        .tb-header-actions .tb-btn { padding: 0 12px; font-size: 11px; min-height: 36px; }
        .tb-brand-name { font-size: 17px; }
        .tb-brand-mark { width: 38px; height: 38px; font-size: 18px; }
        .tb-brand-tag { font-size: 8px; }
        .tb-hero { padding: 40px 18px 100px; }
        .tb-hero-stats { padding-top: 22px; margin-top: 30px; gap: 20px; }
        .tb-hero-stat-num { font-size: 20px; }
        .tb-section { padding: 46px 18px; }
        .tb-search-wrap { padding: 0 14px; }
        .tb-search-card { padding: 16px; }
        .tb-dest-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
        .tb-footer-grid { grid-template-columns: 1fr 1fr; padding: 40px 18px 24px; gap: 22px; }
        .tb-footer-grid > div:first-child { grid-column: 1 / -1; }
        .tb-footer-bottom { padding: 18px 18px; }
        .tb-promo { padding: 30px 22px; }
        .tb-dropdown { width: 280px; right: -8px; }
    }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: .01ms !important;
            transition-duration: .01ms !important;
            scroll-behavior: auto !important;
        }
    }
</style>

<div class="tb-home">

    {{-- ─── ANNOUNCEMENT ─────────────────────────────────── --}}
    <div class="tb-announce">
        <i class="fa-solid fa-sparkles"></i>
        <span>New on TolBazar — discover services, manage your wallet, and book everything in one place.</span>
    </div>

    {{-- ─── HEADER ────────────────────────────────────────── --}}
    <header class="tb-header">
        <div class="tb-header-inner">
            <a href="{{ route('home') }}" class="tb-brand">
                <span class="tb-brand-mark"><i class="fa-solid fa-bag-shopping"></i></span>
                <span class="tb-brand-copy">
                    <span class="tb-brand-name d-block">{{ config('app.name', 'TolBazar') }}</span>
                    <span class="tb-brand-tag d-block">DISCOVER MORE · LIVE BETTER</span>
                </span>
            </a>

            {{-- Main nav --}}
            <nav class="tb-nav">
                @foreach($serviceLinks as $service)
                    @if($routeExists($service['route']))
                        <a href="{{ route($service['route']) }}" class="tb-nav-link">
                            <i class="fa-solid {{ $service['icon'] }}"></i>
                            <span>{{ $service['name'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="tb-header-actions">
                {{-- Search toggle --}}
                <button type="button" class="tb-icon-btn" id="tbSearchToggle"
                        aria-label="Search" aria-expanded="false">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

                {{-- Cart --}}
                @if($routeExists('cart.index'))
                    <a href="{{ route('cart.index') }}" class="tb-icon-btn" aria-label="Cart">
                        <i class="fa-solid fa-cart-shopping"></i>
                        @if(($cartCount = count((array) session('cart', []))) > 0)
                            <span class="tb-badge">{{ $cartCount }}</span>
                        @endif
                    </a>
                @endif

                {{-- Theme toggle --}}
                <button type="button" class="tb-icon-btn" id="tbThemeToggle"
                        aria-label="Toggle theme" title="Toggle theme">
                    <i class="fa-solid fa-moon" id="tbThemeIcon"></i>
                </button>

                @auth
                    {{-- User menu --}}
                    <div class="tb-dropdown-wrap">
                        <button type="button" class="tb-icon-btn" id="tbAccountToggle"
                                aria-label="Account menu" aria-expanded="false">
                            <span class="tb-avatar" style="width:29px;height:29px;font-size:11px;">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </span>
                        </button>

                        <div class="tb-dropdown" id="tbAccountMenu">
                            <div class="tb-dd-head">
                                <div class="tb-dd-head-name">{{ $user->name }}</div>
                                <div class="tb-dd-head-meta">{{ $user->email }}</div>
                                <div class="tb-dd-head-meta mt-1" style="color:var(--success);">
                                    <i class="fa-solid fa-circle" style="font-size:6px;vertical-align:middle;"></i>
                                    {{ ucfirst($user->active_mode ?? 'customer') }} mode
                                </div>
                            </div>

                            {{-- Account sections --}}
                            @foreach($accountMenuSections as $section)
                                @php
                                    $visible = array_filter($section['items'], fn ($i) => $routeExists($i['route']));
                                @endphp
                                @if(count($visible))
                                    <div class="tb-dd-section">{{ $section['title'] }}</div>
                                    @foreach($visible as $item)
                                        <a href="{{ route($item['route']) }}" class="tb-dd-link">
                                            <i class="fa-solid {{ $item['icon'] }}"></i>
                                            {{ $item['label'] }}
                                        </a>
                                    @endforeach
                                @endif
                            @endforeach

                            {{-- Workspace switcher --}}
                            <div class="tb-dd-divider"></div>
                            <div class="tb-dd-section">Switch Workspace</div>

                            @if($hasCustomer && $routeExists('switch.mode'))
                                <form action="{{ route('switch.mode', 'customer') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="tb-dd-link">
                                        <i class="fa-solid fa-house"></i> Customer Mode
                                    </button>
                                </form>
                            @endif

                            @if($isBranchStaff && $staffBranch && $routeExists('switch.mode'))
                                <form action="{{ route('switch.mode', 'seller') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="tb-dd-link">
                                        <i class="fa-solid fa-gauge"></i>
                                        {{ $staffRoleLabel }} Dashboard · {{ $staffBranch->branch_name }}
                                    </button>
                                </form>
                            @elseif($isOwner && $sellerProfile)
                                @foreach($ownerBranches as $branch)
                                    <form action="{{ route('switch.mode', 'seller') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                        <button type="submit" class="tb-dd-link">
                                            <i class="fa-solid fa-store"></i>
                                            {{ $branch->branch_name }}
                                            @if($branch->is_default)
                                                <span style="margin-left:auto;font-size:9px;font-weight:800;color:var(--success);">HQ</span>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach

                                @if($routeExists('seller.branches.index'))
                                    <a href="{{ route('seller.branches.index') }}" class="tb-dd-link">
                                        <i class="fa-solid fa-gear"></i> Manage Branches
                                    </a>
                                @endif
                            @elseif($routeExists('seller.apply'))
                                <a href="{{ route('seller.apply') }}" class="tb-dd-link">
                                    <i class="fa-solid fa-store"></i>
                                    {{ $hasSellerRole ? 'Complete Store Setup' : 'Become a Seller' }}
                                </a>
                            @endif

                            @if($hasDelivery && $routeExists('switch.mode'))
                                <form action="{{ route('switch.mode', 'delivery') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="tb-dd-link">
                                        <i class="fa-solid fa-motorcycle"></i> Delivery Mode
                                    </button>
                                </form>
                            @endif

                            @if($routeExists('customer.dashboard'))
                                <a href="{{ route('customer.dashboard') }}" class="tb-dd-link">
                                    <i class="fa-solid fa-gauge-high"></i> Customer Dashboard
                                </a>
                            @endif

                            <div class="tb-dd-divider"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="tb-dd-link" style="color:var(--danger);">
                                    <i class="fa-solid fa-right-from-bracket" style="color:var(--danger);"></i>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    @if($routeExists('login'))
                        <a href="{{ route('login') }}" class="tb-btn tb-btn-outline">Log in</a>
                    @endif
                    @if($routeExists('register'))
                        <a href="{{ route('register') }}" class="tb-btn tb-btn-primary">
                            <i class="fa-solid fa-user-plus"></i>
                            <span class="d-none d-sm-inline">Sign up</span>
                        </a>
                    @endif
                @endauth

                <button type="button" class="tb-icon-btn tb-mobile-toggle" id="tbMobileToggle"
                        aria-label="Menu" aria-expanded="false">
                    <i class="fa-solid fa-bars" id="tbMobileIcon"></i>
                </button>
            </div>
        </div>

        {{-- Search panel --}}
        <div class="tb-search-panel" id="tbSearchPanel">
            @php
                $searchRoute = $routeExists('search') ? 'search'
                    : ($routeExists('search.index') ? 'search.index' : null);
            @endphp
            @if($searchRoute)
                <form action="{{ route($searchRoute) }}" method="GET" class="tb-search-form">
                    <input type="search" name="q" class="tb-search-input"
                           value="{{ request('q') }}"
                           placeholder="Search hotels, tours, products, restaurants..."
                           aria-label="Search">
                    <button type="submit" class="tb-search-submit">
                        <i class="fa-solid fa-magnifying-glass"></i>Search
                    </button>
                </form>
            @else
                <form action="{{ $routeExists('services.index') ? route('services.index') : route('home') }}"
                      method="GET" class="tb-search-form">
                    <input type="search" name="q" class="tb-search-input"
                           value="{{ request('q') }}"
                           placeholder="Search services and products..."
                           aria-label="Search services">
                    <button type="submit" class="tb-search-submit">
                        <i class="fa-solid fa-magnifying-glass"></i>Search
                    </button>
                </form>
            @endif
        </div>

        {{-- Mobile nav --}}
        <nav class="tb-mobile-nav" id="tbMobileNav">
            @foreach($serviceLinks as $service)
                @if($routeExists($service['route']))
                    <a href="{{ route($service['route']) }}" class="tb-mobile-link">
                        <i class="fa-solid {{ $service['icon'] }}"></i>
                        {{ $service['name'] }}
                    </a>
                @endif
            @endforeach

            @auth
                @foreach($accountMenuSections as $section)
                    @php
                        $visible = array_filter($section['items'], fn ($i) => $routeExists($i['route']));
                    @endphp
                    @if(count($visible))
                        <div class="tb-mobile-section">{{ $section['title'] }}</div>
                        @foreach($visible as $item)
                            <a href="{{ route($item['route']) }}" class="tb-mobile-link">
                                <i class="fa-solid {{ $item['icon'] }}"></i>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    @endif
                @endforeach

                <div class="tb-mobile-section">Account</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="tb-mobile-link w-100" style="border:0;background:transparent;color:var(--danger);">
                        <i class="fa-solid fa-right-from-bracket" style="color:var(--danger);"></i>
                        Sign Out
                    </button>
                </form>
            @else
                <div class="tb-mobile-section">Account</div>
                @if($routeExists('login'))
                    <a href="{{ route('login') }}" class="tb-mobile-link">
                        <i class="fa-solid fa-right-to-bracket"></i>Log in
                    </a>
                @endif
                @if($routeExists('register'))
                    <a href="{{ route('register') }}" class="tb-mobile-link">
                        <i class="fa-solid fa-user-plus"></i>Create Account
                    </a>
                @endif
            @endauth
        </nav>
    </header>

    {{-- ─── HERO ───────────────────────────────────────────── --}}
    <section class="tb-hero">
        <div class="tb-hero-inner">
            <span class="tb-hero-pill">
                <i class="fa-solid fa-sparkles"></i> YOUR EVERYDAY MARKETPLACE
            </span>

            <h1>
                @if($user)
                    Welcome back, <span>{{ \Illuminate\Support\Str::words($user->name, 2, '') }}.</span><br>
                    What are we doing today?
                @else
                    Everything you need.<br>
                    <span>All in one place.</span>
                @endif
            </h1>

            <p>
                Discover stays, tours, everyday products, transport and food — plus a built-in
                wallet for fast, secure payments. One account. Every service.
            </p>

            <div class="tb-hero-ctas">
                <a href="#tb-explore" class="tb-hero-btn tb-hero-btn-primary">
                    <i class="fa-solid fa-compass"></i>Explore services
                </a>

                @if(!$user && $routeExists('register'))
                    <a href="{{ route('register') }}" class="tb-hero-btn tb-hero-btn-outline">
                        <i class="fa-solid fa-user-plus"></i>Join free
                    </a>
                @elseif($user && $routeExists('my-bookings.index'))
                    <a href="{{ route('my-bookings.index') }}" class="tb-hero-btn tb-hero-btn-outline">
                        <i class="fa-solid fa-calendar-check"></i>My bookings
                    </a>
                @elseif($user && $routeExists('wallet.dashboard'))
                    <a href="{{ route('wallet.dashboard') }}" class="tb-hero-btn tb-hero-btn-outline">
                        <i class="fa-solid fa-wallet"></i>Open wallet
                    </a>
                @endif
            </div>

            <div class="tb-hero-stats">
                <div>
                    <div class="tb-hero-stat-num">7+</div>
                    <div class="tb-hero-stat-lbl">Service categories</div>
                </div>
                <div>
                    <div class="tb-hero-stat-num">1 Account</div>
                    <div class="tb-hero-stat-lbl">One secure login</div>
                </div>
                <div>
                    <div class="tb-hero-stat-num">Wallet</div>
                    <div class="tb-hero-stat-lbl">Built-in payments</div>
                </div>
                <div>
                    <div class="tb-hero-stat-num">24/7</div>
                    <div class="tb-hero-stat-lbl">Self-service access</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── SEARCH BAR ─────────────────────────────────────── --}}
    <div class="tb-search-wrap">
        @php
            $searchRoute = $routeExists('search') ? 'search'
                : ($routeExists('search.index') ? 'search.index'
                : ($routeExists('services.index') ? 'services.index' : null));
        @endphp
        @if($searchRoute)
            <form action="{{ route($searchRoute) }}" method="GET" class="tb-search-card">
                <div class="tb-search-field">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" name="q" value="{{ request('q') }}"
                           placeholder="What are you looking for?">
                </div>
                <div class="tb-search-field">
                    <i class="fa-solid fa-layer-group"></i>
                    <select name="category">
                        <option value="">All services</option>
                        <option value="hotel">Hotels</option>
                        <option value="tour">Tours</option>
                        <option value="activity">Activities</option>
                        <option value="product">Products</option>
                        <option value="bus">Bus tickets</option>
                        <option value="rental">Vehicle rental</option>
                        <option value="restaurant">Restaurants</option>
                    </select>
                </div>
                <button type="submit" class="tb-search-submit">
                    <i class="fa-solid fa-magnifying-glass"></i> Search
                </button>
            </form>
        @endif
    </div>

    {{-- ─── FLASH ───────────────────────────────────────────── --}}
    <div style="max-width:1160px;margin:24px auto 0;padding:0 28px;">
        @if(session('success'))
            <div class="tb-flash tb-flash-success">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="tb-flash tb-flash-error">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
            </div>
        @endif
    </div>

    {{-- ─── SERVICES ────────────────────────────────────────── --}}
    <section class="tb-section" id="tb-explore">
        <div class="tb-sec-head">
            <div>
                <span class="tb-sec-eyebrow"><i class="fa-solid fa-compass me-1"></i>Explore</span>
                <h2 class="tb-sec-title">Everything TolBazar offers</h2>
                <p class="tb-sec-desc">
                    One place for all your needs — from shopping to travel, transport to dining.
                </p>
            </div>
            @if($routeExists('services.index'))
                <a href="{{ route('services.index') }}" class="tb-sec-link">
                    Browse all <i class="fa-solid fa-arrow-right"></i>
                </a>
            @endif
        </div>

        <div class="tb-services">
            @foreach($serviceLinks as $service)
                @if($routeExists($service['route']))
                    <a href="{{ route($service['route']) }}" class="tb-service-card">
                        <div class="tb-service-icon"
                             style="background:{{ $service['color'] }};color:{{ $service['iconColor'] }};">
                            <i class="fa-solid {{ $service['icon'] }}"></i>
                        </div>
                        <h3>{{ $service['name'] }}</h3>
                        <p>{{ $service['description'] }}</p>
                    </a>
                @endif
            @endforeach
        </div>
    </section>

    {{-- ─── DESTINATIONS ───────────────────────────────────── --}}
    <section class="tb-section" style="padding-top:0;">
        <div class="tb-sec-head">
            <div>
                <span class="tb-sec-eyebrow"><i class="fa-solid fa-map-location-dot me-1"></i>Discover</span>
                <h2 class="tb-sec-title">Popular destinations</h2>
                <p class="tb-sec-desc">Find inspiration for your next getaway across Nepal.</p>
            </div>
            @if($routeExists('tours.index'))
                <a href="{{ route('tours.index') }}" class="tb-sec-link">
                    All tours <i class="fa-solid fa-arrow-right"></i>
                </a>
            @endif
        </div>

        <div class="tb-dest-grid">
            @php
                $destinations = [
                    ['name' => 'Kathmandu', 'tag' => 'Culture & heritage',
                        'img' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Pokhara', 'tag' => 'Lakes & mountains',
                        'img' => 'https://images.unsplash.com/photo-1544735716-4866b6f0e8b0?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Chitwan', 'tag' => 'Nature & wildlife',
                        'img' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Himalayas', 'tag' => 'Adventure & trekking',
                        'img' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80'],
                ];
            @endphp
            @foreach($destinations as $dest)
                <a href="{{ $routeExists('tours.index') ? route('tours.index') : '#tb-explore' }}"
                   class="tb-destination">
                    <img loading="lazy" src="{{ $dest['img'] }}" alt="{{ $dest['name'] }}">
                    <div class="tb-dest-caption">
                        <h3>{{ $dest['name'] }}</h3>
                        <span>{{ $dest['tag'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ─── WALLET SHOWCASE (logged-in only) ───────────────── --}}
    @auth
        @if($routeExists('wallet.dashboard'))
            <section class="tb-section tb-section-sm" style="padding-top:0;">
                <div class="tb-sec-head">
                    <div>
                        <span class="tb-sec-eyebrow"><i class="fa-solid fa-wallet me-1"></i>Wallet</span>
                        <h2 class="tb-sec-title">Manage money in one place</h2>
                        <p class="tb-sec-desc">Load, transfer, withdraw and track every transaction.</p>
                    </div>
                    <a href="{{ route('wallet.dashboard') }}" class="tb-sec-link">
                        Open wallet <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="tb-services">
                    @php
                        $walletQuick = [
                            ['label' => 'Dashboard',    'icon' => 'fa-wallet',           'route' => 'wallet.dashboard'],
                            ['label' => 'Add Money',    'icon' => 'fa-circle-plus',      'route' => 'wallet.add-money'],
                            ['label' => 'Withdraw',     'icon' => 'fa-money-bill-transfer','route' => 'wallet.withdraw'],
                            ['label' => 'Transfer',     'icon' => 'fa-paper-plane',      'route' => 'wallet.transfer'],
                            ['label' => 'Transactions', 'icon' => 'fa-list',             'route' => 'wallet.transactions'],
                            ['label' => 'Rewards',      'icon' => 'fa-gift',             'route' => 'wallet.rewards'],
                        ];
                    @endphp
                    @foreach($walletQuick as $w)
                        @if($routeExists($w['route']))
                            <a href="{{ route($w['route']) }}" class="tb-service-card">
                                <div class="tb-service-icon" style="background:var(--brand-soft);color:var(--brand);">
                                    <i class="fa-solid {{ $w['icon'] }}"></i>
                                </div>
                                <h3>{{ $w['label'] }}</h3>
                                <p>Tap to manage</p>
                            </a>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif
    @endauth

    {{-- ─── PROMO / BECOME A SELLER ────────────────────────── --}}
    @if($routeExists('seller.apply') && (!$user || (!$hasSellerRole && !$isBranchStaff)))
        <section class="tb-section">
            <div class="tb-promo">
                <div class="tb-promo-inner">
                    <span class="tb-promo-badge">FOR BUSINESS OWNERS</span>
                    <h2>Grow your business with TolBazar.</h2>
                    <p>
                        Bring your products or services online, reach thousands of customers
                        and manage everything — branches, staff, products and payments —
                        from one powerful dashboard.
                    </p>
                    <a href="{{ route('seller.apply') }}" class="tb-promo-btn">
                        <i class="fa-solid fa-store"></i>Start selling
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ─── FEATURES ───────────────────────────────────────── --}}
    <section class="tb-section">
        <div class="tb-sec-head">
            <div>
                <span class="tb-sec-eyebrow"><i class="fa-solid fa-shield-halved me-1"></i>Why TolBazar</span>
                <h2 class="tb-sec-title">Built for convenience and trust</h2>
                <p class="tb-sec-desc">Thoughtful features that make every interaction simpler and safer.</p>
            </div>
        </div>

        <div class="tb-features">
            <div class="tb-feature">
                <div class="tb-feature-icon"><i class="fa-solid fa-layer-group"></i></div>
                <div>
                    <h3>Many services, one place</h3>
                    <p>Shop, travel, transport and dining — all from a single account.</p>
                </div>
            </div>
            <div class="tb-feature">
                <div class="tb-feature-icon"><i class="fa-solid fa-wallet"></i></div>
                <div>
                    <h3>Built-in wallet</h3>
                    <p>Load, transfer and pay securely without leaving the platform.</p>
                </div>
            </div>
            <div class="tb-feature">
                <div class="tb-feature-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
                <div>
                    <h3>Fully responsive</h3>
                    <p>A polished experience on every device — phone, tablet or desktop.</p>
                </div>
            </div>
            <div class="tb-feature">
                <div class="tb-feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div>
                    <h3>Secure by design</h3>
                    <p>Session management, PIN protection and activity logs built in.</p>
                </div>
            </div>
            <div class="tb-feature">
                <div class="tb-feature-icon"><i class="fa-solid fa-store"></i></div>
                <div>
                    <h3>Local businesses welcome</h3>
                    <p>Discover trusted local sellers and support your community.</p>
                </div>
            </div>
            <div class="tb-feature">
                <div class="tb-feature-icon"><i class="fa-solid fa-user-shield"></i></div>
                <div>
                    <h3>You're in control</h3>
                    <p>Switch modes, manage devices and adjust settings any time.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── FOOTER ─────────────────────────────────────────── --}}
    <footer class="tb-footer">
        <div class="tb-footer-grid">
            <div>
                <a href="{{ route('home') }}" class="tb-brand">
                    <span class="tb-brand-mark"><i class="fa-solid fa-bag-shopping"></i></span>
                    <span class="tb-brand-copy">
                        <span class="tb-brand-name d-block">{{ config('app.name', 'TolBazar') }}</span>
                        <span class="tb-brand-tag d-block">DISCOVER MORE · LIVE BETTER</span>
                    </span>
                </a>
                <p class="tb-footer-desc">
                    One marketplace for shopping, stays, travel, transport, food and everyday services.
                    Discover more with TolBazar.
                </p>
                <div class="tb-socials">
                    <a href="#" class="tb-social" aria-label="Facebook" rel="nofollow"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="tb-social" aria-label="Instagram" rel="nofollow"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="tb-social" aria-label="YouTube" rel="nofollow"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <div class="tb-footer-col">
                <h4>Explore</h4>
                @foreach($serviceLinks as $service)
                    @if($routeExists($service['route']))
                        <a href="{{ route($service['route']) }}">{{ $service['name'] }}</a>
                    @endif
                @endforeach
            </div>

            <div class="tb-footer-col">
                <h4>Your Account</h4>
                @auth
                    @if($routeExists('profile.me'))
                        <a href="{{ route('profile.me') }}">My Profile</a>
                    @endif
                    @if($routeExists('my-bookings.index'))
                        <a href="{{ route('my-bookings.index') }}">My Bookings</a>
                    @endif
                    @if($routeExists('wallet.dashboard'))
                        <a href="{{ route('wallet.dashboard') }}">My Wallet</a>
                    @endif
                    @if($routeExists('account.settings'))
                        <a href="{{ route('account.settings') }}">Settings</a>
                    @endif
                @else
                    @if($routeExists('login'))
                        <a href="{{ route('login') }}">Log in</a>
                    @endif
                    @if($routeExists('register'))
                        <a href="{{ route('register') }}">Create Account</a>
                    @endif
                @endauth
                @if($routeExists('seller.apply'))
                    <a href="{{ route('seller.apply') }}">Become a Seller</a>
                @endif
                @if($routeExists('seller.login'))
                    <a href="{{ route('seller.login') }}">Seller Login</a>
                @endif
            </div>

            <div class="tb-footer-col">
                <h4>Help & Info</h4>
                @if($routeExists('support.index'))
                    <a href="{{ route('support.index') }}">Help & Support</a>
                @endif
                @if($routeExists('privacy'))
                    <a href="{{ route('privacy') }}">Privacy Policy</a>
                @endif
                @if($routeExists('terms'))
                    <a href="{{ route('terms') }}">Terms & Conditions</a>
                @endif
                @if($routeExists('services.index'))
                    <a href="{{ route('services.index') }}">Browse Services</a>
                @endif
                @if($routeExists('cart.index'))
                    <a href="{{ route('cart.index') }}">View Cart</a>
                @endif
                @if($routeExists('checkout'))
                    <a href="{{ route('checkout') }}">Checkout</a>
                @endif
            </div>
        </div>

        <div class="tb-footer-bottom">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'TolBazar') }}. All rights reserved.</span>
            <span>Made for easier everyday living.</span>
        </div>
    </footer>

    <button type="button" id="tbBackTop" class="tb-back-top" aria-label="Back to top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
</div>

<script>
(() => {
    'use strict';
    const html = document.documentElement;

    // ── THEME ──────────────────────────────────────────────
    const themeToggle = document.getElementById('tbThemeToggle');
    const themeIcon = document.getElementById('tbThemeIcon');
    let theme = 'light';
    try { theme = localStorage.getItem('tolbazar-theme') || 'light'; } catch (e) {}

    const applyTheme = (val) => {
        html.setAttribute('data-theme', val);
        html.style.colorScheme = val;
        if (themeIcon) {
            themeIcon.className = val === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        }
        themeToggle?.setAttribute('aria-label', val === 'dark' ? 'Light mode' : 'Dark mode');
        try { localStorage.setItem('tolbazar-theme', val); } catch (e) {}
    };
    applyTheme(theme);
    themeToggle?.addEventListener('click', () => {
        applyTheme(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });

    // ── SEARCH TOGGLE ──────────────────────────────────────
    const searchToggle = document.getElementById('tbSearchToggle');
    const searchPanel = document.getElementById('tbSearchPanel');
    searchToggle?.addEventListener('click', () => {
        const open = searchPanel?.classList.toggle('open') || false;
        searchToggle.setAttribute('aria-expanded', String(open));
        if (open) searchPanel?.querySelector('input')?.focus();
    });

    // ── ACCOUNT DROPDOWN ───────────────────────────────────
    const accToggle = document.getElementById('tbAccountToggle');
    const accMenu = document.getElementById('tbAccountMenu');
    accToggle?.addEventListener('click', (e) => {
        e.stopPropagation();
        const open = accMenu?.classList.toggle('open') || false;
        accToggle.setAttribute('aria-expanded', String(open));
    });

    // ── MOBILE NAV ─────────────────────────────────────────
    const mobToggle = document.getElementById('tbMobileToggle');
    const mobNav = document.getElementById('tbMobileNav');
    const mobIcon = document.getElementById('tbMobileIcon');
    mobToggle?.addEventListener('click', () => {
        const open = mobNav?.classList.toggle('open') || false;
        mobToggle.setAttribute('aria-expanded', String(open));
        if (mobIcon) mobIcon.className = open ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
    });
    mobNav?.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', () => {
            mobNav.classList.remove('open');
            mobToggle?.setAttribute('aria-expanded', 'false');
            if (mobIcon) mobIcon.className = 'fa-solid fa-bars';
        });
    });

    // ── OUTSIDE CLICK ──────────────────────────────────────
    document.addEventListener('click', (e) => {
        if (accMenu && !accMenu.contains(e.target) && !accToggle?.contains(e.target)) {
            accMenu.classList.remove('open');
            accToggle?.setAttribute('aria-expanded', 'false');
        }
        if (searchPanel?.classList.contains('open') &&
            !searchPanel.contains(e.target) && !searchToggle?.contains(e.target)) {
            searchPanel.classList.remove('open');
            searchToggle?.setAttribute('aria-expanded', 'false');
        }
    });

    // ── ESC / "/" SHORTCUTS ────────────────────────────────
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            accMenu?.classList.remove('open');
            searchPanel?.classList.remove('open');
            mobNav?.classList.remove('open');
            accToggle?.setAttribute('aria-expanded', 'false');
            searchToggle?.setAttribute('aria-expanded', 'false');
            mobToggle?.setAttribute('aria-expanded', 'false');
            if (mobIcon) mobIcon.className = 'fa-solid fa-bars';
        }
        if (e.key === '/' &&
            !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName) &&
            !e.ctrlKey && !e.metaKey && !e.altKey) {
            e.preventDefault();
            searchPanel?.classList.add('open');
            searchToggle?.setAttribute('aria-expanded', 'true');
            searchPanel?.querySelector('input')?.focus();
        }
    });

    // ── BACK TO TOP ────────────────────────────────────────
    const backTop = document.getElementById('tbBackTop');
    window.addEventListener('scroll', () => {
        backTop?.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });
    backTop?.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();
</script>
@endsection
