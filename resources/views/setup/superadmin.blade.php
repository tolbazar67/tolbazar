<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>System Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, sans-serif;
            padding: 20px;
        }
        .setup-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            max-width: 540px;
            width: 100%;
            overflow: hidden;
        }
        .setup-header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: #fff;
            padding: 28px;
            text-align: center;
        }
        .setup-header i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 8px;
        }
        .setup-body { padding: 28px; }
        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

<div class="setup-card">

    <div class="setup-header">
        <i class="bi bi-shield-lock-fill"></i>
        <h4 class="fw-bold mb-1">System Setup</h4>
        <p class="mb-0 opacity-75 small">Authorized personnel only</p>
    </div>

    <div class="setup-body">

        <div class="security-badge">
            <i class="bi bi-lock-fill"></i>
            Hardened • Audit Logged • One-Time
        </div>

        @if($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-warning small">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            This page will permanently disable after creation. Do not share the token.
        </div>

        <form method="POST" action="{{ route('superadmin.setup.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold small">Setup Token *</label>
                <input type="password" name="setup_token" class="form-control form-control-sm"
                       required autocomplete="off" placeholder="Paste the secret token">
                <div class="form-text">From .env — never share.</div>
            </div>

            <hr class="my-3">

            <div class="mb-3">
                <label class="form-label fw-semibold small">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="form-control form-control-sm" required maxlength="255">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="form-control form-control-sm" required maxlength="255">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Password *</label>
                <input type="password" name="password" class="form-control form-control-sm"
                       required minlength="12">
                <div class="form-text">Min 12 chars, must include upper, lower, number, symbol.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Confirm Password *</label>
                <input type="password" name="password_confirmation"
                       class="form-control form-control-sm" required minlength="12">
            </div>

            <button type="submit" class="btn btn-danger w-100 py-2 fw-semibold">
                <i class="bi bi-shield-check me-1"></i> Create Super Admin
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">Attempts are rate-limited. All actions logged.</small>
        </div>

    </div>
</div>

</body>
</html>