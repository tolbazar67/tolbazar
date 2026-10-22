<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceTaxRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceTaxRuleController extends Controller
{
    public function index(Request $request)
    {
        $rules = ServiceTaxRule::with(['service', 'category'])
            ->when($request->filled('service_id'), fn ($q) => $q->where('service_id', $request->integer('service_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->latest()->paginate(20)->withQueryString();
        $services = Service::orderBy('name')->get(['id', 'name']);
        return view('admin.service-management.tax.index', compact('rules', 'services'));
    }

    public function create()
    {
        $rule = new ServiceTaxRule(['tax_type' => 'percentage', 'currency' => 'NPR', 'applies_to' => 'customer', 'is_active' => true]);
        $services = Service::orderBy('name')->get(['id', 'name']);
        $categories = ServiceCategory::orderBy('name')->get(['id', 'service_id', 'name']);
        return view('admin.service-management.tax.form', compact('rule', 'services', 'categories'));
    }

    public function store(Request $request)
    {
        ServiceTaxRule::create($this->validated($request));
        return redirect()->route('admin.service-tax-rules.index')->with('success', 'Tax rule created.');
    }

    public function edit(ServiceTaxRule $service_tax_rule)
    {
        $rule = $service_tax_rule;
        $services = Service::orderBy('name')->get(['id', 'name']);
        $categories = ServiceCategory::orderBy('name')->get(['id', 'service_id', 'name']);
        return view('admin.service-management.tax.form', compact('rule', 'services', 'categories'));
    }

    public function update(Request $request, ServiceTaxRule $service_tax_rule)
    {
        $service_tax_rule->update($this->validated($request));
        return redirect()->route('admin.service-tax-rules.index')->with('success', 'Tax rule updated.');
    }

    public function destroy(ServiceTaxRule $service_tax_rule)
    {
        $service_tax_rule->delete();
        return back()->with('success', 'Tax rule deleted.');
    }

    public function toggleActive(ServiceTaxRule $service_tax_rule)
    {
        $service_tax_rule->update(['is_active' => ! $service_tax_rule->is_active]);
        return back()->with('success', 'Tax rule status updated.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'category_id' => ['nullable', 'integer', Rule::exists('service_categories', 'id')->where(fn ($q) => $q->where('service_id', $request->input('service_id')))],
            'name' => ['required', 'string', 'max:150'],
            'tax_code' => ['nullable', 'string', 'max:50'],
            'tax_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'rate' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'applies_to' => ['required', Rule::in(['customer', 'partner', 'platform'])],
            'is_inclusive' => ['nullable', 'boolean'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'settings' => ['nullable', 'array'],
        ]);
        $data['category_id'] = $data['category_id'] ?: null;
        $data['is_inclusive'] = $request->boolean('is_inclusive');
        $data['is_active'] = $request->boolean('is_active');
        $data['priority'] = (int) ($data['priority'] ?? 0);
        return $data;
    }
}
