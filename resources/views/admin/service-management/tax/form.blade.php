@extends('layouts.admin')
@section('title',$rule->exists?'Edit Tax Rule':'Add Tax Rule')
@section('heading',$rule->exists?'Edit Tax Rule':'Add Tax Rule')
@section('content')
<div class="panel"><form method="POST" action="{{ $rule->exists?route('admin.service-tax-rules.update',$rule):route('admin.service-tax-rules.store') }}">@csrf @if($rule->exists)@method('PUT')@endif
<div class="form-row">
<div class="form-group col-md-6"><label>Service *</label><select required name="service_id" class="form-control">@foreach($services as $s)<option value="{{ $s->id }}" @selected(old('service_id',$rule->service_id)==$s->id)>{{ $s->name }}</option>@endforeach</select></div>
<div class="form-group col-md-6"><label>Category (optional)</label><select name="category_id" class="form-control"><option value="">All categories</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id',$rule->category_id)==$c->id)>{{ $c->name }} (service #{{ $c->service_id }})</option>@endforeach</select></div>
<div class="form-group col-md-6"><label>Rule name *</label><input required name="name" class="form-control" value="{{ old('name',$rule->name) }}"></div>
<div class="form-group col-md-6"><label>Tax code</label><input name="tax_code" class="form-control" value="{{ old('tax_code',$rule->tax_code) }}"></div>
<div class="form-group col-md-4"><label>Type *</label><select name="tax_type" class="form-control"><option value="percentage" @selected(old('tax_type',$rule->tax_type)==='percentage')>Percentage</option><option value="fixed" @selected(old('tax_type',$rule->tax_type)==='fixed')>Fixed amount</option></select></div>
<div class="form-group col-md-4"><label>Rate *</label><input required type="number" min="0" step="0.0001" name="rate" class="form-control" value="{{ old('rate',$rule->rate ?? 0) }}"></div>
<div class="form-group col-md-4"><label>Currency *</label><input required maxlength="3" name="currency" class="form-control" value="{{ old('currency',$rule->currency ?? 'NPR') }}"></div>
<div class="form-group col-md-6"><label>Applies to *</label><select name="applies_to" class="form-control">@foreach(['customer','partner','platform'] as $v)<option value="{{ $v }}" @selected(old('applies_to',$rule->applies_to)===$v)>{{ ucfirst($v) }}</option>@endforeach</select></div>
<div class="form-group col-md-3"><label>Priority</label><input type="number" min="0" name="priority" class="form-control" value="{{ old('priority',$rule->priority ?? 0) }}"></div>
<div class="form-group col-md-3"><label>Starts at</label><input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at',$rule->starts_at?->format('Y-m-d\TH:i')) }}"></div>
<div class="form-group col-md-6"><label>Ends at</label><input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at',$rule->ends_at?->format('Y-m-d\TH:i')) }}"></div>
<div class="form-group col-md-6"><div class="custom-control custom-checkbox"><input id="inc" type="checkbox" name="is_inclusive" value="1" class="custom-control-input" @checked(old('is_inclusive',$rule->is_inclusive ?? false))><label class="custom-control-label" for="inc">Tax included in listed price</label></div><div class="custom-control custom-checkbox mt-2"><input id="act" type="checkbox" name="is_active" value="1" class="custom-control-input" @checked(old('is_active',$rule->is_active ?? true))><label class="custom-control-label" for="act">Active</label></div></div>
</div><button class="btn btn-gold">Save Tax Rule</button> <a class="btn btn-light" href="{{ route('admin.service-tax-rules.index') }}">Cancel</a></form></div>
@endsection
