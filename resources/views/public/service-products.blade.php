@extends('layouts.guest')
@section('title', $service->name . ' — TolBazar')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-8" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-4 flex items-center gap-1.5 flex-wrap">
            <a href="{{ route('services.index') }}" class="hover:text-gray-900">Services</a>
            <span>/</span>
            <span class="text-gray-900 font-medium">{{ $service->name }}</span>
        </nav>

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 mt-0">{{ $service->name }}</h1>
            <p class="text-sm text-gray-500 mb-0">
                {{ $products->total() }} {{ \Illuminate\Support\Str::plural('product', $products->total()) }} found
                @if($service->description)
                    · {{ $service->description }}
                @endif
            </p>
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-white border border-gray-200 rounded-2xl p-4 mb-6 flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..."
                   class="flex-1 min-w-[200px] px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">

            <select name="branch" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="">All Branches</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" @selected(request('branch') == $b->id)>{{ $b->branch_name }}</option>
                @endforeach
            </select>

            <select name="sort" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="newest" @selected(request('sort','newest')==='newest')>Newest</option>
                <option value="price_low" @selected(request('sort')==='price_low')>Price: Low → High</option>
                <option value="price_high" @selected(request('sort')==='price_high')>Price: High → Low</option>
                <option value="featured" @selected(request('sort')==='featured')>Featured</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">Filter</button>

            @if(request()->hasAny(['q', 'branch', 'sort']))
                <a href="{{ route('services.products', $service->slug) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                    Clear
                </a>
            @endif
        </form>

        {{-- Products Grid --}}
        @if($products->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-600 mb-2">No products available</p>
                <p class="text-sm text-gray-500 mb-0">Try adjusting filters or check back later.</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($products as $p)
                    <a href="{{ route('services.product', [$service->slug, $p->slug]) }}"
                       class="group bg-white border border-gray-200 rounded-2xl overflow-hidden hover:border-blue-300 hover:shadow-lg transition flex flex-col">

                        {{-- Image --}}
                        <div class="aspect-square bg-gray-100 overflow-hidden relative">
                            @if($p->thumbnail)
                                <img src="{{ Storage::url($p->thumbnail) }}" alt="{{ $p->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            @if($p->is_featured)
                                <span class="absolute top-2 left-2 bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">★ Featured</span>
                            @endif
                            @if($p->track_stock && $p->stock_quantity <= 0)
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                    <span class="text-white text-xs font-bold uppercase">Out of Stock</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-3 flex-1 flex flex-col">
                            <p class="text-xs text-gray-500 mb-1">{{ $p->branch?->branch_name }}</p>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 flex-1 group-hover:text-blue-700">{{ $p->name }}</h3>
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span class="text-base font-bold text-gray-900">Rs. {{ number_format($p->price, 2) }}</span>
                                @if($p->compare_price && $p->compare_price > $p->price)
                                    <span class="text-xs text-gray-400 line-through">Rs. {{ number_format($p->compare_price, 0) }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection
