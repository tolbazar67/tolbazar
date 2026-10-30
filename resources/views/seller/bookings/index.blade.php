@extends('layouts.seller')
@section('title', 'Bookings — Seller Panel')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

@php
    $statusBadges = [
        'pending'    => ['label' => 'Pending',    'class' => 'bg-amber-100 text-amber-800',       'dot' => 'bg-amber-500'],
        'confirmed'  => ['label' => 'Confirmed',  'class' => 'bg-blue-100 text-blue-800',         'dot' => 'bg-blue-500'],
        'processing' => ['label' => 'Processing', 'class' => 'bg-indigo-100 text-indigo-800',     'dot' => 'bg-indigo-500'],
        'completed'  => ['label' => 'Completed',  'class' => 'bg-emerald-100 text-emerald-800',   'dot' => 'bg-emerald-500'],
        'cancelled'  => ['label' => 'Cancelled',  'class' => 'bg-red-100 text-red-800',           'dot' => 'bg-red-500'],
    ];

    $paymentBadges = [
        'unpaid'   => ['label' => 'Unpaid',   'class' => 'bg-red-50 text-red-700 border border-red-200'],
        'partial'  => ['label' => 'Partial',  'class' => 'bg-amber-50 text-amber-700 border border-amber-200'],
        'paid'     => ['label' => 'Paid',     'class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200'],
        'refunded' => ['label' => 'Refunded', 'class' => 'bg-gray-50 text-gray-700 border border-gray-200'],
    ];

    $modeLabels = [
        'cart'        => ['label' => 'Cart Order',        'color' => 'bg-emerald-50 text-emerald-700'],
        'booking'     => ['label' => 'Hotel',             'color' => 'bg-blue-50 text-blue-700'],
        'appointment' => ['label' => 'Appointment',       'color' => 'bg-pink-50 text-pink-700'],
        'enrollment'  => ['label' => 'Enrollment',        'color' => 'bg-violet-50 text-violet-700'],
        'route'       => ['label' => 'Ride',              'color' => 'bg-amber-50 text-amber-700'],
        'service'     => ['label' => 'Service',           'color' => 'bg-slate-50 text-slate-700'],
        'quote'       => ['label' => 'Quote',             'color' => 'bg-orange-50 text-orange-700'],
        'package'     => ['label' => 'Travel',            'color' => 'bg-teal-50 text-teal-700'],
    ];

    $serviceLabels = [
        'HOTEL' => 'Hotel', 'RETAIL' => 'Retail', 'RESTAURANT' => 'Restaurant',
        'EDUCATION' => 'Education', 'HEALTHCARE' => 'Healthcare', 'TRANSPORT' => 'Transport',
        'BEAUTY' => 'Beauty', 'REPAIR' => 'Repair', 'CONSTRUCTION' => 'Construction',
        'TRAVEL' => 'Travel',
    ];
