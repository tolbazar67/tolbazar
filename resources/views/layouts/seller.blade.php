<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Seller Dashboard') — {{ config('app.name', 'TolBazar') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #6366f1;
            --brand-dark:    #1e1b4b;
            --brand-light:   #f8f9fc;
            --sidebar-w:     260px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: var(--brand-light);
            margin: 0;
            min-height: 100vh;
        }

        /* ============ SIDEBAR ============ */
        .seller-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
            color: #e0e7ff;
            display: flex;
            flex-direction: column;
            z-index: 1030;
            transition: transform .25s ease;
            overflow-y: auto;
        }

        .seller-sidebar .brand {
            padding: 20px 22px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 10px;
            font-weight: 700; font-size: 1.15rem; color: #fff;
        }

        .seller-sidebar .brand i { color: #a5b4fc; font-size: 1.4rem; }

        .seller-sidebar .store-box {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .seller-sidebar .store-box .store-name {
            font-weight: 600; color: #fff; font-size: .95rem;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .seller-sidebar .store-box .store-mode {
            font-size: .72rem; text-transform: uppercase;
            color: #a5b4fc; letter-spacing: .5px;
        }

        .seller-sidebar .nav-label {
            font-size: .7rem; text-transform: uppercase;
            letter-spacing: 1px; color: #818cf8;
            padding: 18px 22px 6px; font-weight: 700;
        }

        .seller-sidebar .nav-link {
            color: #c7d2fe;
            padding: 11px 22px;
            display: flex; align-items: center; gap: 12px;
            font-size: .9rem; font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .15s ease;
        }
        .seller-sidebar .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: #fff;
        }
        .seller-sidebar .nav-link.active {
            background: rgba(165,180,252,0.15);
            color: #fff;
            border-left-color: #a5b4fc;
        }
        .seller-sidebar .nav-link i { width: 20px; text-align: center; font-size: 1.05rem; }

        .seller-sidebar .badge-branch {
            background: rgba(165,180,252,0.25);
            color: #e0e7ff;
            font-size: .68rem;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: auto;
        }

        .seller-sidebar .sidebar-footer {
            margin-top: auto;
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
            font-size: .75rem;
            color: #818cf8;
        }

        /* ============ MAIN CONTENT ============ */
        .seller-main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        .seller-topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 14px 28px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 1020;
        }

        .seller-topbar .page-title {
            font-weight: 700; font-size: 1.1rem; color: #111827;
            margin: 0;
        }

        .seller-topbar .user-chip {
            display: flex; align-items: center; gap: 10px;
            padding: 6px 12px; border-radius: 30px;
            background: var(--brand-light);
            font-size: .85rem;
        }
        .seller-topbar .user-chip .avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--brand-primary); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .75rem;
        }

        .seller-content {
            padding: 28px;
            flex: 1;
        }

        /* ============ MOBILE ============ */
        .sidebar-toggle {
            display: none;
            background: none; border: none;
            font-size: 1.4rem; color: #111827;
        }

        @media (max-width: 991.98px) {
            .seller-sidebar { transform: translateX(-100%); }
            .seller-sidebar.show { transform: translateX(0); }
            .seller-main { margin-left: 0; }
            .sidebar-toggle { display: inline-block; }
            .seller-content { padding: 18px; }
        }

        .sidebar-backdrop {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 1029;
        }
        .sidebar-backdrop.show { display: block; }

        /* ============ UTILITIES ============ */
        .card { border-radius: 14px; }
        .btn { border-radius: 9px; font-weight: 500; }
        .btn-primary { background: var(--brand-primary); border-color: var(--brand-primary); }
        .btn-primary:hover { background: #4f46e5; border-color: #4f46e5; }
        .form-control, .form-select { border-radius: 9px; }
        .badge { font-weight: 500; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ==================== SIDEBAR ==================== --}}
