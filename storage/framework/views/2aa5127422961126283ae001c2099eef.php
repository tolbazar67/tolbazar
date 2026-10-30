<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'Discover More'); ?> — <?php echo e(config('app.name', 'TolBazar')); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Discover hotels, tours, activities, shopping, transport and local services on TolBazar.'); ?>">
    <meta name="theme-color" content="#ffffff">

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23625bf6'%3E%3Cpath d='M3 10.5 12 3l9 7.5V21h-6v-6H9v6H3z'/%3E%3C/svg%3E">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f1f0ff', 100: '#e6e4ff', 500: '#7168f8',
                            600: '#625bf6', 700: '#5148e5', 900: '#292454'
                        }
                    }
                }
            }
        };
    </script>

    <style>
        :root {
            color-scheme: light;
            --brand: #625bf6;
            --brand-hover: #5148e5;
            --brand-soft: #f1f0ff;
            --page: #ffffff;
            --surface: #ffffff;
            --surface-soft: #f8f9fc;
            --text: #202638;
            --muted: #747e91;
            --border: #e8eaf1;
            --header-height: 74px;
            --radius: 14px;
            --shadow: 0 8px 30px rgba(25, 33, 60, .07);
        }

        html[data-theme="dark"] {
            color-scheme: dark;
            --page: #111522;
            --surface: #191e2e;
            --surface-soft: #22283a;
            --text: #edf0f7;
            --muted: #a0aac0;
            --border: #30364a;
            --brand-soft: #302d55;
            --shadow: 0 8px 30px rgba(0, 0, 0, .2);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-padding-top: 100px; }

        body {
            margin: 0; min-height: 100vh;
            background: var(--page); color: var(--text);
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
            transition: background .2s ease, color .2s ease;
        }

        body, button, input { font-family: 'Inter', system-ui, sans-serif; }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; }
        button:focus-visible, a:focus-visible, input:focus-visible {
            outline: 3px solid rgba(98,91,246,.4); outline-offset: 3px;
        }

        .public-shell { min-height: 100vh; display: flex; flex-direction: column; }

        .public-announcement {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 9px 16px; color: #fff;
            background: linear-gradient(100deg, #5148e5, #7968f8);
            text-align: center; font-size: 11px; font-weight: 600;
        }
        .public-announcement a { text-decoration: underline; text-underline-offset: 3px; }

        .public-header {
            position: sticky; top: 0; z-index: 1000;
            background: var(--surface); border-bottom: 1px solid var(--border);
        }
        .public-header-inner {
            max-width: 1440px; min-height: var(--header-height);
            margin: auto; padding: 0 28px;
            display: flex; align-items: center; gap: 26px;
        }

        .public-brand { display: inline-flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .public-brand-icon {
            width: 41px; height: 41px; display: grid; place-items: center;
            border-radius: 13px; background: linear-gradient(135deg, #7d73ff, #5148e5);
            color: #fff; font-size: 22px;
            box-shadow: 0 5px 14px rgba(98,91,246,.23); transition: transform .2s;
        }
        .public-brand:hover .public-brand-icon { transform: translateY(-2px); }
        .public-brand-name {
            font-size: 19px; font-weight: 800; letter-spacing: -.8px;
            color: var(--text); white-space: nowrap;
        }
        .public-brand-tagline {
            margin-top: 2px; font-size: 9px; color: var(--muted);
            letter-spacing: .7px; font-weight: 600;
        }

        .public-nav { display: flex; align-items: center; gap: 4px; margin-left: auto; }
        .public-nav-link {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 11px; border-radius: 9px;
            color: var(--muted); font-size: 11px; font-weight: 600;
            white-space: nowrap; transition: .15s ease;
        }
        .public-nav-link:hover, .public-nav-link.active {
            color: var(--brand); background: var(--brand-soft);
        }
        .public-nav-link i { font-size: 15px; }

        .public-header-actions { display: flex; align-items: center; gap: 9px; flex-shrink: 0; }

        .public-icon-btn {
            width: 39px; height: 39px; display: inline-grid; place-items: center;
            border: 1px solid var(--border); border-radius: 11px;
            background: var(--surface); color: var(--text);
            font-size: 17px; cursor: pointer; transition: .15s ease;
        }
        .public-icon-btn:hover { color: var(--brand); background: var(--surface-soft); }

        .public-btn {
            min-height: 39px; display: inline-flex; align-items: center; justify-content: center;
            gap: 8px; padding: 0 16px; border-radius: 10px;
            border: 1px solid transparent; font-size: 11px; font-weight: 700;
            white-space: nowrap; cursor: pointer; transition: background .15s, transform .15s;
        }
        .public-btn:hover { transform: translateY(-1px); }
        .public-btn-primary { background: var(--brand); color: #fff; }
        .public-btn-primary:hover { color: #fff; background: var(--brand-hover); }
        .public-btn-outline { border-color: var(--border); color: var(--text); background: var(--surface); }
        .public-btn-outline:hover { color: var(--brand); border-color: var(--brand); }

        .public-dropdown-wrap { position: relative; }
        .public-dropdown {
            display: none; position: absolute; top: calc(100% + 10px); right: 0;
            z-index: 1100; width: 280px; max-height: 80vh; overflow-y: auto;
            padding: 8px; background: var(--surface);
            border: 1px solid var(--border); border-radius: 14px;
            box-shadow: var(--shadow);
            scrollbar-width: thin; scrollbar-color: var(--border) transparent;
        }
        .public-dropdown.open { display: block; animation: publicDropIn .15s ease-out; }

        @keyframes publicDropIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .public-dropdown-heading {
            padding: 10px 11px; border-bottom: 1px solid var(--border);
            margin-bottom: 5px; overflow-wrap: anywhere;
        }
        .public-dropdown-section-title {
            padding: 10px 11px 5px; color: var(--muted);
            font-size: 9px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .public-dropdown-link {
            display: flex; align-items: center; gap: 10px;
            width: 100%; padding: 10px 11px; border: 0; border-radius: 9px;
            background: transparent; color: var(--text);
            text-align: left; font-size: 11px; font-weight: 500; cursor: pointer;
        }
        .public-dropdown-link i { width: 18px; color: var(--muted); font-size: 15px; }
        .public-dropdown-link:hover { color: var(--brand); background: var(--surface-soft); }
        .public-dropdown-divider { height: 1px; margin: 5px 0; background: var(--border); }

        .public-user-avatar {
            width: 35px; height: 35px; flex: 0 0 35px;
            display: grid; place-items: center; border-radius: 11px;
            background: var(--brand-soft); color: var(--brand);
            font-size: 12px; font-weight: 800;
        }

        .public-search-panel {
            display: none; padding: 16px 28px;
            background: var(--surface); border-top: 1px solid var(--border);
        }
        .public-search-panel.open { display: block; }
        .public-search-inner { display: flex; gap: 10px; max-width: 850px; margin: auto; }
        .public-search-input {
            min-width: 0; flex: 1; height: 44px; padding: 0 14px;
            color: var(--text); background: var(--surface-soft);
            border: 1px solid var(--border); border-radius: 10px;
            outline: none; font-size: 12px;
        }
        .public-search-input:focus { border-color: var(--brand); }

        .public-mobile-toggle { display: none; }
        .public-mobile-nav {
            display: none; padding: 10px 16px 16px;
            background: var(--surface); border-top: 1px solid var(--border);
            max-height: 75vh; overflow-y: auto;
        }
        .public-mobile-nav.open { display: block; }
        .public-mobile-nav a {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 12px; border-radius: 9px;
            color: var(--muted); font-size: 12px; font-weight: 600;
        }
        .public-mobile-nav a:hover { color: var(--brand); background: var(--brand-soft); }
        .public-mobile-section {
            padding: 12px 12px 5px; color: var(--muted);
            font-size: 9px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .public-mobile-auth {
            display: flex; gap: 9px; padding-top: 12px;
            border-top: 1px solid var(--border); margin-top: 8px;
        }

        .public-main { width: 100%; flex: 1; min-width: 0; }
        .public-container {
            width: 100%; max-width: 1440px;
            padding: 26px 28px; margin: 0 auto;
        }

        .public-alerts {
            width: 100%; max-width: 1440px;
            margin: 0 auto; padding: 14px 28px 0;
        }
        .public-alert {
            padding: 13px 15px; border: 1px solid var(--border);
            border-radius: 11px; margin-bottom: 8px; font-size: 12px;
        }
        .public-alert-success { color: #08784f; background: #eafaf3; border-color: #c9f0dd; }
        .public-alert-error { color: #b42332; background: #fff0f1; border-color: #ffd4d9; }
        .public-alert-warning { color: #946200; background: #fff8e6; border-color: #f9e5a8; }
        .public-alert-info { color: #2455a4; background: #edf5ff; border-color: #d4e5ff; }

        .public-surface {
            color: var(--text); background: var(--surface);
            border: 1px solid var(--border); border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .public-muted { color: var(--muted); }

        .public-footer { margin-top: auto; background: var(--surface); border-top: 1px solid var(--border); }
        .public-footer-main {
            max-width: 1440px; margin: auto; padding: 42px 28px 30px;
            display: grid; grid-template-columns: 1.5fr repeat(3, 1fr); gap: 35px;
        }
        .public-footer-description {
            max-width: 300px; margin-top: 12px;
            color: var(--muted); font-size: 11px; line-height: 1.9;
        }
        .public-footer-heading { margin: 0 0 15px; color: var(--text); font-size: 11px; font-weight: 800; }
        .public-footer-link {
            display: block; width: fit-content; margin: 11px 0;
            color: var(--muted); font-size: 11px; transition: color .15s;
        }
        .public-footer-link:hover { color: var(--brand); }

        .public-footer-bottom {
            max-width: 1440px; margin: auto; padding: 18px 28px;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
            border-top: 1px solid var(--border);
            color: var(--muted); font-size: 10px;
        }
        .public-footer-legal { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
        .public-footer-legal a:hover { color: var(--brand); }

        .public-socials { display: flex; gap: 8px; margin-top: 16px; }
        .public-social-link {
            width: 34px; height: 34px; display: grid; place-items: center;
            border: 1px solid var(--border); border-radius: 10px;
            color: var(--muted); font-size: 15px;
        }
        .public-social-link:hover { color: var(--brand); background: var(--brand-soft); }

        .public-back-top {
            position: fixed; right: 22px; bottom: 22px; z-index: 900;
            opacity: 0; pointer-events: none; transform: translateY(8px); transition: .2s;
        }
        .public-back-top.visible { opacity: 1; pointer-events: auto; transform: translateY(0); }

        .public-fade-in { animation: publicFadeIn .3s ease-out both; }
        @keyframes publicFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        html[data-theme="dark"] input,
        html[data-theme="dark"] textarea,
        html[data-theme="dark"] select {
            color: var(--text); background-color: var(--surface); border-color: var(--border);
        }
        html[data-theme="dark"] input::placeholder,
        html[data-theme="dark"] textarea::placeholder { color: var(--muted); }

        @media (max-width: 1200px) {
            .public-header-inner { gap: 13px; padding: 0 20px; }
            .public-nav-link { padding: 9px 8px; font-size: 10px; }
            .public-footer-main { padding-inline: 20px; gap: 24px; }
        }

        @media (max-width: 991.98px) {
            :root { --header-height: 66px; }
            .public-header-inner { padding: 0 18px; gap: 12px; }
            .public-nav { display: none; }
            .public-header-actions { margin-left: auto; gap: 7px; }
            .public-mobile-toggle { display: inline-grid; }
            .public-header-inner > .public-btn { display: none; }
            .public-container { padding: 22px 18px; }
            .public-alerts { padding-inline: 18px; }
            .public-footer-main { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .public-search-panel { padding-inline: 18px; }
        }

        @media (max-width: 575.98px) {
            .public-brand-name { font-size: 17px; }
            .public-brand-icon { width: 37px; height: 37px; }
            .public-brand-tagline { font-size: 8px; }
            .public-header-inner { padding-inline: 12px; }
            .public-header-actions { gap: 5px; }
            .public-icon-btn { width: 35px; height: 35px; border-radius: 9px; }
            .public-header-actions > .public-btn { padding: 0 10px; min-height: 35px; font-size: 10px; }
            .public-container { padding: 16px 12px; }
            .public-alerts { padding-inline: 12px; }
            .public-search-panel { padding-inline: 12px; }
            .public-footer-main { grid-template-columns: 1fr 1fr; padding: 30px 16px 20px; gap: 24px 15px; }
            .public-footer-main > div:first-child { grid-column: 1 / -1; }
            .public-footer-bottom { padding: 17px 16px; }
            .public-footer-legal { gap: 12px; }
            .public-announcement { font-size: 10px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
<?php
    $publicRouteExists = fn ($name) => \Illuminate\Support\Facades\Route::has($name);
    $publicUser = auth()->user();

    /* ── MAIN NAV ─────────────────────────────────────────── */
    $publicNavigation = [
        ['label' => 'Home',     'icon' => 'bi-house',     'route' => 'home'],
        ['label' => 'Services', 'icon' => 'bi-grid-3x3-gap', 'route' => 'services.index'],
        ['label' => 'Cart',     'icon' => 'bi-cart3',     'route' => 'cart.index'],
        ['label' => 'Checkout', 'icon' => 'bi-credit-card', 'route' => 'checkout'],
    ];

    /* ── CUSTOMER ACCOUNT MENU (sectioned) ───────────────── */
    $publicCustomerMenuSections = [
        [
            'title' => 'My Activity',
            'items' => [
                ['label' => 'My Profile',    'icon' => 'bi-person',           'route' => 'profile.me'],
                ['label' => 'My Bookings',   'icon' => 'bi-calendar2-check',  'route' => 'my-bookings.index'],
                ['label' => 'Shopping Cart', 'icon' => 'bi-cart3',            'route' => 'cart.index'],
            ],
        ],
        [
            'title' => 'My Wallet',
            'items' => [
                ['label' => 'Wallet Dashboard',  'icon' => 'bi-wallet2',           'route' => 'wallet.dashboard'],
                ['label' => 'Transactions',      'icon' => 'bi-arrow-left-right',  'route' => 'wallet.transactions'],
                ['label' => 'Add Money',         'icon' => 'bi-plus-circle',       'route' => 'wallet.add-money'],
                ['label' => 'Load Requests',     'icon' => 'bi-clock-history',     'route' => 'wallet.load-requests'],
                ['label' => 'Withdraw',          'icon' => 'bi-cash-stack',        'route' => 'wallet.withdraw'],
                ['label' => 'Withdraw Requests', 'icon' => 'bi-hourglass-split',   'route' => 'wallet.withdraw-requests'],
                ['label' => 'Transfer',          'icon' => 'bi-send',              'route' => 'wallet.transfer'],
                ['label' => 'Bank Accounts',     'icon' => 'bi-bank',              'route' => 'wallet.bank-accounts'],
                ['label' => 'Security PIN',      'icon' => 'bi-shield-lock',       'route' => 'wallet.security'],
                ['label' => 'Rewards',           'icon' => 'bi-gift',              'route' => 'wallet.rewards'],
            ],
        ],
        [
            'title' => 'Account Settings',
            'items' => [
                ['label' => 'Settings Hub',   'icon' => 'bi-gear',          'route' => 'account.settings'],
                ['label' => 'Login Activity', 'icon' => 'bi-clock-history', 'route' => 'account.login-activity'],
                ['label' => 'Active Sessions','icon' => 'bi-laptop',        'route' => 'account.sessions'],
                ['label' => 'Switch Account', 'icon' => 'bi-arrow-repeat',  'route' => 'account.switcher'],
            ],
        ],
        [
            'title' => 'Profile & Security',
            'items' => [
                ['label' => 'Change Username',  'icon' => 'bi-at',           'route' => 'account.change-username'],
                ['label' => 'Change Email',     'icon' => 'bi-envelope',     'route' => 'account.change-email'],
                ['label' => 'Change Name',      'icon' => 'bi-person-badge', 'route' => 'account.change-name'],
                ['label' => 'Change Mobile',    'icon' => 'bi-phone',        'route' => 'account.change-mobile'],
                ['label' => 'Verification',     'icon' => 'bi-patch-check',  'route' => 'verification.center'],
            ],
        ],
        [
            'title' => 'Business',
            'items' => [
                ['label' => 'Become a Seller', 'icon' => 'bi-shop', 'route' => 'seller.apply'],
            ],
        ],
    ];

    /* Flat list for mobile nav */
    $publicCustomerLinks = [];
    foreach ($publicCustomerMenuSections as $section) {
        foreach ($section['items'] as $item) {
            $publicCustomerLinks[] = $item;
        }
    }

    /* ── FOOTER ──────────────────────────────────────────── */
    $publicFooterGroups = [
        [
            'title' => 'Explore',
            'links' => [
                ['label' => 'Browse Services', 'route' => 'services.index'],
                ['label' => 'Shopping Cart',   'route' => 'cart.index'],
                ['label' => 'Checkout',        'route' => 'checkout'],
            ],
        ],
        [
            'title' => 'My TolBazar',
            'links' => [
                ['label' => 'My Profile',        'route' => 'profile.me'],
                ['label' => 'My Bookings',       'route' => 'my-bookings.index'],
                ['label' => 'My Wallet',         'route' => 'wallet.dashboard'],
                ['label' => 'Account Settings',  'route' => 'account.settings'],
            ],
        ],
        [
            'title' => 'For Business',
            'links' => [
                ['label' => 'Become a Seller', 'route' => 'seller.apply'],
                ['label' => 'Seller Login',    'route' => 'seller.login'],
            ],
        ],
    ];
?>

<div class="public-shell">

    <?php if (! empty(trim($__env->yieldContent('announcement')))): ?>
        <div class="public-announcement">
            <?php echo $__env->yieldContent('announcement'); ?>
        </div>
    <?php endif; ?>

    
    <header class="public-header">
        <div class="public-header-inner">

            <a href="<?php echo e($publicRouteExists('home') ? route('home') : url('/')); ?>"
               class="public-brand" aria-label="<?php echo e(config('app.name', 'TolBazar')); ?> home">
                <span class="public-brand-icon"><i class="bi bi-shop"></i></span>
                <span>
                    <span class="public-brand-name d-block"><?php echo e(config('app.name', 'TolBazar')); ?></span>
                    <span class="public-brand-tagline d-block">DISCOVER MORE. LIVE BETTER.</span>
                </span>
            </a>

            
            <nav class="public-nav" aria-label="Main navigation">
                <?php $__currentLoopData = $publicNavigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($publicRouteExists($item['route'])): ?>
                        <a href="<?php echo e(route($item['route'])); ?>"
                           class="public-nav-link <?php echo e(request()->routeIs($item['route']) ? 'active' : ''); ?>">
                            <i class="bi <?php echo e($item['icon']); ?>"></i>
                            <span><?php echo e($item['label']); ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>

            <div class="public-header-actions">

                <button type="button" class="public-icon-btn" id="publicSearchToggle"
                        aria-label="Open search" aria-expanded="false" title="Search">
                    <i class="bi bi-search"></i>
                </button>

                <button type="button" class="public-icon-btn" id="publicThemeToggle"
                        aria-label="Switch theme" title="Switch theme">
                    <i class="bi bi-moon-stars" id="publicThemeIcon"></i>
                </button>

                <?php if(auth()->guard()->check()): ?>
                    <div class="public-dropdown-wrap">
                        <button type="button" class="public-icon-btn" id="publicAccountToggle"
                                aria-label="Open account menu" aria-expanded="false">
                            <span class="public-user-avatar" style="width:29px;height:29px;font-size:11px;">
                                <?php echo e(strtoupper(substr($publicUser->name ?? 'U', 0, 1))); ?>

                            </span>
                        </button>

                        <div class="public-dropdown" id="publicAccountMenu">
                            <div class="public-dropdown-heading">
                                <div style="font-size:12px;font-weight:800;">
                                    <?php echo e($publicUser->name ?? 'My Account'); ?>

                                </div>
                                <div class="public-muted" style="font-size:10px;margin-top:4px;">
                                    <?php echo e($publicUser->email ?? ''); ?>

                                </div>
                            </div>

                            <?php $__currentLoopData = $publicCustomerMenuSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $visibleItems = array_filter(
                                        $section['items'],
                                        fn ($i) => $publicRouteExists($i['route'])
                                    );
                                ?>
                                <?php if(count($visibleItems)): ?>
                                    <div class="public-dropdown-section-title"><?php echo e($section['title']); ?></div>
                                    <?php $__currentLoopData = $visibleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(route($item['route'])); ?>" class="public-dropdown-link">
                                            <i class="bi <?php echo e($item['icon']); ?>"></i><?php echo e($item['label']); ?>

                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <div class="public-dropdown-divider"></div>

                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="public-dropdown-link" style="color:#dc4545;">
                                    <i class="bi bi-box-arrow-right" style="color:#dc4545;"></i>Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if($publicRouteExists('login')): ?>
                        <a href="<?php echo e(route('login')); ?>" class="public-btn public-btn-outline">Log in</a>
                    <?php endif; ?>
                    <?php if($publicRouteExists('register')): ?>
                        <a href="<?php echo e(route('register')); ?>" class="public-btn public-btn-primary">
                            <i class="bi bi-person-plus"></i><span>Sign up</span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

                <button type="button" class="public-icon-btn public-mobile-toggle"
                        id="publicMobileToggle" aria-label="Open navigation"
                        aria-expanded="false" aria-controls="publicMobileNav">
                    <i class="bi bi-list" id="publicMobileIcon"></i>
                </button>
            </div>
        </div>

        
        <div class="public-search-panel" id="publicSearchPanel">
            <?php if($publicRouteExists('search')): ?>
                <form action="<?php echo e(route('search')); ?>" method="GET" class="public-search-inner">
                    <input type="search" name="q" class="public-search-input"
                           value="<?php echo e(request('q')); ?>"
                           placeholder="Search services, products, bookings..."
                           aria-label="Search TolBazar">
                    <button type="submit" class="public-btn public-btn-primary">
                        <i class="bi bi-search"></i>Search
                    </button>
                </form>
            <?php else: ?>
                <div class="public-search-inner">
                    <input type="search" id="publicLocalSearch" class="public-search-input"
                           placeholder="Search available navigation links..."
                           aria-label="Search available navigation">
                    <button type="button" id="publicLocalSearchButton"
                            class="public-btn public-btn-primary">
                        <i class="bi bi-search"></i>Find
                    </button>
                </div>
            <?php endif; ?>
        </div>

        
        <nav class="public-mobile-nav" id="publicMobileNav" aria-label="Mobile navigation">
            <?php $__currentLoopData = $publicNavigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($publicRouteExists($item['route'])): ?>
                    <a href="<?php echo e(route($item['route'])); ?>">
                        <i class="bi <?php echo e($item['icon']); ?>"></i><?php echo e($item['label']); ?>

                    </a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if(auth()->guard()->check()): ?>
                <?php $__currentLoopData = $publicCustomerMenuSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $visibleItems = array_filter(
                            $section['items'],
                            fn ($i) => $publicRouteExists($i['route'])
                        );
                    ?>
                    <?php if(count($visibleItems)): ?>
                        <div class="public-mobile-section"><?php echo e($section['title']); ?></div>
                        <?php $__currentLoopData = $visibleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route($item['route'])); ?>">
                                <i class="bi <?php echo e($item['icon']); ?>"></i><?php echo e($item['label']); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="public-mobile-auth">
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="public-btn public-btn-outline">
                            <i class="bi bi-box-arrow-right"></i>Sign Out
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="public-mobile-auth">
                    <?php if($publicRouteExists('login')): ?>
                        <a href="<?php echo e(route('login')); ?>" class="public-btn public-btn-outline">Log in</a>
                    <?php endif; ?>
                    <?php if($publicRouteExists('register')): ?>
                        <a href="<?php echo e(route('register')); ?>" class="public-btn public-btn-primary">Sign up</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </nav>
    </header>

    
    <div class="public-alerts">
        <?php $__currentLoopData = ['success', 'error', 'warning', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(session($type)): ?>
                <div class="public-alert public-alert-<?php echo e($type); ?> public-fade-in" role="alert">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <span>
                            <?php if($type === 'success'): ?><i class="bi bi-check-circle-fill me-2"></i>
                            <?php elseif($type === 'error'): ?><i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php elseif($type === 'warning'): ?><i class="bi bi-exclamation-circle-fill me-2"></i>
                            <?php else: ?><i class="bi bi-info-circle-fill me-2"></i><?php endif; ?>
                            <?php echo e(session($type)); ?>

                        </span>
                        <button type="button" class="btn-close" aria-label="Dismiss message"
                                onclick="this.closest('[role=alert]').remove()"></button>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($errors->any()): ?>
            <div class="public-alert public-alert-error" role="alert">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Please check the following:</strong>
                <ul class="mt-2 mb-0 ps-4">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    
    <main class="public-main">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <footer class="public-footer">
        <div class="public-footer-main">
            <div>
                <a href="<?php echo e($publicRouteExists('home') ? route('home') : url('/')); ?>" class="public-brand">
                    <span class="public-brand-icon"><i class="bi bi-shop"></i></span>
                    <span>
                        <span class="public-brand-name d-block"><?php echo e(config('app.name', 'TolBazar')); ?></span>
                        <span class="public-brand-tagline d-block">DISCOVER MORE. LIVE BETTER.</span>
                    </span>
                </a>

                <p class="public-footer-description">
                    Your destination for shopping, travel, stays, activities and everyday services.
                    Discover local businesses and make your next experience easier with TolBazar.
                </p>

                <div class="public-socials">
                    <a href="#" class="public-social-link" aria-label="Facebook" rel="nofollow"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="public-social-link" aria-label="Instagram" rel="nofollow"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="public-social-link" aria-label="YouTube" rel="nofollow"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <?php $__currentLoopData = $publicFooterGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <h2 class="public-footer-heading"><?php echo e($group['title']); ?></h2>
                    <?php $__currentLoopData = $group['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($publicRouteExists($item['route'])): ?>
                            <a href="<?php echo e(route($item['route'])); ?>" class="public-footer-link">
                                <?php echo e($item['label']); ?>

                            </a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="public-footer-bottom">
            <span>&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name', 'TolBazar')); ?>. All rights reserved.</span>

            <div class="public-footer-legal">
                <?php if($publicRouteExists('privacy')): ?>
                    <a href="<?php echo e(route('privacy')); ?>">Privacy Policy</a>
                <?php endif; ?>
                <?php if($publicRouteExists('terms')): ?>
                    <a href="<?php echo e(route('terms')); ?>">Terms of Service</a>
                <?php endif; ?>
                <?php if($publicRouteExists('support.index')): ?>
                    <a href="<?php echo e(route('support.index')); ?>">Help & Support</a>
                <?php endif; ?>
                <span><i class="bi bi-shield-check me-1"></i>Secure experience</span>
            </div>
        </div>
    </footer>
</div>

<button type="button" id="publicBackTop" class="public-icon-btn public-back-top"
        aria-label="Back to top" title="Back to top">
    <i class="bi bi-arrow-up"></i>
</button>

<script>
(() => {
    'use strict';
    const html = document.documentElement;
    const themeToggle = document.getElementById('publicThemeToggle');
    const themeIcon = document.getElementById('publicThemeIcon');
    const searchToggle = document.getElementById('publicSearchToggle');
    const searchPanel = document.getElementById('publicSearchPanel');
    const accountToggle = document.getElementById('publicAccountToggle');
    const accountMenu = document.getElementById('publicAccountMenu');
    const mobileToggle = document.getElementById('publicMobileToggle');
    const mobileNav = document.getElementById('publicMobileNav');
    const mobileIcon = document.getElementById('publicMobileIcon');
    const backTop = document.getElementById('publicBackTop');

    let theme = 'light';
    try { theme = localStorage.getItem('tolbazar-public-theme') || 'light'; } catch (e) {}

    const applyTheme = (value) => {
        html.setAttribute('data-theme', value);
        html.style.colorScheme = value;
        if (themeIcon) themeIcon.className = value === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
        themeToggle?.setAttribute('aria-label', value === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
        try { localStorage.setItem('tolbazar-public-theme', value); } catch (e) {}
    };
    applyTheme(theme);

    themeToggle?.addEventListener('click', () => {
        applyTheme(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });

    searchToggle?.addEventListener('click', () => {
        const isOpen = searchPanel?.classList.toggle('open') || false;
        searchToggle.setAttribute('aria-expanded', String(isOpen));
        if (isOpen) searchPanel?.querySelector('input')?.focus();
    });

    accountToggle?.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = accountMenu?.classList.toggle('open') || false;
        accountToggle.setAttribute('aria-expanded', String(isOpen));
    });

    mobileToggle?.addEventListener('click', () => {
        const isOpen = mobileNav?.classList.toggle('open') || false;
        mobileToggle.setAttribute('aria-expanded', String(isOpen));
        if (mobileIcon) mobileIcon.className = isOpen ? 'bi bi-x-lg' : 'bi bi-list';
    });

    mobileNav?.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileNav.classList.remove('open');
            mobileToggle?.setAttribute('aria-expanded', 'false');
            if (mobileIcon) mobileIcon.className = 'bi bi-list';
        });
    });

    document.addEventListener('click', (e) => {
        if (accountMenu && !accountMenu.contains(e.target) && !accountToggle?.contains(e.target)) {
            accountMenu.classList.remove('open');
            accountToggle?.setAttribute('aria-expanded', 'false');
        }
        if (searchPanel?.classList.contains('open') &&
            !searchPanel.contains(e.target) && !searchToggle?.contains(e.target)) {
            searchPanel.classList.remove('open');
            searchToggle?.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            accountMenu?.classList.remove('open');
            searchPanel?.classList.remove('open');
            mobileNav?.classList.remove('open');
            accountToggle?.setAttribute('aria-expanded', 'false');
            searchToggle?.setAttribute('aria-expanded', 'false');
            mobileToggle?.setAttribute('aria-expanded', 'false');
            if (mobileIcon) mobileIcon.className = 'bi bi-list';
        }
        if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName) &&
            !e.ctrlKey && !e.metaKey && !e.altKey) {
            e.preventDefault();
            searchPanel?.classList.add('open');
            searchToggle?.setAttribute('aria-expanded', 'true');
            searchPanel?.querySelector('input')?.focus();
        }
    });

    const updateBackTop = () => backTop?.classList.toggle('visible', window.scrollY > 350);
    window.addEventListener('scroll', updateBackTop, { passive: true });
    updateBackTop();
    backTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    const localSearch = document.getElementById('publicLocalSearch');
    const localSearchButton = document.getElementById('publicLocalSearchButton');
    const performLocalSearch = () => {
        const query = localSearch?.value.trim().toLowerCase();
        if (!query) { localSearch?.focus(); return; }
        const links = Array.from(document.querySelectorAll('.public-nav a, .public-mobile-nav a'));
        const match = links.find(link => link.textContent.toLowerCase().includes(query));
        if (match) window.location.href = match.href;
        else {
            localSearch?.setCustomValidity('No matching navigation link found.');
            localSearch?.reportValidity();
            localSearch?.addEventListener('input', () => localSearch.setCustomValidity(''), { once: true });
        }
    };
    localSearchButton?.addEventListener('click', performLocalSearch);
    localSearch?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); performLocalSearch(); }
    });
})();
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/layouts/guest.blade.php ENDPATH**/ ?>