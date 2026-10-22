@extends('layouts.admin')
@section('title',$rule->exists?'Edit Commission Rule':'Add Commission Rule')
@section('heading',$rule->exists?'Edit Commission Rule':'Add Commission Rule')
@section('content')
<div class="panel"><form method="POST" action="{{ $rule->exists?route('admin.partner-commission-rules.update',$rule):route('admin.partner-commission-rules.store') }}">@csrf @if($rule->exists)@method('PUT')@endif
<div class="form-row">
<div class="form-group col-md-6"><label>Service *</label><select required name="service_id" class="form-control">@foreach($services as $s)<option value="{{ $s->id }}" @selected(old('service_id',$rule->service_id)==$s->id)>{{ $s->name }}</option>@endforeach</select></div>
<div class="form-group col-md-6"><label>Category</label><select name="category_id" class="form-control"><option value="">All categories</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id',$rule->category_id)==$c->id)>{{ $c->name }} (service #{{ $c->service_id }})</option>@endforeach</select></div>
<div class="form-group col-md-6"><label>Partner (blank = all partners)</label><select name="seller_id" class="form-control"><option value="">All partners</option>@foreach($sellers as $s)<option value="{{ $s->id }}" @selected(old('seller_id',$rule->seller_id)==$s->id)>{{ $s->store_name }}</option>@endforeach</select></div>
<div class="form-group col-md-6"><label>Branch (optional)</label><select name="branch_id" class="form-control"><option value="">All branches</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected(old('branch_id',$rule->branch_id)==$b->id)>{{ $b->name ?? ('Branch #'.$b->id) }} (partner #{{ $b->seller_id }})</option>@endforeach</select></div>
<div class="form-group col-md-6"><label>Rule name *</label><input required name="name" class="form-control" value="{{ old('name',$rule->name) }}"></div>
<div class="form-group col-md-4"><label>Commission type *</label><select name="commission_type" class="form-control"><option value="percentage" @selected(old('commission_type',$rule->commission_type)==='percentage')>Percentage</option><option value="fixed" @selected(old('commission_type',$rule->commission_type)==='fixed')>Fixed amount</option></select></div>
<div class="form-group col-md-4"><label>Rate *</label><input required type="number" min="0" step="0.0001" name="rate" class="form-control" value="{{ old('rate',$rule->rate ?? 0) }}"></div>
<div class="form-group col-md-4"><label>Currency *</label><input required maxlength="3" name="currency" class="form-control" value="{{ old('currency',$rule->currency ?? 'NPR') }}"></div>
<div class="form-group col-md-6"><label>Calculation basis *</label><select name="calculation_basis" class="form-control"><option value="subtotal" @selected(old('calculation_basis',$rule->calculation_basis)==='subtotal')>Subtotal</option><option value="subtotal_after_discount" @selected(old('calculation_basis',$rule->calculation_basis)==='subtotal_after_discount')>Subtotal after discount</option></select></div>
<div class="form-group col-md-3"><label>Minimum amount</label><input type="number" min="0" step=".01" name="min_amount" class="form-control" value="{{ old('min_amount',$rule->min_amount) }}"></div>
<div class="form-group col-md-3"><label>Maximum amount</label><input type="number" min="0" step=".01" name="max_amount" class="form-control" value="{{ old('max_amount',$rule->max_amount) }}"></div>
<div class="form-group col-md-6"><label>Starts at</label><input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at',$rule->starts_at?->format('Y-m-d\TH:i')) }}"></div>
<div class="form-group col-md-6"><label>Ends at</label><input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at',$rule->ends_at?->format('Y-m-d\TH:i')) }}"></div>
<div class="form-group col-12"><div class="custom-control custom-checkbox"><input id="active" type="checkbox" name="is_active" value="1" class="custom-control-input" @checked(old('is_active',$rule->is_active ?? true))><label class="custom-control-label" for="active">Active</label></div></div>
</div><button class="btn btn-gold">Save Commission</button> <a class="btn btn-light" href="{{ route('admin.partner-commission-rules.index') }}">Cancel</a></form></div>
@endsection
