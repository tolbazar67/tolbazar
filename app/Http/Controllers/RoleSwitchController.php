<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleSwitchController extends Controller
{
    /**
     * Route mode → canonical role name (must match DB roles.name exactly).
     */
    private const MODE_ROLE_MAP = [
        'customer'    => 'Customer',
        'seller'      => 'Seller',
        'delivery'    => 'Delivery Boy',
        'admin'       => 'Admin',
        'super_admin' => 'Super Admin',
    ];

    /**
     * Roles that lock the account into seller mode.
     * Users with any of these CANNOT switch back to customer mode.
     */
    private const SELLER_LOCKED_ROLES = ['Seller', 'Branch Manager', 'Branch Staff'];

    /**
     * ============================================================
     * SWITCH MODE
     * ============================================================
     * Rules:
     *  - Customer → Seller   : allowed if user has Seller role (via apply)
     *  - Seller   → Customer : BLOCKED (strict separation)
     *  - Seller   → Admin/Delivery : allowed only if role exists
     */
    public function switchMode(Request $request, string $mode)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login to switch modes.');
        }

        $formattedMode = strtolower(trim($mode));

        if (!array_key_exists($formattedMode, self::MODE_ROLE_MAP)) {
            return redirect()->back()->with('error', 'Invalid mode selected.');
        }

        // -----------------------------------------------------------------
        // STRICT SEPARATION — sellers cannot switch to customer mode
        // -----------------------------------------------------------------
        if ($formattedMode === 'customer') {
            $isSeller = $user->roles()
                ->whereIn('name', self::SELLER_LOCKED_ROLES)
                ->exists();

            if ($isSeller) {
                return redirect()->back()
                    ->with('error', 'Seller accounts cannot switch to customer mode. This account is registered as a seller.');
            }
        }

        // -----------------------------------------------------------------
        // Verify the user actually owns the target role
        // -----------------------------------------------------------------
        $targetRoleName = self::MODE_ROLE_MAP[$formattedMode];

        if (!$user->roles()->where('name', $targetRoleName)->exists()) {
            return redirect()->back()
                ->with('error', "You do not have access to this mode. Missing role: {$targetRoleName}");
        }

        // -----------------------------------------------------------------
        // Update active mode
        // -----------------------------------------------------------------
        if ($user->active_mode !== $formattedMode) {
            $user->update(['active_mode' => $formattedMode]);
        }

        // -----------------------------------------------------------------
        // Per-mode routing
        // -----------------------------------------------------------------

        if ($formattedMode === 'seller') {
            return $this->enterSellerMode($request, $user);
        }

        if ($formattedMode === 'delivery') {
            $this->clearSellerSession();

            return redirect()->route('delivery.dashboard')
                ->with('success', 'Switched to Delivery Mode.');
        }

        if (in_array($formattedMode, ['admin', 'super_admin'], true)) {
            $this->clearSellerSession();

            return redirect()->route('admin.dashboard')
                ->with('success', 'Switched to ' . ucfirst(str_replace('_', ' ', $formattedMode)) . ' Mode.');
        }

        // Customer mode (default)
        $this->clearSellerSession();

        return redirect()->route('home')
            ->with('success', 'Switched to Customer Mode.');
    }

    /**
     * ============================================================
     * ENTER SELLER MODE
     * ============================================================
     * Decides which branch the user lands on.
     */
    private function enterSellerMode(Request $request, $user)
    {
        // A) Branch staff → force assigned branch
        if ($user->isBranchStaff()) {
            $branchStaff = $user->branchStaff;

            if (!$branchStaff || !$branchStaff->status) {
                $this->clearSellerSession();
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Your staff account is inactive. Contact store owner.');
            }

            if (!$branchStaff->branch_id || !$branchStaff->branch) {
                $this->clearSellerSession();
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Branch assignment missing. Contact store owner.');
            }

            session([
                'active_branch_id'   => $branchStaff->branch_id,
                'active_branch_role' => $branchStaff->role,
            ]);

            return redirect()->route('seller.dashboard')
                ->with('success', 'Switched to Seller Mode (' . ucfirst($branchStaff->role) . ' - ' . $branchStaff->branch->branch_name . ')');
        }

        $sellerProfile = $user->sellerProfile;

        if (!$sellerProfile) {
            $this->clearSellerSession();

            return redirect()->route('seller.apply')
                ->with('error', 'Please register your store first.');
        }

        // B) Owner → requested branch or default HQ
        $requestedBranchId = $request->input('branch_id');

        if ($requestedBranchId) {
            $validBranch = $sellerProfile->branches()
                ->where('id', $requestedBranchId)
                ->where('status', 1)
                ->first();

            if (!$validBranch) {
                return redirect()->back()->with('error', 'Invalid branch selected.');
            }

            $branchId = $validBranch->id;
        } else {
            $defaultBranch = $sellerProfile->branches()
                ->where('is_default', 1)
                ->where('status', 1)
                ->first();

            $branchId = $defaultBranch?->id
                ?? $sellerProfile->branches()->where('status', 1)->first()?->id;
        }

        if (!$branchId) {
            $this->clearSellerSession();

            return redirect()->route('seller.branches.index')
                ->with('error', 'No active branch found. Please create a branch.');
        }

        session([
            'active_branch_id'   => $branchId,
            'active_branch_role' => 'owner',
        ]);

        return redirect()->route('seller.dashboard')
            ->with('success', 'Switched to Seller Mode.');
    }

    /**
     * Clear seller-specific session data when leaving seller mode.
     */
    private function clearSellerSession(): void
    {
        session()->forget(['active_branch_id', 'active_branch_role']);
    }
}
