@extends('layouts.seller')

@section('title', 'Manager Dashboard')

@section('content')

@php
    $user = auth()->user();
    $branchStaff = $user->branchStaff;
    $branch = $branchStaff->branch;
@endphp

{{-- ============================================ --}}
{{-- HERO — Manager branding                    --}}
{{-- ============================================ --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden"
     style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);">
    <div class="card-body p-4 p-md-5 text-white position-relative">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill small">
                        <i class="bi bi-briefcase-fill me-1"></i> Branch Manager
                    </span>
                    @if($branch->is_default)
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small">
                            <i class="bi bi-star-fill me-1"></i> Headquarters
                        </span>
                    @endif
                </div>
                <h2 class="fw-bold mb-2">Hello, {{ $user->name }}! 👋</h2>
                <p class="mb-0 text-white opacity-75">
                    Managing <strong>{{ $branch->branch_name }}</strong>
                    for <strong>{{ $branch->seller->store_name }}</strong>
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('seller.staff.index') }}" class="btn btn-light fw-semibold">
                    <i class="bi bi-people-fill me-1"></i> Manage Team
                </a>
            </div>
        </div>
        <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;
                    background:rgba(255,255,255,0.08);border-radius:50%;pointer-events:none;"></div>
    </div>
</div>


{{-- ============================================ --}}
{{-- QUICK STATS                                 --}}
{{-- ============================================ --}}
<div class="row g-3 mb-4">

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#dbeafe;color:#2563eb;">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Team Size</div>
                    <h3 class="fw-bold mb-0">{{ $branch->staff()->where('status',1)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#fef3c7;color:#d97706;">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Pending Orders</div>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#ede9fe;color:#6366f1;">
                    <i class="bi bi-box-seam fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Products</div>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#dcfce7;color:#16a34a;">
                    <i class="bi bi-currency-rupee fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Branch Earnings</div>
                    <h3 class="fw-bold mb-0">Rs. 0</h3>
                </div>
            </div>
        </div>
    </div>

</div>


{{-- ============================================ --}}
{{-- BRANCH INFO CARD                            --}}
{{-- ============================================ --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-geo-alt-fill text-primary me-1"></i> My Branch Details
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Branch Name</div>
                        <div class="fw-semibold">{{ $branch->branch_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Branch Code</div>
                        <span class="badge bg-secondary">{{ $branch->branch_code }}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Phone</div>
                        <div class="fw-semibold">
                            <i class="bi bi-telephone me-1"></i> {{ $branch->phone }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Email</div>
                        <div class="fw-semibold">
                            <i class="bi bi-envelope me-1"></i> {{ $branch->email ?? '—' }}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-muted small mb-1">Address</div>
                        <div class="fw-semibold">
                            <i class="bi bi-geo-alt me-1"></i> {{ $branch->address }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Actions
                </h6>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('seller.staff.index') }}" class="btn btn-outline-primary text-start">
                    <i class="bi bi-people me-2"></i> Manage Team
                </a>
                <a href="{{ route('seller.branches.edit', $branch->id) }}" class="btn btn-outline-secondary text-start">
                    <i class="bi bi-pencil me-2"></i> Edit Branch Info
                </a>
                <a href="{{ route('seller.social.feed') }}" class="btn btn-outline-success text-start">
                    <i class="bi bi-people-fill me-2"></i> Community Feed
                </a>
            </div>
        </div>
    </div>
</div>


{{-- ============================================ --}}
{{-- TEAM MEMBERS                                --}}
{{-- ============================================ --}}
@php
    $team = \App\Models\BranchStaff::with('user')
        ->where('branch_id', $branch->id)
        ->where('status', 1)
        ->orderByDesc('role')
        ->limit(10)
        ->get();
@endphp

@if($team->count() > 0)
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-people-fill text-primary me-1"></i> My Branch Team
        </h6>
        <a href="{{ route('seller.staff.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Member</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($team as $member)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width:34px;height:34px;background:#e0e7ff;color:#4f46e5;font-weight:600;">
                                        {{ strtoupper(substr($member->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $member->user->name }}</div>
                                        @if($member->user_id === auth()->id())
                                            <span class="badge bg-info" style="font-size:.6rem;">You</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="small">{{ $member->user->email }}</td>
                            <td>
                                <span class="badge {{ $member->role === 'manager' ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ ucfirst($member->role) }}
                                </span>
                            </td>
                            <td class="pe-4">
                                <span class="badge {{ $member->status ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $member->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection