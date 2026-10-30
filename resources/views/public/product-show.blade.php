@extends('layouts.guest')
@section('title', $product->name . ' — TolBazar')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-8" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-4 flex items-center gap-1.5 flex-wrap">
            <a href="{{ route('services.index') }}" class="hover:text-gray-900">Services</a>
            <span>/</span>
            <a href="{{ route('services.products', $service->slug) }}" class="hover:text-gray-900">{{ $service->name }}</a>
            <span>/</span>
            <span class="text-gray-900 font-medium truncate">{{ $product->name }}</span>
        </nav>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                <ul class="list-disc pl-4 mb-0">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif

        {{-- Main grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">

            {{-- LEFT: Images --}}
            <div class="lg:col-span-3">
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                    <div class="aspect-square bg-gray-100">
                        @if($product->thumbnail)
                            <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                @if($product->images->count() > 0)
                    <div class="grid grid-cols-5 gap-2 mt-2">
                        @foreach($product->images as $img)
                            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-200 hover:border-blue-300 transition cursor-pointer">
                                <img src="{{ Storage::url($img->image_path) }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- RIGHT: Info + Booking Form --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Product Info Card --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-5">
                    <p class="text-xs text-gray-500 mb-1 mt-0">
                        {{ $product->branch?->branch_name }}
                        <span class="text-gray-400">·</span>
                        {{ $product->branch?->seller?->store_name }}
                    </p>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 mt-0">{{ $product->name }}</h1>

                    @if($product->short_description)
                        <p class="text-sm text-gray-600 mb-3">{{ $product->short_description }}</p>
                    @endif

                    <div class="flex items-baseline gap-2 mb-4 flex-wrap">
                        <span class="text-3xl font-bold text-gray-900">Rs. {{ number_format($product->price, 2) }}</span>
                        @if($product->compare_price && $product->compare_price > $product->price)
                            <span class="text-sm text-gray-400 line-through">Rs. {{ number_format($product->compare_price, 2) }}</span>
                            @php $discount = round((($product->compare_price - $product->price) / $product->compare_price) * 100); @endphp
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Save {{ $discount }}%</span>
                        @endif
                    </div>

                    <div class="text-xs text-gray-500 mb-4 flex items-center gap-2 flex-wrap">
                        <span>Per {{ $product->unit }}</span>
                        @if($product->track_stock)
                            @if($product->stock_quantity > 0)
                                <span class="text-emerald-600 font-semibold">✓ In Stock</span>
                            @else
                                <span class="text-red-600 font-semibold">✗ Out of Stock</span>
                            @endif
                        @endif
                    </div>

                    {{-- DYNAMIC BOOKING FORM --}}
                    @include('public.partials.booking-form', [
                        'mode'    => $bookingMode,
                        'product' => $product,
                        'service' => $service,
                    ])
                </div>

                {{-- Seller Info --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-5">
                    <p class="text-xs text-gray-500 mb-2 mt-0 uppercase tracking-wide">Sold by</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold shrink-0">
                            {{ strtoupper(substr($product->branch?->seller?->store_name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 mb-0 truncate">{{ $product->branch?->seller?->store_name }}</p>
                            <p class="text-xs text-gray-500 mb-0">{{ $product->branch?->branch_name }}</p>
                        </div>
                    </div>
                </div>

                {{-- Service details (auto-generated from ProductDetailService) --}}
                @if($product->relationLoaded('service') && $product->service)
                    <div class="bg-white border border-gray-200 rounded-2xl p-5">
                        <p class="text-xs text-gray-500 mb-2 mt-0 uppercase tracking-wide">Service</p>
                        <p class="text-sm font-medium text-gray-800 mb-0">{{ $product->service->name }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Description --}}
        @if($product->description)
            <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3 mt-0">Description</h2>
                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed" style="white-space: pre-line;">{{ $product->description }}</div>
            </div>
        @endif

        {{-- Related Products --}}
        @if($related->count() > 0)
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 mt-0">Similar Products</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($related as $r)
                        <a href="{{ route('services.product', [$service->slug, $r->slug]) }}"
                           class="group bg-white border border-gray-200 rounded-2xl overflow-hidden hover:border-blue-300 hover:shadow-lg transition">
                            <div class="aspect-square bg-gray-100">
                                @if($r->thumbnail)
                                    <img src="{{ Storage::url($r->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @endif
                            </div>
                            <div class="p-3">
                                <p class="text-sm font-medium text-gray-900 mb-1 line-clamp-2 group-hover:text-blue-700">{{ $r->name }}</p>
                                <p class="text-sm font-bold text-gray-900 mb-0">Rs. {{ number_format($r->price, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
