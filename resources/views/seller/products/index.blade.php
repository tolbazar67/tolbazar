@extends('layouts.seller')

@section('title', 'My Products')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">📦 Products</h3>
            <p class="text-muted mb-0 small">
                Manage products for <strong>{{ $branch->branch_name }}</strong>
            </p>
        </div>
        @if(!$noServicesFlag)
            <a href="{{ route('seller.products.create', ['branch_id' => $branch->id]) }}"
               class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Product
            </a>
        @endif
    </div>

    {{-- NO SERVICES WARNING --}}
    @if($noServicesFlag)
        <div class="alert alert-warning d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
            <div>
                <strong>No services enabled on this branch.</strong>
                <p class="mb-0 small">Please enable at least one service first.</p>
            </div>
            <a href="{{ route('seller.services.index', ['branch_id' => $branch->id]) }}"
               class="btn btn-warning btn-sm ms-auto text-white">
                <i class="bi bi-grid-3x3-gap-fill"></i> Enable Services
            </a>
        </div>
    @endif

    {{-- FLASH --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- BRANCH SWITCHER (owner with multiple branches) --}}
    @if($branches->count() > 1)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small fw-semibold">Branch:</span>
                    @foreach($branches as $b)
                        <a href="{{ route('seller.products.index', ['branch_id' => $b->id]) }}"
                           class="btn btn-sm {{ $b->id == $branch->id ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ $b->branch_name }}
                            @if($b->is_default)
                                <span class="badge bg-warning text-dark" style="font-size:.6rem;">HQ</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if(!$noServicesFlag)
        {{-- STATS --}}
        <div class="row g-3 mb-3">
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Total</div>
                        <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Active</div>
                        <h3 class="fw-bold mb-0 text-success">{{ $stats['active'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Inactive</div>
                        <h3 class="fw-bold mb-0 text-secondary">{{ $stats['inactive'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Out of Stock</div>
                        <h3 class="fw-bold mb-0 text-danger">{{ $stats['out_of_stock'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTERS --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">

                    <div class="col-md-4">
                        <input type="text" name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search by name, SKU...">
                    </div>
                    <div class="col-md-3">
                        <select name="service_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Services</option>
                            @foreach($services as $s)
                                <option value="{{ $s->id }}"
                                    {{ request('service_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active"       {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive"     {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-outline-primary flex-fill">Filter</button>
                        @if(request()->hasAny(['search', 'service_id', 'status']))
                            <a href="{{ route('seller.products.index', ['branch_id' => $branch->id]) }}"
                               class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card border-0 shadow-sm">
            
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Created By</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
                
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($p->image_url)
                                <img src="{{ $p->image_url }}"
                                     class="rounded"
                                     style="width: 42px; height: 42px; object-fit: cover;">
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $p->name }}</div>
                                <div class="text-muted small">{{ $p->sku }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <td class="fw-semibold">Rs. {{ number_format($p->price, 2) }}</td>
                    <td>
                        @if($p->stock_quantity > 0)
                            <span class="badge bg-success-subtle text-success">
                                {{ $p->stock_quantity }} in stock
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">Out of stock</span>
                        @endif
                    </td>
                    <td>
                        {{-- 👇 CREATED BY COLUMN --}}
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                 style="width: 28px; height: 28px; font-size: 11px;">
                                {{ strtoupper(substr($p->created_by_name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="small">
                                <div class="fw-semibold">{{ $p->created_by_name ?? '—' }}</div>
                                <div class="text-muted text-capitalize">
                                    {{ $p->created_by_role ?? '' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($p->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                        @if($p->is_featured)
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-star-fill"></i> Featured
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('seller.products.show', $p) }}"
                               class="btn btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('seller.products.edit', $p) }}"
                               class="btn btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('seller.products.toggle-active', $p) }}"
                                  method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-outline-{{ $p->is_active ? 'warning' : 'success' }}">
                                    <i class="bi {{ $p->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('seller.products.destroy', $p) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this item?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        No items yet.
                        <a href="{{ route('seller.products.create', ['branch_id' => $branch->id]) }}">
                            Create your first item
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

            @if($products->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection