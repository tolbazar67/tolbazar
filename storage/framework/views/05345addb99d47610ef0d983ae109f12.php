

<?php $__env->startSection('title', 'Edit Service'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">

    
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h3 class="fw-bold mb-0">Edit Service</h3>
            <p class="text-muted mb-0 small">Update service details</p>
        </div>
        <span class="badge <?php echo e($service->is_active ? 'bg-success' : 'bg-secondary'); ?>">
            <?php echo e($service->is_active ? 'Active' : 'Inactive'); ?>

        </span>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.services.update', $service)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="row g-4">

            
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
                            <input type="text" name="name" value="<?php echo e(old('name', $service->name)); ?>"
                                   class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   required maxlength="255">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug</label>
                            <input type="text" name="slug" value="<?php echo e(old('slug', $service->slug)); ?>"
                                   class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   maxlength="255">
                            <small class="text-muted">Used in URLs. Must be unique.</small>
                            <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="4"
                                      class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      maxlength="2000"><?php echo e(old('description', $service->description)); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                        <i id="iconPreview" class="<?php echo e(old('icon', $service->icon ?? 'bi bi-grid')); ?>"></i>
                                    </span>
                                    <input type="text" name="icon" id="iconInput"
                                           value="<?php echo e(old('icon', $service->icon)); ?>"
                                           class="form-control"
                                           placeholder="bi bi-grid">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Color</label>
                                <div class="input-group">
                                    <input type="color" name="color" id="colorInput"
                                           value="<?php echo e(old('color', $service->color ?? '#6366f1')); ?>"
                                           class="form-control form-control-color">
                                    <input type="text" id="colorText"
                                           value="<?php echo e(old('color', $service->color ?? '#6366f1')); ?>"
                                           class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Cover Image</label>

                            <?php if($service->image): ?>
                                <div class="mb-2 d-flex align-items-center gap-3 p-2 border rounded">
                                    <img src="<?php echo e(asset('storage/' . $service->image)); ?>"
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
                            <?php endif; ?>

                            <input type="file" name="image"
                                   class="form-control <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   accept="image/*">
                            <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

            </div>

            
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
                                   <?php echo e(old('is_active', $service->is_active) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="isActive">Active</label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="is_featured" id="isFeatured"
                                   value="1" class="form-check-input"
                                   <?php echo e(old('is_featured', $service->is_featured) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="isFeatured">Featured</label>
                        </div>

                        <div>
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order"
                                   value="<?php echo e(old('sort_order', $service->sort_order)); ?>"
                                   class="form-control" min="0">
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body small text-muted">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Created:</span>
                            <strong><?php echo e($service->created_at->format('M d, Y')); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Updated:</span>
                            <strong><?php echo e($service->updated_at->diffForHumans()); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Products:</span>
                            <strong><?php echo e($service->products()->count()); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Branches:</span>
                            <strong><?php echo e($service->branches()->count()); ?></strong>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Save Changes
                        </button>
                        <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-light">
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

    <form id="deleteForm" action="<?php echo e(route('admin.services.destroy', $service)); ?>"
          method="POST" style="display: none;">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.getElementById('iconInput').addEventListener('input', function () {
        document.getElementById('iconPreview').className = this.value || 'bi bi-grid';
    });

    document.getElementById('colorInput').addEventListener('input', function () {
        document.getElementById('colorText').value = this.value;
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/admin/services/edit.blade.php ENDPATH**/ ?>