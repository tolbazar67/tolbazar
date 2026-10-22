@extends('layouts.admin')
@section('title','Service Categories')
@section('heading','Service Categories')
@section('content')
<div class="panel mb-3">
<form class="form-row align-items-end" method="GET">
<div class="form-group col-md-5"><label>Service</label><select name="service_id" class="form-control"><option value="">All services</option>@foreach($services as $s)<option value="{{ $s->id }}" @selected(request('service_id')==$s->id)>{{ $s->name }}</option>@endforeach</select></div>
<div class="form-group col-md-3"><label>Status</label><select name="status" class="form-control"><option value="">All</option><option value="active" @selected(request('status')==='active')>Active</option><option value="inactive" @selected(request('status')==='inactive')>Inactive</option></select></div>
<div class="form-group col-md-4"><button class="btn btn-secondary">Filter</button> <a class="btn btn-gold" href="{{ route('admin.service-categories.create') }}">+ Add Category</a></div>
</form></div>
<div class="panel table-responsive"><table class="table table-hover"><thead><tr><th>Category</th><th>Service</th><th>Parent</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($categories as $c)<tr><td><strong>{{ $c->name }}</strong><div class="muted">{{ $c->slug }}</div></td><td>{{ $c->service?->name }}</td><td>{{ $c->parent?->name ?? '—' }}</td><td><span class="badge badge-{{ $c->is_active?'success':'secondary' }}">{{ $c->is_active?'Active':'Inactive' }}</span></td><td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.service-categories.edit',$c) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('admin.service-categories.toggle-active',$c) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-warning">Toggle</button></form> <form class="d-inline" method="POST" action="{{ route('admin.service-categories.destroy',$c) }}" data-confirm="Delete this category?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
@empty<tr><td colspan="6" class="text-center text-muted">No categories found.</td></tr>@endforelse
</tbody></table>{{ $categories->links() }}</div>
@endsection
