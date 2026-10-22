<?php

namespace App\Http\Controllers;

use App\Models\BranchStaff;
use App\Models\Role;
use App\Models\SellerBranch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BranchStaffController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $sellerId = $user->isBranchStaff()
            ? $user->branchStaff?->seller_id
            : $user->sellerProfile?->id;

        if (!$sellerId) {
            return redirect()->route('seller.apply')
                ->with('error', 'Seller profile required.');
        }

        $branchId = $request->query('branch_id');

        if ($user->isBranchStaff()) {
            $branchId = $user->branchStaff?->branch_id;
        }

        $query = BranchStaff::with(['branch', 'user.roles'])
            ->where('seller_id', $sellerId);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $staff    = $query->latest()->get();
        $branches = $user->getAccessibleBranches();

        return view('seller.staff.index', compact('staff', 'branches', 'branchId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->canManageStaff()) {
            return back()->with('error', 'You do not have permission to add staff.');
        }

        $request->validate([
            'branch_id' => 'required|exists:seller_branches,id',
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'nullable|string|max:20',
            'password'  => 'required|string|min:8',
            'role'      => 'required|in:manager,staff',
        ]);

        $branch = $this->authorizedBranch($request->branch_id);

        DB::beginTransaction();

        try {
            // 1. Create user
            $newUser = User::create([
                'name'              => $request->name,
                'username'          => $this->generateUsername($request->name),
                'email'             => $request->email,
                'phone'             => $request->phone,
                'password'          => Hash::make($request->password),
                'email_verified_at' => now(),
                'active_mode'       => 'seller',
                'is_branch_staff'   => 1,
                'staff_owner_id'    => $branch->seller->user_id,
            ]);

            // 2. Attach Seller (4)
            $sellerRole = Role::where('name', 'Seller')->first();
            if ($sellerRole && !$newUser->roles()->where('role_id', $sellerRole->id)->exists()) {
                $newUser->roles()->attach($sellerRole->id);
            }

            // 3. Attach Manager (6) / Staff (7)
            $roleName = $request->role === 'manager' ? 'Branch Manager' : 'Branch Staff';
            $level    = $request->role === 'manager' ? 6 : 7;

            $specificRole = Role::firstOrCreate(
                ['name' => $roleName],
                ['level' => $level]
            );

            if (!$newUser->roles()->where('role_id', $specificRole->id)->exists()) {
                $newUser->roles()->attach($specificRole->id);
            }

            // 4. Create branch staff record
            BranchStaff::create([
                'seller_id'  => $branch->seller_id,
                'branch_id'  => $branch->id,
                'user_id'    => $newUser->id,
                'role'       => $request->role,
                'status'     => 1,
                'created_by' => $user->id,
            ]);

            // 5. If manager, assign as branch manager
            if ($request->role === 'manager') {
                $branch->update(['manager_id' => $newUser->id]);
            }

            DB::commit();

            return back()->with('success',
                ucfirst($request->role) . ' account created successfully.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $user = auth()->user();

        if (!$user->canManageStaff()) {
            return back()->with('error', 'You do not have permission to modify staff.');
        }

        $sellerId = $user->isBranchStaff()
            ? $user->branchStaff?->seller_id
            : $user->sellerProfile->id;

        $staff = BranchStaff::where('seller_id', $sellerId)
            ->where('id', $id)
            ->firstOrFail();

        if ($user->isBranchStaff()
            && $user->branchStaff->branch_id !== $staff->branch_id) {
            abort(403, 'You can only manage staff in your branch.');
        }

        if ($staff->user_id === $user->id) {
            return back()->with('error', 'You cannot disable your own account.');
        }

        DB::beginTransaction();
        try {
            $staff->update(['status' => $staff->status ? 0 : 1]);
            DB::commit();

            return back()->with('success', 'Staff status updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user->canManageStaff()) {
            return back()->with('error', 'You do not have permission to delete staff.');
        }

        $sellerId = $user->isBranchStaff()
            ? $user->branchStaff?->seller_id
            : $user->sellerProfile->id;

        $staff = BranchStaff::where('seller_id', $sellerId)
            ->where('id', $id)
            ->firstOrFail();

        if ($user->isBranchStaff()
            && $user->branchStaff->branch_id !== $staff->branch_id) {
            abort(403, 'You can only manage staff in your branch.');
        }

        if ($staff->user_id === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($staff->role === 'manager' && !$user->isSellerOwner()) {
            return back()->with('error', 'Only the store owner can remove a manager.');
        }

        DB::beginTransaction();
        try {
            $staffUser = $staff->user;

            if ($staffUser && $staffUser->is_branch_staff) {
                $staffUser->roles()->detach();
                $staffUser->delete();
            }

            if ($staff->role === 'manager' && $staff->branch) {
                $staff->branch->update(['manager_id' => null]);
            }

            $staff->delete();
            DB::commit();

            return back()->with('success', 'Staff removed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    private function authorizedBranch($branchId): SellerBranch
    {
        $user = auth()->user();

        $sellerId = $user->isBranchStaff()
            ? $user->branchStaff?->seller_id
            : $user->sellerProfile?->id;

        if (!$sellerId) {
            abort(403, 'Seller not found.');
        }

        $query = SellerBranch::where('id', $branchId)
            ->where('seller_id', $sellerId)
            ->where('status', 1);

        if ($user->isBranchStaff()) {
            $query->where('id', $user->branchStaff->branch_id);
        }

        return $query->firstOrFail();
    }

    private function generateUsername($name)
    {
        $base     = Str::slug($name, '');
        $username = $base;
        $i        = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }

        return $username;
    }
}