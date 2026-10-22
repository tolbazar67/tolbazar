<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * ============================================================
     * LIST ALL SERVICES
     * ============================================================
     */
    public function index(Request $request)
    {
        $query = Service::query();

        // Search
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->query('status') === 'active') {
            $query->where('is_active', 1);
        } elseif ($request->query('status') === 'inactive') {
            $query->where('is_active', 0);
        }

        $services = $query->ordered()->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total'      => Service::count(),
            'active'     => Service::where('is_active', 1)->count(),
            'inactive'   => Service::where('is_active', 0)->count(),
            'featured'   => Service::where('is_featured', 1)->count(),
        ];

        return view('admin.services.index', compact('services', 'stats'));
    }

    /**
     * ============================================================
     * SHOW CREATE FORM
     * ============================================================
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * ============================================================
     * STORE NEW SERVICE
     * ============================================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255|unique:services,name',
            'slug'         => 'nullable|string|max:255|unique:services,slug',
            'icon'         => 'nullable|string|max:100',
            'color'        => 'nullable|string|max:20',
            'description'  => 'nullable|string|max:2000',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'nullable|boolean',
            'is_featured'  => 'nullable|boolean',
            'settings'     => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only([
                'name', 'icon', 'color', 'description', 'sort_order',
            ]);

            // Slug
            $data['slug'] = $request->filled('slug')
                ? Str::slug($request->slug)
                : Str::slug($request->name) . '-' . rand(1000, 9999);

            // Booleans
            $data['is_active']   = $request->boolean('is_active', true);
            $data['is_featured'] = $request->boolean('is_featured', false);

            // Settings (JSON)
            if ($request->filled('settings') && is_array($request->settings)) {
                $data['settings'] = $request->settings;
            }

            // Image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('services', 'public');
            }

            Service::create($data);

            DB::commit();

            return redirect()
                ->route('admin.services.index')
                ->with('success', 'Service created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Cleanup uploaded image on failure
            if (isset($data['image']) && Storage::disk('public')->exists($data['image'])) {
                Storage::disk('public')->delete($data['image']);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to create service: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * SHOW SERVICE DETAILS (optional, if used)
     * ============================================================
     */
    public function show(Service $service)
    {
        $service->loadCount(['products', 'branches']);

        return view('admin.services.show', compact('service'));
    }

    /**
     * ============================================================
     * SHOW EDIT FORM
     * ============================================================
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * ============================================================
     * UPDATE SERVICE
     * ============================================================
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255|unique:services,name,' . $service->id,
            'slug'         => 'nullable|string|max:255|unique:services,slug,' . $service->id,
            'icon'         => 'nullable|string|max:100',
            'color'        => 'nullable|string|max:20',
            'description'  => 'nullable|string|max:2000',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'nullable|boolean',
            'is_featured'  => 'nullable|boolean',
            'settings'     => 'nullable|array',
            'remove_image' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only([
                'name', 'icon', 'color', 'description', 'sort_order',
            ]);

            // Slug
            if ($request->filled('slug')) {
                $data['slug'] = Str::slug($request->slug);
            } elseif ($request->filled('name') && $service->name !== $request->name) {
                $data['slug'] = Str::slug($request->name) . '-' . rand(1000, 9999);
            }

            // Booleans
            $data['is_active']   = $request->boolean('is_active', true);
            $data['is_featured'] = $request->boolean('is_featured', false);

            // Settings
            if ($request->filled('settings') && is_array($request->settings)) {
                $data['settings'] = $request->settings;
            }

            // Remove existing image
            if ($request->boolean('remove_image') && $service->image) {
                if (Storage::disk('public')->exists($service->image)) {
                    Storage::disk('public')->delete($service->image);
                }
                $data['image'] = null;
            }

            // Upload new image
            if ($request->hasFile('image')) {
                // Delete old
                if ($service->image && Storage::disk('public')->exists($service->image)) {
                    Storage::disk('public')->delete($service->image);
                }
                $data['image'] = $request->file('image')->store('services', 'public');
            }

            $service->update($data);

            DB::commit();

            return redirect()
                ->route('admin.services.index')
                ->with('success', 'Service updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * TOGGLE ACTIVE STATUS
     * ============================================================
     */
    public function toggleActive(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);

        return back()->with('success',
            'Service "' . $service->name . '" is now ' . ($service->is_active ? 'active' : 'inactive') . '.'
        );
    }

    /**
     * ============================================================
     * TOGGLE FEATURED
     * ============================================================
     */
    public function toggleFeatured(Service $service)
    {
        $service->update(['is_featured' => !$service->is_featured]);

        return back()->with('success',
            'Service "' . $service->name . '" ' . ($service->is_featured ? 'marked as featured' : 'unmarked') . '.'
        );
    }

    /**
     * ============================================================
     * DELETE SERVICE
     * ============================================================
     */
    public function destroy(Service $service)
    {
        // Safety: don't delete if products exist
        $productCount = $service->products()->count();
        if ($productCount > 0) {
            return back()->with('error',
                "Cannot delete \"{$service->name}\" — {$productCount} product(s) exist. Deactivate instead."
            );
        }

        DB::beginTransaction();

        try {
            // Delete image
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }

            // Detach branches
            $service->branches()->detach();

            // Delete service
            $service->delete();

            DB::commit();

            return redirect()
                ->route('admin.services.index')
                ->with('success', 'Service deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }
}