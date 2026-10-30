<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <meta name="referrer" content="no-referrer">
    <title>Secure Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { min-height:100vh; background: radial-gradient(circle at 30% 20%,#1e293b 0%,#0f172a 60%,#020617 100%); display:flex; align-items:center; color:#e2e8f0; }
        .card-login { border:1px solid #1e293b; border-radius:14px; background:rgba(15,23,42,.85); backdrop-filter:blur(10px); box-shadow:0 20px 60px rgba(0,0,0,.5); }
        .brand-badge { background:linear-gradient(135deg,#ef4444,#b91c1c); color:#fff; border-radius:50%; width:56px; height:56px; display:inline-flex; align-items:center; justify-content:center; font-size:26px; box-shadow:0 0 20px rgba(239,68,68,.4); }
        .form-control { background:#1e293b; border-color:#334155; color:#e2e8f0; }
        .form-control:focus { background:#1e293b; border-color:#ef4444; color:#e2e8f0; box-shadow:0 0 0 .2rem rgba(239,68,68,.25); }
        .form-control::placeholder { color:#64748b; }
        .form-label { color:#cbd5e1; }
        .btn-admin { background:linear-gradient(135deg,#ef4444,#b91c1c); border:none; color:#fff; }
        .btn-admin:hover { background:linear-gradient(135deg,#dc2626,#991b1b); color:#fff; }
        .security-note { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); color:#fca5a5; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card card-login p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="brand-badge mb-3"><i class="bi bi-shield-lock-fill"></i></div>
                    <h4 class="fw-bold mb-1 text-white">Administrator Access</h4>
                    <p class="small mb-0" style="color:#94a3b8;">Restricted · Authorized personnel only</p>
                </div>

                @if (session('success')) <div class="alert alert-success small">{{ session('success') }}</div> @endif
                @if ($errors->any())
                    <div class="alert alert-danger small">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('admin.auth.login.attempt') }}" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Administrator Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               autocomplete="off" class="form-control form-control-lg" placeholder="admin@company.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password</label>
                        <input type="password" name="password" required autocomplete="off"
                               class="form-control form-control-lg" placeholder="••••••••">
                    </div>
                    <div class="form-check mb-4">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label small" style="color:#94a3b8;">Trust this device</label>
                    </div>
                    <button type="submit" class="btn btn-admin btn-lg w-100">
                        <i class="bi bi-lock-fill me-2"></i> Secure Sign In
                    </button>
                </form>

                <div class="security-note rounded p-3 mt-4 small">
                    <i class="bi bi-shield-exclamation me-1"></i>
                    <strong>Security Notice:</strong> All access attempts are logged.
                    Unauthorized access is prohibited and may be reported.
                </div>

                <p class="text-center small mt-3 mb-0" style="color:#64748b;">
                    Forgot password? Contact your Super Admin for recovery.
                </p>
            </div>
            <p class="text-center small mt-3 mb-0" style="color:#475569;">
                TolBazar Control Panel · v{{ config('app.version', '1.0') }}
            </p>
        </div>
    </div>
</div>
</body>
</html>
