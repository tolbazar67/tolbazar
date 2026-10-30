@extends('layouts.seller')

@section('title', 'Create Product')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Create Product / Service</h1>
            <p class="text-muted mb-0">Add a listing to {{ $branch->name ?? 'your selected branch' }}.</p>
        </div>
        <a href="{{ route('seller.products.index', ['branch_id' => $branch->id]) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to products</a>
    </div>

    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    <form class="product-form" method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('seller.products._form')
        <div class="d-flex flex-wrap justify-content-end mb-5">
            <a href="{{ route('seller.products.index', ['branch_id' => $branch->id]) }}" class="btn btn-light mr-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Create product</button>
        </div>
    </form>
</div>
@endsection
