@extends('layouts.seller')

@section('title', 'Edit Product')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Product</h1>
            <p class="text-muted mb-0">{{ $product->name }} · {{ $product->sku ?: 'No SKU' }}</p>
        </div>
        <a href="{{ route('seller.products.show', $product) }}" class="btn btn-outline-secondary"><i class="fas fa-eye mr-1"></i> View product</a>
    </div>
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    <form class="product-form" method="POST" action="{{ route('seller.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('seller.products._form')
        <div class="d-flex flex-wrap justify-content-end mb-5">
            <a href="{{ route('seller.products.index', ['branch_id' => $product->branch_id]) }}" class="btn btn-light mr-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Save changes</button>
        </div>
    </form>
</div>
@endsection
