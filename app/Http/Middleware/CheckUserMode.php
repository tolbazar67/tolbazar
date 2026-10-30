<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserMode
{
    /**
     * Role IDs:
     *  1 = Admin
     *  2 = Super Admin
     *  3 = Customer
     *  4 = Seller
     *  5 = Delivery Boy
     *  6 = Branch Manager
     *  7 = Branch Staff
     */
    private const ROLE_ID = [
        'admin'          => 1,
        'super_admin'    => 2,
        'customer'       => 3,
        'seller'         => 4,
        'delivery_boy'   => 5,
        'branch_manager' => 6,
        'branch_staff'   => 7,
    ];

    /**
     * Mode → allowed role IDs.
     * Seller mode handled separately (needs branch/profile setup).
     */
    private const MODE_ROLE_IDS = [
        'customer' => [3],
        'delivery' => [5],
        'admin'    => [1, 2],
    ];

    /**
     * Seller-related role IDs.
     */
    private const SELLER_ROLE_IDS = [4, 6, 7];

    public function handle(Request $request, Closure $next, string $mode): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Please login to continue.']);
        }

        // ============================================================
        // SELLER MODE
        // ============================================================
        if ($mode === 'seller') {
            $hasSellerRole = $user->roles()
                ->whereIn('roles.id', self::SELLER_ROLE_IDS)
                ->exists();

            if (!$hasSellerRole) {
                abort(403, 'Seller role required.');
            }

            if (!$user->isSellerOwner() && !$user->isBranchStaff()) {
                abort(403, 'Only seller accounts can access this panel.');
            }

            // ---- Branch Staff / Manager (role 6, 7) ----
            if ($user->isBranchStaff()) {
                $staff = $user->branchStaff;

                if (!$staff || !$staff->status) {
                    abort(403, 'Your staff account is inactive.');
                }

                if ($user->active_mode !== 'seller') {
                    $user->update(['active_mode' => 'seller']);
                }

                session([
                    'active_branch_id'   => $staff->branch_id,
                    'active_branch_role' => $staff->role,
                ]);

                return $next($request);
            }

            // ---- Seller Owner (role 4) ----
            $seller = $user->sellerProfile;

            if (!$seller) {
                return redirect()->route('seller.apply')
                    ->with('error', 'Please complete store registration.');
            }

            if ($user->active_mode !== 'seller') {
                $user->update(['active_mode' => 'seller']);
            }

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
        if (!isset(self::MODE_ROLE_IDS[$mode])) {
            abort(403, 'Unknown mode requested.');
        }

        $hasAccess = $user->roles()
            ->whereIn('roles.id', self::MODE_ROLE_IDS[$mode])
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this mode.');
        }

        // ============================================================
        // NEW: STRICT SEPARATION — sellers cannot enter customer mode
        // ============================================================
        if ($mode === 'customer') {
            $isSeller = $user->roles()
                ->whereIn('roles.id', self::SELLER_ROLE_IDS)
                ->exists();

            if ($isSeller) {
                abort(403, 'Seller accounts cannot access customer mode.');
            }
        }

        // ============================================================
        // NEW: Strict active_mode check
        // ============================================================
        if ($user->active_mode !== $mode) {
            abort(403, "You must switch to {$mode} mode first.");
        }

        // Sync active_mode (aba yeha pugdaina kinnaki mathi block gareko chha,
        // tara safety ko lagi rakhya)
        if ($user->active_mode !== $mode) {
            $user->update(['active_mode' => $mode]);
            session()->forget(['active_branch_id', 'active_branch_role']);
        }

        return $next($request);
    }
}
