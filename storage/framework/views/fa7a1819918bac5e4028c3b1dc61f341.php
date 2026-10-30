<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'Seller Dashboard'); ?> — <?php echo e(config('app.name', 'TolBazar')); ?></title>
    <meta name="theme-color" content="#ffffff">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            color-scheme: light;
            --sb-width: 264px;
            --sb-mini: 78px;
            --primary: #625bf6;
            --primary-hover: #5148e5;
            --primary-soft: #efeeff;
            --page: #f7f8fc;
            --surface: #ffffff;
            --surface-2: #f4f5fa;
            --text: #1d2433;
            --muted: #7b8497;
            --border: #e8eaf1;
            --sidebar: #171b2e;
            --sidebar-text: #b6bfd2;
            --sidebar-active: #34304f;
            --success: #15966b;
            --danger: #dc4545;
            --shadow: 0 4px 20px rgba(22, 30, 50, .045);
        }

        html[data-theme="dark"] {
            color-scheme: dark;
            --page: #111522;
            --surface: #191e2e;
            --surface-2: #22283a;
            --text: #edf0f7;
            --muted: #a0aac0;
            --border: #30364a;
            --primary-soft: #302d55;
            --shadow: 0 4px 20px rgba(0, 0, 0, .14);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--page);
            color: var(--text);
            font-size: 14px;
            transition: background .2s, color .2s;
        }

        a { text-decoration: none; }

        /* Sidebar */
        .seller-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--sb-width);
            display: flex;
            flex-direction: column;
            background: var(--sidebar);
            color: #fff;
            z-index: 1040;
            transition: width .22s ease, transform .22s ease;
            overflow: hidden;
        }

        .seller-brand {
            min-height: 76px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 22px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            color: white;
            flex-shrink: 0;
        }

        .seller-brand-mark {
            display: grid;
            place-items: center;
            width: 39px;
            height: 39px;
            flex: 0 0 39px;
            border-radius: 12px;
            background: linear-gradient(135deg, #817aff, #5b50e9);
            color: white;
            font-size: 21px;
        }

        .seller-brand-name {
            font-weight: 800;
            font-size: 17px;
            letter-spacing: -.6px;
            white-space: nowrap;
        }

        .seller-brand-sub {
            display: block;
            color: #929bb4;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 1.2px;
            margin-top: 2px;
        }

        .seller-store {
            margin: 16px 13px 8px;
            padding: 13px;
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 0;
            background: rgba(255,255,255,.055);
            border: 1px solid rgba(255,255,255,.075);
            border-radius: 13px;
        }

        .seller-store-avatar {
            flex: 0 0 39px;
            height: 39px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            background: #3c3b67;
            color: #d8d6ff;
            font-size: 18px;
        }

        .seller-store-info { min-width: 0; flex: 1; }

        .seller-store-name {
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .seller-store-meta {
            color: #a9b2c9;
            font-size: 10px;
            margin-top: 4px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .seller-nav {
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1;
            padding: 5px 12px 16px;
            scrollbar-width: thin;
            scrollbar-color: #444961 transparent;
        }

        .seller-nav-label {
            padding: 20px 11px 8px;
            color: #7f8aa6;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 800;
            white-space: nowrap;
        }

        .seller-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 43px;
            padding: 10px 12px;
            margin: 3px 0;
            color: var(--sidebar-text);
            border-radius: 9px;
            border: 1px solid transparent;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
            transition: background .15s, color .15s;
        }

        .seller-nav-link > i:first-child {
            width: 20px;
            flex: 0 0 20px;
            font-size: 17px;
            text-align: center;
        }

        .seller-nav-link:hover {
            color: white;
            background: rgba(255,255,255,.06);
        }

        .seller-nav-link.active {
            background: var(--sidebar-active);
            color: #fff;
            border-color: rgba(167,160,255,.14);
        }

        .seller-nav-link.active > i:first-child { color: #aaa5ff; }

        .seller-nav-link .nav-count {
            margin-left: auto;
            padding: 3px 7px;
            border-radius: 6px;
            background: rgba(255,255,255,.1);
            font-size: 10px;
        }

        .seller-nav-link .nav-arrow {
            margin-left: auto;
            font-size: 11px;
            color: #7f8aa6;
        }

        .seller-nav-link.disabled-link {
            opacity: .52;
            cursor: not-allowed;
        }

        .seller-sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid rgba(255,255,255,.08);
            color: #8792ab;
            font-size: 10px;
            white-space: nowrap;
        }

        /* Main layout */
        .seller-main {
            margin-left: var(--sb-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left .22s ease;
        }

        .seller-topbar {
            height: 76px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 1020;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 0 27px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .seller-topbar-left,
        .seller-topbar-actions {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }

        .seller-page-heading { min-width: 0; }

        .seller-page-title {
            margin: 0;
            font-size: 17px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -.45px;
        }

        .seller-breadcrumb {
            margin-top: 4px;
            font-size: 10px;
            color: var(--muted);
        }

        .seller-icon-btn {
            position: relative;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            display: inline-grid;
            place-items: center;
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            background: var(--surface);
            font-size: 17px;
            cursor: pointer;
            transition: .15s;
        }

        .seller-icon-btn:hover {
            background: var(--surface-2);
            color: var(--primary);
        }

        .seller-search {
            display: flex;
            align-items: center;
            gap: 9px;
            height: 38px;
            padding: 0 11px;
            min-width: 205px;
            border: 1px solid var(--border);
            background: var(--surface-2);
            border-radius: 10px;
            color: var(--muted);
        }

        .seller-search input {
            min-width: 0;
            width: 100%;
            outline: none;
            border: 0;
            background: transparent;
            color: var(--text);
            font: inherit;
            font-size: 11px;
        }

        .seller-search kbd {
            font-size: 9px;
            padding: 2px 5px;
            border: 1px solid var(--border);
            border-radius: 4px;
            white-space: nowrap;
        }

        .seller-user-chip {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 4px 7px 4px 4px;
            border: 1px solid var(--border);
            background: var(--surface);
            border-radius: 12px;
            color: var(--text);
            max-width: 210px;
        }

        .seller-avatar {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 800;
            font-size: 13px;
        }

        .seller-user-name {
            font-size: 11px;
            font-weight: 700;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .seller-user-role {
            color: var(--muted);
            font-size: 10px;
            margin-top: 3px;
        }

        .seller-content {
            flex: 1;
            padding: 27px;
            min-width: 0;
        }

        .seller-footer {
            padding: 17px 27px;
            color: var(--muted);
            border-top: 1px solid var(--border);
            font-size: 11px;
        }

        /* Dropdowns */
        .seller-dropdown {
            min-width: 210px;
            padding: 7px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .seller-dropdown .dropdown-item {
            padding: 10px 11px;
            color: var(--text);
            border-radius: 8px;
            font-size: 12px;
        }

        .seller-dropdown .dropdown-item:hover { background: var(--surface-2); }
        .seller-dropdown .dropdown-divider { border-color: var(--border); }

        /* Compatibility with existing Bootstrap pages */
        .seller-main .card {
            color: var(--text);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
        }

        .seller-main .card-header {
            color: var(--text);
            background: transparent;
            border-color: var(--border);
        }

        .seller-main .table {
            --bs-table-bg: var(--surface);
            --bs-table-color: var(--text);
            --bs-table-border-color: var(--border);
            --bs-table-hover-bg: var(--surface-2);
            --bs-table-hover-color: var(--text);
        }

        .seller-main .form-control,
        .seller-main .form-select {
            color: var(--text);
            background-color: var(--surface);
            border-color: var(--border);
        }

        .seller-main .form-control::placeholder { color: var(--muted); }

        .seller-main .form-control:focus,
        .seller-main .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 .2rem rgba(98,91,246,.13);
        }

        .seller-main .btn { border-radius: 9px; font-weight: 600; }
        .seller-main .btn-primary { background: var(--primary); border-color: var(--primary); }
        .seller-main .btn-primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); }

        .seller-main .text-muted { color: var(--muted) !important; }

        html[data-theme="dark"] .seller-main .text-dark,
        html[data-theme="dark"] .seller-main .text-body { color: var(--text) !important; }

        html[data-theme="dark"] .seller-main .bg-white,
        html[data-theme="dark"] .seller-main .bg-light {
            background-color: var(--surface) !important;
        }

        html[data-theme="dark"] .seller-main .border,
        html[data-theme="dark"] .seller-main .border-bottom,
        html[data-theme="dark"] .seller-main .border-top {
            border-color: var(--border) !important;
        }

        .seller-sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1035;
            background: rgba(10,15,30,.56);
            backdrop-filter: blur(2px);
        }

        .seller-sidebar-backdrop.show { display: block; }

        .seller-empty-search {
            padding: 15px 10px;
            text-align: center;
            color: #8792ab;
            font-size: 11px;
        }

        /* Desktop compact sidebar */
        @media (min-width: 992px) {
            body.sidebar-collapsed .seller-sidebar { width: var(--sb-mini); }
            body.sidebar-collapsed .seller-main { margin-left: var(--sb-mini); }

            body.sidebar-collapsed .seller-brand { padding: 0 19px; }
            body.sidebar-collapsed .seller-brand-copy,
            body.sidebar-collapsed .seller-store-info,
            body.sidebar-collapsed .seller-store > .bi,
            body.sidebar-collapsed .seller-nav-label,
            body.sidebar-collapsed .seller-nav-link .nav-copy,
            body.sidebar-collapsed .seller-nav-link .nav-count,
            body.sidebar-collapsed .seller-nav-link .nav-arrow,
            body.sidebar-collapsed .seller-sidebar-footer .footer-copy {
                display: none !important;
            }

            body.sidebar-collapsed .seller-store {
                justify-content: center;
                padding: 8px 4px;
                margin: 14px 12px;
            }

            body.sidebar-collapsed .seller-nav { padding-inline: 10px; }
            body.sidebar-collapsed .seller-nav-link { justify-content: center; padding: 10px 5px; }
            body.sidebar-collapsed .seller-nav-link > i:first-child { font-size: 19px; }
        }

        /* Mobile */
        @media (max-width: 991.98px) {
            .seller-sidebar { transform: translateX(-100%); width: 280px; }
            .seller-sidebar.show { transform: translateX(0); }
            .seller-main { margin-left: 0; }
            .seller-topbar { height: 68px; padding: 0 17px; }
            .seller-content { padding: 18px; }
            .seller-footer { padding: 15px 18px; }
            .seller-search { display: none; }
            .seller-search.mobile-search-open {
                display: flex;
                position: absolute;
                top: 68px;
                left: 0;
                right: 0;
                min-width: 0;
                border-radius: 0;
                height: 48px;
                background: var(--surface);
                z-index: 1021;
            }
        }

        @media (max-width: 575.98px) {
            .seller-topbar { gap: 7px; padding: 0 12px; }
            .seller-topbar-left, .seller-topbar-actions { gap: 7px; }
            .seller-page-title { font-size: 14px; }
            .seller-breadcrumb { display: none; }
            .seller-content { padding: 14px; }
            .seller-user-chip { padding: 3px; border: 0; }
            .seller-user-chip .seller-user-copy { display: none; }
            .seller-icon-btn { width: 35px; height: 35px; flex-basis: 35px; }
            .seller-content .table-responsive { font-size: 12px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
<?php
    $sellerUser = auth()->user();
    $sellerProfile = $sellerUser?->sellerProfile;
    $activeBranchId = session('active_branch_id');
    $activeBranchRole = session('active_branch_role', 'owner');

    $activeBranch = null;
    $branchCount = 0;

    if ($sellerProfile) {
        $branchCount = $sellerProfile->branches()->count();
        if ($activeBranchId) {
            $activeBranch = $sellerProfile->branches()
                ->where('id', $activeBranchId)
                ->first();
        }
    }

    $sellerRoute = fn ($name) => \Illuminate\Support\Facades\Route::has($name);

    $sellerNavGroups = [
        [
            'label' => 'Workspace',
            'items' => [
                ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'seller.dashboard'],
            ],
        ],
        [
            'label' => 'Store Management',
            'items' => [
                ['label' => 'Branches',          'icon' => 'bi-shop-window', 'route' => 'seller.branches.index', 'count' => $branchCount],
                ['label' => 'Add Branch',        'icon' => 'bi-plus-square', 'route' => 'seller.branches.create'],
                ['label' => 'Staff & Access',    'icon' => 'bi-people',      'route' => 'seller.staff.index'],
                ['label' => 'Services',          'icon' => 'bi-grid',        'route' => 'seller.services.index'],
                ['label' => 'Products',          'icon' => 'bi-box-seam',    'route' => 'seller.products.index'],
                ['label' => 'Add Product',       'icon' => 'bi-plus-circle', 'route' => 'seller.products.create'],
                ['label' => 'Featured Products', 'icon' => 'bi-star',        'route' => 'seller.products.featured'],
            ],
        ],
        [
            'label' => 'Bookings & Payments',
            'items' => [
                ['label' => 'Bookings',          'icon' => 'bi-calendar2-check', 'route' => 'seller.bookings.index'],
                ['label' => 'Payments Overview', 'icon' => 'bi-cash-coin',       'route' => 'seller.payments.index'],
                ['label' => 'Product Payments',  'icon' => 'bi-receipt',         'route' => 'seller.payments.products'],
                ['label' => 'Collections',       'icon' => 'bi-collection',      'route' => 'seller.payments.collections'],
                ['label' => 'Pending COD',       'icon' => 'bi-truck',           'route' => 'seller.payments.pending-cod'],
            ],
        ],
        [
            'label' => 'Finance & Wallet',
            'items' => [
                ['label' => 'Wallet Dashboard',  'icon' => 'bi-wallet2',          'route' => 'wallet.dashboard'],
                ['label' => 'Transactions',      'icon' => 'bi-arrow-left-right', 'route' => 'wallet.transactions'],
                ['label' => 'Add Money',         'icon' => 'bi-plus-circle',      'route' => 'wallet.add-money'],
                ['label' => 'Load Requests',     'icon' => 'bi-clock-history',    'route' => 'wallet.load-requests'],
                ['label' => 'Withdraw',          'icon' => 'bi-cash-stack',       'route' => 'wallet.withdraw'],
                ['label' => 'Withdraw Requests', 'icon' => 'bi-hourglass-split',  'route' => 'wallet.withdraw-requests'],
                ['label' => 'Transfer',          'icon' => 'bi-send',             'route' => 'wallet.transfer'],
                ['label' => 'Bank Accounts',     'icon' => 'bi-bank',             'route' => 'wallet.bank-accounts'],
                ['label' => 'Wallet Security',   'icon' => 'bi-shield-lock',      'route' => 'wallet.security'],
                ['label' => 'Rewards',           'icon' => 'bi-gift',             'route' => 'wallet.rewards'],
            ],
        ],
        [
            'label' => 'Community',
            'items' => [
                ['label' => 'Community Feed', 'icon' => 'bi-people-fill',  'route' => 'seller.social.feed'],
                ['label' => 'Create Post',    'icon' => 'bi-plus-square', 'route' => 'seller.posts.create'],
                ['label' => 'My Posts',       'icon' => 'bi-collection',   'route' => 'seller.posts.my'],
                ['label' => 'Saved Posts',    'icon' => 'bi-bookmark',     'route' => 'seller.posts.saved'],
            ],
        ],
    ];
?>

<aside class="seller-sidebar" id="sellerSidebar" aria-label="Seller navigation">
    <a class="seller-brand" href="<?php echo e($sellerRoute('seller.dashboard') ? route('seller.dashboard') : url('/')); ?>">
        <span class="seller-brand-mark"><i class="bi bi-shop"></i></span>
        <span class="seller-brand-copy">
            <span class="seller-brand-name"><?php echo e(config('app.name', 'TolBazar')); ?></span>
            <span class="seller-brand-sub">SELLER WORKSPACE</span>
        </span>
    </a>

    <?php if(auth()->guard()->check()): ?>
        <div class="seller-store" title="<?php echo e($sellerProfile->store_name ?? 'My Store'); ?>">
            <div class="seller-store-avatar"><i class="bi bi-shop-window"></i></div>
            <div class="seller-store-info">
                <div class="seller-store-name"><?php echo e($sellerProfile->store_name ?? 'My Store'); ?></div>
                <div class="seller-store-meta">
                    <i class="bi bi-person-badge me-1"></i>
                    <?php echo e(ucfirst($activeBranchRole)); ?>

                    <?php if($activeBranch): ?>
                        · <?php echo e($activeBranch->branch_name); ?>

                    <?php endif; ?>
                </div>
            </div>
            <i class="bi bi-chevron-expand text-white-50"></i>
        </div>
    <?php endif; ?>

    <nav class="seller-nav" id="sellerNav">
        <?php $__currentLoopData = $sellerNavGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="seller-nav-group" data-nav-group>
                <div class="seller-nav-label"><?php echo e($group['label']); ?></div>

                <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $routeExists = $sellerRoute($item['route']);
                        $routeIsActive = $routeExists && request()->routeIs($item['route']);
                        $routePattern = str_replace('.index', '.*', $item['route']);
                        $routeIsActive = $routeIsActive ||
                            ($routeExists && str_ends_with($item['route'], '.index') &&
                            request()->routeIs($routePattern));
                    ?>

                    <?php if($routeExists): ?>
                        <a href="<?php echo e(route($item['route'])); ?>"
                           class="seller-nav-link <?php echo e($routeIsActive ? 'active' : ''); ?>"
                           data-nav-item
                           title="<?php echo e($item['label']); ?>"
                           <?php if($routeIsActive): ?> aria-current="page" <?php endif; ?>>
                            <i class="bi <?php echo e($item['icon']); ?>"></i>
                            <span class="nav-copy"><?php echo e($item['label']); ?></span>
                            <?php if(isset($item['count']) && $item['count'] > 0): ?>
                                <span class="nav-count"><?php echo e($item['count']); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php else: ?>
                        <span class="seller-nav-link disabled-link"
                              data-nav-item
                              title="Route not configured yet">
                            <i class="bi <?php echo e($item['icon']); ?>"></i>
                            <span class="nav-copy"><?php echo e($item['label']); ?></span>
                            <span class="nav-arrow" title="Coming soon">
                                <i class="bi bi-lock"></i>
                            </span>
                        </span>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="seller-nav-group" data-nav-group>
            <div class="seller-nav-label">Account</div>

            <?php if($sellerRoute('profile.me')): ?>
                <a href="<?php echo e(route('profile.me')); ?>" class="seller-nav-link" data-nav-item
                   title="My Profile">
                    <i class="bi bi-person"></i><span class="nav-copy">My Profile</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('profile.edit')): ?>
                <a href="<?php echo e(route('profile.edit')); ?>" class="seller-nav-link" data-nav-item
                   title="Edit Profile">
                    <i class="bi bi-pencil-square"></i><span class="nav-copy">Edit Profile</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('account.settings')): ?>
                <a href="<?php echo e(route('account.settings')); ?>" class="seller-nav-link" data-nav-item
                   title="Account Settings">
                    <i class="bi bi-gear"></i><span class="nav-copy">Account Settings</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('account.login-activity')): ?>
                <a href="<?php echo e(route('account.login-activity')); ?>" class="seller-nav-link" data-nav-item
                   title="Login Activity">
                    <i class="bi bi-clock-history"></i><span class="nav-copy">Login Activity</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('account.sessions')): ?>
                <a href="<?php echo e(route('account.sessions')); ?>" class="seller-nav-link" data-nav-item
                   title="Active Sessions">
                    <i class="bi bi-laptop"></i><span class="nav-copy">Active Sessions</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('account.switcher')): ?>
                <a href="<?php echo e(route('account.switcher')); ?>" class="seller-nav-link" data-nav-item
                   title="Switch Account">
                    <i class="bi bi-arrow-repeat"></i><span class="nav-copy">Switch Account</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('account.change-username')): ?>
                <a href="<?php echo e(route('account.change-username')); ?>" class="seller-nav-link" data-nav-item
                   title="Change Username">
                    <i class="bi bi-at"></i><span class="nav-copy">Change Username</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('account.change-email')): ?>
                <a href="<?php echo e(route('account.change-email')); ?>" class="seller-nav-link" data-nav-item
                   title="Change Email">
                    <i class="bi bi-envelope"></i><span class="nav-copy">Change Email</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('account.change-name')): ?>
                <a href="<?php echo e(route('account.change-name')); ?>" class="seller-nav-link" data-nav-item
                   title="Change Name">
                    <i class="bi bi-person-badge"></i><span class="nav-copy">Change Name</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('account.change-mobile')): ?>
                <a href="<?php echo e(route('account.change-mobile')); ?>" class="seller-nav-link" data-nav-item
                   title="Change Mobile">
                    <i class="bi bi-phone"></i><span class="nav-copy">Change Mobile</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('verification.center')): ?>
                <a href="<?php echo e(route('verification.center')); ?>" class="seller-nav-link" data-nav-item
                   title="Verification Center">
                    <i class="bi bi-patch-check"></i><span class="nav-copy">Verification</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('home')): ?>
                <a href="<?php echo e(route('home')); ?>" class="seller-nav-link" data-nav-item
                   title="Customer Home">
                    <i class="bi bi-house"></i><span class="nav-copy">Customer Home</span>
                </a>
            <?php endif; ?>

            <?php if($sellerRoute('switch.mode')): ?>
                <form method="POST" action="<?php echo e(route('switch.mode', 'customer')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="seller-nav-link w-100 text-start bg-transparent"
                            style="border:0" data-nav-item title="Switch to Customer">
                        <i class="bi bi-arrow-left-right"></i>
                        <span class="nav-copy">Switch to Customer</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <div class="seller-empty-search d-none" id="sellerNoResults">
            No matching menu items found.
        </div>
    </nav>

    <div class="seller-sidebar-footer">
        <div class="d-flex justify-content-between align-items-center">
            <span class="footer-copy">Seller Workspace · v1.0</span>
            <i class="bi bi-shield-check"></i>
        </div>
    </div>
