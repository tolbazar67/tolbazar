


<?php $__env->startSection('title', 'Create Product'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-box-seam me-2 text-primary"></i> Create New Item
            </h4>
            <p class="text-muted mb-0 small">
                Branch: <strong><?php echo e($branch->name); ?></strong>
                &nbsp;•&nbsp;
                You are creating as:
                <span class="badge bg-primary">
                    <?php echo e(auth()->user()->isSellerOwner() ? 'Owner' : 'Staff'); ?>

                </span>
            </p>
        </div>
        <a href="<?php echo e(route('seller.products.index', ['branch_id' => $branch->id])); ?>"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('seller.products.store')); ?>"
          method="POST"
          enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <input type="hidden" name="branch_id" value="<?php echo e($branch->id); ?>">

        <div class="row g-4">

            
            <div class="col-lg-8">

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>
                            Select Service Type
                        </h6>
                        <small class="text-muted">
                            Choose which service this item belongs to
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $key = $svc->slug ?? $svc->name;
                                    $meta = $serviceCatalog[$key] ?? [
                                        'label' => ucfirst($svc->name),
                                        'icon'  => 'bi-tag'
                                    ];
                                ?>
                                <div class="col-md-4 col-sm-6">
                                    <label class="service-radio-card w-100">
                                        <input type="radio"
                                               name="service_id"
                                               value="<?php echo e($svc->id); ?>"
                                               class="d-none service-radio"
                                               <?php echo e(old('service_id') == $svc->id ? 'checked' : ''); ?>

                                               required>
                                        <div class="service-card-inner p-3 border rounded-3 text-center h-100">
                                            <i class="bi <?php echo e($meta['icon']); ?> fs-2 text-primary"></i>
                                            <div class="fw-semibold mt-2 small">
                                                <?php echo e($meta['label']); ?>

                                            </div>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-info-circle me-2 text-primary"></i>
                            Basic Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="<?php echo e(old('name')); ?>"
                                       placeholder="e.g. Everest Base Camp Trek"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">SKU</label>
                                <input type="text"
                                       name="sku"
                                       class="form-control"
                                       value="<?php echo e(old('sku')); ?>"
                                       placeholder="Auto-generated if empty">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="unit"
                                       class="form-control"
                                       value="<?php echo e(old('unit', 'pcs')); ?>"
                                       placeholder="pcs / night / trip / person"
                                       required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Short Description</label>
                                <input type="text"
                                       name="short_description"
                                       class="form-control"
                                       value="<?php echo e(old('short_description')); ?>"
                                       maxlength="500">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description"
                                          rows="4"
                                          class="form-control"><?php echo e(old('description')); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-cash-stack me-2 text-success"></i>
                            Pricing
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number"
                                           step="0.01"
                                           name="price"
                                           class="form-control"
                                           value="<?php echo e(old('price')); ?>"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Compare Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number"
                                           step="0.01"
                                           name="compare_price"
                                           class="form-control"
                                           value="<?php echo e(old('compare_price')); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Cost Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number"
                                           step="0.01"
                                           name="cost_price"
                                           class="form-control"
                                           value="<?php echo e(old('cost_price')); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tax %</label>
                                <input type="number"
                                       step="0.01"
                                       name="tax_percent"
                                       class="form-control"
                                       value="<?php echo e(old('tax_percent', 0)); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-boxes me-2 text-warning"></i>
                            Stock
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number"
                                       name="stock_quantity"
                                       class="form-control"
                                       value="<?php echo e(old('stock_quantity', 0)); ?>"
                                       required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Min Order</label>
                                <input type="number"
                                       name="min_order_qty"
                                       class="form-control"
                                       value="<?php echo e(old('min_order_qty', 1)); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Max Order</label>
                                <input type="number"
                                       name="max_order_qty"
                                       class="form-control"
                                       value="<?php echo e(old('max_order_qty')); ?>">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox"
                                           name="track_stock"
                                           value="1"
                                           class="form-check-input"
                                           id="trackStock"
                                           <?php echo e(old('track_stock', true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="trackStock">
                                        Track stock for this item
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-images me-2 text-info"></i>
                            Media
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Thumbnail</label>
                                <input type="file"
                                       name="thumbnail"
                                       class="form-control"
                                       accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gallery (max 5)</label>
                                <input type="file"
                                       name="gallery[]"
                                       class="form-control"
                                       accept="image/*"
                                       multiple>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-search me-2 text-secondary"></i>
                            SEO & Tags
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Meta Title</label>
                                <input type="text"
                                       name="meta_title"
                                       class="form-control"
                                       value="<?php echo e(old('meta_title')); ?>"
                                       maxlength="255">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Meta Description</label>
                                <textarea name="meta_description"
                                          rows="2"
                                          class="form-control"
                                          maxlength="500"><?php echo e(old('meta_description')); ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tags (comma separated)</label>
                                <input type="text"
                                       name="tags"
                                       class="form-control"
                                       value="<?php echo e(old('tags')); ?>"
                                       placeholder="adventure, trekking, nepal">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4">

                
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 90px;">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-toggle-on me-2 text-success"></i>
                            Publish Status
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   class="form-check-input"
                                   id="isActive"
                                   <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="isActive">
                                Active (visible to customers)
                            </label>
                        </div>

                        
                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Featured status</strong> is managed separately.
                            After creating, you can toggle it from the
                            <a href="<?php echo e(route('seller.products.featured')); ?>">Featured Items</a> page.
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle me-1"></i> Create Item
                        </button>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block mb-2">Created by</small>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                 style="width: 36px; height: 36px;">
                                <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                            </div>
                            <div>
                                <div class="fw-semibold small"><?php echo e(auth()->user()->name); ?></div>
                                <div class="text-muted small">
                                    <?php echo e(auth()->user()->isSellerOwner() ? 'Owner' : 'Staff'); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    .service-radio-card { cursor: pointer; }
    .service-card-inner {
        transition: all .2s ease;
        border-color: #e5e7eb !important;
    }
    .service-radio-card:hover .service-card-inner {
        border-color: #6366f1 !important;
        background: #f8f9ff;
    }
    .service-radio:checked + .service-card-inner {
        border-color: #6366f1 !important;
        background: #eef2ff;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/seller/products/create.blade.php ENDPATH**/ ?>