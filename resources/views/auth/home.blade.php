<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - TolBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

@php
    $user = auth()->user();

    // ========== ROLE DETECTION ==========
    $hasSellerRole   = $user->hasRole('Seller');
    $isOwner         = $user->isSellerOwner();          // seller + not staff
    $isBranchStaff   = $user->isBranchStaff();          // manager or staff
    $branchStaff     = $isBranchStaff ? $user->branchStaff : null;
    $staffBranch     = $branchStaff?->branch;
    $staffRole       = $branchStaff?->role;             // 'manager' | 'staff'
    $staffRoleLabel  = $staffRole ? ucfirst($staffRole) : null;
    $sellerProfile   = $user->sellerProfile;

    // Owner branches
    $ownerBranches = ($isOwner && $sellerProfile)
        ? $sellerProfile->branches()->where('status', 1)->orderByDesc('is_default')->get()
        : collect();

    $hasDelivery  = $user->hasRole('Delivery Boy');
    $hasCustomer  = $user->hasRole('Customer');
@endphp

{{-- ==================== NAVBAR ==================== --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">TolBazar</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">

                {{-- ==================== MODE SWITCHER ==================== --}}
                <li class="nav-item dropdown me-3">
                    <a class="nav-link dropdown-toggle text-warning fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-user-tag me-1"></i>
                        Mode: {{ ucfirst($user->active_mode) }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow">

                        {{-- ---------- CUSTOMER MODE ---------- --}}
                        @if($hasCustomer)
                            <li>
                                <form action="{{ route('switch.mode', 'customer') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fa-solid fa-house me-2 text-primary"></i> Customer Mode
                                    </button>
                                </form>
                            </li>
                        @endif

                        {{-- ---------- SELLER / BRANCH ENTRY ---------- --}}

                        {{-- CASE 1: Branch Staff (Manager / Staff) — direct dashboard --}}
                        @if($isBranchStaff && $staffBranch)
                            <li><hr class="dropdown-divider"></li>
                            <li class="dropdown-header">
                                <i class="fa-solid fa-building me-1"></i>
                                {{ $staffBranch->branch_name }}
                                <span class="badge bg-info ms-1" style="font-size:.6rem;">{{ $staffRoleLabel }}</span>
                            </li>
                            <li>
                                <form action="{{ route('switch.mode', 'seller') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-primary fw-semibold">
                                        <i class="fa-solid fa-gauge me-2"></i>
                                        My {{ $staffRoleLabel }} Dashboard
                                    </button>
                                </form>
                            </li>

                        {{-- CASE 2: Owner with branches — branch picker --}}
                        @elseif($isOwner && $ownerBranches->count() > 0)
                            <li><hr class="dropdown-divider"></li>
                            <li class="dropdown-header text-warning fw-bold">
                                <i class="fa-solid fa-store me-1"></i> {{ $sellerProfile->store_name }}
                            </li>
                            @foreach($ownerBranches as $branch)
                                <li>
                                    <form action="{{ route('switch.mode', 'seller') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                        <button type="submit" class="dropdown-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fa-solid fa-building me-2"></i>
                                                {{ $branch->branch_name }}
                                            </span>
                                            @if($branch->is_default)
                                                <span class="badge bg-success" style="font-size:9px;">HQ</span>
                                            @endif
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="{{ route('seller.branches.index') }}" class="dropdown-item text-muted">
                                    <i class="fa-solid fa-gear me-2"></i> Manage Branches
                                </a>
                            </li>

                        {{-- CASE 3: Seller role but no profile → Complete Setup --}}
                        @elseif($hasSellerRole && !$sellerProfile)
                            <li>
                                <a class="dropdown-item" href="{{ route('seller.apply') }}">
                                    <i class="fa-solid fa-rocket me-2 text-success"></i> Complete Store Setup
                                </a>
                            </li>

                        {{-- CASE 4: NO SELLER ROLE → Become a Seller --}}
                        @else
                            <li>
                                <a class="dropdown-item text-primary fw-semibold" href="{{ route('seller.apply') }}">
                                    <i class="fa-solid fa-rocket me-2"></i> Become a Seller
                                </a>
                            </li>
                        @endif

                        {{-- ---------- DELIVERY MODE ---------- --}}
                        @if($hasDelivery)
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('switch.mode', 'delivery') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fa-solid fa-motorcycle me-2"></i> Delivery Mode
                                    </button>
                                </form>
                            </li>
                        @endif
                    </ul>
                </li>

                {{-- ==================== PROFILE ==================== --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-circle-user me-1"></i> {{ $user->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><div class="dropdown-item-text small text-muted">{{ $user->email }}</div></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>


{{-- ==================== MAIN ==================== --}}
<div class="container mt-5">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ==================== HERO ==================== --}}
    <div class="p-5 mb-4 bg-white rounded-3 shadow-sm border">
        <div class="container-fluid py-3">
            <h1 class="display-5 fw-bold">Welcome, {{ $user->name ?? 'Guest' }}! 👋</h1>
            <p class="col-md-8 fs-5 text-muted">
                You are browsing as
                <strong class="text-primary">{{ ucfirst($user->active_mode ?? 'customer') }}</strong>.
            </p>

            {{-- ============ ROLE-AWARE CTA ============ --}}

            {{-- Branch Staff → own dashboard --}}
            @if($isBranchStaff && $staffBranch)
                <div class="alert alert-info d-inline-flex align-items-center gap-2 mt-2">
                    <i class="fa-solid fa-building"></i>
                    <span>
                        You work at <strong>{{ $staffBranch->branch_name }}</strong>
                        as <strong>{{ $staffRoleLabel }}</strong>
                    </span>
                </div>
                <br>
                <form action="{{ route('switch.mode', 'seller') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-info btn-lg text-dark fw-semibold mt-3">
                        <i class="fa-solid fa-gauge me-1"></i>
                        Open My {{ $staffRoleLabel }} Dashboard
                    </button>
                </form>

            {{-- Owner → seller dashboard --}}
            @elseif($isOwner && $ownerBranches->count() > 0)
                <form action="{{ route('switch.mode', 'seller') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-dark btn-lg mt-3">
                        <i class="fa-solid fa-gauge me-1"></i> Go to Seller Control Panel
                    </button>
                </form>

            {{-- No seller role → apply --}}
            @elseif(!$hasSellerRole)
                <a href="{{ route('seller.apply') }}" class="btn btn-primary btn-lg mt-3">
                    <i class="fa-solid fa-rocket me-1"></i> Start Selling Today
                </a>
            @endif
        </div>
    </div>


    {{-- ==================== QUICK INFO ==================== --}}
    @if($isBranchStaff && $staffBranch)
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Branch</div>
                        <h5 class="fw-bold mb-0">{{ $staffBranch->branch_name }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Your Role</div>
                        <h5 class="fw-bold mb-0">{{ $staffRoleLabel }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Store</div>
                        <h5 class="fw-bold mb-0">{{ $staffBranch->seller->store_name ?? '—' }}</h5>
                    </div>
                </div>
            </div>
        </div>

    @elseif($isOwner)
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">My Store</div>
                        <h5 class="fw-bold mb-0">{{ $sellerProfile->store_name }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Total Branches</div>
                        <h5 class="fw-bold mb-0">{{ $ownerBranches->count() }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Status</div>
                        <h5 class="fw-bold mb-0 text-success">Active</h5>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>