<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerBranch;
use App\Models\Service;
use App\Services\ProductDetailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function guardSellerMode()
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthenticated.');
        }

        if ($user->active_mode !== 'seller') {
            abort(403, 'You must be in seller mode to access products.');
        }

        if (!$user->isSellerOwner() && !$user->isBranchStaff()) {
            abort(403, 'Only seller accounts can access products.');
        }

        return $user;
    }

    private function getSellerId($user): int
    {
        if ($user->isSellerOwner()) {
            return $user->sellerProfile->id;
        }

        if ($user->isBranchStaff()) {
            $staff = $user->branchStaff;
            if (!$staff) {
                abort(403, 'Branch assignment missing.');
            }
            return $staff->seller_id;
        }

        abort(403, 'Seller not found.');
    }

    private function creatorRole($user): string
    {
        if ($user->isSellerOwner()) return 'owner';
        if ($user->isBranchStaff()) return 'staff';
        return 'unknown';
    }

    private function serviceCatalog(): array
    {
        return [
            'HOTEL'        => ['label' => 'Hotel & Accommodation',       'icon' => 'bi-building'],
            'RETAIL'       => ['label' => 'Retail & Shopping',           'icon' => 'bi-shop'],
            'RESTAURANT'   => ['label' => 'Restaurant & Catering',       'icon' => 'bi-cup-hot'],
            'EDUCATION'    => ['label' => 'Education & Training',        'icon' => 'bi-mortarboard'],
            'HEALTHCARE'   => ['label' => 'Healthcare & Medical',        'icon' => 'bi-heart-pulse'],
            'TRANSPORT'    => ['label' => 'Transportation',              'icon' => 'bi-truck'],
            'BEAUTY'       => ['label' => 'Beauty & Personal Care',      'icon' => 'bi-scissors'],
            'REPAIR'       => ['label' => 'Repair & Maintenance',        'icon' => 'bi-tools'],
            'CONSTRUCTION' => ['label' => 'Construction & Home Services','icon' => 'bi-house-gear'],
            'TRAVEL'       => ['label' => 'Travel & Tourism',            'icon' => 'bi-map'],
        ];
    }

    public function index(Request $request)
    {
        $user     = $this->guardSellerMode();
        $sellerId = $this->getSellerId($user);

        $branches = $user->getAccessibleBranches();

        if ($branches->isEmpty()) {
            return redirect()->route('seller.branches.index')
                ->with('error', 'No active branch found. Please create one.');
        }

        $branchId = $request->query('branch_id', session('active_branch_id'));
        $branch   = $branches->firstWhere('id', $branchId) ?? $branches->first();

        $serviceIds = $branch->enabledServices()->pluck('services.id')->toArray();

        if (empty($serviceIds)) {
            return view('seller.products.index', [
                'branches'       => $branches,
                'branch'         => $branch,
                'products'       => collect(),
                'services'       => collect(),
                'stats'          => ['total' => 0, 'active' => 0, 'inactive' => 0, 'out_of_stock' => 0],
                'noServicesFlag' => true,
                'serviceCatalog' => $this->serviceCatalog(),
            ]);
        }

        $query = Product::with(['service', 'creator'])
            ->where('seller_id', $sellerId)
            ->where('branch_id', $branch->id)
            ->whereIn('service_id', $serviceIds);

        if ($serviceId = $request->query('service_id')) {
            $query->where('service_id', $serviceId);
        }

        if ($status = $request->query('status')) {
            if ($status === 'active')       $query->where('is_active', 1);
            if ($status === 'inactive')     $query->where('is_active', 0);
            if ($status === 'out_of_stock') $query->where('stock_quantity', '<=', 0);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('short_description', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $stats = [
            'total'        => Product::where('branch_id', $branch->id)->count(),
            'active'       => Product::where('branch_id', $branch->id)->where('is_active', 1)->count(),
            'inactive'     => Product::where('branch_id', $branch->id)->where('is_active', 0)->count(),
            'out_of_stock' => Product::where('branch_id', $branch->id)->where('stock_quantity', '<=', 0)->count(),
        ];

        $services = $branch->enabledServices()->get();

        return view('seller.products.index', compact(
            'branches', 'branch', 'products', 'services', 'stats'
        ) + [
            'noServicesFlag' => false,
            'serviceCatalog' => $this->serviceCatalog(),
        ]);
    }

    public function create(Request $request)
    {
        $user = $this->guardSellerMode();

        $branches = $user->getAccessibleBranches();

        if ($branches->isEmpty()) {
            return redirect()->route('seller.branches.index')
                ->with('error', 'No active branch found.');
        }

        $branchId = $request->query('branch_id', session('active_branch_id'));
        $branch   = $branches->firstWhere('id', $branchId) ?? $branches->first();

        $services = $branch->enabledServices()->get();

        if ($services->isEmpty()) {
            return redirect()->route('seller.services.index', ['branch_id' => $branch->id])
                ->with('error', 'Please enable at least one service before creating products.');
        }

        return view('seller.products.create', [
            'branches'       => $branches,
            'branch'         => $branch,
            'services'       => $services,
            'serviceCatalog' => $this->serviceCatalog(),
        ]);
    }

    public function store(Request $request)
    {
        $user     = $this->guardSellerMode();
        $sellerId = $this->getSellerId($user);

        $request->validate([
            'branch_id'         => 'required|exists:seller_branches,id',
            'service_id'        => 'required|exists:services,id',
            'name'              => 'required|string|max:255',
            'sku'               => 'nullable|string|max:100|unique:products,sku',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string|max:10000',

            'price'             => 'required|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0|gte:price',
            'cost_price'        => 'nullable|numeric|min:0',
            'unit'              => 'required|string|max:50',
            'tax_percent'       => 'nullable|numeric|min:0|max:100',

            'stock_quantity'    => 'required|integer|min:0',
            'min_order_qty'     => 'nullable|integer|min:1',
            'max_order_qty'     => 'nullable|integer|min:1|gte:min_order_qty',
            'track_stock'       => 'nullable|boolean',

            'thumbnail'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery'           => 'nullable|array|max:5',
            'gallery.*'         => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'is_active'         => 'nullable|boolean',
            // ❌ NOTE: is_featured NOT accepted here — toggle only

            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
            'tags'              => 'nullable|string|max:500',
        ]);

        $branch = $this->authorizedBranch($request->branch_id, $user);

        if (!$branch->hasService($request->service_id)) {
            return back()->withInput()
                ->with('error', 'This service is not enabled on the selected branch.');
        }

        // Load the selected service only after confirming it is enabled for this branch.
        $service = Service::query()
            ->whereKey($request->service_id)
            ->where('is_active', true)
            ->firstOrFail();

        // Service-specific fields use names such as details[room_type].
        $request->validate(
            app(ProductDetailService::class)->rulesFor($service)
        );

        DB::beginTransaction();

        try {
            $product = new Product();
            $product->fill([
                'seller_id'         => $sellerId,
                'branch_id'         => $branch->id,
                'service_id'        => $request->service_id,

                // 👇 NEW: created-by audit
                'created_by'        => $user->id,
                'created_by_role'   => $this->creatorRole($user),
                'created_by_name'   => $user->name,

                'name'              => $request->name,
                'slug'              => Str::slug($request->name) . '-' . rand(1000, 99999),
                'sku'               => $request->sku,
                'short_description' => $request->short_description,
                'description'       => $request->description,
                'price'             => $request->price,
                'compare_price'     => $request->compare_price,
                'cost_price'        => $request->cost_price,
                'unit'              => $request->unit,
                'tax_percent'       => $request->tax_percent ?? 0,
                'stock_quantity'    => $request->stock_quantity,
                'min_order_qty'     => $request->min_order_qty ?? 1,
                'max_order_qty'     => $request->max_order_qty,
                'track_stock'       => $request->boolean('track_stock', true),
                'in_stock'          => $request->stock_quantity > 0,
                'has_variants'      => false,
                'is_active'         => $request->boolean('is_active', true),
                'is_featured'       => false, // 🔒 always false on create (toggle-only)
                'meta_title'        => $request->meta_title,
                'meta_description'  => $request->meta_description,
                'tags'              => $request->tags,
            ]);

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store("products/{$branch->id}", 'public');
                $product->thumbnail = $path;
            }

            $product->save();

            // Save service-specific fields in the same transaction as the main product.
            app(ProductDetailService::class)->saveForProduct(
                $product,
                $request->input('details', [])
            );

            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $index => $file) {
                    $path = $file->store("products/{$branch->id}/gallery", 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0 && !$product->thumbnail,
                        'sort_order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('seller.products.index', ['branch_id' => $branch->id])
                ->with('success', 'Product created successfully by ' . $product->created_by_label . '.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $this->authorizeProduct($product);

        $product->load(['service', 'branch', 'images', 'variants', 'creator']);

        return view('seller.products.show', [
            'product'        => $product,
            'serviceCatalog' => $this->serviceCatalog(),
        ]);
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);

        $user     = auth()->user();
        $branches = $user->getAccessibleBranches();
        $branch   = $product->branch;
        $services = $branch->enabledServices()->get();

        return view('seller.products.edit', [
            'product'        => $product,
            'branches'       => $branches,
            'branch'         => $branch,
            'services'       => $services,
            'serviceCatalog' => $this->serviceCatalog(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);

        $request->validate([
            'name'              => 'required|string|max:255',
            'sku'               => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string|max:10000',

            'price'             => 'required|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0|gte:price',
            'cost_price'        => 'nullable|numeric|min:0',
            'unit'              => 'required|string|max:50',
            'tax_percent'       => 'nullable|numeric|min:0|max:100',

            'stock_quantity'    => 'required|integer|min:0',
            'min_order_qty'     => 'nullable|integer|min:1',
            'max_order_qty'     => 'nullable|integer|min:1',
            'track_stock'       => 'nullable|boolean',

            'thumbnail'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery'           => 'nullable|array|max:5',
            'gallery.*'         => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_thumbnail'  => 'nullable|boolean',

            'is_active'         => 'nullable|boolean',
            // ❌ is_featured NOT here — separate toggle only

            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
            'tags'              => 'nullable|string|max:500',
        ]);

        // The service of an existing product is fixed; validate its own detail fields.
        $product->loadMissing('service');
        if (!$product->service) {
            abort(422, 'The product service could not be found.');
        }

        $request->validate(
            app(ProductDetailService::class)->rulesFor($product->service)
        );

        DB::beginTransaction();

        try {
            $product->fill([
                'name'              => $request->name,
                'sku'               => $request->sku,
                'short_description' => $request->short_description,
                'description'       => $request->description,
                'price'             => $request->price,
                'compare_price'     => $request->compare_price,
                'cost_price'        => $request->cost_price,
                'unit'              => $request->unit,
                'tax_percent'       => $request->tax_percent ?? 0,
                'stock_quantity'    => $request->stock_quantity,
                'in_stock'          => $request->stock_quantity > 0,
                'min_order_qty'     => $request->min_order_qty ?? 1,
                'max_order_qty'     => $request->max_order_qty,
                'track_stock'       => $request->boolean('track_stock', true),
                'is_active'         => $request->boolean('is_active', true),
                // 🔒 is_featured not touched here
                'meta_title'        => $request->meta_title,
                'meta_description'  => $request->meta_description,
                'tags'              => $request->tags,
            ]);

            if ($request->boolean('remove_thumbnail') && $product->thumbnail) {
                if (Storage::disk('public')->exists($product->thumbnail)) {
                    Storage::disk('public')->delete($product->thumbnail);
                }
                $product->thumbnail = null;
            }

            if ($request->hasFile('thumbnail')) {
                if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
                    Storage::disk('public')->delete($product->thumbnail);
                }
                $product->thumbnail = $request->file('thumbnail')
                    ->store("products/{$product->branch_id}", 'public');
            }

            $product->save();

            // Update (or create) the one-to-one service detail record.
            app(ProductDetailService::class)->saveForProduct(
                $product,
                $request->input('details', [])
            );

            if ($request->hasFile('gallery')) {
                $currentCount = $product->images()->count();
                foreach ($request->file('gallery') as $i => $file) {
                    $path = $file->store("products/{$product->branch_id}/gallery", 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => false,
                        'sort_order' => $currentCount + $i,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('seller.products.index', ['branch_id' => $product->branch_id])
                ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    public function toggleActive(Product $product)
    {
        $this->authorizeProduct($product);

        $product->update(['is_active' => !$product->is_active]);

        return back()->with('success',
            'Product "' . $product->name . '" is now ' .
            ($product->is_active ? 'active' : 'inactive') . '.'
        );
    }

    public function toggleFeatured(Product $product)
    {
        $this->authorizeProduct($product);

        $product->update(['is_featured' => !$product->is_featured]);

        return back()->with('success',
            'Product "' . $product->name . '" ' .
            ($product->is_featured ? 'marked as featured' : 'unmarked') . '.'
        );
    }

    public function featuredIndex(Request $request)
    {
        $user     = $this->guardSellerMode();
        $sellerId = $this->getSellerId($user);

        $branches = $user->getAccessibleBranches();

        if ($branches->isEmpty()) {
            return redirect()->route('seller.branches.index')
                ->with('error', 'No active branch found.');
        }

        $branchId = $request->query('branch_id', session('active_branch_id'));
        $branch   = $branches->firstWhere('id', $branchId) ?? $branches->first();

        $serviceIds = $branch->enabledServices()->pluck('services.id')->toArray();

        $products = Product::with(['service', 'creator'])
            ->where('seller_id', $sellerId)
            ->where('branch_id', $branch->id)
            ->whereIn('service_id', $serviceIds)
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('seller.products.featured', [
            'branches'       => $branches,
            'branch'         => $branch,
            'products'       => $products,
            'serviceCatalog' => $this->serviceCatalog(),
        ]);
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);

        $product->loadMissing(['images', 'variants']);

        DB::beginTransaction();

        try {
            if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
                Storage::disk('public')->delete($product->thumbnail);
            }

            foreach ($product->images as $img) {
                if (Storage::disk('public')->exists($img->image_path)) {
                    Storage::disk('public')->delete($img->image_path);
                }
            }

            foreach ($product->variants as $variant) {
                if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                    Storage::disk('public')->delete($variant->image);
                }
            }

            $product->delete();

            DB::commit();

            return redirect()
                ->route('seller.products.index', ['branch_id' => $product->branch_id])
                ->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        $this->authorizeProduct($product);

        if ($image->product_id !== $product->id) {
            abort(403);
        }

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    private function authorizedBranch(int $branchId, $user): SellerBranch
    {
        $sellerId = $this->getSellerId($user);

        $query = SellerBranch::where('id', $branchId)
            ->where('seller_id', $sellerId)
            ->where('status', 1);

        if ($user->isBranchStaff()) {
            $staff = $user->branchStaff;
            if (!$staff) {
                abort(403, 'Staff assignment missing.');
            }
            $query->where('id', $staff->branch_id);
        }

        return $query->firstOrFail();
    }

    private function authorizeProduct(Product $product): void
    {
        $user     = $this->guardSellerMode();
        $sellerId = $this->getSellerId($user);

        if ($product->seller_id !== $sellerId) {
            abort(403, 'Unauthorized — product belongs to another seller.');
        }

        if ($user->isBranchStaff()) {
            $staff = $user->branchStaff;
            if (!$staff || $product->branch_id !== $staff->branch_id) {
                abort(403, 'You can only manage products in your branch.');
            }
        }
    }
}