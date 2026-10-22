<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerRole
{
    /**
     * Roles allowed to access the seller panel.
     * Using names (not IDs) so it survives DB re-seeds / imports.
     */
    private const ALLOWED_ROLES = ['Seller', 'Branch Manager', 'Branch Staff'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Must be in seller mode
        if ($user->active_mode !== 'seller') {
            abort(403, 'You must be in seller mode.');
        }

        // Must have a seller-type role
        $hasSellerRole = $user->roles()
            ->whereIn('name', self::ALLOWED_ROLES)
            ->exists();

        if (!$hasSellerRole) {
            abort(403, 'Seller role required.');
        }

        // Must actually be owner or branch staff
        if (!$user->isSellerOwner() && !$user->isBranchStaff()) {
            abort(403, 'Only seller accounts can access this panel.');
        }

        return $next($request);
    }
}