@extends('layouts.seller')

@section('title', 'Featured Products')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div><h1 class="h3 mb-1">Featured Listings</h1><p class="text-muted mb-0">Toggle featured placement for listings in this branch.</p></div>
        <a href="{{ route('seller.products.index', ['branch_id' => $branch->id]) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to products</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0">
        <thead class="thead-light"><tr><th>Listing</th><th>Service</th><th>Price</th><th>Status</th><th>Featured</th><th>Creator</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
        @forelse($products as $product)
            <tr>
                <td><div class="d-flex align-items-center">
                    @if($product->thumbnail)<img src="{{ asset('storage/'.$product->thumbnail) }}" class="rounded border mr-3" style="width:48px;height:48px;object-fit:cover" alt="">@endif
                    <div><a class="font-weight-bold text-dark" href="{{ route('seller.products.show',$product) }}">{{ $product->name }}</a><div class="small text-muted">{{ $product->sku ?: 'No SKU' }}</div></div>
                </div></td>
                <td>{{ $product->service->name ?? $product->service->label ?? $product->service->code ?? '—' }}</td>
                <td>Rs. {{ number_format((float)$product->price,2) }}</td>
                <td><span class="badge badge-{{ $product->is_active ? 'success' : 'secondary' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td><form method="POST" action="{{ route('seller.products.toggle-featured',$product) }}">@csrf @method('PATCH')
                    <button class="btn btn-sm {{ $product->is_featured ? 'btn-warning' : 'btn-outline-secondary' }}" type="submit"><i class="fas fa-star mr-1"></i>{{ $product->is_featured ? 'Featured' : 'Not featured' }}</button>
                </form></td>
                <td>{{ $product->created_by_label ?? ($product->creator->name ?? '—') }}</td>
                <td class="text-right"><a class="btn btn-sm btn-outline-info" href="{{ route('seller.products.show',$product) }}"><i class="fas fa-eye"></i></a> <a class="btn btn-sm btn-outline-primary" href="{{ route('seller.products.edit',$product) }}"><i class="fas fa-edit"></i></a></td>
            </tr>
        @empty<tr><td colspan="7" class="text-center text-muted py-5">No listings available for this branch.</td></tr>@endforelse
        </tbody>
    </table></div>
    @if($products->hasPages())<div class="card-footer bg-white">{{ $products->links() }}</div>@endif
    </div>
</div>
@endsection
