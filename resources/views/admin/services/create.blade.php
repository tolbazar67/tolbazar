@extends('layouts.admin')

@section('title', 'Create Service')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0">Create Service</h3>
            <p class="text-muted mb-0 small">Add a new service category to the platform</p>
        </div>
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

    <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">

            {{-- LEFT: Main Info --}}
            <div class="col-lg-8">

                {{-- BASIC INFO --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-info-circle text-primary me-1"></i> Basic Information
                        </h6>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Service Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g., Restaurant, Travel, Retail"
                                   required maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug') }}"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="auto-generated from name if empty"
                                   maxlength="255">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i>
                                Leave empty to auto-generate (e.g., "restaurant-1234")
                            </small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Describe what this service offers..."
                                      maxlength="2000">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- VISUAL --}}
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
                                        <i id="iconPreview" class="{{ old('icon', 'bi bi-grid') }}"></i>
                                    </span>
                                    <input type="text" name="icon" id="iconInput"
                                           value="{{ old('icon', 'bi bi-grid') }}"
                                           class="form-control @error('icon') is-invalid @enderror"
                                           placeholder="bi bi-grid">
                                </div>
                                <small class="text-muted">
                                    Bootstrap Icons — e.g., <code>bi bi-cup-hot-fill</code>,
                                    <code>bi bi-airplane-fill</code>
                                </small>
                                @error('icon')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror

                                {{-- Quick icon picker --}}
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    @foreach(['bi bi-cup-hot-fill', 'bi bi-building', 'bi bi-airplane-fill', 'bi bi-graph-up-arrow', 'bi bi-activity', 'bi bi-bag-fill', 'bi bi-heart-pulse', 'bi bi-book-fill'] as $quickIcon)
                                        <button type="button"
                                                class="btn btn-sm btn-light border icon-btn"
                                                data-icon="{{ $quickIcon }}"
                                                title="{{ $quickIcon }}">
                                            <i class="{{ $quickIcon }}"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Color</label>
                                <div class="input-group">
                                    <input type="color" name="color" id="colorInput"
                                           value="{{ old('color', '#6366f1') }}"
                                           class="form-control form-control-color">
                                    <input type="text" id="colorText"
                                           value="{{ old('color', '#6366f1') }}"
                                           class="form-control" readonly>
                                </div>
                                <small class="text-muted">Theme color for the service card</small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Cover Image</label>
                            <input type="file" name="image"
                                   class="form-control @error('image') is-invalid @enderror"
                                   accept="image/*"
                                   onchange="previewImage(this)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="imagePreviewImg" class="rounded-3 border"
                                     style="max-height: 180px;">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT: Settings --}}
            <div class="col-lg-4">

                {{-- PUBLISH --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-toggle-on text-primary me-1"></i> Publish Settings
                        </h6>
                    </div>
                    <div class="card-body">

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="is_active" id="isActive"
                                   value="1"
                                   class="form-check-input"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">
                                Active
                            </label>
                            <div class="small text-muted">Sellers can enable this service</div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="is_featured" id="isFeatured"
                                   value="1"
                                   class="form-check-input"
                                   {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isFeatured">
                                Featured
                            </label>
                            <div class="small text-muted">Show on homepage / highlight</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order"
                                   value="{{ old('sort_order', 0) }}"
                                   class="form-control" min="0">
                            <small class="text-muted">Lower = appears first</small>
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Create Service
                        </button>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Icon preview
    document.getElementById('iconInput').addEventListener('input', function () {
        document.getElementById('iconPreview').className = this.value || 'bi bi-grid';
    });

    // Quick icon picker
    document.querySelectorAll('.icon-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const icon = this.dataset.icon;
            document.getElementById('iconInput').value = icon;
            document.getElementById('iconPreview').className = icon;
        });
    });

    // Color sync
    document.getElementById('colorInput').addEventListener('input', function () {
        document.getElementById('colorText').value = this.value;
    });

    // Image preview
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const img = document.getElementById('imagePreviewImg');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                img.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection