@extends('layouts.guest')
@section('title', 'My Bookings — TolBazar')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

@php
    $modeLabels = [
        'cart'        => ['label' => 'Cart Order',        'color' => 'bg-emerald-50 text-emerald-700'],
        'booking'     => ['label' => 'Hotel Booking',     'color' => 'bg-blue-50 text-blue-700'],
        'appointment' => ['label' => 'Appointment',       'color' => 'bg-pink-50 text-pink-700'],
        'enrollment'  => ['label' => 'Enrollment',        'color' => 'bg-violet-50 text-violet-700'],
        'route'       => ['label' => 'Ride Booking',      'color' => 'bg-amber-50 text-amber-700'],
        'service'     => ['label' => 'Service Request',   'color' => 'bg-slate-50 text-slate-700'],
        'quote'       => ['label' => 'Project Quote',     'color' => 'bg-orange-50 text-orange-700'],
        'package'     => ['label' => 'Travel Package',    'color' => 'bg-teal-50 text-teal-700'],
    ];

    $statusBadges = [
        'pending'    => ['label' => 'Pending',    'class' => 'bg-amber-100 text-amber-800'],
        'confirmed'  => ['label' => 'Confirmed',  'class' => 'bg-blue-100 text-blue-800'],
        'processing' => ['label' => 'Processing', 'class' => 'bg-indigo-100 text-indigo-800'],
        'completed'  => ['label' => 'Completed',  'class' => 'bg-emerald-100 text-emerald-800'],
        'cancelled'  => ['label' => 'Cancelled',  'class' => 'bg-red-100 text-red-800'],
    ];

    $paymentBadges = [
        'unpaid'   => ['label' => 'Unpaid',   'class' => 'bg-red-50 text-red-700 border border-red-200'],
        'partial'  => ['label' => 'Partial',  'class' => 'bg-amber-50 text-amber-700 border border-amber-200'],
        'paid'     => ['label' => 'Paid',     'class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200'],
        'refunded' => ['label' => 'Refunded', 'class' => 'bg-gray-50 text-gray-700 border border-gray-200'],
    ];
@endphp

