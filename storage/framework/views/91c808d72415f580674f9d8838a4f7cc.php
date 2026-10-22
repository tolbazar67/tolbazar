

<?php $__env->startSection('title', 'Create Service'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">

    
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0">Create Service</h3>
            <p class="text-muted mb-0 small">Add a new service category to the platform</p>
        </div>
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

    <form action="<?php echo e(route('admin.services.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

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
                            <label class="form-label fw-semibold">
                                Service Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" value="<?php echo e(old('name')); ?>"
                                   class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="e.g., Restaurant, Travel, Retail"
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
                            <input type="text" name="slug" value="<?php echo e(old('slug')); ?>"
                                   class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="auto-generated from name if empty"
                                   maxlength="255">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i>
                                Leave empty to auto-generate (e.g., "restaurant-1234")
                            </small>
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

                        <div class="mb-3">
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
                                      placeholder="Describe what this service offers..."
                                      maxlength="2000"><?php echo e(old('description')); ?></textarea>
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
                                        <i id="iconPreview" class="<?php echo e(old('icon', 'bi bi-grid')); ?>"></i>
                                    </span>
                                    <input type="text" name="icon" id="iconInput"
                                           value="<?php echo e(old('icon', 'bi bi-grid')); ?>"
                                           class="form-control <?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           placeholder="bi bi-grid">
                                </div>
                                <small class="text-muted">
                                    Bootstrap Icons — e.g., <code>bi bi-cup-hot-fill</code>,
                                    <code>bi bi-airplane-fill</code>
                                </small>
                                <?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger small"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    <?php $__currentLoopData = ['bi bi-cup-hot-fill', 'bi bi-building', 'bi bi-airplane-fill', 'bi bi-graph-up-arrow', 'bi bi-activity', 'bi bi-bag-fill', 'bi bi-heart-pulse', 'bi bi-book-fill']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quickIcon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <button type="button"
                                                class="btn btn-sm btn-light border icon-btn"
                                                data-icon="<?php echo e($quickIcon); ?>"
                                                title="<?php echo e($quickIcon); ?>">
                                            <i class="<?php echo e($quickIcon); ?>"></i>
                                        </button>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Color</label>
                                <div class="input-group">
                                    <input type="color" name="color" id="colorInput"
                                           value="<?php echo e(old('color', '#6366f1')); ?>"
                                           class="form-control form-control-color">
                                    <input type="text" id="colorText"
                                           value="<?php echo e(old('color', '#6366f1')); ?>"
                                           class="form-control" readonly>
                                </div>
                                <small class="text-muted">Theme color for the service card</small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Cover Image</label>
                            <input type="file" name="image"
                                   class="form-control <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   accept="image/*"
                                   onchange="previewImage(this)">
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
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="imagePreviewImg" class="rounded-3 border"
                                     style="max-height: 180px;">
                            </div>
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
                                   value="1"
                                   class="form-check-input"
                                   <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="isActive">
                                Active
                            </label>
                            <div class="small text-muted">Sellers can enable this service</div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="is_featured" id="isFeatured"
                                   value="1"
                                   class="form-check-input"
                                   <?php echo e(old('is_featured') ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="isFeatured">
                                Featured
                            </label>
                            <div class="small text-muted">Show on homepage / highlight</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order"
                                   value="<?php echo e(old('sort_order', 0)); ?>"
                                   class="form-control" min="0">
                            <small class="text-muted">Lower = appears first</small>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Create Service
                        </button>
                        <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-light">
                            Cancel
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/admin/services/create.blade.php ENDPATH**/ ?>