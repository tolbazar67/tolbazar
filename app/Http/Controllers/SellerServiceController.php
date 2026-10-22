<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\SellerBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerServiceController extends Controller
{
    /**
     * ============================================================
     * LIST SERVICES
     * - Owner   → all branches (with branch switcher)
     * - Manager → own branch only
     * - Staff   → own branch only (view + enable/disable)
     * ============================================================
     */
    public function index(Request $request)
    {
        $user   = auth()->user();
        $seller = $user->sellerProfile;

        // Staff/Manager has no sellerProfile; get seller via branch
        if ($user->isBranchStaff()) {
            $staff = $user->branchStaff;
            if (!$staff || !$staff->status) {
                return redirect()->route('login')
                    ->with('error', 'Your staff account is inactive.');
            }
        } else {
            if (!$seller) {
                return redirect()->route('seller.apply')
                    ->with('error', 'Please register your store first.');
            }
        }

        // Accessible branches
        $branches = $user->getAccessibleBranches();

        if ($branches->isEmpty()) {
            return redirect()->route('seller.branches.index')
                ->with('error', 'No active branch found.');
        }

        // Selected branch
        $selectedBranchId = $request->query('branch_id', session('active_branch_id'));
        $selectedBranch   = $branches->firstWhere('id', $selectedBranchId) ?? $branches->first();

        // All active services
        $services = Service::active()->ordered()->get();

        // Currently enabled service IDs for this branch
        $enabledServiceIds = $selectedBranch
            ? $selectedBranch->enabledServices()->pluck('services.id')->toArray()
            : [];

        // Stats for the branch
        $stats = [
            'available' => $services->count(),
            'enabled'   => count($enabledServiceIds),
            'disabled'  => $services->count() - count($enabledServiceIds),
            'products'  => $selectedBranch
                ? $selectedBranch->products()->count()
                : 0,
        ];

        return view('seller.services.index', compact(
            'branches',
            'selectedBranch',
            'services',
            'enabledServiceIds',
            'stats'
        ));
    }

    /**
     * ============================================================
     * ENABLE SERVICE ON BRANCH
     * ============================================================
     */
    public function enable(Request $request, Service $service)
    {
        $user   = auth()->user();
        $branch = $this->authorizedBranch($request->input('branch_id'), $user);

        // Service must be active
        if (!$service->is_active) {
            return back()->with('error', 'This service is currently unavailable.');
        }

        DB::beginTransaction();

        try {
            $meta = $request->input('meta', []);

            // If already enabled → update meta only
            if ($branch->hasService($service->id)) {
                $branch->services()->updateExistingPivot($service->id, [
                    'is_enabled' => 1,
                    'meta'       => !empty($meta) ? json_encode($meta) : null,
                ]);
            } else {
                // Fresh enable
                $branch->enableService($service->id, $meta);
            }

            DB::commit();

            return back()->with('success',
                "\"{$service->name}\" enabled for {$branch->branch_name}."
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to enable service: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * DISABLE SERVICE ON BRANCH
     * ============================================================
     */
    public function disable(Request $request, Service $service)
    {
        $user   = auth()->user();
        $branch = $this->authorizedBranch($request->input('branch_id'), $user);

        // Safety: block disable if products exist under this service
        $productCount = $branch->products()
            ->where('service_id', $service->id)
            ->count();

        if ($productCount > 0) {
            return back()->with('error',
                "Cannot disable \"{$service->name}\" — {$productCount} product(s) exist. Delete or deactivate products first."
            );
        }

        DB::beginTransaction();

        try {
            $branch->disableService($service->id);
            DB::commit();

            return back()->with('success',
                "\"{$service->name}\" disabled for {$branch->branch_name}."
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to disable service: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * TOGGLE SERVICE (enable ↔ disable)
     * ============================================================
     */
    public function toggle(Request $request, Service $service)
    {
        $user   = auth()->user();
        $branch = $this->authorizedBranch($request->input('branch_id'), $user);

        // Branch has service enabled? → disable, else → enable
        if ($branch->hasService($service->id)) {
            return $this->disable($request, $service);
        }

        return $this->enable($request, $service);
    }

    /**
     * ============================================================
     * HELPER: Authorize branch access
     * - Owner   → any branch of their seller
     * - Manager → own branch only
     * - Staff   → own branch only
     * ============================================================
     */
    private function authorizedBranch($branchId, $user): SellerBranch
    {
        if (!$branchId) {
            abort(422, 'Branch ID is required.');
        }

        // Staff: sellerId from their branch
        $sellerId = $user->isBranchStaff()
            ? $user->branchStaff?->seller_id
            : $user->sellerProfile?->id;

        if (!$sellerId) {
            abort(403, 'Seller not found.');
        }

        $query = SellerBranch::where('id', $branchId)
            ->where('seller_id', $sellerId)
            ->where('status', 1);

        // Staff limited to own branch
        if ($user->isBranchStaff()) {
            $staff = $user->branchStaff;
            if (!$staff) {
                abort(403, 'Staff assignment missing.');
            }
            $query->where('id', $staff->branch_id);
        }

        return $query->firstOrFail();
    }
}