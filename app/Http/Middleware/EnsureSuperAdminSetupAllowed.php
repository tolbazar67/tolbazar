<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdminSetupAllowed
{
    public function handle(Request $request, Closure $next): Response
    {
        // LAYER 1: Kill switch
        if (!config('app.super_admin.setup_enabled')) {
            abort(404);
        }

        // LAYER 2: Lock file
        $lockFile = config('app.super_admin.lock_file');
        if ($lockFile && file_exists($lockFile)) {
            abort(404);
        }

        // LAYER 3: Super admin must NOT exist
        if ($this->superAdminExists()) {
            abort(404);
        }

        // LAYER 4: IP whitelist
        $allowedIps = config('app.super_admin.setup_ips', []);
        if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps, true)) {
            Log::warning('SuperAdmin setup: IP blocked', [
                'ip' => $request->ip(),
                'ua' => $request->userAgent(),
            ]);
            abort(404);
        }

        // LAYER 5: Auth required
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login first before super admin setup.');
        }

        // LAYER 6: Rate limit
        $rateKey  = 'superadmin_setup:' . $request->ip();
        $attempts = Cache::get($rateKey, 0);

        if ($attempts >= 3) {
            Log::warning('SuperAdmin setup: Rate limited', ['ip' => $request->ip()]);
            abort(429, 'Too many attempts. Try again in 5 minutes.');
        }

        return $next($request);
    }

    private function superAdminExists(): bool
    {
        $role = Role::where('id', 1)->first()
            ?? Role::where('name', 'Super Admin')->first();

        if (!$role) return false;

        return User::whereHas('roles', function ($q) use ($role) {
            $q->where('roles.id', $role->id);
        })->exists();
    }
}