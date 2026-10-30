@extends('layouts.seller')
@section('title', 'Product Payment Breakdown — Seller')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

@php
    $serviceLabels = [
        'HOTEL' => 'Hotel', 'RETAIL' => 'Retail', 'RESTAURANT' => 'Restaurant',
        'EDUCATION' => 'Education', 'HEALTHCARE' => 'Healthcare', 'TRANSPORT' => 'Transport',
        'BEAUTY' => 'Beauty', 'REPAIR' => 'Repair', 'CONSTRUCTION' => 'Construction',
        'TRAVEL' => 'Travel',
    ];
@endphp

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Back --}}
        <a href="{{ route('seller.payments.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Payments
        </a>

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 mt-0">Product Payment Breakdown</h1>
            <p class="text-sm text-gray-500 mb-0">Revenue per product — gross, commission, and net</p>
        </div>

        {{-- ============================================================ --}}
        {{-- TOP FINANCIAL CARDS                                           --}}
        {{-- ============================================================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-4 text-white">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[10px] uppercase tracking-wider text-white/70 mb-0 mt-0">Net Revenue</p>
                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <p class="text-xl md:text-2xl font-bold mb-0">Rs. {{ number_format($stats['net_revenue'], 0) }}</p>
                <p class="text-[10px] text-white/70 mt-1 mb-0">After commission</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 mb-0 mt-0">Gross Revenue</p>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xl md:text-2xl font-bold text-gray-900 mb-0">Rs. {{ number_format($stats['gross_revenue'], 0) }}</p>
                <p class="text-[10px] text-gray-500 mt-1 mb-0">Customer paid</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 mb-0 mt-0">Commission Paid</p>
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m8-8l-8 8 8 8"/></svg>
                </div>
                <p class="text-xl md:text-2xl font-bold text-amber-600 mb-0">Rs. {{ number_format($stats['commission_total'], 0) }}</p>
                <p class="text-[10px] text-gray-500 mt-1 mb-0">Platform fee</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 mb-0 mt-0">Products Sold</p>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <p class="text-xl md:text-2xl font-bold text-gray-900 mb-0">{{ $stats['total_products'] }}</p>
                <p class="text-[10px] text-gray-500 mt-1 mb-0">{{ $stats['total_orders'] }} orders</p>
            </div>
        </div>

        {{-- Advance vs Full breakdown --}}
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[10px] uppercase tracking-wider text-amber-800 mb-0 mt-0">Advance Payments</p>
                    <span class="text-[10px] font-bold text-amber-900 bg-amber-200 px-1.5 py-0.5 rounded-full">{{ $stats['advance_orders'] }}</span>
                </div>
                <p class="text-lg md:text-xl font-bold text-amber-900 mb-0">Rs. {{ number_format($stats['advance_amount'], 0) }}</p>
                <p class="text-[10px] text-amber-700 mt-1 mb-0">Partially paid bookings</p>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[10px] uppercase tracking-wider text-emerald-800 mb-0 mt-0">Full Payments</p>
                    <span class="text-[10px] font-bold text-emerald-900 bg-emerald-200 px-1.5 py-0.5 rounded-full">{{ $stats['full_orders'] }}</span>
                </div>
                <p class="text-lg md:text-xl font-bold text-emerald-900 mb-0">Rs. {{ number_format($stats['full_amount'], 0) }}</p>
                <p class="text-[10px] text-emerald-700 mt-1 mb-0">Fully paid bookings</p>
            </div>
        </div>

        {{-- FILTERS --}}
        <form method="GET" class="bg-white border border-gray-200 rounded-2xl p-4 mb-4 flex flex-wrap gap-2">
            <select name="service" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                <option value="">All Services</option>
                @foreach($serviceCodes as $code)
                    <option value="{{ $code }}" @selected(request('service') === $code)>
                        {{ $serviceLabels[$code] ?? $code }}
                    </option>
                @endforeach
            </select>

            <select name="payment_type" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                <option value="">All Payments</option>
                <option value="advance" @selected(request('payment_type') === 'advance')>Advance Only</option>
                <option value="full" @selected(request('payment_type') === 'full')>Full Only</option>
            </select>

            <input type="date" name="from" value="{{ request('from') }}"
                   class="px-3 py-2 rounded-lg border border-gray-300 text-sm">

            <input type="date" name="to" value="{{ request('to') }}"
                   class="px-3 py-2 rounded-lg border border-gray-300 text-sm">

            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700">Filter</button>

            @if(request()->hasAny(['service', 'payment_type', 'from', 'to']))
                <a href="{{ route('seller.payments.products') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">Clear</a>
            @endif
        </form>

        {{-- TABLE --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider px-4 py-3">Product</th>
                            <th class="text-right text-xs font-semibold text-gray-600 uppercase tracking-wider px-4 py-3">Orders</th>
                            <th class="text-right text-xs font-semibold text-gray-600 uppercase tracking-wider px-4 py-3">Advance / Full</th>
                            <th class="text-right text-xs font-semibold text-gray-600 uppercase tracking-wider px-4 py-3">Qty Sold</th>
                            <th class="text-right text-xs font-semibold text-gray-600 uppercase tracking-wider px-4 py-3">Gross</th>
                            <th class="text-right text-xs font-semibold text-amber-700 uppercase tracking-wider px-4 py-3">Commission</th>
                            <th class="text-right text-xs font-semibold text-emerald-700 uppercase tracking-wider px-4 py-3">Net Earned</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($productStats as $p)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($p['thumbnail'])
                                            <img src="{{ Storage::url($p['thumbnail']) }}" class="w-10 h-10 rounded-lg object-cover shrink-0" alt="">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300 shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 mb-0 truncate">{{ $p['name'] }}</p>
                                            <p class="text-[10px] text-gray-500 mb-0">ID: #{{ $p['product_id'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <p class="text-sm font-semibold text-gray-900 mb-0">{{ $p['orders'] }}</p>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5 text-[10px]">
                                        @if($p['advance_orders'] > 0)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded font-semibold bg-amber-100 text-amber-800">
                                                {{ $p['advance_orders'] }}A
                                            </span>
                                        @endif
                                        @if($p['full_orders'] > 0)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded font-semibold bg-emerald-100 text-emerald-800">
                                                {{ $p['full_orders'] }}F
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-gray-400 mb-0 mt-0.5">
                                        @if($p['advance_amount'] > 0 || $p['full_amount'] > 0)
                                            Rs. {{ number_format($p['advance_amount'], 0) }} / Rs. {{ number_format($p['full_amount'], 0) }}
                                        @endif
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-700">{{ $p['qty_sold'] }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">
                                    Rs. {{ number_format($p['gross_revenue'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-amber-700">
                                    − Rs. {{ number_format($p['commission'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-emerald-700">
                                    Rs. {{ number_format($p['net_revenue'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-16 text-center">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-sm font-semibold text-gray-700 mb-1">No paid bookings yet</p>
                                    <p class="text-xs text-gray-500 mb-0">Product-level breakdown will appear here</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Legend --}}
        <div class="mt-3 flex flex-wrap items-center gap-4 text-[11px] text-gray-500">
            <span class="inline-flex items-center gap-1">
                <span class="inline-flex items-center px-1.5 py-0.5 rounded font-semibold bg-amber-100 text-amber-800">A</span>
                Advance order (partial payment)
            </span>
            <span class="inline-flex items-center gap-1">
                <span class="inline-flex items-center px-1.5 py-0.5 rounded font-semibold bg-emerald-100 text-emerald-800">F</span>
                Full order (fully paid)
            </span>
            <span>·</span>
            <span>Net = Gross − Commission (proportional split from booking)</span>
        </div>
    </div>
</div>
@endsection
