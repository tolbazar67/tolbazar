@extends('layouts.seller')
@section('title', 'Payments Received — Seller')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-6xl mx-auto px-4">

        {{-- HEADER --}}
        <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 mt-0">Payments Received</h1>
                <p class="text-sm text-gray-500 mb-0">All customer payments credited to your wallet</p>
            </div>
            <a href="{{ route('seller.payments.products') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Product Breakdown
            </a>
        </div>

        {{-- ============================================================ --}}
        {{-- FINANCIAL OVERVIEW                                            --}}
        {{-- ============================================================ --}}
        <div class="bg-gradient-to-br from-emerald-500 via-teal-600 to-cyan-700 rounded-2xl p-6 mb-4 text-white relative overflow-hidden">
            <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-20 -left-10 w-48 h-48 rounded-full bg-white/5"></div>

            <div class="relative">
                <p class="text-xs uppercase tracking-wider text-white/70 mb-1 mt-0">Total Net Received</p>
                <p class="text-3xl md:text-4xl font-bold mb-1">Rs. {{ number_format($stats['net_total'], 2) }}</p>
                <p class="text-xs text-white/70 mb-5">After platform commission</p>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white/10 backdrop-blur rounded-xl p-3">
                        <p class="text-[10px] uppercase tracking-wider text-white/60 mb-1 mt-0">Customer Paid (Gross)</p>
                        <p class="text-base md:text-lg font-bold mb-0">Rs. {{ number_format($stats['gross_total'], 0) }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl p-3">
                        <p class="text-[10px] uppercase tracking-wider text-white/60 mb-1 mt-0">Commission Paid</p>
                        <p class="text-base md:text-lg font-bold text-amber-200 mb-0">− Rs. {{ number_format($stats['commission_total'], 0) }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl p-3">
                        <p class="text-[10px] uppercase tracking-wider text-white/60 mb-1 mt-0">Reversed</p>
                        <p class="text-base md:text-lg font-bold text-red-200 mb-0">− Rs. {{ number_format($stats['reversed_total'], 0) }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl p-3">
                        <p class="text-[10px] uppercase tracking-wider text-white/60 mb-1 mt-0">Payments Count</p>
                        <p class="text-base md:text-lg font-bold mb-0">{{ $stats['count_received'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- This month row --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-[10px] uppercase tracking-wider text-gray-500 mb-1 mt-0">This Month — Gross</p>
                <p class="text-lg font-bold text-gray-900 mb-0">Rs. {{ number_format($stats['month_gross'], 0) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-[10px] uppercase tracking-wider text-gray-500 mb-1 mt-0">This Month — Net</p>
                <p class="text-lg font-bold text-emerald-700 mb-0">Rs. {{ number_format($stats['month_net'], 0) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-[10px] uppercase tracking-wider text-gray-500 mb-1 mt-0">Wallet Balance</p>
                <p class="text-lg font-bold text-blue-700 mb-0">Rs. {{ number_format($wallet?->balance ?? 0, 2) }}</p>
            </div>
        </div>

        {{-- FILTERS --}}
        <form method="GET" class="bg-white border border-gray-200 rounded-2xl p-4 mb-4 flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by description or TXN ID..."
                   class="flex-1 min-w-[200px] px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">

            <input type="date" name="from" value="{{ request('from') }}"
                   class="px-3 py-2 rounded-lg border border-gray-300 text-sm">

            <input type="date" name="to" value="{{ request('to') }}"
                   class="px-3 py-2 rounded-lg border border-gray-300 text-sm">

            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700">Filter</button>

            @if(request()->hasAny(['q', 'from', 'to']))
                <a href="{{ route('seller.payments.index') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">Clear</a>
            @endif
        </form>

        {{-- TRANSACTIONS LIST --}}
        <div class="space-y-2">
            @forelse($transactions as $txn)
                @php
                    $isReversal = $txn->category === 'booking_reversal';
                    $meta = $txn->meta ?? [];
                    $deltaGross = (float) ($meta['delta_gross'] ?? 0);
                    $deltaComm  = (float) ($meta['delta_commission'] ?? 0);
                    $deltaNet   = (float) ($meta['delta_net'] ?? $txn->amount);
                    $totalPaid  = (float) ($meta['total_paid'] ?? 0);
                    $totalGross = (float) ($meta['total_gross'] ?? 0);
                    $totalNet   = (float) ($meta['total_net'] ?? 0);
                    $booking    = $txn->booking;
                    $isAdvance  = $booking && $booking->payment_status === 'partial';
                    $isFull     = $booking && $booking->payment_status === 'paid';
                @endphp

                <div class="bg-white border {{ $isReversal ? 'border-red-200' : 'border-gray-200' }} rounded-2xl p-4 hover:shadow-md transition">

                    <div class="flex items-start gap-3 flex-wrap md:flex-nowrap">

                        {{-- Icon --}}
                        <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center
                            {{ $isReversal ? 'bg-red-50' : 'bg-emerald-50' }}">
                            @if($isReversal)
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            @else
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            @endif
                        </div>

                        {{-- Main info --}}
                        <div class="flex-1 min-w-0">

                            {{-- Status + type badges --}}
                            <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                @if($isReversal)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-800">
                                        Reversal
                                    </span>
                                @elseif($isAdvance)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-800">
                                        ⏳ Advance
                                    </span>
                                @elseif($isFull)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                                        ✓ Full Payment
                                    </span>
                                @endif
                                <span class="font-mono text-[10px] text-gray-500">{{ $txn->transaction_id }}</span>
                            </div>

                            {{-- Description + customer --}}
                            <p class="text-sm font-semibold text-gray-900 mb-0.5 truncate">
                                {{ $txn->description }}
                            </p>
                            @if(!empty($meta['from_user_name']))
                                <p class="text-xs text-gray-500 mb-1 truncate">
                                    From: <strong class="text-gray-700">{{ $meta['from_user_name'] }}</strong>
                                    @if(!empty($meta['from_user_email']))
                                        <span class="text-gray-400">·</span>
                                        {{ $meta['from_user_email'] }}
                                    @endif
                                    @if(!empty($meta['booking_reference']))
                                        <span class="text-gray-400">·</span>
                                        <code class="text-[10px] font-mono">{{ $meta['booking_reference'] }}</code>
                                    @endif
                                </p>
                            @endif

                            <p class="text-[11px] text-gray-400 mb-0">
                                {{ $txn->created_at->format('d M Y, h:i A') }} ({{ $txn->created_at->diffForHumans() }})
                            </p>
                        </div>

                        {{-- Amount breakdown --}}
                        <div class="text-right shrink-0 min-w-[160px]">
                            @if($isReversal)
                                <p class="text-sm font-bold text-red-600 mb-0">− Rs. {{ number_format($txn->amount, 2) }}</p>
                                <p class="text-[10px] text-red-600 font-semibold mb-0">Reversed</p>
                            @else
                                {{-- Net credited (bold) --}}
                                <p class="text-base font-bold text-emerald-700 mb-0">
                                    + Rs. {{ number_format($txn->amount, 2) }}
                                </p>
                                <p class="text-[10px] text-emerald-700 font-semibold mb-1">Credited</p>

                                {{-- Gross → Commission breakdown --}}
                                @if($deltaGross > 0)
                                    <div class="bg-gray-50 rounded-lg px-2.5 py-1.5 text-[10px] text-left mt-1 space-y-0.5">
                                        <div class="flex justify-between gap-3">
                                            <span class="text-gray-500">Customer paid</span>
                                            <span class="text-gray-900 font-semibold">Rs. {{ number_format($deltaGross, 2) }}</span>
                                        </div>
                                        @if($deltaComm > 0)
                                            <div class="flex justify-between gap-3">
                                                <span class="text-gray-500">Commission</span>
                                                <span class="text-amber-700 font-semibold">− Rs. {{ number_format($deltaComm, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between gap-3 pt-1 border-t border-gray-200">
                                            <span class="text-gray-700 font-semibold">You got</span>
                                            <span class="text-emerald-700 font-bold">Rs. {{ number_format($deltaNet, 2) }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Cumulative booking totals (when booking loaded) --}}
                    @if($booking && !$isReversal)
                        <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center gap-3 text-[11px]">
                            <div class="flex items-center gap-1.5">
                                <span class="text-gray-500">Booking total:</span>
                                <span class="font-semibold text-gray-900">Rs. {{ number_format($booking->total, 2) }}</span>
                            </div>
                            <span class="text-gray-300">|</span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-gray-500">Paid:</span>
                                <span class="font-semibold text-emerald-700">Rs. {{ number_format($booking->paid_amount, 2) }}</span>
                            </div>
                            @if((float) $booking->due_amount > 0)
                                <span class="text-gray-300">|</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-gray-500">Due:</span>
                                    <span class="font-semibold text-amber-700">Rs. {{ number_format($booking->due_amount, 2) }}</span>
                                </div>
                            @endif
                            <span class="text-gray-300">|</span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-gray-500">Your total net:</span>
                                <span class="font-semibold text-blue-700">Rs. {{ number_format($booking->seller_amount, 2) }}</span>
                            </div>
                            <a href="{{ route('seller.bookings.show', $booking->reference) }}"
                               class="ml-auto inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-semibold">
                                View Booking
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-2xl p-16 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <p class="text-sm font-semibold text-gray-700 mb-1">No payments received yet</p>
                    <p class="text-xs text-gray-500 mb-0">Customer payments will appear here once they pay</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $transactions->links() }}</div>
    </div>
</div>
@endsection
