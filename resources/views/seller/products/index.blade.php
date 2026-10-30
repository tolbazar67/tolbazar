@extends('layouts.seller')

@section('title', 'Products')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div><h1 class="h3 mb-1">Products & Services</h1><p class="text-muted mb-0">Manage listings for your branches.</p></div>
        <div class="d-flex flex-wrap">
            <a href="{{ route('seller.products.featured', ['branch_id' => $branch->id]) }}" class="btn btn-outline-warning mr-2 mb-2"><i class="fas fa-star mr-1"></i> Featured</a>
            <a href="{{ route('seller.products.create', ['branch_id' => $branch->id]) }}" class="btn btn-primary mb-2"><i class="fas fa-plus mr-1"></i> Add product</a>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>@endif

    @if(!empty($noServicesFlag))
        <div class="card border-0 shadow-sm"><div class="card-body text-center py-5">
            <i class="fas fa-layer-group fa-3x text-muted mb-3"></i><h4>No services enabled</h4>
            <p class="text-muted">Enable a service for this branch before adding a product or service listing.</p>
            <a class="btn btn-primary" href="{{ route('seller.services.index', ['branch_id' => $branch->id]) }}">Manage branch services</a>
        </div></div>
    @else
        <div class="row mb-4">
            @foreach([
                ['Total listings', $stats['total'] ?? 0, 'fa-boxes', 'primary'],
                ['Active', $stats['active'] ?? 0, 'fa-check-circle', 'success'],
                ['Inactive', $stats['inactive'] ?? 0, 'fa-pause-circle', 'secondary'],
                ['Out of stock', $stats['out_of_stock'] ?? 0, 'fa-exclamation-triangle', 'danger'],
            ] as [$label, $count, $icon, $color])
                <div class="col-6 col-xl-3 mb-3"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex align-items-center">
                    <div class="rounded bg-light p-3 mr-3"><i class="fas {{ $icon }} text-{{ $color }} fa-lg"></i></div>
                    <div><div class="text-muted small">{{ $label }}</div><div class="h4 mb-0">{{ number_format($count) }}</div></div>
                </div></div></div>
            @endforeach
        </div>

        <div class="card border-0 shadow-sm mb-4"><div class="card-body">
            <form method="GET" action="{{ route('seller.products.index') }}">
                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                <div class="form-row align-items-end">
                    <div class="col-md-3 form-group"><label>Branch</label><select class="form-control" onchange="if(this.value) location.href='{{ route('seller.products.index') }}?branch_id='+encodeURIComponent(this.value)">
                        @foreach($branches as $branchOption)<option value="{{ $branchOption->id }}" @selected($branchOption->id == $branch->id)>{{ $branchOption->name ?? ('Branch #'.$branchOption->id) }}</option>@endforeach
                    </select></div>
                    <div class="col-md-3 form-group"><label>Service</label><select name="service_id" class="form-control"><option value="">All enabled services</option>@foreach($services as $service)<option value="{{ $service->id }}" @selected(request('service_id') == $service->id)>{{ $service->name ?? $service->label ?? $service->code }}</option>@endforeach</select></div>
                    <div class="col-md-2 form-group"><label>Status</label><select name="status" class="form-control">
                        <option value="">All statuses</option><option value="active" @selected(request('status')==='active')>Active</option><option value="inactive" @selected(request('status')==='inactive')>Inactive</option><option value="out_of_stock" @selected(request('status')==='out_of_stock')>Out of stock</option>
                    </select></div>
                    <div class="col-md-3 form-group"><label>Search</label><input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, SKU or description"></div>
                    <div class="col-md-1 form-group"><button class="btn btn-primary btn-block" type="submit" title="Filter"><i class="fas fa-search"></i></button></div>
                </div>
            </form>
        </div></div>

        <div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex justify-content-between align-items-center"><h5 class="mb-0">Listing inventory</h5><span class="text-muted small">{{ $products->total() }} result(s)</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-light"><tr><th>Product</th><th>Service</th><th>Price</th><th>Stock</th><th>Status</th><th>Featured</th><th>Created by</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td style="min-width:220px"><div class="d-flex align-items-center">
                                @if($product->thumbnail)<img src="{{ asset('storage/'.$product->thumbnail) }}" alt="" style="width:54px;height:54px;object-fit:cover" class="rounded border mr-3">@else<div class="rounded bg-light border mr-3 d-flex align-items-center justify-content-center" style="width:54px;height:54px"><i class="fas fa-image text-muted"></i></div>@endif
                                <div><a class="font-weight-bold text-dark" href="{{ route('seller.products.show', $product) }}">{{ $product->name }}</a><div class="small text-muted">{{ $product->sku ?: 'No SKU' }}</div></div>
                            </div></td>
                            <td>{{ $product->service->name ?? $product->service->label ?? $product->service->code ?? '—' }}</td>
                            <td><strong>Rs. {{ number_format((float)$product->price, 2) }}</strong>@if($product->compare_price)<div class="small text-muted"><del>Rs. {{ number_format((float)$product->compare_price, 2) }}</del></div>@endif</td>
                            <td>{{ $product->track_stock ? number_format((int)$product->stock_quantity) : 'Not tracked' }}<div class="small text-muted">{{ $product->unit }}</div></td>
                            <td><span class="badge badge-{{ $product->is_active ? 'success' : 'secondary' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <form method="POST" action="{{ route('seller.products.toggle-featured', $product) }}">@csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $product->is_featured ? 'btn-warning' : 'btn-outline-secondary' }}" type="submit" title="Toggle featured"><i class="fas fa-star"></i> {{ $product->is_featured ? 'Featured' : 'Feature' }}</button>
                                </form>
                            </td>
                            <td>{{ $product->created_by_label ?? ($product->creator->name ?? '—') }}</td>
                            <td class="text-right" style="min-width:180px">
                                <a href="{{ route('seller.products.show', $product) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('seller.products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                <form class="d-inline" method="POST" action="{{ route('seller.products.toggle-active', $product) }}">@csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-{{ $product->is_active ? 'secondary' : 'success' }}" type="submit" title="Toggle active"><i class="fas fa-power-off"></i></button>
                                </form>
                                <form class="d-inline" method="POST" action="{{ route('seller.products.destroy', $product) }}" onsubmit="return confirm('Delete this product and its images? This cannot be undone.')">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-5"><i class="fas fa-box-open fa-2x text-muted mb-3"></i><p class="mb-2">No products found for these filters.</p><a href="{{ route('seller.products.create', ['branch_id' => $branch->id]) }}" class="btn btn-sm btn-primary">Create your first listing</a></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())<div class="card-footer bg-white">{{ $products->links() }}</div>@endif
        </div>
    @endif
</div>
@endsection