@endphp

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-7xl mx-auto px-4">

        {{-- HEADER --}}
        <div class="flex items-start justify-between gap-3 mb-6 flex-wrap">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 mt-0">Bookings</h1>
                <p class="text-sm text-gray-500 mb-0">Manage all customer bookings for your store</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="font-semibold text-amber-700">{{ $stats['pending'] }}</span>
                    <span class="text-gray-500">awaiting action</span>
                </span>
            </div>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        {{-- ============================================================ --}}
        {{-- STATS --}}
        {{-- ============================================================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    @if($stats['pending'] > 0)
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded-full">ACTION</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1 mt-0">Pending</p>
                <p class="text-2xl font-bold text-gray-900 mb-0">{{ $stats['pending'] }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                </div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1 mt-0">In Progress</p>
                <p class="text-2xl font-bold text-gray-900 mb-0">{{ $stats['confirmed'] + $stats['processing'] }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1 mt-0">Completed</p>
                <p class="text-2xl font-bold text-gray-900 mb-0">{{ $stats['completed'] }}</p>
            </div>

            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="text-xs text-white/70 uppercase tracking-wide mb-1 mt-0">Revenue (This Month)</p>
                <p class="text-xl font-bold mb-0">Rs. {{ number_format($stats['revenue_month'], 0) }}</p>
            </div>
        </div>

        {{-- Small metrics row --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-3 flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-0.5 mt-0">Total Bookings</p>
                    <p class="text-base font-bold text-gray-900 mb-0">{{ $stats['total'] }}</p>
                </div>
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-3 flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-0.5 mt-0">Total Revenue</p>
                    <p class="text-base font-bold text-gray-900 mb-0">Rs. {{ number_format($stats['revenue_total'], 0) }}</p>
                </div>
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-3 flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-0.5 mt-0">Cancelled</p>
                    <p class="text-base font-bold text-red-600 mb-0">{{ $stats['cancelled'] }}</p>
                </div>
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-3 flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-0.5 mt-0">Total Due</p>
                    <p class="text-base font-bold text-amber-600 mb-0">Rs. {{ number_format($stats['due_total'], 0) }}</p>
                </div>
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- FILTERS --}}
        {{-- ============================================================ --}}
        <form method="GET" class="bg-white border border-gray-200 rounded-2xl p-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 mb-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search reference, customer..."
                       class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">

                <select name="status" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                    <option value="">All Statuses</option>
                    @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'processing' => 'Processing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $k => $v)
                        <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
                    @endforeach
                </select>

                <select name="payment" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                    <option value="">All Payments</option>
                    @foreach(['unpaid' => 'Unpaid', 'partial' => 'Partial', 'paid' => 'Paid', 'refunded' => 'Refunded'] as $k => $v)
                        <option value="{{ $k }}" @selected(request('payment') === $k)>{{ $v }}</option>
                    @endforeach
                </select>

                <select name="service" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                    <option value="">All Services</option>
                    @foreach($serviceCodes as $code)
                        <option value="{{ $code }}" @selected(request('service') === $code)>
                            {{ $serviceLabels[$code] ?? $code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @if($branches->count())
                    <select name="branch" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                        <option value="">All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" @selected(request('branch') == $b->id)>{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                @endif

                <input type="date" name="from" value="{{ request('from') }}"
                       class="px-3 py-2 rounded-lg border border-gray-300 text-sm" placeholder="From">

                <input type="date" name="to" value="{{ request('to') }}"
                       class="px-3 py-2 rounded-lg border border-gray-300 text-sm" placeholder="To">

                <select name="sort" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                    <option value="priority" @selected(request('sort') === 'priority')>Priority (Pending first)</option>
                    <option value="amount_high" @selected(request('sort') === 'amount_high')>Amount: High → Low</option>
                    <option value="amount_low" @selected(request('sort') === 'amount_low')>Amount: Low → High</option>
                </select>
            </div>

            <div class="flex gap-2 mt-3">
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700">Filter</button>
                @if(request()->hasAny(['q', 'status', 'payment', 'service', 'branch', 'from', 'to', 'sort']))
                    <a href="{{ route('seller.bookings.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">Clear</a>
                @endif
            </div>
        </form>

        {{-- ============================================================ --}}
        {{-- BOOKINGS LIST --}}
        {{-- ============================================================ --}}
        @if($bookings->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-sm font-semibold text-gray-700 mb-1">
                    {{ request()->hasAny(['q', 'status', 'payment', 'service', 'branch', 'from', 'to']) ? 'No bookings match your filters' : 'No bookings yet' }}
                </p>
                <p class="text-xs text-gray-500 mb-0">
                    {{ request()->hasAny(['q', 'status', 'payment', 'service', 'branch', 'from', 'to']) ? 'Try clearing filters.' : 'Customer bookings will appear here.' }}
                </p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($bookings as $b)
                    @php
                        $st = $statusBadges[$b->status] ?? ['label' => ucfirst($b->status), 'class' => 'bg-gray-100 text-gray-800', 'dot' => 'bg-gray-500'];
                        $pt = $paymentBadges[$b->payment_status] ?? ['label' => ucfirst($b->payment_status), 'class' => 'bg-gray-50 text-gray-700 border border-gray-200'];
                        $mode = $modeLabels[$b->booking_mode] ?? ['label' => ucfirst($b->booking_mode), 'color' => 'bg-gray-50 text-gray-700'];
                        $firstItem = $b->items[0] ?? null;
                        $isPending = $b->status === 'pending';
                    @endphp

                    <div class="bg-white border {{ $isPending ? 'border-amber-300 bg-amber-50/30' : 'border-gray-200' }} rounded-2xl p-4 hover:shadow-md transition">
                        <div class="flex items-start gap-3 flex-wrap md:flex-nowrap">

                            {{-- Thumbnail --}}
                            <div class="w-14 h-14 rounded-xl bg-gray-100 shrink-0 overflow-hidden">
                                @if($firstItem && !empty($firstItem['thumbnail']))
                                    <img src="{{ Storage::url($firstItem['thumbnail']) }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Center info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $st['class'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                        {{ $st['label'] }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $mode['color'] }}">
                                        {{ $mode['label'] }}
                                    </span>
                                    @if($b->payment_status !== 'unpaid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $pt['class'] }}">
                                            {{ $pt['label'] }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm font-semibold text-gray-900 mb-0.5">
                                    <a href="{{ route('seller.bookings.show', $b->reference) }}" class="hover:text-blue-700">
                                        {{ $b->customer_name }}
                                    </a>
                                    <span class="text-xs text-gray-400 font-normal">·</span>
                                    <span class="text-xs text-gray-500 font-mono">{{ $b->reference }}</span>
                                </p>

                                <p class="text-xs text-gray-500 mb-0">
                                    {{ $firstItem['name'] ?? 'Booking' }}
                                    @if(count($b->items) > 1)
                                        <span class="text-gray-400">+{{ count($b->items) - 1 }} more</span>
                                    @endif
                                    @if($b->branch)
                                        · {{ $b->branch->branch_name }}
                                    @endif
                                </p>

                                <p class="text-xs text-gray-400 mb-0 mt-0.5">
                                    <svg class="inline w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $b->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Right: amount --}}
                            <div class="text-right shrink-0">
                                <p class="text-base font-bold text-gray-900 mb-0.5">Rs. {{ number_format($b->total, 2) }}</p>
                                @if((float) $b->due_amount > 0 && $b->status !== 'cancelled')
                                    <p class="text-[10px] font-semibold text-amber-700 mb-0">Due: Rs. {{ number_format($b->due_amount, 2) }}</p>
                                @elseif((float) $b->paid_amount > 0 && $b->status !== 'cancelled')
                                    <p class="text-[10px] font-semibold text-emerald-700 mb-0">Paid: Rs. {{ number_format($b->paid_amount, 2) }}</p>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-col gap-1.5 shrink-0">
                                @if($isPending)
                                    <form action="{{ route('seller.bookings.accept', $b->reference) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            Accept
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('seller.bookings.show', $b->reference) }}"
                                   class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition">
                                    View
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">{{ $bookings->links() }}</div>
        @endif
    </div>
</div>
@endsection
