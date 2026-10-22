<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserMode
{
    /**
     * Mode → allowed role names.
     * Seller mode handled separately (needs branch/profile setup).
     */
    private const MODE_ROLES = [
        'customer' => ['Customer'],
        'delivery' => ['Delivery Boy'],
        'admin'    => ['Super Admin', 'Admin'],
    ];

    public function handle(Request $request, Closure $next, string $mode): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Please login to continue.']);
        }

        // ============================================================
        // SELLER MODE — check role first, then branch/profile setup
        // ============================================================
        if ($mode === 'seller') {
            $hasSellerRole = $user->roles()
                ->whereIn('name', ['Seller', 'Branch Manager', 'Branch Staff'])
                ->exists();

            if (!$hasSellerRole) {
                abort(403, 'Seller role required.');
            }

            if (!$user->isSellerOwner() && !$user->isBranchStaff()) {
                abort(403, 'Only seller accounts can access this panel.');
            }

            // ---- Branch Staff / Manager ----
            if ($user->isBranchStaff()) {
                $staff = $user->branchStaff;

                if (!$staff || !$staff->status) {
                    abort(403, 'Your staff account is inactive.');
                }

                // Keep active_mode in sync
                if ($user->active_mode !== 'seller') {
                    $user->update(['active_mode' => 'seller']);
                }

                // Force own branch into session
                session([
                    'active_branch_id'   => $staff->branch_id,
                    'active_branch_role' => $staff->role,
                ]);

                return $next($request);
            }

            // ---- Seller Owner ----
            $seller = $user->sellerProfile;

            if (!$seller) {
                return redirect()->route('seller.apply')
                    ->with('error', 'Please complete store registration.');
            }

            // Keep active_mode in sync
            if ($user->active_mode !== 'seller') {
                $user->update(['active_mode' => 'seller']);
            }

            // Ensure an active branch exists in session
            if (!session()->has('active_branch_id')) {
                $defaultBranch = $seller->branches()
                    ->where('is_default', 1)
                    ->where('status', 1)
                    ->first()
                    ?? $seller->branches()->where('status', 1)->first();

                if ($defaultBranch) {
                    session([
                        'active_branch_id'   => $defaultBranch->id,
                        'active_branch_role' => 'owner',
                    ]);
                } else {
                    return redirect()->route('seller.branches.create')
                        ->with('error', 'Please create at least one branch.');
                }
            }

            return $next($request);
        }

        // ============================================================
        // OTHER MODES (customer / delivery / admin)
        // ============================================================
        if (!isset(self::MODE_ROLES[$mode])) {
            abort(403, 'Unknown mode requested.');
        }

        $hasAccess = $user->roles()
            ->whereIn('name', self::MODE_ROLES[$mode])
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this mode.');
        }

        // Sync active_mode (role is valid, so it's safe to force)
        if ($user->active_mode !== $mode) {
            $user->update(['active_mode' => $mode]);
            session()->forget(['active_branch_id', 'active_branch_role']);
        }

        return $next($request);
    }
}