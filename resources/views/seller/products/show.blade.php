@extends('layouts.seller')

@section('title', $product->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div><h1 class="h3 mb-1">{{ $product->name }}</h1><p class="text-muted mb-0">Product details and audit information</p></div>
        <div><a href="{{ route('seller.products.index', ['branch_id' => $product->branch_id]) }}" class="btn btn-outline-secondary mr-1"><i class="fas fa-arrow-left"></i> Back</a><a href="{{ route('seller.products.edit', $product) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a></div>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm"><div class="card-body">
                @if($product->thumbnail)<img src="{{ asset('storage/'.$product->thumbnail) }}" class="img-fluid rounded mb-3" alt="{{ $product->name }}" style="width:100%;max-height:360px;object-fit:contain">@else<div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height:260px"><i class="fas fa-image fa-3x text-muted"></i></div>@endif
                <div class="d-flex flex-wrap mb-3"><span class="badge badge-{{ $product->is_active ? 'success' : 'secondary' }} mr-2 mb-2">{{ $product->is_active ? 'Active' : 'Inactive' }}</span><span class="badge badge-{{ $product->is_featured ? 'warning' : 'light' }} border mb-2">{{ $product->is_featured ? 'Featured' : 'Not featured' }}</span></div>
                <h5>Gallery</h5><div class="row">
                    @forelse($product->images as $image)<div class="col-4 mb-2"><a href="{{ asset('storage/'.$image->image_path) }}" target="_blank" rel="noopener"><img src="{{ asset('storage/'.$image->image_path) }}" class="img-fluid rounded border" alt="Gallery image" style="height:100px;width:100%;object-fit:cover"></a></div>@empty<div class="col-12 text-muted small">No gallery images uploaded.</div>@endforelse
                </div>
            </div></div>
        </div>
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h5 class="mb-0">Overview</h5></div><div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Service</dt><dd class="col-sm-8">{{ $product->service->name ?? $product->service->label ?? $product->service->code ?? '—' }}</dd>
                    <dt class="col-sm-4">Branch</dt><dd class="col-sm-8">{{ $product->branch->name ?? ('Branch #'.$product->branch_id) }}</dd>
                    <dt class="col-sm-4">SKU</dt><dd class="col-sm-8">{{ $product->sku ?: '—' }}</dd>
                    <dt class="col-sm-4">Price</dt><dd class="col-sm-8 font-weight-bold">Rs. {{ number_format((float)$product->price,2) }} / {{ $product->unit }}</dd>
                    <dt class="col-sm-4">Compare price</dt><dd class="col-sm-8">{{ $product->compare_price !== null ? 'Rs. '.number_format((float)$product->compare_price,2) : '—' }}</dd>
                    <dt class="col-sm-4">Cost price</dt><dd class="col-sm-8">{{ $product->cost_price !== null ? 'Rs. '.number_format((float)$product->cost_price,2) : '—' }}</dd>
                    <dt class="col-sm-4">Tax</dt><dd class="col-sm-8">{{ number_format((float)$product->tax_percent,2) }}%</dd>
                    <dt class="col-sm-4">Stock</dt><dd class="col-sm-8">{{ $product->track_stock ? number_format((int)$product->stock_quantity) : 'Not tracked' }}</dd>
                    <dt class="col-sm-4">Order quantity</dt><dd class="col-sm-8">{{ $product->min_order_qty ?? 1 }} – {{ $product->max_order_qty ?? 'No limit' }}</dd>
                    <dt class="col-sm-4">Created by</dt><dd class="col-sm-8">{{ $product->created_by_label ?? ($product->creator->name ?? '—') }}</dd>
                    <dt class="col-sm-4">Created at</dt><dd class="col-sm-8">{{ optional($product->created_at)->format('M d, Y h:i A') }}</dd>
                    <dt class="col-sm-4">Updated at</dt><dd class="col-sm-8">{{ optional($product->updated_at)->format('M d, Y h:i A') }}</dd>
                </dl>
            </div></div>
            <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h5 class="mb-0">Descriptions</h5></div><div class="card-body">
                <h6>Short description</h6><p>{{ $product->short_description ?: '—' }}</p><hr><h6>Full description</h6><div>{!! nl2br(e($product->description ?: '—')) !!}</div>
            </div></div>
            @php
                $detailRelations = [
                    'HOTEL'=>['hotelRoomDetail','Hotel / Accommodation'], 'RETAIL'=>['retailProductDetail','Retail product'],
                    'RESTAURANT'=>['restaurantItemDetail','Restaurant item'], 'EDUCATION'=>['educationCourseDetail','Education course'],
                    'HEALTHCARE'=>['healthcareServiceDetail','Healthcare service'], 'TRANSPORT'=>['transportServiceDetail','Transport'],
                    'BEAUTY'=>['beautyServiceDetail','Beauty service'], 'REPAIR'=>['repairServiceDetail','Repair service'],
                    'CONSTRUCTION'=>['constructionServiceDetail','Construction service'], 'TRAVEL'=>['travelPackageDetail','Travel package'],
                ];
                $detailRelation = $detailRelations[strtoupper($product->service->code ?? '')] ?? null;
                $detailObject = $detailRelation ? $product->{$detailRelation[0]} : null;
            @endphp
            @if($detailObject)
                <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h5 class="mb-0">{{ $detailRelation[1] }} details</h5></div>
                    <div class="table-responsive"><table class="table table-sm mb-0"><tbody>
                    @foreach($detailObject->getAttributes() as $key => $value)
                        @continue(in_array($key, ['id','product_id','created_at','updated_at']))
                        <tr><th style="width:35%">{{ \Illuminate\Support\Str::headline($key) }}</th><td>
                            @if(is_string($value) && (str_starts_with(trim($value), '[') || str_starts_with(trim($value), '{')))
                                <pre class="mb-0 text-wrap">{{ json_encode(json_decode($value, true) ?? $value, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                            @elseif(in_array($value, [0,1,'0','1']) && str_starts_with($key, 'is_')){{ $value ? 'Yes' : 'No' }}
                            @else{{ is_scalar($value) ? ($value === null || $value === '' ? '—' : $value) : json_encode($value) }}@endif
                        </td></tr>
                    @endforeach
                    </tbody></table></div>
                </div>
            @else
                <div class="card border-0 shadow-sm mb-4"><div class="card-body text-muted">No service detail record found for this product.</div></div>
            @endif
            <div class="card border-0 shadow-sm"><div class="card-header bg-white"><h5 class="mb-0">SEO & tags</h5></div><div class="card-body">
                <p><strong>SEO title:</strong> {{ $product->meta_title ?: '—' }}</p><p><strong>SEO description:</strong> {{ $product->meta_description ?: '—' }}</p><p class="mb-0"><strong>Tags:</strong> {{ $product->tags ?: '—' }}</p>
            </div></div>
        </div>
    </div>
</div>
@endsection
