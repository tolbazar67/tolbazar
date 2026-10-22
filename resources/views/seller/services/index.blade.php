@extends('layouts.seller')

@section('title', 'My Services')

@section('content')
<div class="container-fluid py-4">

    {{-- ============================================================ --}}
    {{-- HEADER --}}
    {{-- ============================================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-grid-3x3-gap-fill text-primary me-1"></i>
                Store Services
            </h3>
            <p class="text-muted mb-0 small">
                Choose which services your branch offers to customers
            </p>
        </div>

        @if($selectedBranch && $stats['enabled'] > 0)
            <a href="{{ route('seller.products.index', ['branch_id' => $selectedBranch->id]) }}"
               class="btn btn-primary">
                <i class="bi bi-box-seam"></i> Manage Products
            </a>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- FLASH --}}
    {{-- ============================================================ --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>{{ session('success') }}</div>
            <button class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>{{ session('error') }}</div>
            <button class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-2"></i>
            <div>{{ session('info') }}</div>
            <button class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- ============================================================ --}}
    {{-- BRANCH SWITCHER (Owner only) --}}
    {{-- ============================================================ --}}
    @if($branches->count() > 1)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small fw-semibold me-2">
                        <i class="bi bi-building"></i> Branch:
                    </span>
                    @foreach($branches as $b)
                        <a href="{{ route('seller.services.index', ['branch_id' => $b->id]) }}"
                           class="btn btn-sm {{ $b->id == $selectedBranch->id ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ $b->branch_name }}
                            @if($b->is_default)
                                <span class="badge bg-warning text-dark ms-1"
                                      style="font-size:.6rem;">HQ</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif


    {{-- ============================================================ --}}
    {{-- CURRENT BRANCH HEADER --}}
    {{-- ============================================================ --}}
    @if($selectedBranch)
        <div class="card border-0 shadow-sm mb-4"
             style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
            <div class="card-body p-4 text-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill small">
                                <i class="bi bi-geo-alt-fill me-1"></i> {{ $selectedBranch->branch_code }}
                            </span>
                            @if($selectedBranch->is_default)
                                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small">
                                    <i class="bi bi-star-fill me-1"></i> Headquarters
                                </span>
                            @endif
                        </div>
                        <h4 class="fw-bold mb-1">{{ $selectedBranch->branch_name }}</h4>
                        <p class="mb-0 opacity-75 small">
                            <i class="bi bi-telephone me-1"></i> {{ $selectedBranch->phone }}
                            @if($selectedBranch->email)
                                · <i class="bi bi-envelope ms-2 me-1"></i> {{ $selectedBranch->email }}
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                            <div class="bg-white bg-opacity-10 rounded-3 px-3 py-2 text-center">
                                <div class="fs-4 fw-bold">{{ $stats['enabled'] }}</div>
                                <div class="small opacity-75">Enabled</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-3 px-3 py-2 text-center">
                                <div class="fs-4 fw-bold">{{ $stats['products'] }}</div>
                                <div class="small opacity-75">Products</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- ============================================================ --}}
    {{-- STATS --}}
    {{-- ============================================================ --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#ede9fe;color:#6366f1;">
                        <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Available</div>
                        <h4 class="fw-bold mb-0">{{ $stats['available'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#dcfce7;color:#16a34a;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Enabled</div>
                        <h4 class="fw-bold mb-0 text-success">{{ $stats['enabled'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f1f5f9;color:#64748b;">
                        <i class="bi bi-x-circle fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Disabled</div>
                        <h4 class="fw-bold mb-0 text-secondary">{{ $stats['disabled'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#dbeafe;color:#2563eb;">
                        <i class="bi bi-box-seam fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Products</div>
                        <h4 class="fw-bold mb-0 text-primary">{{ $stats['products'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- ============================================================ --}}
    {{-- SERVICES GRID --}}
    {{-- ============================================================ --}}
    @if($services->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-inbox text-muted" style="font-size:3rem;"></i>
                </div>
                <h5 class="text-muted">No services available</h5>
                <p class="text-muted small">
                    The administrator has not created any services yet.<br>
                    Please check back later.
                </p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($services as $service)
                @php
                    $isEnabled    = in_array($service->id, $enabledServiceIds);
                    $productCount = $selectedBranch
                        ? $selectedBranch->products()->where('service_id', $service->id)->count()
                        : 0;
                @endphp

                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 service-card
                                {{ $isEnabled ? 'border-success border-2' : '' }}"
                         style="position:relative;{{ $isEnabled ? 'background: linear-gradient(135deg, rgba(16,185,129,0.04) 0%, rgba(16,185,129,0.10) 100%);' : '' }}">

                        {{-- Enabled ribbon --}}
                        @if($isEnabled)
                            <div style="position:absolute;top:12px;right:12px;z-index:1;">
                                <span class="badge bg-success" style="font-size:.7rem;">
                                    <i class="bi bi-check-circle-fill"></i> Active
                                </span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">

                            {{-- HEAD --}}
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:56px;height:56px;
                                            background:{{ $service->color }}20;
                                            color:{{ $service->color }};">
                                    <i class="{{ $service->icon ?? 'bi bi-grid' }} fs-4"></i>
                                </div>

                                <div class="flex-grow-1 min-w-0 pe-4">
                                    <h6 class="fw-bold mb-1">{{ $service->name }}</h6>
                                    <div class="text-muted small">
                                        <code>{{ $service->slug }}</code>
                                    </div>
                                </div>
                            </div>

                            {{-- DESCRIPTION --}}
                            <p class="text-muted small mb-3" style="min-height:42px;">
                                {{ $service->description
                                        ? Str::limit($service->description, 100)
                                        : 'No description available.' }}
                            </p>

                            {{-- META --}}
                            @if($isEnabled)
                                <div class="d-flex gap-3 small text-muted mb-3 pb-2 border-top pt-2">
                                    <div>
                                        <i class="bi bi-box-seam"></i>
                                        <strong>{{ $productCount }}</strong> product(s)
                                    </div>
                                    <div>
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        Enabled
                                    </div>
                                </div>
                            @else
                                <div class="d-flex gap-3 small text-muted mb-3 pb-2 border-top pt-2">
                                    <div>
                                        <i class="bi bi-circle"></i>
                                        Not enabled
                                    </div>
                                </div>
                            @endif

                            {{-- ACTION --}}
                            <div class="mt-auto">
                                <form action="{{ $isEnabled
                                        ? route('seller.services.disable', $service)
                                        : route('seller.services.enable', $service) }}"
                                      method="POST">
                                    @csrf
                                    <input type="hidden" name="branch_id"
                                           value="{{ $selectedBranch->id }}">

                                    @if($isEnabled)
                                        <button type="submit"
                                                class="btn btn-outline-danger btn-sm w-100"
                                                onclick="return confirmDisable('{{ addslashes($service->name) }}', {{ $productCount }})">
                                            <i class="bi bi-x-circle me-1"></i> Disable Service
                                        </button>
                                    @else
                                        <button type="submit"
                                                class="btn btn-success btn-sm w-100">
                                            <i class="bi bi-check-circle me-1"></i> Enable Service
                                        </button>
                                    @endif
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif


    {{-- ============================================================ --}}
    {{-- HELP CARD --}}
    {{-- ============================================================ --}}
    @if(!$services->isEmpty())
        <div class="card border-0 shadow-sm mt-4"
             style="background: linear-gradient(135deg, #f8f9fc 0%, #eef1ff 100%);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-1">
                            <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                            How does this work?
                        </h6>
                        <p class="text-muted small mb-0">
                            Enable services that your branch offers. Once enabled, you can
                            create products under that service. Customers will see only
                            the services you've enabled.
                        </p>
                    </div>
                    @if($stats['enabled'] > 0)
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('seller.products.create', ['branch_id' => $selectedBranch->id]) }}"
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Create First Product
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
@endsection


@push('styles')
<style>
    .service-card {
        transition: all .2s ease;
    }
    .service-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.08) !important;
    }
</style>
@endpush


@push('scripts')
<script>
    function confirmDisable(serviceName, productCount) {
        if (productCount > 0) {
            return confirm(
                '⚠️ "' + serviceName + '" has ' + productCount + ' product(s).\n\n' +
                'You must delete or deactivate these products before disabling this service.\n\n' +
                'Continue anyway? (This action may fail)'
            );
        }
        return confirm('Disable "' + serviceName + '"?\n\nCustomers will no longer see this service.');
    }
</script>
@endpush