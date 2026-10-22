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

class SellerBranchController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $seller = $user->sellerProfile;

        if (!$seller && !$user->isBranchStaff()) {
            return redirect()->route('seller.apply')
                ->with('error', 'Please register your store first.');
        }

        $branches = $user->getAccessibleBranches();

        return view('seller.branches.index', compact('seller', 'branches'));
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->isSellerOwner()) {
            return redirect()->route('seller.branches.index')
                ->with('error', 'Only store owner can create new branches.');
        }

        return view('seller.branches.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSellerOwner()) {
            abort(403, 'Only owner can create branches.');
        }

        $request->validate([
            'branch_name' => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'email'       => 'nullable|email|max:255',
            'address'     => 'required|string|max:255',

            'create_manager'   => 'nullable|boolean',
            'manager_name'     => 'required_if:create_manager,1|nullable|string|max:255',
            'manager_email'    => 'required_if:create_manager,1|nullable|email|unique:users,email',
            'manager_password' => 'required_if:create_manager,1|nullable|string|min:8',
        ]);

        $seller = $user->sellerProfile;

        DB::beginTransaction();
        try {
            // 1. Create branch
            $branch = SellerBranch::create([
                'seller_id'   => $seller->id,
                'branch_name' => $request->branch_name,
                'phone'       => $request->phone,
                'email'       => $request->email,
                'address'     => $request->address,
                'is_default'  => 0,
                'status'      => 1,
            ]);

            // 2. Optionally create manager
            if ($request->create_manager) {
                $manager = User::create([
                    'name'              => $request->manager_name,
                    'username'          => $this->generateUsername($request->manager_name),
                    'email'             => $request->manager_email,
                    'password'          => Hash::make($request->manager_password),
                    'email_verified_at' => now(),
                    'active_mode'       => 'seller',
                    'is_branch_staff'   => 1,
                    'staff_owner_id'    => $user->id,
                ]);

                // Seller (4)
                $sellerRole = Role::where('name', 'Seller')->first();
                if ($sellerRole) {
                    $manager->roles()->attach($sellerRole->id);
                }

                // Branch Manager (6) ← 🎯 थप
                $managerRole = Role::firstOrCreate(
                    ['name' => 'Branch Manager'],
                    ['level' => 6]
                );
                if ($managerRole && !$manager->roles()->where('role_id', $managerRole->id)->exists()) {
                    $manager->roles()->attach($managerRole->id);
                }

                $branch->update(['manager_id' => $manager->id]);

                BranchStaff::create([
                    'seller_id'  => $seller->id,
                    'branch_id'  => $branch->id,
                    'user_id'    => $manager->id,
                    'role'       => 'manager',
                    'status'     => 1,
                    'created_by' => $user->id,
                ]);
            }

            DB::commit();

            return redirect()->route('seller.branches.index')
                ->with('success', 'Branch created successfully!' .
                    ($request->create_manager ? ' Manager account created.' : '')
                );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $branch = $this->getAuthorizedBranch($id);

        return view('seller.branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        $branch = $this->getAuthorizedBranch($id);

        $request->validate([
            'branch_name' => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'email'       => 'nullable|email|max:255',
            'address'     => 'required|string|max:255',
        ]);

        $branch->update([
            'branch_name' => $request->branch_name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'address'     => $request->address,
        ]);

        return redirect()->route('seller.branches.index')
            ->with('success', 'Branch updated successfully!');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user->isSellerOwner()) {
            abort(403, 'Only owner can delete branches.');
        }

        $seller = $user->sellerProfile;
        $branch = SellerBranch::where('id', $id)
            ->where('seller_id', $seller->id)
            ->firstOrFail();

        if ($branch->is_default == 1) {
            return back()->with('error', 'Cannot delete HQ branch.');
        }

        if ($seller->branches()->count() <= 1) {
            return back()->with('error', 'At least one branch must exist.');
        }

        DB::beginTransaction();
        try {
            // 1. Clear manager_id
            $branch->update(['manager_id' => null]);

            // 2. Delete staff users + records
            foreach ($branch->staff as $staff) {
                $staffUser = $staff->user;
                if ($staffUser && $staffUser->id !== $user->id) {
                    if ($staffUser->is_branch_staff && $staffUser->staff_owner_id == $user->id) {
                        $staffUser->roles()->detach();
                        $staffUser->delete();
                    }
                }
                $staff->delete();
            }

            // 3. Delete branch
            $branch->delete();
            DB::commit();

            return redirect()->route('seller.branches.index')
                ->with('success', 'Branch and its staff accounts removed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    private function getAuthorizedBranch($branchId)
    {
        $user = auth()->user();
        $seller = $user->sellerProfile;

        $sellerId = $user->isBranchStaff()
            ? $user->branchStaff?->seller_id
            : $seller?->id;

        $query = SellerBranch::where('id', $branchId)
            ->where('seller_id', $sellerId);

        if ($user->isBranchStaff() && $user->branchStaff) {
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