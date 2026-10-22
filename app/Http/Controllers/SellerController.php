<?php

namespace App\Http\Controllers;

use App\Models\BranchStaff;
use App\Models\Role;
use App\Models\Seller;
use App\Models\SellerBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SellerController extends Controller
{
    /**
     * ============================================================
     * SHOW APPLY FORM — behavior:
     *   1. Branch Staff (manager/staff)  → dashboard
     *   2. Owner (sellerProfile exists)  → dashboard
     *   3. New customer                  → apply form
     * ============================================================
     */
    public function showApplyForm()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // ------------------------------------------------------------
        // 1. Branch Staff (manager / staff) → dashboard
        // ------------------------------------------------------------
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
            $user->update(['active_mode' => 'seller']);

            return redirect()->route('seller.dashboard')
                ->with('info', 'You are a branch ' . ucfirst($staff->role) . '. Manage your branch from the dashboard.');
        }

        // ------------------------------------------------------------
        // 2. Owner → dashboard
        // ------------------------------------------------------------
        if ($user->sellerProfile) {
            $user->update(['active_mode' => 'seller']);
            return redirect()->route('seller.dashboard');
        }

        // ------------------------------------------------------------
        // 3. New customer → apply form
        // ------------------------------------------------------------
        return view('seller.apply');
    }

    /**
     * ============================================================
     * STORE APPLY — register new seller + HQ branch
     * ============================================================
     */
    public function storeApply(Request $request)
    {
        $user = auth()->user();

        // Staff ले seller register गर्न पाउँदैन
        if ($user->isBranchStaff()) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'You are a branch staff and cannot register as a seller.');
        }

        // पहिल्यै seller भइसकेको छ भने
        if ($user->sellerProfile) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'You already have a store registered.');
        }

        $request->validate([
            'store_name'      => 'required|string|max:255|unique:sellers,store_name',
            'phone'           => 'required|string|max:20',
            'address'         => 'required|string|max:255',
            'pan_vat_number'  => 'nullable|string|max:50',
            'branch_name'     => 'required|string|max:255',
            'branch_phone'    => 'required|string|max:20',
            'branch_address'  => 'required|string|max:255',
            'branch_email'    => 'nullable|email|max:255',
        ]);

        DB::beginTransaction();

        try {
            $seller = Seller::create([
                'user_id'         => $user->id,
                'store_name'      => $request->store_name,
                'slug'            => Str::slug($request->store_name) . '-' . rand(1000, 9999),
                'phone'           => $request->phone,
                'address'         => $request->address,
                'pan_vat_number'  => $request->pan_vat_number,
                'status'          => 1,
            ]);

            $hqBranch = SellerBranch::create([
                'seller_id'   => $seller->id,
                'branch_name' => $request->branch_name,
                'branch_code' => 'HQ-' . strtoupper(Str::random(6)),
                'phone'       => $request->branch_phone,
                'email'       => $request->branch_email ?? $user->email,
                'address'     => $request->branch_address,
                'is_default'  => 1,
                'manager_id'  => $user->id,
                'status'      => 1,
            ]);

            $sellerRole = Role::where('name', 'Seller')->first();
            if ($sellerRole && !$user->roles()->where('role_id', $sellerRole->id)->exists()) {
                $user->roles()->attach($sellerRole->id);
            }

            $user->update(['active_mode' => 'seller']);
            session([
                'active_branch_id'   => $hqBranch->id,
                'active_branch_role' => 'owner',
            ]);

            DB::commit();

            return redirect()->route('seller.dashboard')
                ->with('success', 'Store and HQ branch registered successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * DASHBOARD — renders the right view for owner / manager / staff
     * ============================================================
     */
    public function dashboard()
    {
        $user = auth()->user();

        // ------------------------------------------------------------
        // 1. Branch Staff (manager / staff)
        // ------------------------------------------------------------
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

            if ($staff->role === 'manager') {
                return view('seller.manager-dashboard');
            }

            return view('seller.staff-dashboard');
        }

        // ------------------------------------------------------------
        // 2. Owner
        // ------------------------------------------------------------
        $seller = $user->sellerProfile;

        if (!$seller) {
            return redirect()->route('seller.apply')
                ->with('error', 'Please register your store first.');
        }

        $activeBranchRole = session('active_branch_role', 'owner');
        $activeBranchId   = session('active_branch_id');

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

        if (session('active_branch_id') != $activeBranch->id) {
            session(['active_branch_id' => $activeBranch->id]);
        }
        if (session('active_branch_role') !== 'owner') {
            session(['active_branch_role' => 'owner']);
        }

        $stats = [
            'branches_count' => $branches->count(),
            'staff_count'    => BranchStaff::where('seller_id', $seller->id)->where('status', 1)->count(),
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
            'seller', 'branches', 'activeBranch', 'activeBranchRole',
            'stats', 'team', 'recentActivity'
        ));
    }
}