<aside class="seller-sidebar" id="sellerSidebar">

    {{-- Brand --}}
    <div class="brand">
        <i class="bi bi-shop"></i>
        <span>{{ config('app.name', 'TolBazar') }}</span>
    </div>

    {{-- Store Info --}}
    @auth
        @php
            $sellerProfile = auth()->user()->sellerProfile;
            $activeBranchId = session('active_branch_id');
            $activeBranchRole = session('active_branch_role', 'owner');
            $activeBranch = $sellerProfile && $activeBranchId
                ? $sellerProfile->branches()->where('id', $activeBranchId)->first()
                : null;
        @endphp

        <div class="store-box">
            <div class="store-name">{{ $sellerProfile->store_name ?? 'My Store' }}</div>
            <div class="store-mode">
                <i class="bi bi-person-badge"></i>
                {{ ucfirst($activeBranchRole) }}
                @if($activeBranch)
                    · {{ $activeBranch->branch_name }}
                @endif
            </div>
        </div>
    @endauth

    {{-- ============ NAVIGATION ============ --}}
    <nav>
        <div class="nav-label">Main</div>

        <a href="{{ route('seller.dashboard') }}"
           class="nav-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-label">Store Management</div>

        <a href="{{ route('seller.branches.index') }}"
           class="nav-link {{ request()->routeIs('seller.branches.*') ? 'active' : '' }}">
            <i class="bi bi-shop-window"></i> Branches
            @if($sellerProfile)
                <span class="badge-branch">{{ $sellerProfile->branches()->count() }}</span>
            @endif
        </a>

        <a href="{{ route('seller.staff.index') }}"
           class="nav-link {{ request()->routeIs('seller.staff.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Staff
        </a>

        <div class="nav-label">Community</div>

        <a href="{{ route('seller.social.feed') }}"
           class="nav-link {{ request()->routeIs('seller.social.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Feed
        </a>

        <a href="{{ route('seller.posts.create') }}"
           class="nav-link {{ request()->routeIs('seller.posts.create') ? 'active' : '' }}">
            <i class="bi bi-plus-square"></i> Create Post
        </a>

        <a href="{{ route('seller.posts.my') }}"
           class="nav-link {{ request()->routeIs('seller.posts.my') ? 'active' : '' }}">
            <i class="bi bi-collection"></i> My Posts
        </a>

        <a href="{{ route('seller.posts.saved') }}"
           class="nav-link {{ request()->routeIs('seller.posts.saved') ? 'active' : '' }}">
            <i class="bi bi-bookmark"></i> Saved Posts
        </a>

        <div class="nav-label">Account</div>

        <a href="{{ route('home') }}" class="nav-link">
            <i class="bi bi-house"></i> Customer Home
        </a>
    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <div class="d-flex justify-content-between align-items-center">
            <span>v1.0 · TolBazar</span>
            <i class="bi bi-shield-check"></i>
        </div>
    </div>
</aside>

{{-- Mobile backdrop --}}
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>


{{-- ==================== MAIN ==================== --}}
<div class="seller-main">

    {{-- Topbar --}}
    <header class="seller-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="page-title">@yield('title', 'Seller Dashboard')</h5>
        </div>

        <div class="d-flex align-items-center gap-3">

            {{-- Branch Switcher (Owner only) --}}
            @auth
                @php $isOwner = auth()->user()->isSellerOwner(); @endphp
                @if($isOwner && $sellerProfile && $sellerProfile->branches->count() > 1)
                    <form method="POST" action="{{ route('switch.mode', 'seller') }}" class="d-none d-md-block">
                        @csrf
                        <select name="branch_id"
                                class="form-select form-select-sm"
                                onchange="this.form.submit()"
                                style="min-width: 180px;">
                            @foreach($sellerProfile->branches()->where('status',1)->get() as $b)
                                <option value="{{ $b->id }}"
                                    {{ session('active_branch_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->branch_name }}
                                    {{ $b->is_default ? '(HQ)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
            @endauth

            {{-- User chip --}}
            @auth
                <div class="user-chip">
                    <div class="avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="d-none d-md-block">
                        <div style="font-weight:600;font-size:.82rem;line-height:1.1;">
                            {{ auth()->user()->name }}
                        </div>
                        <div style="font-size:.72rem;color:#6b7280;">
                            {{ auth()->user()->email }}
                        </div>
                    </div>
                </div>
            @endauth

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </header>

    {{-- Flash Messages --}}
    <div class="px-4 pt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Please fix the following:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- Page Content --}}
    <main class="seller-content">
        @yield('content')
    </main>
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar toggle (mobile)
    const sidebar   = document.getElementById('sellerSidebar');
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

    if (toggleBtn)   toggleBtn.addEventListener('click', openSidebar);
    if (backdrop)    backdrop.addEventListener('click', closeSidebar);

    // Auto-dismiss flash messages after 4s
    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach(el => {
            const alert = bootstrap.Alert.getOrCreateInstance(el);
            alert.close();
        });
    }, 4000);
</script>

@stack('scripts')
</body>
</html>