</aside>

<div class="seller-sidebar-backdrop" id="sellerSidebarBackdrop"></div>


<div class="seller-main">
    <header class="seller-topbar">
        <div class="seller-topbar-left">
            <button type="button" class="seller-icon-btn" id="sellerSidebarToggle"
                    aria-label="Toggle sidebar" aria-controls="sellerSidebar" aria-expanded="true">
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>

            <div class="seller-page-heading">
                <h1 class="seller-page-title"><?php echo $__env->yieldContent('title', 'Seller Dashboard'); ?></h1>
            </div>
        </div>

        <div class="seller-topbar-actions">
            <label class="seller-search" id="sellerSearchBox">
                <i class="bi bi-search"></i>
                <input id="sellerNavSearch" type="search" placeholder="Search menu..." autocomplete="off">
                <kbd> / </kbd>
            </label>

            <button type="button" class="seller-icon-btn d-lg-none" id="sellerMobileSearch"
                    aria-label="Search menu">
                <i class="bi bi-search"></i>
            </button>

            <button type="button" class="seller-icon-btn" id="sellerThemeToggle"
                    aria-label="Toggle dark mode" title="Toggle dark mode">
                <i class="bi bi-moon-stars" id="sellerThemeIcon"></i>
            </button>

            <?php if(auth()->guard()->check()): ?>
                <?php
                    $isSellerOwner = method_exists($sellerUser, 'isSellerOwner')
                        ? $sellerUser->isSellerOwner()
                        : ($activeBranchRole === 'owner');
                ?>

                <?php if($isSellerOwner && $sellerProfile && $branchCount > 1 && $sellerRoute('switch.mode')): ?>
                    <form method="POST" action="<?php echo e(route('switch.mode', 'seller')); ?>"
                          class="d-none d-md-block">
                        <?php echo csrf_field(); ?>
                        <select name="branch_id" class="form-select form-select-sm"
                                aria-label="Switch branch"
                                onchange="this.form.submit()" style="max-width:180px">
                            <?php $__currentLoopData = $sellerProfile->branches()->where('status', 1)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($branch->id); ?>"
                                    <?php echo e((string) session('active_branch_id') === (string) $branch->id ? 'selected' : ''); ?>>
                                    <?php echo e($branch->branch_name); ?><?php echo e($branch->is_default ? ' (HQ)' : ''); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

            <?php if(auth()->guard()->check()): ?>
                <div class="dropdown">
                    <button class="seller-user-chip dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="seller-avatar">
                            <?php echo e(strtoupper(substr($sellerUser->name ?? 'S', 0, 1))); ?>

                        </span>
                        <span class="seller-user-copy text-start">
                            <span class="seller-user-name d-block"><?php echo e($sellerUser->name); ?></span>
                            <span class="seller-user-role d-block"><?php echo e(ucfirst($activeBranchRole)); ?></span>
                        </span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end seller-dropdown mt-2">
                        <div class="px-3 py-2">
                            <div class="fw-bold"><?php echo e($sellerUser->name); ?></div>
                            <div class="small text-muted text-break"><?php echo e($sellerUser->email); ?></div>
                        </div>
                        <div class="dropdown-divider"></div>

                        <?php if($sellerRoute('profile.me')): ?>
                            <a class="dropdown-item" href="<?php echo e(route('profile.me')); ?>">
                                <i class="bi bi-person me-2"></i>My Profile
                            </a>
                        <?php endif; ?>

                        <?php if($sellerRoute('profile.edit')): ?>
                            <a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>">
                                <i class="bi bi-pencil-square me-2"></i>Edit Profile
                            </a>
                        <?php endif; ?>

                        <?php if($sellerRoute('account.settings')): ?>
                            <a class="dropdown-item" href="<?php echo e(route('account.settings')); ?>">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a>
                        <?php endif; ?>

                        <?php if($sellerRoute('wallet.dashboard')): ?>
                            <a class="dropdown-item" href="<?php echo e(route('wallet.dashboard')); ?>">
                                <i class="bi bi-wallet2 me-2"></i>My Wallet
                            </a>
                        <?php endif; ?>

                        <?php if($sellerRoute('home')): ?>
                            <a class="dropdown-item" href="<?php echo e(route('home')); ?>">
                                <i class="bi bi-house me-2"></i>Customer Website
                            </a>
                        <?php endif; ?>

                        <div class="dropdown-divider"></div>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </header>

    
    <div class="px-3 px-md-4 pt-3">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-2" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(session('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show mb-2" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo e(session('warning')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(session('info')): ?>
            <div class="alert alert-info alert-dismissible fade show mb-2" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i><?php echo e(session('info')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <main class="seller-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer class="seller-footer d-flex flex-wrap justify-content-between gap-2">
        <span>© <?php echo e(date('Y')); ?> <?php echo e(config('app.name', 'TolBazar')); ?>. All rights reserved.</span>
        <span><i class="bi bi-shield-check me-1"></i>Seller Workspace</span>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(() => {
    'use strict';

    const body = document.body;
    const html = document.documentElement;
    const sidebar = document.getElementById('sellerSidebar');
    const backdrop = document.getElementById('sellerSidebarBackdrop');
    const toggle = document.getElementById('sellerSidebarToggle');
    const themeToggle = document.getElementById('sellerThemeToggle');
    const themeIcon = document.getElementById('sellerThemeIcon');
    const search = document.getElementById('sellerNavSearch');
    const searchBox = document.getElementById('sellerSearchBox');
    const mobileSearch = document.getElementById('sellerMobileSearch');
    const noResults = document.getElementById('sellerNoResults');

    const isMobile = () => window.matchMedia('(max-width: 991.98px)').matches;

    let savedTheme = 'light';
    try { savedTheme = localStorage.getItem('tolbazar-seller-theme') || 'light'; } catch (e) {}

    const setTheme = (theme) => {
        html.setAttribute('data-theme', theme);
        html.style.colorScheme = theme;

        if (themeIcon) {
            themeIcon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
        }
        if (themeToggle) {
            themeToggle.setAttribute('aria-label',
                theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
        }
        const meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.content = theme === 'dark' ? '#191e2e' : '#ffffff';
        try { localStorage.setItem('tolbazar-seller-theme', theme); } catch (e) {}
    };

    setTheme(savedTheme);

    themeToggle?.addEventListener('click', () => {
        setTheme(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });

    let savedCollapsed = false;
    try { savedCollapsed = localStorage.getItem('tolbazar-seller-sidebar') === 'collapsed'; } catch (e) {}

    if (savedCollapsed && !isMobile()) body.classList.add('sidebar-collapsed');

    const openSidebar = () => {
        sidebar?.classList.add('show');
        backdrop?.classList.add('show');
        toggle?.setAttribute('aria-expanded', 'true');
        body.style.overflow = 'hidden';
    };

    const closeSidebar = () => {
        sidebar?.classList.remove('show');
        backdrop?.classList.remove('show');
        toggle?.setAttribute('aria-expanded', 'false');
        body.style.overflow = '';
    };

    toggle?.addEventListener('click', () => {
        if (isMobile()) {
            sidebar?.classList.contains('show') ? closeSidebar() : openSidebar();
        } else {
            body.classList.toggle('sidebar-collapsed');
            try {
                localStorage.setItem(
                    'tolbazar-seller-sidebar',
                    body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded'
                );
            } catch (e) {}
        }
    });

    backdrop?.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
            searchBox?.classList.remove('mobile-search-open');
        }
        if (event.key === '/' &&
            !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName) &&
            !event.ctrlKey && !event.metaKey && !event.altKey) {
            event.preventDefault();
            searchBox?.classList.add('mobile-search-open');
            search?.focus();
        }
    });

    mobileSearch?.addEventListener('click', () => {
        searchBox?.classList.toggle('mobile-search-open');
        if (searchBox?.classList.contains('mobile-search-open')) search?.focus();
    });

    window.addEventListener('resize', () => {
        if (!isMobile()) closeSidebar();
    });

    search?.addEventListener('input', () => {
        const query = search.value.trim().toLowerCase();
        let totalVisible = 0;

        document.querySelectorAll('[data-nav-group]').forEach(group => {
            let groupVisible = 0;
            group.querySelectorAll('[data-nav-item]').forEach(item => {
                const matches = item.textContent.toLowerCase().includes(query);
                item.classList.toggle('d-none', !matches);
                if (matches) { groupVisible++; totalVisible++; }
            });
            group.classList.toggle('d-none', groupVisible === 0);
        });

        noResults?.classList.toggle('d-none', totalVisible !== 0 || query === '');
    });

    window.setTimeout(() => {
        document.querySelectorAll('.alert.alert-dismissible').forEach(alert => {
            if (window.bootstrap?.Alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        });
    }, 6000);

    sidebar?.querySelectorAll('a.seller-nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (isMobile()) closeSidebar();
        });
    });
})();
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/layouts/seller.blade.php ENDPATH**/ ?>