<div class="min-h-screen py-8" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-5xl mx-auto px-4">

        {{-- Header --}}
        <div class="mb-6 flex items-start justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1 mt-0">My Bookings</h1>
                <p class="text-sm text-gray-500 mb-0">Track all your orders and bookings</p>
            </div>
            <a href="{{ route('services.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Booking
            </a>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <p class="text-xs font-medium text-gray-500 mb-0 uppercase tracking-wide">Total</p>
                </div>
                <p class="text-xl font-bold text-gray-900 mb-0">{{ $stats['total'] }}</p>
            </div>
            <a href="{{ route('my-bookings.index', ['status' => 'pending']) }}"
               class="bg-white border border-gray-200 rounded-xl p-4 hover:border-amber-300 hover:shadow-sm transition">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <p class="text-xs font-medium text-gray-500 mb-0 uppercase tracking-wide">Pending</p>
                </div>
                <p class="text-xl font-bold text-gray-900 mb-0">{{ $stats['pending'] }}</p>
            </a>
            <a href="{{ route('my-bookings.index', ['status' => 'completed']) }}"
               class="bg-white border border-gray-200 rounded-xl p-4 hover:border-emerald-300 hover:shadow-sm transition">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <p class="text-xs font-medium text-gray-500 mb-0 uppercase tracking-wide">Completed</p>
                </div>
                <p class="text-xl font-bold text-gray-900 mb-0">{{ $stats['completed'] }}</p>
            </a>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <p class="text-xs font-medium text-gray-500 mb-0 uppercase tracking-wide">Due Total</p>
                </div>
                <p class="text-xl font-bold text-amber-700 mb-0">Rs. {{ number_format($stats['due_total'], 0) }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-white border border-gray-200 rounded-2xl p-4 mb-4 flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by reference..."
                   class="flex-1 min-w-[180px] px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">

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

            <select name="sort" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                <option value="amount_high" @selected(request('sort') === 'amount_high')>Amount: High → Low</option>
                <option value="amount_low" @selected(request('sort') === 'amount_low')>Amount: Low → High</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">Filter</button>

            @if(request()->hasAny(['q', 'status', 'payment', 'sort']))
                <a href="{{ route('my-bookings.index') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">Clear</a>
            @endif
        </form>

        {{-- Bookings List --}}
        @if($bookings->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-16 text-center">
                <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <p class="text-gray-600 font-semibold mb-1">
                    {{ request()->hasAny(['q', 'status', 'payment']) ? 'No bookings match your filters' : 'No bookings yet' }}
                </p>
                <p class="text-sm text-gray-500 mb-4">
                    {{ request()->hasAny(['q', 'status', 'payment']) ? 'Try clearing the filters to see all bookings.' : 'Start exploring services and make your first booking!' }}
                </p>
                <a href="{{ route('services.index') }}"
                   class="inline-block px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                    Browse Services
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($bookings as $b)
                    @php
                        $mode    = $modeLabels[$b->booking_mode] ?? ['label' => ucfirst($b->booking_mode), 'color' => 'bg-gray-50 text-gray-700'];
                        $status  = $statusBadges[$b->status]       ?? ['label' => ucfirst($b->status), 'class' => 'bg-gray-100 text-gray-800'];
                        $payment = $paymentBadges[$b->payment_status] ?? ['label' => ucfirst($b->payment_status), 'class' => 'bg-gray-50 text-gray-700 border border-gray-200'];
                        $firstItem = $b->items[0] ?? null;
                    @endphp

                    <a href="{{ route('my-bookings.show', $b->reference) }}"
                       class="block bg-white border border-gray-200 rounded-2xl p-4 hover:border-blue-300 hover:shadow-md transition">

                        <div class="flex items-start gap-4">

                            {{-- Thumbnail --}}
                            <div class="w-16 h-16 rounded-xl bg-gray-100 shrink-0 overflow-hidden">
                                @if($firstItem && !empty($firstItem['thumbnail']))
                                    <img src="{{ Storage::url($firstItem['thumbnail']) }}"
                                         class="w-full h-full object-cover" alt="">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Middle: Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $mode['color'] }}">
                                        {{ $mode['label'] }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                    @if($b->payment_status !== 'unpaid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $payment['class'] }}">
                                            {{ $payment['label'] }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm font-semibold text-gray-900 mb-0.5 truncate">
                                    {{ $firstItem['name'] ?? 'Booking' }}
                                    @if(count($b->items) > 1)
                                        <span class="text-xs text-gray-500 font-normal">+ {{ count($b->items) - 1 }} more</span>
                                    @endif
                                </p>

                                <p class="text-xs text-gray-500 mb-1 truncate">
                                    <span class="font-mono">{{ $b->reference }}</span>
                                    @if($b->branch)
                                        · {{ $b->branch->branch_name }}
                                    @endif
                                </p>

                                <p class="text-xs text-gray-400 mb-0">
                                    {{ $b->created_at->format('d M Y, h:i A') }}
                                </p>
                            </div>

                            {{-- Right: Amount + Arrow --}}
                            <div class="text-right shrink-0">
                                <p class="text-base font-bold text-gray-900 mb-0.5">
                                    Rs. {{ number_format($b->total, 2) }}
                                </p>
                                @if((float) $b->due_amount > 0 && $b->status !== 'cancelled')
                                    <p class="text-[11px] font-semibold text-amber-700 mb-0">
                                        Due: Rs. {{ number_format($b->due_amount, 2) }}
                                    </p>
                                @elseif((float) $b->paid_amount > 0 && $b->status !== 'cancelled')
                                    <p class="text-[11px] font-semibold text-emerald-700 mb-0">
                                        Paid: Rs. {{ number_format($b->paid_amount, 2) }}
                                    </p>
                                @endif
                                <svg class="w-4 h-4 text-gray-400 ml-auto mt-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">{{ $bookings->links() }}</div>
        @endif
    </div>
</div>
@endsection
