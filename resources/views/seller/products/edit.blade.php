@extends('layouts.seller')

@section('title', 'Edit Product')

@section('content')
<div class="container py-4">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('seller.products.index', ['branch_id' => $product->branch_id]) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h3 class="fw-bold mb-0">Edit: {{ $product->name }}</h3>
            <p class="text-muted mb-0 small">{{ $branch->branch_name }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('seller.products.update', $product) }}"
          method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">

                {{-- BASIC --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">Basic Information</h6>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Service</label>
                            <input type="text" value="{{ $product->service->name }}"
                                   class="form-control" disabled>
                            <small class="text-muted">Service cannot be changed after creation.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product Name *</label>
                            <input type="text" name="name"
                                   value="{{ old('name', $product->name) }}"
                                   class="form-control" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">SKU</label>
                                <input type="text" name="sku"
                                       value="{{ old('sku', $product->sku) }}"
                                       class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Unit *</label>
                                <input type="text" name="unit"
                                       value="{{ old('unit', $product->unit) }}"
                                       class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Short Description</label>
                            <input type="text" name="short_description"
                                   value="{{ old('short_description', $product->short_description) }}"
                                   class="form-control" maxlength="500">
                        </div>

                        <div>
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="5"
                                      class="form-control">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- PRICING --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">Pricing</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Price *</label>
                                <input type="number" name="price"
                                       value="{{ old('price', $product->price) }}"
                                       class="form-control" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Compare</label>
                                <input type="number" name="compare_price"
                                       value="{{ old('compare_price', $product->compare_price) }}"
                                       class="form-control" min="0" step="0.01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Cost</label>
                                <input type="number" name="cost_price"
                                       value="{{ old('cost_price', $product->cost_price) }}"
                                       class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STOCK --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">Stock</h6>
                    </div>
                    <div class="card-body">

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="track_stock" id="trackStock"
                                   value="1" class="form-check-input"
                                   {{ old('track_stock', $product->track_stock) ? 'checked' : '' }}>
                            <label for="trackStock" class="form-check-label">Track Stock</label>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Stock Qty</label>
                                <input type="number" name="stock_quantity"
                                       value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                       class="form-control" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Min</label>
                                <input type="number" name="min_order_qty"
                                       value="{{ old('min_order_qty', $product->min_order_qty) }}"
                                       class="form-control" min="1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Max</label>
                                <input type="number" name="max_order_qty"
                                       value="{{ old('max_order_qty', $product->max_order_qty) }}"
                                       class="form-control" min="1">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- IMAGES --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">Images</h6>
                    </div>
                    <div class="card-body">

                        {{-- Current thumbnail --}}
                        @if($product->thumbnail)
                            <div class="d-flex align-items-center gap-3 mb-3 p-2 border rounded">
                                <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                                <div class="flex-grow-1">
                                    <div class="small fw-semibold">Current Thumbnail</div>
                                    <div class="form-check">
                                        <input type="checkbox" name="remove_thumbnail"
                                               id="removeThumb" value="1"
                                               class="form-check-input">
                                        <label for="removeThumb"
                                               class="form-check-label small text-danger">
                                            Remove
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Thumbnail</label>
                            <input type="file" name="thumbnail"
                                   class="form-control" accept="image/*">
                        </div>

                        {{-- Existing gallery --}}
                        @if($product->images->count() > 0)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Gallery</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($product->images as $img)
                                        <div class="position-relative">
                                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                                 style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                            <form action="{{ route('seller.products.images.delete', [$product, $img]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Remove this image?')"
                                                  class="position-absolute top-0 end-0">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-danger btn-sm p-0 rounded-circle"
                                                        style="width:22px;height:22px;font-size:.65rem;">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="form-label fw-semibold">Add More Images</label>
                            <input type="file" name="gallery[]"
                                   class="form-control" accept="image/*" multiple>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-lg-4">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">Publish</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="is_active" id="isActive"
                                   value="1" class="form-check-input"
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label for="isActive" class="form-check-label">Active</label>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input type="checkbox" name="is_featured" id="isFeatured"
                                   value="1" class="form-check-input"
                                   {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            <label for="isFeatured" class="form-check-label">Featured</label>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Save Changes
                        </button>
                        <a href="{{ route('seller.products.index', ['branch_id' => $product->branch_id]) }}"
                           class="btn btn-light">Cancel</a>
                        <hr class="my-1">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="if(confirm('Delete?')) document.getElementById('delForm').submit();">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <form id="delForm" action="{{ route('seller.products.destroy', $product) }}"
          method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>
</div>
@endsection