@extends('layouts.seller')

@section('title', 'My Branches')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">🏪 Branch Outlets</h3>
            <p class="text-muted mb-0">Manage all your store branches and staff</p>
        </div>
        @if(auth()->user()->isSellerOwner())
            <a href="{{ route('seller.branches.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Branch
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-3">
        @forelse($branches as $branch)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <h5 class="fw-bold mb-0">{{ $branch->branch_name }}</h5>
                            @if($branch->is_default)
                                <span class="badge bg-success">HQ</span>
                            @endif
                        </div>

                        <p class="text-muted small mb-1">
                            <i class="bi bi-tag"></i> {{ $branch->branch_code }}
                        </p>
                        <p class="text-muted small mb-1">
                            <i class="bi bi-telephone"></i> {{ $branch->phone }}
                        </p>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-geo-alt"></i> {{ $branch->address }}
                        </p>

                        @if($branch->manager)
                            <p class="small mb-2">
                                <strong>Manager:</strong> {{ $branch->manager->name }}
                            </p>
                        @endif

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('seller.branches.edit', $branch->id) }}"
                               class="btn btn-sm btn-outline-primary">Edit</a>

                            <a href="{{ route('seller.staff.index', ['branch_id' => $branch->id]) }}"
                               class="btn btn-sm btn-outline-info">
                               Staff ({{ $branch->staff()->count() }})
                            </a>

                            @if(!$branch->is_default && auth()->user()->isSellerOwner())
                                <form action="{{ route('seller.branches.destroy', $branch->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this branch and its staff?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No branches yet.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection