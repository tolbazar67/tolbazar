<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isSoftDeleted() || $user->account_status === 'deletion_pending') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account has been scheduled for deletion.']);
            }

            if ($user->isDeactivated() && !$request->routeIs('account.reactivate*', 'logout')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account is deactivated. Login again to reactivate it.']);
            }
        }

        return $next($request);
    }
}
