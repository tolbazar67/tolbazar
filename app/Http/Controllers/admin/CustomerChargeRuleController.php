<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerChargeRule;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerChargeRuleController extends Controller
{
    public function index(Request $request)
    {
        $rules = CustomerChargeRule::with(['service', 'category'])
            ->when($request->filled('service_id'), fn ($q) => $q->where('service_id', $request->integer('service_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->latest()->paginate(20)->withQueryString();
        $services = Service::orderBy('name')->get(['id', 'name']);
        return view('admin.service-management.charges.index', compact('rules', 'services'));
    }

    public function create()
    {
        $rule = new CustomerChargeRule(['charge_type' => 'fixed', 'currency' => 'NPR', 'calculation_basis' => 'subtotal', 'is_active' => true]);
        return $this->form($rule);
    }

    public function store(Request $request)
    {
        CustomerChargeRule::create($this->validated($request));
        return redirect()->route('admin.customer-charge-rules.index')->with('success', 'Customer charge created.');
    }

    public function edit(CustomerChargeRule $customer_charge_rule)
    {
        return $this->form($customer_charge_rule);
    }

    public function update(Request $request, CustomerChargeRule $customer_charge_rule)
    {
        $customer_charge_rule->update($this->validated($request));
        return redirect()->route('admin.customer-charge-rules.index')->with('success', 'Customer charge updated.');
    }

    public function destroy(CustomerChargeRule $customer_charge_rule)
    {
        $customer_charge_rule->delete();
        return back()->with('success', 'Customer charge deleted.');
    }

    public function toggleActive(CustomerChargeRule $customer_charge_rule)
    {
        $customer_charge_rule->update(['is_active' => ! $customer_charge_rule->is_active]);
        return back()->with('success', 'Customer charge status updated.');
    }

    private function form(CustomerChargeRule $rule)
    {
        $services = Service::orderBy('name')->get(['id', 'name']);
        $categories = ServiceCategory::orderBy('name')->get(['id', 'service_id', 'name']);
        return view('admin.service-management.charges.form', compact('rule', 'services', 'categories'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'category_id' => ['nullable', 'integer', Rule::exists('service_categories', 'id')->where(fn ($q) => $q->where('service_id', $request->input('service_id')))],
            'name' => ['required', 'string', 'max:150'],
            'charge_code' => ['nullable', 'string', 'max:80'],
            'charge_type' => ['required', Rule::in(['percentage', 'fixed', 'per_item'])],
            'rate' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'calculation_basis' => ['required', Rule::in(['subtotal', 'subtotal_after_discount'])],
            'min_charge' => ['nullable', 'numeric', 'min:0'],
            'max_charge' => ['nullable', 'numeric', 'gte:min_charge'],
            'is_taxable' => ['nullable', 'boolean'],
            'is_refundable' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'settings' => ['nullable', 'array'],
        ]);
        $data['category_id'] = $data['category_id'] ?: null;
        $data['is_taxable'] = $request->boolean('is_taxable');
        $data['is_refundable'] = $request->boolean('is_refundable');
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
