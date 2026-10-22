<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = ServiceCategory::with(['service', 'parent'])
            ->when($request->filled('service_id'), fn ($q) => $q->where('service_id', $request->integer('service_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->orderBy('service_id')->orderBy('sort_order')->orderBy('name')
            ->paginate(20)->withQueryString();

        $services = Service::orderBy('name')->get(['id', 'name']);
        return view('admin.service-management.categories.index', compact('categories', 'services'));
    }

    public function create()
    {
        $category = new ServiceCategory(['is_active' => true]);
        $services = Service::orderBy('name')->get(['id', 'name']);
        $parents = collect();
        return view('admin.service-management.categories.form', compact('category', 'services', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['service_id'], $data['slug'] ?: $data['name']);
        ServiceCategory::create($data);

        return redirect()->route('admin.service-categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(ServiceCategory $service_category)
    {
        $category = $service_category;
        $services = Service::orderBy('name')->get(['id', 'name']);
        $parents = ServiceCategory::where('service_id', $category->service_id)
            ->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('admin.service-management.categories.form', compact('category', 'services', 'parents'));
    }

    public function update(Request $request, ServiceCategory $service_category)
    {
        $data = $this->validated($request, $service_category);
        if ((int) $data['parent_id'] === (int) $service_category->id) {
            return back()->withErrors(['parent_id' => 'A category cannot be its own parent.'])->withInput();
        }
        $data['slug'] = $this->uniqueSlug($data['service_id'], $data['slug'] ?: $data['name'], $service_category->id);
        $service_category->update($data);

        return redirect()->route('admin.service-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(ServiceCategory $service_category)
    {
        if ($service_category->children()->exists()) {
            return back()->with('error', 'Move or delete subcategories before deleting this category.');
        }
        if ($service_category->taxRules()->exists() || $service_category->commissionRules()->exists() || $service_category->customerChargeRules()->exists()) {
            return back()->with('error', 'This category has financial rules attached. Remove those rules first.');
        }
        $service_category->delete();
        return redirect()->route('admin.service-categories.index')->with('success', 'Category deleted.');
    }

    public function toggleActive(ServiceCategory $service_category)
    {
        $service_category->update(['is_active' => ! $service_category->is_active]);
        return back()->with('success', 'Category status updated.');
    }

    private function validated(Request $request, ?ServiceCategory $category = null): array
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'parent_id' => ['nullable', 'integer', Rule::exists('service_categories', 'id')->where(fn ($q) => $q->where('service_id', $request->input('service_id')))],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'settings' => ['nullable', 'array'],
        ]);
        $data['slug'] = trim((string) ($data['slug'] ?? '')) ?: Str::slug($data['name']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');
        $data['settings'] = $data['settings'] ?? null;
        $data['parent_id'] = $data['parent_id'] ?: null;
        return $data;
    }

    private function uniqueSlug(int $serviceId, string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'category';
        $slug = $base;
        $i = 2;
        while (ServiceCategory::where('service_id', $serviceId)->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
