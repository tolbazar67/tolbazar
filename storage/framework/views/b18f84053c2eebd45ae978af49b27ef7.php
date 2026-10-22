

<?php $__env->startSection('title', 'Seller Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $activeBranchId = session('active_branch_id');
    $activeBranch   = $branches->where('id', $activeBranchId)->first()
                        ?? $branches->where('is_default', 1)->first();
    $activeBranchRole = session('active_branch_role', 'owner');
    $isOwner = auth()->user()->isSellerOwner();
?>




<div class="card border-0 shadow-sm mb-4 overflow-hidden"
     style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
    <div class="card-body p-4 p-md-5 text-white position-relative">
        <div class="row align-items-center">

            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill small">
                        <i class="bi bi-person-badge me-1"></i> <?php echo e(ucfirst($activeBranchRole)); ?> Mode
                    </span>
                    <?php if($activeBranch && $activeBranch->is_default): ?>
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small">
                            <i class="bi bi-star-fill me-1"></i> Headquarters
                        </span>
                    <?php endif; ?>
                </div>

                <h2 class="fw-bold mb-2">
                    Welcome back, <?php echo e(auth()->user()->name); ?>! 👋
                </h2>
                <p class="mb-0 text-white opacity-75">
                    Managing <strong><?php echo e($seller->store_name ?? 'My Store'); ?></strong>
                    <?php if($activeBranch): ?>
                        · Active branch: <strong><?php echo e($activeBranch->branch_name); ?></strong>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('seller.branches.index')); ?>"
                   class="btn btn-light fw-semibold">
                    <i class="bi bi-gear-fill me-1"></i> Manage Store
                </a>
            </div>

        </div>

        
        <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;
                    background:rgba(255,255,255,0.08);border-radius:50%;pointer-events:none;"></div>
    </div>
</div>





<div class="row g-3 mb-4">

    
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#ede9fe;color:#6366f1;">
                    <i class="bi bi-shop-window fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Branches</div>
                    <h3 class="fw-bold mb-0"><?php echo e($branches->count()); ?></h3>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#dbeafe;color:#2563eb;">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Team Members</div>
                    <h3 class="fw-bold mb-0">
                        <?php echo e($seller ? \App\Models\BranchStaff::where('seller_id', $seller->id)->count() : 0); ?>

                    </h3>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#fef3c7;color:#d97706;">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Pending Orders</div>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#dcfce7;color:#16a34a;">
                    <i class="bi bi-currency-rupee fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Earnings</div>
                    <h3 class="fw-bold mb-0">Rs. 0</h3>
                </div>
            </div>
        </div>
    </div>

</div>





<?php if($branches->count() > 0): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-buildings me-1 text-primary"></i> Your Branches
        </h6>
        <?php if($isOwner): ?>
            <a href="<?php echo e(route('seller.branches.create')); ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg"></i> Add Branch
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="row g-0">

            <?php $__currentLoopData = $branches->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 col-lg-4 p-3 border-bottom">
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 h-100"
                         style="background:#f9fafb;">

                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px;height:44px;background:<?php echo e($branch->is_default ? '#fef3c7' : '#e0e7ff'); ?>;color:<?php echo e($branch->is_default ? '#d97706' : '#4f46e5'); ?>;">
                            <i class="bi bi-<?php echo e($branch->is_default ? 'star-fill' : 'shop'); ?> fs-5"></i>
                        </div>

                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <strong class="text-truncate"><?php echo e($branch->branch_name); ?></strong>
                                <?php if($branch->is_default): ?>
                                    <span class="badge bg-warning text-dark" style="font-size:.65rem;">HQ</span>
                                <?php endif; ?>
                            </div>
                            <div class="small text-muted text-truncate">
                                <i class="bi bi-geo-alt me-1"></i> <?php echo e($branch->address); ?>

                            </div>
                            <div class="small text-muted mt-1">
                                <i class="bi bi-people me-1"></i>
                                <?php echo e($branch->staff()->count()); ?> staff
                                <?php if($branch->manager): ?>
                                    · Manager: <?php echo e($branch->manager->name); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </div>

        <?php if($branches->count() > 6): ?>
            <div class="text-center py-3 border-top">
                <a href="<?php echo e(route('seller.branches.index')); ?>" class="text-decoration-none small fw-semibold">
                    View all <?php echo e($branches->count()); ?> branches <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>





<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5 text-center">
        <div class="mb-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                 style="width:70px;height:70px;background:#e0e7ff;color:#4f46e5;">
                <i class="bi bi-diagram-3-fill fs-2"></i>
            </div>
        </div>

        <h4 class="fw-bold mb-2">Multi-Branch Outlets Control Panel</h4>
        <p class="text-muted mb-4 mx-auto" style="max-width:560px;">
            You have currently configured
            <strong><?php echo e($branches->count()); ?></strong> branch location(s)
            under <strong><?php echo e($seller->store_name); ?></strong>.
            Manage your outlets, add managers, and control staff — all from one place.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="<?php echo e(route('seller.branches.index')); ?>" class="btn btn-primary px-4">
                <i class="bi bi-list-check me-1"></i> Branch Management Center
            </a>
            <a href="<?php echo e(route('seller.staff.index')); ?>" class="btn btn-outline-primary px-4">
                <i class="bi bi-people me-1"></i> Staff Management
            </a>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/seller/dashboard.blade.php ENDPATH**/ ?>