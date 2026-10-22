@extends('layouts.seller')

@section('title', $product->name)

@section('content')
<div class="container py-4">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('seller.products.index', ['branch_id' => $product->branch_id]) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h3 class="fw-bold mb-0">{{ $product->name }}</h3>
    </div>

    <div class="row g-4">
        <div class="col-md-5">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" class="img-fluid rounded border">
            @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                     style="height:300px;">
                    <i class="bi bi-image text-muted" style="font-size:4rem;"></i>
                </div>
            @endif
        </div>

        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge"
                              style="background:{{ $product->service->color }}20; color:{{ $product->service->color }};">
                            <i class="{{ $product->service->icon }}"></i>
                            {{ $product->service->name }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-primary">
                        Rs. {{ number_format($product->price, 2) }}
                        @if($product->compare_price)
                            <small class="text-muted text-decoration-line-through ms-2"
                                   style="font-size:1rem;">
                                Rs. {{ number_format($product->compare_price, 2) }}
                            </small>
                            <span class="badge bg-danger ms-2">
                                -{{ $product->discount_percent }}%
                            </span>
                        @endif
                    </h4>

                    @if($product->short_description)
                        <p class="text-muted">{{ $product->short_description }}</p>
                    @endif

                    <hr>

                    <table class="table table-sm">
                        <tr><td>SKU</td><td><code>{{ $product->sku }}</code></td></tr>
                        <tr><td>Stock</td><td>{{ $product->stock_quantity }} {{ $product->unit }}</td></tr>
                        <tr><td>Status</td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr><td>Branch</td><td>{{ $product->branch->branch_name }}</td></tr>
                    </table>

                    @if($product->description)
                        <hr>
                        <h6 class="fw-bold">Description</h6>
                        <p>{{ $product->description }}</p>
                    @endif

                    <a href="{{ route('seller.products.edit', $product) }}"
                       class="btn btn-primary mt-3">
                        <i class="bi bi-pencil"></i> Edit Product
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection