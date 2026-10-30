<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\SellerBranch;
use App\Support\BookingMode;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Listing of all 10 services.
     */
    public function services()
    {
        $services = Service::active()
            ->ordered()
            ->withCount(['activeProducts as product_count'])
            ->get();

        return view('public.services', compact('services'));
    }

    /**
     * Products list per service.
     */
    public function serviceProducts(Request $request, string $serviceSlug)
    {
        $service = Service::where('slug', $serviceSlug)->where('is_active', 1)->firstOrFail();

        $query = Product::with(['service', 'branch', 'creator'])
            ->where('service_id', $service->id)
            ->where('is_active', 1)
            ->whereHas('branch', fn($q) => $q->where('status', 1));

        // Sorting
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_low'  => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'featured'   => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
            default      => $query->orderByDesc('created_at'),
        };

        // Search
        if ($q = $request->get('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('short_description', 'like', "%{$q}%")
                  ->orWhere('tags', 'like', "%{$q}%");
            });
        }

        // Branch filter
        if ($branchId = $request->get('branch')) {
            $query->where('branch_id', $branchId);
        }

        $products = $query->paginate(16)->withQueryString();

        $branches = SellerBranch::whereHas('products', function ($q) use ($service) {
            $q->where('service_id', $service->id)->where('is_active', 1);
        })->where('status', 1)->get();

        $bookingMode = BookingMode::for($service->code);

        return view('public.service-products', compact(
            'service', 'products', 'branches', 'bookingMode', 'sort'
        ));
    }

    /**
     * Single product detail with dynamic booking UI.
     */
    public function showProduct(string $serviceSlug, string $productSlug)
    {
        $service = Service::where('slug', $serviceSlug)->where('is_active', 1)->firstOrFail();

        $product = Product::with(['service', 'branch.seller', 'images', 'creator'])
            ->where('slug', $productSlug)
            ->where('is_active', 1)
            ->firstOrFail();

        if ($product->service_id !== $service->id) {
            abort(404);
        }

        // Related products
        $related = Product::where('service_id', $service->id)
            ->where('id', '!=', $product->id)
            ->where('is_active', 1)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $bookingMode = BookingMode::for($service->code);

        return view('public.product-show', compact(
            'service', 'product', 'related', 'bookingMode'
        ));
    }
}
