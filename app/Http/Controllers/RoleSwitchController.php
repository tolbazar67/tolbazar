<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSwitchController extends Controller
{
    /**
     * Switch between user modes: Customer <-> Seller <-> Delivery <-> Admin
     *
     * Logic:
     * - Owner (sellerProfile + not branch staff) → picks branch or default HQ
     * - Branch Staff (is_branch_staff=1)        → forced to assigned branch
     * - Customer / Delivery / Admin             → normal flow
     */
    public function switchMode(Request $request, $mode)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login to switch modes.');
        }

        $formattedMode = strtolower(trim($mode));

        // Map route-mode → DB role name
        $roleMap = [
            'customer'    => 'Customer',
            'seller'      => 'Seller',
            'delivery'    => 'Delivery Boy',
            'admin'       => 'Admin',
            'super_admin' => 'Super Admin',
        ];

        if (!array_key_exists($formattedMode, $roleMap)) {
            return redirect()->back()->with('error', 'Invalid mode selected.');
        }

        $targetRoleName = $roleMap[$formattedMode];

        // ------------------------------------------------------------------
        // 1. Auto-attach Customer role when switching to customer mode
        // ------------------------------------------------------------------
        if ($formattedMode === 'customer') {
            $customerRole = Role::where('name', 'Customer')->first();
            if ($customerRole && !$user->roles()->where('role_id', $customerRole->id)->exists()) {
                $user->roles()->attach($customerRole->id);
            }
        }

        // ------------------------------------------------------------------
        // 2. Verify role ownership
        // ------------------------------------------------------------------
        if (!$user->roles()->where('name', $targetRoleName)->exists()) {
            return redirect()->back()
                ->with('error', "You do not have access to this mode. Missing role: {$targetRoleName}");
        }

        // ------------------------------------------------------------------
        // 3. Update active mode
        // ------------------------------------------------------------------
        $user->update(['active_mode' => $formattedMode]);

        // ------------------------------------------------------------------
        // 4. Handle SELLER mode (most complex logic)
        // ------------------------------------------------------------------
        if ($formattedMode === 'seller') {
            return $this->enterSellerMode($request, $user);
        }

        // ------------------------------------------------------------------
        // 5. Handle DELIVERY mode
        // ------------------------------------------------------------------
        if ($formattedMode === 'delivery') {
            $this->clearSellerSession();
            return redirect()->route('delivery.dashboard')
                ->with('success', 'Switched to Delivery Mode.');
        }

        // ------------------------------------------------------------------
        // 6. Handle ADMIN / SUPER ADMIN mode
        // ------------------------------------------------------------------
        if (in_array($formattedMode, ['admin', 'super_admin'])) {
            $this->clearSellerSession();
            return redirect()->route('admin.dashboard')
                ->with('success', 'Switched to ' . ucfirst(str_replace('_', ' ', $formattedMode)) . ' Mode.');
        }

        // ------------------------------------------------------------------
        // 7. Default → Customer Mode
        // ------------------------------------------------------------------
        $this->clearSellerSession();

        return redirect()->route('home')->with('success', 'Switched to Customer Mode.');
    }

    /**
     * Enter Seller Mode — decides branch based on user type
     */
    private function enterSellerMode(Request $request, $user)
    {
        // ------------------------------------------------
        // A) BRANCH STAFF → force assigned branch
        // ------------------------------------------------
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

            session(['active_branch_id' => $branchStaff->branch_id]);
            session(['active_branch_role' => $branchStaff->role]); // manager | staff

            return redirect()->route('seller.dashboard')
                ->with('success', 'Switched to Seller Mode (' . ucfirst($branchStaff->role) . ' - ' . $branchStaff->branch->branch_name . ')');
        }

        $sellerProfile = $user->sellerProfile;

        if (!$sellerProfile) {
            // Not registered as seller yet → push to apply form
            $this->clearSellerSession();
            return redirect()->route('seller.apply')
                ->with('error', 'Please register your store first.');
        }

        // ------------------------------------------------
        // B) SELLER OWNER → pick requested or default HQ
        // ------------------------------------------------
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
            // Fallback: default HQ first, else first active branch
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

        session(['active_branch_id' => $branchId]);
        session(['active_branch_role' => 'owner']);

        return redirect()->route('seller.dashboard')
            ->with('success', 'Switched to Seller Mode.');
    }

    /**
     * Clear seller-specific session data when leaving seller mode
     */
    private function clearSellerSession(): void
    {
        session()->forget(['active_branch_id', 'active_branch_role']);
    }
}