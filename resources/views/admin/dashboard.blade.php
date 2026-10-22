@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')

{{-- ==================== WELCOME BANNER ==================== --}}
<div class="card border-0 mb-4 overflow-hidden position-relative"
     style="background: linear-gradient(135deg, #0b1120 0%, #1a2332 60%, #2d1b0e 100%);">
    <div class="card-body p-4 p-md-5 position-relative" style="z-index:2;">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge mb-3"
                      style="background: rgba(245,158,11,0.15); color: #fbbf24; font-size:.72rem; letter-spacing:.5px; padding:6px 12px;">
                    <i class="bi bi-shield-check me-1"></i>
                    {{ auth()->user()->hasRole('Super Admin') ? 'SUPER ADMIN' : 'ADMIN' }}
                </span>
                <h2 class="text-white fw-bold mb-2" style="letter-spacing:-0.02em;">
                    Welcome back, {{ explode(' ', auth()->user()->name)[0] }} 👋
                </h2>
                <p class="mb-0" style="color:#94a3b8; max-width:520px;">
                    Manage roles, review pending seller requests, and monitor system activity from one central control center.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('admin.services.index') }}"
                   class="btn btn-warning fw-semibold px-4 py-2">
                    <i class="bi bi-grid-3x3-gap-fill me-2"></i> Manage Services
                </a>
            </div>
        </div>
    </div>
    {{-- Decorative circle --}}
    <div style="position:absolute; width:280px; height:280px; border-radius:50%; background:radial-gradient(circle, rgba(245,158,11,0.18), transparent 70%); top:-100px; right:-60px; z-index:1;"></div>
</div>

{{-- ==================== STAT CARDS ==================== --}}
<div class="row g-3 mb-4">

    @php
        $stats = [
            [
                'label' => 'Total Services',
                'value' => \App\Models\Service::count(),
                'icon'  => 'bi-grid-3x3-gap-fill',
                'color' => 'primary',
                'route' => route('admin.services.index'),
            ],
            [
                'label' => 'Active Services',
                'value' => \App\Models\Service::where('is_active', 1)->count(),
                'icon'  => 'bi-check-circle-fill',
                'color' => 'success',
                'route' => route('admin.services.index'),
            ],
            [
                'label' => 'Service Categories',
                'value' => \App\Models\ServiceCategory::count(),
                'icon'  => 'bi-diagram-3-fill',
                'color' => 'info',
                'route' => route('admin.service-categories.index'),
            ],
            [
                'label' => 'Tax Rules',
                'value' => \App\Models\ServiceTaxRule::count(),
                'icon'  => 'bi-percent',
                'color' => 'warning',
                'route' => route('admin.service-tax-rules.index'),
            ],
        ];
    @endphp

    @foreach($stats as $stat)
        <div class="col-sm-6 col-xl-3">
            <a href="{{ $stat['route'] }}" class="text-decoration-none">
                <div class="card border-0 h-100 stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center"
                                 style="width:44px; height:44px; background: rgba(245,158,11,0.1); color:#d97706;">
                                <i class="bi {{ $stat['icon'] }} fs-5"></i>
                            </div>
                            <i class="bi bi-arrow-up-right text-muted small"></i>
                        </div>
                        <div class="text-muted small mb-1">{{ $stat['label'] }}</div>
                        <div class="fw-bold" style="font-size:1.6rem; color:var(--admin-text); letter-spacing:-0.02em;">
                            {{ number_format($stat['value']) }}
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

{{-- ==================== QUICK ACTIONS + RECENT ==================== --}}
<div class="row g-3 mb-4">

    {{-- Quick Actions --}}
    <div class="col-lg-5">
        <div class="card border-0 h-100">
            <div class="card-header bg-white border-0 pt-3 pb-2 px-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body pt-2">
                <div class="row g-2">
                    @php
                        $actions = [
                            ['label'=>'New Service',      'icon'=>'bi-plus-circle-fill',  'route'=>route('admin.services.create'),                'color'=>'#3b82f6'],
                            ['label'=>'New Category',     'icon'=>'bi-folder-plus',       'route'=>route('admin.service-categories.create'),     'color'=>'#10b981'],
                            ['label'=>'Tax Rule',         'icon'=>'bi-percent',           'route'=>route('admin.service-tax-rules.create'),      'color'=>'#f59e0b'],
                            ['label'=>'Commission Rule',  'icon'=>'bi-cash-coin',         'route'=>route('admin.partner-commission-rules.create'),'color'=>'#8b5cf6'],
                            ['label'=>'Charge Rule',      'icon'=>'bi-receipt',           'route'=>route('admin.customer-charge-rules.create'),  'color'=>'#ef4444'],
                            ['label'=>'All Services',     'icon'=>'bi-list-ul',           'route'=>route('admin.services.index'),                'color'=>'#64748b'],
                        ];
                    @endphp

                    @foreach($actions as $action)
                        <div class="col-6">
                            <a href="{{ $action['route'] }}" class="text-decoration-none">
                                <div class="d-flex align-items-center gap-2 p-2 rounded-3 action-tile">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:34px; height:34px; background: {{ $action['color'] }}15; color: {{ $action['color'] }};">
                                        <i class="bi {{ $action['icon'] }}"></i>
                                    </div>
                                    <div class="small fw-semibold text-truncate" style="color:var(--admin-text);">
                                        {{ $action['label'] }}
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Services --}}
    <div class="col-lg-7">
        <div class="card border-0 h-100">
            <div class="card-header bg-white border-0 pt-3 pb-2 px-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Recent Services</h6>
                <a href="{{ route('admin.services.index') }}" class="small text-decoration-none fw-semibold" style="color:var(--admin-accent);">
                    View all <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @php
                    $recentServices = \App\Models\Service::latest()->limit(5)->get();
                @endphp

                @if($recentServices->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                        <div class="small">No services yet</div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th class="text-end">Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentServices as $service)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.services.show', $service) }}"
                                               class="text-decoration-none fw-semibold" style="color:var(--admin-text);">
                                                {{ Str::limit($service->name ?? 'Service #'.$service->id, 40) }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($service->is_active ?? false)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-muted small">
                                            {{ $service->created_at?->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ==================== SYSTEM INFO ==================== --}}
<div class="row g-3">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width:40px; height:40px; background: rgba(16,185,129,0.1); color:#10b981;">
                        <i class="bi bi-shield-fill-check fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-semibold" style="color:var(--admin-text);">System running smoothly</div>
                        <div class="text-muted small">All core services are operational</div>
                    </div>
                </div>
                <div class="d-flex gap-4">
                    <div class="text-center">
                        <div class="fw-bold" style="color:var(--admin-text);">{{ \App\Models\User::count() }}</div>
                        <div class="text-muted small">Users</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold" style="color:var(--admin-text);">{{ \App\Models\Seller::count() }}</div>
                        <div class="text-muted small">Sellers</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold" style="color:var(--admin-text);">{{ \App\Models\Role::count() }}</div>
                        <div class="text-muted small">Roles</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .stat-card {
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(15,23,42,0.08) !important;
    }
    .action-tile {
        transition: background .15s ease;
        border: 1px solid transparent;
    }
    .action-tile:hover {
        background: #f8fafc;
        border-color: var(--admin-border);
    }
    .card { box-shadow: 0 1px 2px rgba(15,23,42,0.04); }
</style>
@endpush