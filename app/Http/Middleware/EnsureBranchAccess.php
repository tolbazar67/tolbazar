<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureBranchAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // ============================================================
        // 0. Not logged in → login
        // ============================================================
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login to continue.');
        }

        // ============================================================
        // 1. BRANCH STAFF (Manager / Staff) — MUST be checked FIRST
        //    किनभने staff सँग sellerProfile हुँदैन।
        // ============================================================
        if ($user->isBranchStaff()) {
            $staff = $user->branchStaff;

            // Missing or inactive staff record
            if (!$staff || !$staff->status) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Your staff account is inactive. Contact store owner.');
            }

            if (!$staff->branch_id || !$staff->branch) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Branch assignment missing. Contact store owner.');
            }

            // Force branch into session (security)
            session([
                'active_branch_id'   => $staff->branch_id,
                'active_branch_role' => $staff->role,
            ]);

            // ------------------------------------------------------
            // Block staff from accessing seller apply/store routes
            // (staff should NEVER see "Become a Seller")
            // ------------------------------------------------------
            if ($request->routeIs('seller.apply') || $request->routeIs('seller.store')) {
                return redirect()->route('seller.dashboard')
                    ->with('info', 'You are a branch staff member. Manage your branch from the dashboard.');
            }

            // ------------------------------------------------------
            // Block creating branches
            // ------------------------------------------------------
            if ($request->routeIs('seller.branches.create') || $request->routeIs('seller.branches.store')) {
                return redirect()->route('seller.dashboard')
                    ->with('error', 'Only store owner can create branches.');
            }

            // ------------------------------------------------------
            // Block deleting branches
            // ------------------------------------------------------
            if ($request->routeIs('seller.branches.destroy')) {
                abort(403, 'Only store owner can delete branches.');
            }

            // ------------------------------------------------------
            // "staff" role (non-manager) cannot add/remove staff
            // ------------------------------------------------------
            if ($staff->role === 'staff') {
                if ($request->routeIs('seller.staff.store') || $request->routeIs('seller.staff.destroy')) {
                    return redirect()->route('seller.staff.index')
                        ->with('error', 'Only managers can add or remove team members.');
                }
            }

            return $next($request);
        }

        // ============================================================
        // 2. OWNER — must have seller profile
        // ============================================================
        if (!$user->sellerProfile) {
            return redirect()->route('seller.apply')
                ->with('error', 'Seller profile required. Please register your store.');
        }

        // ============================================================
        // 3. OWNER — ensure active branch exists in session
        // ============================================================
        if (!session()->has('active_branch_id')) {
            $seller = $user->sellerProfile;

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
            }
        }

        return $next($request);
    }
}