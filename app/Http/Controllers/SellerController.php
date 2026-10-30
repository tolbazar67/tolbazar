<?php

namespace App\Http\Controllers;

use App\Models\BranchStaff;
use App\Models\Role;
use App\Models\Seller;
use App\Models\SellerBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SellerController extends Controller
{
    /**
     * Canonical role names — never change in DB.
     * Constants avoid typos and magic strings.
     */
    private const ROLE_SELLER   = 'Seller';
    private const ROLE_CUSTOMER = 'Customer';

    /**
     * ============================================================
     * SHOW APPLY FORM
     * ============================================================
     * 1. Branch staff (manager/staff)   → seller dashboard
     * 2. Owner with existing profile    → seller dashboard
     * 3. New customer                   → seller apply form
     */
    public function showApplyForm()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Branch staff → seller dashboard
        if ($user->isBranchStaff()) {
            $staff = $user->branchStaff;

            if (!$staff || !$staff->status) {
                auth()->logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Your staff account is inactive.');
            }

            session([
                'active_branch_id'   => $staff->branch_id,
                'active_branch_role' => $staff->role,
            ]);

            if ($user->active_mode !== 'seller') {
                $user->update(['active_mode' => 'seller']);
            }

            return redirect()->route('seller.dashboard')
                ->with('info', 'You are a branch ' . ucfirst($staff->role) . '. Manage your branch from the dashboard.');
        }

        // 2. Owner with existing profile → dashboard
        if ($user->sellerProfile) {
            if ($user->active_mode !== 'seller') {
                $user->update(['active_mode' => 'seller']);
            }

            return redirect()->route('seller.dashboard');
        }

        // 3. New customer → apply form
        return view('seller.apply');
    }

    /**
     * ============================================================
     * STORE APPLY
     * ============================================================
     * Registers a new seller + HQ branch.
     *
     * STRICT SEPARATION:
     * Removes the Customer role so the account permanently
     * becomes a Seller. Seller cannot switch back to customer.
     */
    public function storeApply(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Staff cannot register as seller
        if ($user->isBranchStaff()) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'You are a branch staff and cannot register as a seller.');
        }

        // Already a seller
        if ($user->sellerProfile) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'You already have a store registered.');
        }

        $validated = $request->validate([
            'store_name'     => 'required|string|max:255|unique:sellers,store_name',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:255',
            'pan_vat_number' => 'nullable|string|max:50',
            'branch_name'    => 'required|string|max:255',
            'branch_phone'   => 'required|string|max:20',
            'branch_address' => 'required|string|max:255',
            'branch_email'   => 'nullable|email|max:255',
        ]);

        DB::beginTransaction();

        try {
            // 1. Create seller profile
            $seller = Seller::create([
                'user_id'        => $user->id,
                'store_name'     => $validated['store_name'],
                'slug'           => Str::slug($validated['store_name']) . '-' . rand(1000, 9999),
                'phone'          => $validated['phone'],
                'address'        => $validated['address'],
                'pan_vat_number' => $validated['pan_vat_number'] ?? null,
                'status'         => 1,
            ]);

            // 2. Create HQ branch (default)
            $hqBranch = SellerBranch::create([
                'seller_id'   => $seller->id,
                'branch_name' => $validated['branch_name'],
                'branch_code' => 'HQ-' . strtoupper(Str::random(6)),
                'phone'       => $validated['branch_phone'],
                'email'       => $validated['branch_email'] ?? $user->email,
                'address'     => $validated['branch_address'],
                'is_default'  => 1,
                'manager_id'  => $user->id,
                'status'      => 1,
            ]);

            // 3. Assign Seller role
            $sellerRole = Role::where('name', self::ROLE_SELLER)->first();
            if ($sellerRole && !$user->roles()->where('roles.id', $sellerRole->id)->exists()) {
                $user->roles()->attach($sellerRole->id);
            }

            // 4. STRICT SEPARATION — remove Customer role
            $customerRole = Role::where('name', self::ROLE_CUSTOMER)->first();
            if ($customerRole) {
                $user->roles()->detach($customerRole->id);
            }

            // 5. Switch account to seller mode
            $user->update(['active_mode' => 'seller']);
            session([
                'active_branch_id'   => $hqBranch->id,
                'active_branch_role' => 'owner',
            ]);

            DB::commit();

            Log::info('Seller registered', [
                'user_id'   => $user->id,
                'seller_id' => $seller->id,
                'branch_id' => $hqBranch->id,
            ]);

            return redirect()->route('seller.dashboard')
                ->with('success', 'Store and HQ branch registered successfully!');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Seller registration failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * DASHBOARD
     * ============================================================
     * Renders the correct view for owner / manager / staff.
     */
    public function dashboard()
    {
        $user = auth()->user();

        // 1. Branch staff
        if ($user->isBranchStaff()) {
            $staff = $user->branchStaff;

            if (!$staff || !$staff->branch) {
                auth()->logout();
                return redirect()->route('login')
                    ->with('error', 'Branch assignment missing. Contact store owner.');
            }

            if (!$staff->status) {
                auth()->logout();
                return redirect()->route('login')
                    ->with('error', 'Your staff account is inactive.');
            }

            session([
                'active_branch_id'   => $staff->branch_id,
                'active_branch_role' => $staff->role,
            ]);

            return $staff->role === 'manager'
                ? view('seller.manager-dashboard')
                : view('seller.staff-dashboard');
        }

        // 2. Owner
        $seller = $user->sellerProfile;

        if (!$seller) {
            return redirect()->route('seller.apply')
                ->with('error', 'Please register your store first.');
        }

        $activeBranchId = session('active_branch_id');

        $branches = $seller->branches()
            ->where('status', 1)
            ->orderByDesc('is_default')
            ->orderBy('branch_name')
            ->get();

        $activeBranch = $branches->firstWhere('id', $activeBranchId)
            ?? $branches->firstWhere('is_default', 1)
            ?? $branches->first();

        if (!$activeBranch) {
            return redirect()->route('seller.branches.index')
                ->with('error', 'No active branch found. Please create one.');
        }

        // Ensure session reflects current active branch
        if ((int) session('active_branch_id') !== (int) $activeBranch->id) {
            session(['active_branch_id' => $activeBranch->id]);
        }
        if (session('active_branch_role') !== 'owner') {
            session(['active_branch_role' => 'owner']);
        }

        $activeBranchRole = 'owner';

        $stats = [
            'branches_count' => $branches->count(),
            'staff_count'    => BranchStaff::where('seller_id', $seller->id)
                                    ->where('status', 1)
                                    ->count(),
            'orders_pending' => 0,
            'total_earnings' => 0,
            'products_count' => 0,
            'today_orders'   => 0,
        ];

        $team = BranchStaff::with('user')
            ->where('branch_id', $activeBranch->id)
            ->where('status', 1)
            ->orderByDesc('role')
            ->limit(10)
            ->get();

        $recentActivity = collect();

        return view('seller.dashboard', compact(
            'seller',
            'branches',
            'activeBranch',
            'activeBranchRole',
            'stats',
            'team',
            'recentActivity'
        ));
    }
}
