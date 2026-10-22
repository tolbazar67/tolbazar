@extends('layouts.admin')
@section('title', $category->exists?'Edit Category':'Add Category')
@section('heading', $category->exists?'Edit Category':'Add Category')
@section('content')
<div class="panel"><form method="POST" action="{{ $category->exists?route('admin.service-categories.update',$category):route('admin.service-categories.store') }}">@csrf @if($category->exists)@method('PUT')@endif
<div class="form-row">
<div class="form-group col-md-6"><label>Service *</label><select required name="service_id" class="form-control">@foreach($services as $s)<option value="{{ $s->id }}" @selected(old('service_id',$category->service_id)==$s->id)>{{ $s->name }}</option>@endforeach</select></div>
<div class="form-group col-md-6"><label>Parent category</label><select name="parent_id" class="form-control"><option value="">Top-level category</option>@foreach($parents as $p)<option value="{{ $p->id }}" @selected(old('parent_id',$category->parent_id)==$p->id)>{{ $p->name }}</option>@endforeach</select></div>
<div class="form-group col-md-6"><label>Name *</label><input required name="name" class="form-control" value="{{ old('name',$category->name) }}"></div>
<div class="form-group col-md-6"><label>Slug (optional)</label><input name="slug" class="form-control" value="{{ old('slug',$category->slug) }}"></div>
<div class="form-group col-md-6"><label>Icon class/name</label><input name="icon" class="form-control" value="{{ old('icon',$category->icon) }}"></div>
<div class="form-group col-md-6"><label>Image path</label><input name="image" class="form-control" value="{{ old('image',$category->image) }}"></div>
<div class="form-group col-md-3"><label>Sort order</label><input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order',$category->sort_order ?? 0) }}"></div>
<div class="form-group col-md-9"><label>Description</label><textarea name="description" class="form-control">{{ old('description',$category->description) }}</textarea></div>
<div class="form-group col-12"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked(old('is_active',$category->is_active ?? true))><label class="custom-control-label" for="is_active">Active</label></div></div>
</div><button class="btn btn-gold">Save Category</button> <a class="btn btn-light" href="{{ route('admin.service-categories.index') }}">Cancel</a></form></div>
@endsection
