<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seller Sign In · TolBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; background: linear-gradient(135deg,#fff8e1 0%,#fef3c7 100%); display:flex; align-items:center; }
        .card-login { border:0; border-radius:16px; box-shadow:0 10px 40px rgba(245,158,11,.15); }
        .brand-badge { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; border-radius:50%; width:56px; height:56px; display:inline-flex; align-items:center; justify-content:center; font-size:26px; }
        .btn-seller { background:linear-gradient(135deg,#f59e0b,#d97706); border:none; color:#fff; }
        .btn-seller:hover { background:linear-gradient(135deg,#d97706,#b45309); color:#fff; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-login p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="brand-badge mb-3"><i class="bi bi-shop-window"></i></div>
                    <span class="badge bg-warning-subtle text-warning-emphasis mb-2">SELLER PORTAL</span>
                    <h3 class="fw-bold mb-1">Seller Sign In</h3>
                    <p class="text-muted small mb-0">Access your store, products & orders</p>
                </div>

                <?php if(session('success')): ?> <div class="alert alert-success small"><?php echo e(session('success')); ?></div> <?php endif; ?>
                <?php if(session('info')): ?>    <div class="alert alert-info small"><?php echo e(session('info')); ?></div> <?php endif; ?>
                <?php if($errors->any()): ?>     <div class="alert alert-danger small"><?php echo e($errors->first()); ?></div> <?php endif; ?>

                <form method="POST" action="<?php echo e(route('seller.login.attempt')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Seller Email</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                               autocomplete="username" class="form-control form-control-lg" placeholder="seller@example.com">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label small fw-semibold">Password</label>
                            <a href="<?php echo e(route('password.request')); ?>" class="small text-warning">Forgot?</a>
                        </div>
                        <input type="password" name="password" required autocomplete="current-password"
                               class="form-control form-control-lg" placeholder="••••••••">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label small">Remember me on this device</label>
                    </div>
                    <button type="submit" class="btn btn-seller btn-lg w-100 mb-2">
                        <i class="bi bi-shop me-2"></i> Enter Seller Dashboard
                    </button>
                </form>

                <hr class="my-4">

                <div class="text-center small">
                    <p class="mb-1 text-muted">Not a seller yet?</p>
                    <a href="<?php echo e(route('register')); ?>" class="fw-semibold text-warning">
                        Register & apply to become a seller <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <hr class="my-4">

                <div class="text-center small">
                    <a href="<?php echo e(route('login')); ?>" class="text-muted">
                        <i class="bi bi-arrow-left me-1"></i> Back to Customer Login
                    </a>
                </div>
            </div>
            <p class="text-center text-muted small mt-3 mb-0">© <?php echo e(date('Y')); ?> TolBazar Seller Services</p>
        </div>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/auth/seller-login.blade.php ENDPATH**/ ?>