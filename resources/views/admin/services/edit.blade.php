@extends('layouts.admin')

@section('title', 'Edit Service')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h3 class="fw-bold mb-0">Edit Service</h3>
            <p class="text-muted mb-0 small">Update service details</p>
        </div>
        <span class="badge {{ $service->is_active ? 'bg-success' : 'bg-secondary' }}">
            {{ $service->is_active ? 'Active' : 'Inactive' }}
        </span>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-lg-8">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-info-circle text-primary me-1"></i> Basic Information
                        </h6>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Service Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $service->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   required maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $service->slug) }}"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   maxlength="255">
                            <small class="text-muted">Used in URLs. Must be unique.</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      maxlength="2000">{{ old('description', $service->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-palette text-primary me-1"></i> Visual Style
                        </h6>
                    </div>
                    <div class="card-body">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Icon</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i id="iconPreview" class="{{ old('icon', $service->icon ?? 'bi bi-grid') }}"></i>
                                    </span>
                                    <input type="text" name="icon" id="iconInput"
                                           value="{{ old('icon', $service->icon) }}"
                                           class="form-control"
                                           placeholder="bi bi-grid">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Color</label>
                                <div class="input-group">
                                    <input type="color" name="color" id="colorInput"
                                           value="{{ old('color', $service->color ?? '#6366f1') }}"
                                           class="form-control form-control-color">
                                    <input type="text" id="colorText"
                                           value="{{ old('color', $service->color ?? '#6366f1') }}"
                                           class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Cover Image</label>

                            @if($service->image)
                                <div class="mb-2 d-flex align-items-center gap-3 p-2 border rounded">
                                    <img src="{{ asset('storage/' . $service->image) }}"
                                         class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <div class="small fw-semibold">Current image</div>
                                        <div class="form-check">
                                            <input type="checkbox" name="remove_image" id="removeImage"
                                                   value="1" class="form-check-input">
                                            <label for="removeImage" class="form-check-label small text-danger">
                                                Remove image
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <input type="file" name="image"
                                   class="form-control @error('image') is-invalid @enderror"
                                   accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-lg-4">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-toggle-on text-primary me-1"></i> Publish Settings
                        </h6>
                    </div>
                    <div class="card-body">

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="is_active" id="isActive"
                                   value="1" class="form-check-input"
                                   {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">Active</label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="is_featured" id="isFeatured"
                                   value="1" class="form-check-input"
                                   {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isFeatured">Featured</label>
                        </div>

                        <div>
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order"
                                   value="{{ old('sort_order', $service->sort_order) }}"
                                   class="form-control" min="0">
                        </div>
                    </div>
                </div>

                {{-- META INFO --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body small text-muted">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Created:</span>
                            <strong>{{ $service->created_at->format('M d, Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Updated:</span>
                            <strong>{{ $service->updated_at->diffForHumans() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Products:</span>
                            <strong>{{ $service->products()->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Branches:</span>
                            <strong>{{ $service->branches()->count() }}</strong>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                        <hr class="my-1">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="if(confirm('Delete this service?')) document.getElementById('deleteForm').submit();">
                            <i class="bi bi-trash"></i> Delete Service
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <form id="deleteForm" action="{{ route('admin.services.destroy', $service) }}"
          method="POST" style="display: none;">
        @csrf @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('iconInput').addEventListener('input', function () {
        document.getElementById('iconPreview').className = this.value || 'bi bi-grid';
    });

    document.getElementById('colorInput').addEventListener('input', function () {
        document.getElementById('colorText').value = this.value;
    });
</script>
@endpush
@endsection