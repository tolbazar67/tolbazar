@extends('layouts.admin')
@section('title','Partner Commission Rules')
@section('heading','Partner Commission Rules')
@section('content')
<div class="panel mb-3"><form class="form-row align-items-end" method="GET">
<div class="form-group col-md-5"><label>Service</label><select name="service_id" class="form-control"><option value="">All services</option>@foreach($services as $s)<option value="{{ $s->id }}" @selected(request('service_id')==$s->id)>{{ $s->name }}</option>@endforeach</select></div>
<div class="form-group col-md-3"><label>Status</label><select name="status" class="form-control"><option value="">All</option><option value="active" @selected(request('status')==='active')>Active</option><option value="inactive" @selected(request('status')==='inactive')>Inactive</option></select></div>
<div class="form-group col-md-4"><button class="btn btn-secondary">Filter</button> <a class="btn btn-gold" href="{{ route('admin.partner-commission-rules.create') }}">+ Add New</a></div>
</form></div>
<div class="panel table-responsive"><table class="table table-hover"><thead><tr><th>Rule</th><th>Service</th><th>Partner / Branch</th><th>Rate</th><th>Category</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($rules as $rule)<tr><td><strong>{{ $rule->name }}</strong></td><td>{{ $rule->service?->name }}</td><td>{{ $rule->seller?->store_name ?? 'All partners' }}<div class="muted">{{ $rule->branch?->name ?? '' }}</div></td><td>{{ $rule->commission_type }} · {{ $rule->rate }} {{ $rule->commission_type==='percentage'?'%':$rule->currency }}</td><td>{{ $rule->category?->name ?? 'All categories' }}</td><td><span class="badge badge-{{ $rule->is_active?'success':'secondary' }}">{{ $rule->is_active?'Active':'Inactive' }}</span></td><td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.partner-commission-rules.edit',$rule) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('admin.partner-commission-rules.toggle-active',$rule) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-warning">Toggle</button></form> <form class="d-inline" method="POST" action="{{ route('admin.partner-commission-rules.destroy',$rule) }}" data-confirm="Delete this rule?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
@empty<tr><td colspan="7" class="text-center text-muted">No records found.</td></tr>@endforelse
</tbody></table>{{ $rules->links() }}</div>
@endsection
