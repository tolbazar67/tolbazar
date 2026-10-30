<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In · TolBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; background: linear-gradient(135deg,#e3f2fd 0%,#f5f7fa 100%); display:flex; align-items:center; }
        .card-login { border:0; border-radius:16px; box-shadow:0 10px 40px rgba(0,0,0,.08); }
        .brand-badge { background:#0d6efd; color:#fff; border-radius:50%; width:56px; height:56px; display:inline-flex; align-items:center; justify-content:center; font-size:26px; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-login p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="brand-badge mb-3"><i class="bi bi-shop"></i></div>
                    <h3 class="fw-bold mb-1">Welcome to TolBazar</h3>
                    <p class="text-muted small mb-0">Sign in as a <strong>Customer</strong> or <strong>Delivery Partner</strong></p>
                </div>

                @if (session('success')) <div class="alert alert-success small">{{ session('success') }}</div> @endif
                @if (session('info'))    <div class="alert alert-info small">{{ session('info') }}</div> @endif
                @if ($errors->any())     <div class="alert alert-danger small">{{ $errors->first() }}</div> @endif

                <form method="POST" action="{{ route('login.attempt') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               autocomplete="username" class="form-control form-control-lg" placeholder="you@example.com">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label small fw-semibold">Password</label>
                            <a href="{{ route('password.request') }}" class="small">Forgot?</a>
                        </div>
                        <input type="password" name="password" required autocomplete="current-password"
                               class="form-control form-control-lg" placeholder="••••••••">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label small">Remember me on this device</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                    </button>
                </form>

                @if (Route::has('auth.google'))
                    <div class="text-center my-3 text-muted small">or continue with</div>
                    <a href="{{ route('auth.google') }}" class="btn btn-outline-dark w-100">
                        <i class="bi bi-google me-2"></i> Continue with Google
                    </a>
                @endif

                <hr class="my-4">

                <div class="text-center small">
                    <p class="mb-1">New to TolBazar?
                        <a href="{{ route('register') }}" class="fw-semibold">Create an account</a>
                    </p>
                    <p class="mb-0 text-muted">
                        Want to sell? <a href="{{ route('seller.login') }}">Seller Portal <i class="bi bi-arrow-right"></i></a>
                    </p>
                </div>
            </div>
            <p class="text-center text-muted small mt-3 mb-0">© {{ date('Y') }} TolBazar. All rights reserved.</p>
        </div>
    </div>
</div>
</body>
</html>
