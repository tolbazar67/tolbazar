<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Admin Panel'); ?> — <?php echo e(config('app.name', 'TolBazar')); ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --admin-primary: #0b1120;
            --admin-primary-light: #1a2332;
            --admin-accent: #f59e0b;
            --admin-accent-2: #ef4444;
            --admin-success: #10b981;
            --admin-info: #3b82f6;
            --admin-bg: #f5f7fb;
            --admin-surface: #ffffff;
            --admin-border: #e5e9f0;
            --admin-text: #0f172a;
            --admin-text-muted: #64748b;
            --sidebar-w: 268px;
            --topbar-h: 68px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: var(--admin-bg);
            margin: 0;
            min-height: 100vh;
            color: var(--admin-text);
            -webkit-font-smoothing: antialiased;
        }

        /* ============ SIDEBAR ============ */
        .admin-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, #0b1120 0%, #131c2e 100%);
            color: #cbd5e1;
            display: flex;
            flex-direction: column;
            z-index: 1030;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid rgba(255,255,255,0.04);
        }

        .admin-sidebar::-webkit-scrollbar { width: 5px; }
        .admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(245,158,11,0.3);
            border-radius: 3px;
        }

        .admin-sidebar .brand {
            padding: 22px 22px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 1.2rem;
            color: #fff;
            text-decoration: none;
            letter-spacing: -0.02em;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .admin-sidebar .brand:hover { color: #fff; }

        .admin-sidebar .brand .brand-icon {
            width: 38px; height: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--admin-accent), #d97706);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem;
            color: #fff;
            box-shadow: 0 6px 16px rgba(245,158,11,0.35);
        }

        .admin-sidebar .brand .brand-sub {
            font-size: .58rem;
            color: var(--admin-accent);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-left: auto;
            padding: 3px 8px;
            background: rgba(245,158,11,0.12);
            border-radius: 5px;
        }

        .admin-sidebar .admin-box {
            margin: 16px 14px;
            padding: 14px;
            border-radius: 12px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-sidebar .admin-box .avatar {
            width: 40px; height: 40px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-2));
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800;
            font-size: .95rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(245,158,11,0.25);
        }

        .admin-sidebar .admin-box .admin-name {
            font-weight: 700;
            color: #fff;
            font-size: .88rem;
            line-height: 1.15;
            letter-spacing: -0.01em;
        }

        .admin-sidebar .admin-box .admin-role {
            font-size: .66rem;
            color: var(--admin-accent);
            text-transform: uppercase;
            letter-spacing: .8px;
            font-weight: 600;
            margin-top: 2px;
        }

        .admin-sidebar .nav-label {
            font-size: .64rem;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: #475569;
            padding: 20px 24px 8px;
            font-weight: 700;
        }

        .admin-sidebar nav { padding-bottom: 16px; }

        .admin-sidebar .nav-link {
            color: #94a3b8;
            padding: 11px 22px;
            margin: 1px 12px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: .855rem;
            font-weight: 500;
            transition: all .18s ease;
            position: relative;
        }

        .admin-sidebar .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: #fff;
        }

        .admin-sidebar .nav-link.active {
            background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.08));
            color: #fff;
            font-weight: 600;
            box-shadow: inset 0 0 0 1px rgba(245,158,11,0.2);
        }

        .admin-sidebar .nav-link.active::before {
            content: "";
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: var(--admin-accent);
            border-radius: 0 3px 3px 0;
        }

        .admin-sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.05rem;
        }

        .admin-sidebar .nav-link.active i { color: var(--admin-accent); }

        .admin-sidebar .nav-link .badge-count {
            background: var(--admin-accent);
            color: #0b1120;
            font-size: .65rem;
            padding: 2px 8px;
            border-radius: 8px;
            margin-left: auto;
            font-weight: 800;
        }

        .admin-sidebar .sidebar-footer {
            margin-top: auto;
            padding: 16px 18px;
            border-top: 1px solid rgba(255,255,255,0.06);
            font-size: .75rem;
            color: #64748b;
        }

        .admin-sidebar .mode-switch-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 10px;
            border-radius: 9px;
            background: rgba(245,158,11,0.1);
            color: var(--admin-accent);
            border: 1px solid rgba(245,158,11,0.2);
            font-size: .78rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .18s;
        }

        .admin-sidebar .mode-switch-btn:hover {
            background: rgba(245,158,11,0.18);
            color: #fbbf24;
        }

        /* ============ MAIN ============ */
        .admin-main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-topbar {
            background: rgba(255,255,255,0.85);
            backdrop-filter: saturate(180%) blur(12px);
            -webkit-backdrop-filter: saturate(180%) blur(12px);
            border-bottom: 1px solid var(--admin-border);
            padding: 0 28px;
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .admin-topbar .page-title {
            font-weight: 800;
            font-size: 1.12rem;
            color: var(--admin-text);
            margin: 0;
            letter-spacing: -0.02em;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .admin-topbar .breadcrumb {
            margin: 0;
            font-size: .76rem;
            --bs-breadcrumb-divider: '›';
        }

        .admin-topbar .breadcrumb-item a {
            color: var(--admin-text-muted);
            text-decoration: none;
            font-weight: 500;
        }

        .admin-topbar .breadcrumb-item.active {
            color: var(--admin-text);
            font-weight: 600;
        }

        .admin-topbar .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 12px 4px 4px;
            border-radius: 30px;
            background: #fff;
            font-size: .82rem;
            border: 1px solid var(--admin-border);
            transition: all .18s;
            cursor: pointer;
        }

        .admin-topbar .user-chip:hover {
            border-color: var(--admin-accent);
            box-shadow: 0 4px 14px rgba(245,158,11,0.12);
        }

        .admin-topbar .user-chip .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-2));
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800;
            font-size: .78rem;
        }

        .admin-topbar .icon-btn {
            width: 40px; height: 40px;
            border-radius: 11px;
            border: 1px solid var(--admin-border);
            background: #fff;
            color: var(--admin-text-muted);
            display: flex; align-items: center; justify-content: center;
            transition: all .18s;
            position: relative;
        }

        .admin-topbar .icon-btn:hover {
            color: var(--admin-accent);
            border-color: var(--admin-accent);
            background: #fffbf3;
        }

        .admin-topbar .icon-btn .notif-dot {
            position: absolute;
            top: 8px; right: 9px;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--admin-accent-2);
            border: 1.5px solid #fff;
        }

        .admin-topbar .search-wrap {
            position: relative;
            width: 260px;
        }

        .admin-topbar .search-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--admin-text-muted);
            font-size: .9rem;
        }

        .admin-topbar .search-wrap input {
            width: 100%;
            height: 40px;
            border-radius: 11px;
            border: 1px solid var(--admin-border);
            background: var(--admin-bg);
            padding: 0 14px 0 40px;
            font-size: .84rem;
            transition: all .18s;
        }

        .admin-topbar .search-wrap input:focus {
            outline: 0;
            background: #fff;
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 4px rgba(245,158,11,0.12);
        }

        .admin-content {
            padding: 26px 28px;
            flex: 1;
        }

        /* ============ MOBILE ============ */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.4rem;
            color: var(--admin-text);
            padding: 4px 8px;
            border-radius: 8px;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(11,17,32,0.55);
            z-index: 1029;
            backdrop-filter: blur(3px);
        }
        .sidebar-backdrop.show { display: block; }

        @media (max-width: 991.98px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .sidebar-toggle { display: inline-block; }
            .admin-content { padding: 16px; }
            .admin-topbar { padding: 0 16px; }
            .admin-topbar .search-wrap { display: none; }
        }

        /* ============ UTILITIES ============ */
        .card { border-radius: 14px; border: 1px solid var(--admin-border); }
        .btn { border-radius: 9px; font-weight: 600; font-size: .85rem; }
        .form-control, .form-select { border-radius: 9px; border-color: var(--admin-border); }
        .form-control:focus, .form-select:focus {
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 4px rgba(245,158,11,0.12);
        }
        .badge { font-weight: 600; }
        .btn-primary {
            background: var(--admin-primary);
            border-color: var(--admin-primary);
        }
        .btn-primary:hover {
            background: #020617;
            border-color: #020617;
        }
        .btn-warning {
            background: var(--admin-accent);
            border-color: var(--admin-accent);
            color: #fff;
        }
        .btn-warning:hover {
            background: #d97706;
            border-color: #d97706;
            color: #fff;
        }

        .table > :not(caption) > * > * { padding: .85rem .65rem; }
        .table thead th {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--admin-text-muted);
            font-weight: 700;
            background: #f8fafc;
        }

        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 14px rgba(0,0,0,0.04);
            font-size: .88rem;
        }

        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid var(--admin-border);
            box-shadow: 0 12px 32px rgba(15,23,42,0.1);
            padding: 6px;
        }
        .dropdown-item {
            border-radius: 7px;
            padding: 9px 12px;
            font-size: .86rem;
            font-weight: 500;
        }
        .dropdown-item:hover { background: #f8fafc; }
        .dropdown-header { font-size: .78rem; color: var(--admin-text); padding: 8px 12px 4px; }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>


<aside class="admin-sidebar" id="adminSidebar">

    
    <a href="<?php echo e(route('admin.dashboard')); ?>" class="brand">
        <span class="brand-icon"><i class="bi bi-shield-check"></i></span>
        <span><?php echo e(config('app.name', 'TolBazar')); ?></span>
        <span class="brand-sub">Admin</span>
    </a>

    
    <?php if(auth()->guard()->check()): ?>
        <?php
            $adminUser = auth()->user();
            $isSuperAdmin = $adminUser->hasRole('Super Admin');
        ?>
        <div class="admin-box">
            <div class="avatar"><?php echo e(strtoupper(substr($adminUser->name, 0, 1))); ?></div>
            <div class="min-w-0">
                <div class="admin-name text-truncate"><?php echo e($adminUser->name); ?></div>
                <div class="admin-role"><?php echo e($isSuperAdmin ? 'Super Admin' : 'Admin'); ?></div>
            </div>
        </div>
    <?php endif; ?>

    

<nav>
    <div class="nav-label">Dashboard</div>

    <a href="<?php echo e(route('admin.dashboard')); ?>"
       class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
        <i class="bi bi-grid-1x2-fill"></i> Overview
    </a>

    
    <div class="nav-label">Service Management</div>

    
    <a href="<?php echo e(route('admin.services.index')); ?>"
       class="nav-link <?php echo e(request()->routeIs('admin.services.*') ? 'active' : ''); ?>">
        <i class="bi bi-grid-3x3-gap-fill"></i> Services
        <?php
            $serviceCount = \App\Models\Service::where('is_active', 1)->count();
        ?>
        <?php if($serviceCount > 0): ?>
            <span class="badge-count"><?php echo e($serviceCount); ?></span>
        <?php endif; ?>
    </a>

    
    <a href="<?php echo e(route('admin.service-categories.index')); ?>"
       class="nav-link <?php echo e(request()->routeIs('admin.service-categories.*') ? 'active' : ''); ?>">
        <i class="bi bi-diagram-3-fill"></i> Service Categories
        <?php
            $categoryCount = \App\Models\ServiceCategory::where('is_active', 1)->count();
        ?>
        <?php if($categoryCount > 0): ?>
            <span class="badge-count"><?php echo e($categoryCount); ?></span>
        <?php endif; ?>
    </a>

    
    <a href="<?php echo e(route('admin.service-tax-rules.index')); ?>"
       class="nav-link <?php echo e(request()->routeIs('admin.service-tax-rules.*') ? 'active' : ''); ?>">
        <i class="bi bi-percent"></i> Tax Rules
    </a>

    
    <a href="<?php echo e(route('admin.partner-commission-rules.index')); ?>"
       class="nav-link <?php echo e(request()->routeIs('admin.partner-commission-rules.*') ? 'active' : ''); ?>">
        <i class="bi bi-cash-coin"></i> Partner Commissions
    </a>

    
    <a href="<?php echo e(route('admin.customer-charge-rules.index')); ?>"
       class="nav-link <?php echo e(request()->routeIs('admin.customer-charge-rules.*') ? 'active' : ''); ?>">
        <i class="bi bi-receipt"></i> Customer Charges
    </a>

    
    <div class="nav-label">Platform</div>

    <a href="#" class="nav-link">
        <i class="bi bi-shop-window"></i> Sellers
    </a>

    <a href="#" class="nav-link">
        <i class="bi bi-people-fill"></i> Users
    </a>

    <a href="#" class="nav-link">
        <i class="bi bi-box-seam-fill"></i> Products
    </a>

    <a href="#" class="nav-link">
        <i class="bi bi-receipt-cutoff"></i> Orders
    </a>

    
    <div class="nav-label">Content</div>

    <a href="#" class="nav-link">
        <i class="bi bi-megaphone-fill"></i> Announcements
    </a>

    <a href="#" class="nav-link">
        <i class="bi bi-image-fill"></i> Banners
    </a>

    <a href="#" class="nav-link">
        <i class="bi bi-chat-square-text-fill"></i> Community Moderation
    </a>

    
    <div class="nav-label">System</div>

    <a href="#" class="nav-link">
        <i class="bi bi-gear-fill"></i> Settings
    </a>

    <a href="#" class="nav-link">
        <i class="bi bi-bar-chart-line-fill"></i> Reports
    </a>

    <a href="#" class="nav-link">
        <i class="bi bi-shield-lock-fill"></i> Roles & Permissions
    </a>
</nav>

    
    <div class="sidebar-footer">
        <form method="POST" action="<?php echo e(route('switch.mode', 'customer')); ?>" class="mb-3">
            <?php echo csrf_field(); ?>
            <button type="submit" class="mode-switch-btn">
                <i class="bi bi-arrow-left-right"></i> Switch to Customer
            </button>
        </form>

        <div class="d-flex justify-content-between align-items-center">
            <span style="font-size:.7rem;">v1.0.0</span>
            <span style="font-size:.7rem; color:#10b981;">
                <i class="bi bi-shield-fill-check"></i> Secure
            </span>
        </div>
    </div>
</aside>


<div class="sidebar-backdrop" id="sidebarBackdrop"></div>



<div class="admin-main">

    
    <header class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>

            <div>
                <h5 class="page-title mb-0"><?php echo $__env->yieldContent('title', 'Admin Dashboard'); ?></h5>
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('admin.dashboard')); ?>">
                                <i class="bi bi-house-door"></i> Admin
                            </a>
                        </li>
                        <?php echo $__env->yieldContent('breadcrumb'); ?>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            
            <div class="search-wrap d-none d-lg-block">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search anything...">
            </div>

            
            <div class="dropdown">
                <button class="icon-btn" data-bs-toggle="dropdown" aria-label="Notifications">
                    <i class="bi bi-bell"></i>
                    <span class="notif-dot"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 320px;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Notifications</span>
                        <span class="badge bg-warning text-dark" style="font-size:.6rem;">3 new</span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex gap-2 py-2" href="#">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:34px;height:34px;background:#dbeafe;color:#2563eb;flex-shrink:0;">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <div>
                                <div class="small fw-semibold">New seller registered</div>
                                <div class="text-muted" style="font-size:.7rem;">2 minutes ago</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex gap-2 py-2" href="#">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:34px;height:34px;background:#d1fae5;color:#059669;flex-shrink:0;">
                                <i class="bi bi-cart-check-fill"></i>
                            </div>
                            <div>
                                <div class="small fw-semibold">New order placed</div>
                                <div class="text-muted" style="font-size:.7rem;">15 minutes ago</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex gap-2 py-2" href="#">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:34px;height:34px;background:#fef3c7;color:#d97706;flex-shrink:0;">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div>
                                <div class="small fw-semibold">Low stock warning</div>
                                <div class="text-muted" style="font-size:.7rem;">1 hour ago</div>
                            </div>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-center small fw-semibold" href="#" style="color:var(--admin-accent);">
                            View all notifications
                        </a>
                    </li>
                </ul>
            </div>

            
            <?php if(auth()->guard()->check()): ?>
                <div class="dropdown">
                    <button class="user-chip border-0 bg-white" data-bs-toggle="dropdown">
                        <div class="avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold" style="font-size:.82rem; line-height:1.1; color:var(--admin-text);">
                                <?php echo e(auth()->user()->name); ?>

                            </div>
                            <div style="font-size:.66rem; color:var(--admin-text-muted); font-weight:500;">
                                <?php echo e($isSuperAdmin ? 'Super Admin' : 'Admin'); ?>

                            </div>
                        </div>
                        <i class="bi bi-chevron-down text-muted small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i> Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </header>

    
    <div class="px-4 pt-3">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-0">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-0">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(session('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show mb-0">
                <i class="bi bi-exclamation-circle-fill me-2"></i> <?php echo e(session('warning')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(session('info')): ?>
            <div class="alert alert-info alert-dismissible fade show mb-0">
                <i class="bi bi-info-circle-fill me-2"></i> <?php echo e(session('info')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-0">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Please fix:</strong>
                <ul class="mb-0 mt-1 small">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    
    <main class="admin-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <footer class="border-top bg-white py-3 px-4 small d-flex justify-content-between flex-wrap gap-2" style="color:var(--admin-text-muted);">
        <div>
            &copy; <?php echo e(date('Y')); ?> <strong style="color:var(--admin-text);"><?php echo e(config('app.name', 'TolBazar')); ?></strong>. All rights reserved.
        </div>
        <div class="d-flex gap-3">
            <a href="#" class="text-decoration-none" style="color:var(--admin-text-muted);">Privacy</a>
            <a href="#" class="text-decoration-none" style="color:var(--admin-text-muted);">Terms</a>
            <a href="#" class="text-decoration-none" style="color:var(--admin-text-muted);">Support</a>
        </div>
    </footer>
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const sidebar   = document.getElementById('adminSidebar');
    const backdrop  = document.getElementById('sidebarBackdrop');
    const toggleBtn = document.getElementById('sidebarToggle');

    function openSidebar() {
        sidebar.classList.add('show');
        backdrop.classList.add('show');
    }
    function closeSidebar() {
        sidebar.classList.remove('show');
        backdrop.classList.remove('show');
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (backdrop)  backdrop.addEventListener('click', closeSidebar);

    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach(el => {
            const alert = bootstrap.Alert.getOrCreateInstance(el);
            alert.close();
        });
    }, 4500);
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/layouts/admin.blade.php ENDPATH**/ ?>