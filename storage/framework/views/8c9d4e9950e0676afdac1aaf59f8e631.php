

<?php $__env->startSection('title', 'Staff Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $user = auth()->user();
    $branchStaff = $user->branchStaff;
    $branch = $branchStaff->branch;
?>




<div class="card border-0 shadow-sm mb-4 overflow-hidden"
     style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
    <div class="card-body p-4 p-md-5 text-white position-relative">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill small">
                        <i class="bi bi-person-badge-fill me-1"></i> Staff Member
                    </span>
                </div>
                <h2 class="fw-bold mb-2">Hi, <?php echo e($user->name); ?>! 👋</h2>
                <p class="mb-0 text-white opacity-75">
                    You're part of <strong><?php echo e($branch->branch_name); ?></strong>
                    at <strong><?php echo e($branch->seller->store_name); ?></strong>
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-white text-success px-3 py-2" style="font-size:.85rem;">
                    <i class="bi bi-check-circle-fill me-1"></i> Active
                </span>
            </div>
        </div>
        <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;
                    background:rgba(255,255,255,0.1);border-radius:50%;pointer-events:none;"></div>
    </div>
</div>





<div class="row g-3 mb-4">

    <div class="col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#fef3c7;color:#d97706;">
                    <i class="bi bi-clipboard-check fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">My Tasks Today</div>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#dbeafe;color:#2563eb;">
                    <i class="bi bi-box-seam fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Products Assigned</div>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#ede9fe;color:#6366f1;">
                    <i class="bi bi-check-circle fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Completed</div>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>

</div>





<div class="row g-3">

    
    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-geo-alt-fill text-success me-1"></i> My Branch
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Branch</div>
                        <div class="fw-semibold"><?php echo e($branch->branch_name); ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Code</div>
                        <span class="badge bg-secondary"><?php echo e($branch->branch_code); ?></span>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Phone</div>
                        <div class="fw-semibold">
                            <i class="bi bi-telephone me-1"></i> <?php echo e($branch->phone); ?>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Email</div>
                        <div class="fw-semibold">
                            <i class="bi bi-envelope me-1"></i> <?php echo e($branch->email ?? '—'); ?>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-muted small mb-1">Address</div>
                        <div class="fw-semibold">
                            <i class="bi bi-geo-alt me-1"></i> <?php echo e($branch->address); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-person-badge-fill text-primary me-1"></i> My Manager
                </h6>
            </div>
            <div class="card-body">
                <?php if($branch->manager): ?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:56px;height:56px;background:#e0e7ff;color:#4f46e5;font-weight:700;font-size:1.3rem;">
                            <?php echo e(strtoupper(substr($branch->manager->name, 0, 1))); ?>

                        </div>
                        <div>
                            <div class="fw-bold"><?php echo e($branch->manager->name); ?></div>
                            <div class="small text-muted"><?php echo e($branch->manager->email); ?></div>
                        </div>
                    </div>
                    <div class="small text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Contact your manager for any branch-related issues.
                    </div>
                <?php else: ?>
                    <div class="text-muted">No manager assigned yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/seller/staff-dashboard.blade.php ENDPATH**/ ?>