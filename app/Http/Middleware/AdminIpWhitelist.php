<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminIpWhitelist
{
    /**
     * @param  string  $scope  'login' (always enforce) or 'panel' (enforce only if ADMIN_PANEL_IP_ENFORCE=true)
     */
    public function handle(Request $request, Closure $next, string $scope = 'login'): Response
    {
        // Master switch
        if (! config('auth.admin_ip_whitelist_enabled', true)) {
            return $next($request);
        }

        // Panel routes can be enforced separately (default: off — so admins don't
        // get locked out mid-session if their IP changes, e.g. VPN drop)
        if ($scope === 'panel' && ! config('auth.admin_panel_ip_enforce', false)) {
            return $next($request);
        }

        $allowed = config('auth.admin_allowed_ips', []);

        // Fail-safe: whitelist enabled but no IPs → deny everything
        if (empty($allowed)) {
            Log::warning('Admin IP whitelist enabled but ADMIN_ALLOWED_IPS empty — denying all', [
                'ip'    => $request->ip(),
                'scope' => $scope,
                'url'   => $request->fullUrl(),
            ]);
            abort(404);
        }

        if (! in_array($request->ip(), $allowed, true)) {
            Log::warning('Admin IP blocked', [
                'ip'    => $request->ip(),
                'scope' => $scope,
                'url'   => $request->fullUrl(),
                'ua'    => $request->userAgent(),
            ]);
            abort(404); // 404 not 403 → hide that this URL is a login page
        }

        return $next($request);
    }
}
