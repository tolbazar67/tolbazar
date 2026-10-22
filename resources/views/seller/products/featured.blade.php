{{-- resources/views/seller/products/featured.blade.php --}}
@extends('layouts.seller')

@section('title', 'Featured Items')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-star-fill me-2 text-warning"></i> Featured Items
            </h4>
            <p class="text-muted mb-0 small">
                Branch: <strong>{{ $branch->name }}</strong> •
                Toggle which items are featured on your storefront
            </p>
        </div>
        <a href="{{ route('seller.products.index', ['branch_id' => $branch->id]) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Items
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Item</th>
                            <th>Service</th>
                            <th>Price</th>
                            <th>Created By</th>
                            <th class="text-end">Featured</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $p)
                            @php
                                $key = $p->service?->slug ?? $p->service?->name;
                                $meta = $serviceCatalog[$key] ?? ['icon' => 'bi-tag', 'label' => '—'];
                            @endphp
                            <tr>
                                <td class="text-muted small">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($p->image_url)
                                            <img src="{{ $p->image_url }}"
                                                 class="rounded"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold small">{{ $p->name }}</div>
                                            <div class="text-muted small">SKU: {{ $p->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi {{ $meta['icon'] }} me-1"></i>
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="fw-semibold">Rs. {{ number_format($p->price, 2) }}</td>
                                <td class="small">
                                    {{ $p->created_by_label }}
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('seller.products.toggle-featured', $p) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm {{ $p->is_featured ? 'btn-warning' : 'btn-outline-secondary' }}"
                                                title="Toggle featured">
                                            <i class="bi {{ $p->is_featured ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            {{ $p->is_featured ? 'Featured' : 'Not Featured' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-star fs-1 d-block mb-2"></i>
                                    No items yet. Create some first.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection