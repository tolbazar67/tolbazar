<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerCommissionRule;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Seller;
use App\Models\SellerBranch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartnerCommissionRuleController extends Controller
{
    public function index(Request $request)
    {
        $rules = PartnerCommissionRule::with(['service', 'category', 'seller', 'branch'])
            ->when($request->filled('service_id'), fn ($q) => $q->where('service_id', $request->integer('service_id')))
            ->when($request->filled('seller_id'), fn ($q) => $q->where('seller_id', $request->integer('seller_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->latest()->paginate(20)->withQueryString();
        $services = Service::orderBy('name')->get(['id', 'name']);
        return view('admin.service-management.commissions.index', compact('rules', 'services'));
    }

    public function create()
    {
        $rule = new PartnerCommissionRule(['commission_type' => 'percentage', 'currency' => 'NPR', 'calculation_basis' => 'subtotal', 'is_active' => true]);
        return $this->form($rule);
    }

    public function store(Request $request)
    {
        PartnerCommissionRule::create($this->validated($request));
        return redirect()->route('admin.partner-commission-rules.index')->with('success', 'Commission rule created.');
    }

    public function edit(PartnerCommissionRule $partner_commission_rule)
    {
        return $this->form($partner_commission_rule);
    }

    public function update(Request $request, PartnerCommissionRule $partner_commission_rule)
    {
        $partner_commission_rule->update($this->validated($request));
        return redirect()->route('admin.partner-commission-rules.index')->with('success', 'Commission rule updated.');
    }

    public function destroy(PartnerCommissionRule $partner_commission_rule)
    {
        $partner_commission_rule->delete();
        return back()->with('success', 'Commission rule deleted.');
    }

    public function toggleActive(PartnerCommissionRule $partner_commission_rule)
    {
        $partner_commission_rule->update(['is_active' => ! $partner_commission_rule->is_active]);
        return back()->with('success', 'Commission rule status updated.');
    }

    private function form(PartnerCommissionRule $rule)
    {
        $services = Service::orderBy('name')->get(['id', 'name']);
        $categories = ServiceCategory::orderBy('name')->get(['id', 'service_id', 'name']);
        $sellers = Seller::orderBy('store_name')->get(['id', 'store_name']);
        $branches = SellerBranch::orderBy('id')->get(['id', 'seller_id']);
        return view('admin.service-management.commissions.form', compact('rule', 'services', 'categories', 'sellers', 'branches'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'category_id' => ['nullable', 'integer', Rule::exists('service_categories', 'id')->where(fn ($q) => $q->where('service_id', $request->input('service_id')))],
            'seller_id' => ['nullable', 'integer', 'exists:sellers,id'],
            'branch_id' => ['nullable', 'integer', 'exists:seller_branches,id'],
            'name' => ['required', 'string', 'max:150'],
            'commission_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'rate' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'calculation_basis' => ['required', Rule::in(['subtotal', 'subtotal_after_discount'])],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_amount' => ['nullable', 'numeric', 'gte:min_amount'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'settings' => ['nullable', 'array'],
        ]);
        if (!empty($data['branch_id']) && empty($data['seller_id'])) {
            $branch = SellerBranch::findOrFail($data['branch_id']);
            $data['seller_id'] = $branch->seller_id;
        }
        if (!empty($data['branch_id']) && !empty($data['seller_id'])) {
            $belongs = SellerBranch::whereKey($data['branch_id'])->where('seller_id', $data['seller_id'])->exists();
            if (!$belongs) {
                throw \Illuminate\Validation\ValidationException::withMessages(['branch_id' => 'Selected branch does not belong to the selected seller.']);
            }
        }
        $data['category_id'] = $data['category_id'] ?: null;
        $data['seller_id'] = $data['seller_id'] ?: null;
        $data['branch_id'] = $data['branch_id'] ?: null;
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
