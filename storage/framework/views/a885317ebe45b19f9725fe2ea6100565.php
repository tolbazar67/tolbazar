

<?php $__env->startSection('title', 'My Products'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">📦 Products</h3>
            <p class="text-muted mb-0 small">
                Manage products for <strong><?php echo e($branch->branch_name); ?></strong>
            </p>
        </div>
        <?php if(!$noServicesFlag): ?>
            <a href="<?php echo e(route('seller.products.create', ['branch_id' => $branch->id])); ?>"
               class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Product
            </a>
        <?php endif; ?>
    </div>

    
    <?php if($noServicesFlag): ?>
        <div class="alert alert-warning d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
            <div>
                <strong>No services enabled on this branch.</strong>
                <p class="mb-0 small">Please enable at least one service first.</p>
            </div>
            <a href="<?php echo e(route('seller.services.index', ['branch_id' => $branch->id])); ?>"
               class="btn btn-warning btn-sm ms-auto text-white">
                <i class="bi bi-grid-3x3-gap-fill"></i> Enable Services
            </a>
        </div>
    <?php endif; ?>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-1"></i> <?php echo e(session('success')); ?>

            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo e(session('error')); ?>

            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <?php if($branches->count() > 1): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small fw-semibold">Branch:</span>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('seller.products.index', ['branch_id' => $b->id])); ?>"
                           class="btn btn-sm <?php echo e($b->id == $branch->id ? 'btn-primary' : 'btn-outline-secondary'); ?>">
                            <?php echo e($b->branch_name); ?>

                            <?php if($b->is_default): ?>
                                <span class="badge bg-warning text-dark" style="font-size:.6rem;">HQ</span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(!$noServicesFlag): ?>
        
        <div class="row g-3 mb-3">
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Total</div>
                        <h3 class="fw-bold mb-0"><?php echo e($stats['total']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Active</div>
                        <h3 class="fw-bold mb-0 text-success"><?php echo e($stats['active']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Inactive</div>
                        <h3 class="fw-bold mb-0 text-secondary"><?php echo e($stats['inactive']); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Out of Stock</div>
                        <h3 class="fw-bold mb-0 text-danger"><?php echo e($stats['out_of_stock']); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <input type="hidden" name="branch_id" value="<?php echo e($branch->id); ?>">

                    <div class="col-md-4">
                        <input type="text" name="search"
                               value="<?php echo e(request('search')); ?>"
                               class="form-control"
                               placeholder="Search by name, SKU...">
                    </div>
                    <div class="col-md-3">
                        <select name="service_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Services</option>
                            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s->id); ?>"
                                    <?php echo e(request('service_id') == $s->id ? 'selected' : ''); ?>>
                                    <?php echo e($s->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active"       <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                            <option value="inactive"     <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                            <option value="out_of_stock" <?php echo e(request('status') === 'out_of_stock' ? 'selected' : ''); ?>>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-outline-primary flex-fill">Filter</button>
                        <?php if(request()->hasAny(['search', 'service_id', 'status'])): ?>
                            <a href="<?php echo e(route('seller.products.index', ['branch_id' => $branch->id])); ?>"
                               class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm">
            
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Created By</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if($p->image_url): ?>
                                <img src="<?php echo e($p->image_url); ?>"
                                     class="rounded"
                                     style="width: 42px; height: 42px; object-fit: cover;">
                            <?php endif; ?>
                            <div>
                                <div class="fw-semibold"><?php echo e($p->name); ?></div>
                                <div class="text-muted small"><?php echo e($p->sku); ?></div>
                            </div>
                        </div>
                    </td>
                    
                    <td class="fw-semibold">Rs. <?php echo e(number_format($p->price, 2)); ?></td>
                    <td>
                        <?php if($p->stock_quantity > 0): ?>
                            <span class="badge bg-success-subtle text-success">
                                <?php echo e($p->stock_quantity); ?> in stock
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger">Out of stock</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                 style="width: 28px; height: 28px; font-size: 11px;">
                                <?php echo e(strtoupper(substr($p->created_by_name ?? 'U', 0, 1))); ?>

                            </div>
                            <div class="small">
                                <div class="fw-semibold"><?php echo e($p->created_by_name ?? '—'); ?></div>
                                <div class="text-muted text-capitalize">
                                    <?php echo e($p->created_by_role ?? ''); ?>

                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if($p->is_active): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                        <?php if($p->is_featured): ?>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-star-fill"></i> Featured
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="<?php echo e(route('seller.products.show', $p)); ?>"
                               class="btn btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?php echo e(route('seller.products.edit', $p)); ?>"
                               class="btn btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?php echo e(route('seller.products.toggle-active', $p)); ?>"
                                  method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                <button class="btn btn-outline-<?php echo e($p->is_active ? 'warning' : 'success'); ?>">
                                    <i class="bi <?php echo e($p->is_active ? 'bi-pause' : 'bi-play'); ?>"></i>
                                </button>
                            </form>
                            <form action="<?php echo e(route('seller.products.destroy', $p)); ?>"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this item?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        No items yet.
                        <a href="<?php echo e(route('seller.products.create', ['branch_id' => $branch->id])); ?>">
                            Create your first item
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

            <?php if($products->hasPages()): ?>
                <div class="card-footer bg-white py-3">
                    <?php echo e($products->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/seller/products/index.blade.php ENDPATH**/ ?>