@extends('layouts.seller')
@section('title', 'Booking ' . $booking->reference . ' — Seller Panel')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

<style>[x-cloak]{display:none!important}</style>

@php
    $statusBadges = [
        'pending'    => ['label' => 'Pending',    'class' => 'bg-amber-100 text-amber-800',       'dot' => 'bg-amber-500'],
        'confirmed'  => ['label' => 'Confirmed',  'class' => 'bg-blue-100 text-blue-800',         'dot' => 'bg-blue-500'],
        'processing' => ['label' => 'Processing', 'class' => 'bg-indigo-100 text-indigo-800',     'dot' => 'bg-indigo-500'],
        'completed'  => ['label' => 'Completed',  'class' => 'bg-emerald-100 text-emerald-800',   'dot' => 'bg-emerald-500'],
        'cancelled'  => ['label' => 'Cancelled',  'class' => 'bg-red-100 text-red-800',           'dot' => 'bg-red-500'],
    ];
    $paymentBadges = [
        'unpaid'   => ['label' => 'Unpaid',   'class' => 'bg-red-100 text-red-800'],
        'partial'  => ['label' => 'Partial',  'class' => 'bg-amber-100 text-amber-800'],
        'paid'     => ['label' => 'Paid',     'class' => 'bg-emerald-100 text-emerald-800'],
        'refunded' => ['label' => 'Refunded', 'class' => 'bg-gray-100 text-gray-800'],
    ];

    $st = $statusBadges[$booking->status] ?? ['label' => ucfirst($booking->status), 'class' => 'bg-gray-100 text-gray-800', 'dot' => 'bg-gray-500'];
    $pt = $paymentBadges[$booking->payment_status] ?? ['label' => ucfirst($booking->payment_status), 'class' => 'bg-gray-100 text-gray-800'];

    $canAccept   = $booking->status === 'pending';
    $canReject   = $booking->status === 'pending';
    $canStart    = $booking->status === 'confirmed';
    $canComplete = in_array($booking->status, ['confirmed', 'processing']);
    $canCancel   = in_array($booking->status, ['pending', 'confirmed', 'processing']);

    $isFullyPaid        = $booking->isFullyPaid();
    $isPartial          = (float) $booking->paid_amount > 0 && (float) $booking->due_amount > 0;

    // ✅ FIX: allow cash collection for ANY payment method when due > 0
    // (previously required payment_method === 'cod')
    $canCollectCash = (float) $booking->due_amount > 0
                    && in_array($booking->status, ['processing', 'completed']);

    // Wallet vs cash breakdown for cancel warning
    $walletPortion = (float) ($booking->wallet_paid_amount ?? 0);
    $cashPortion   = (float) ($booking->cash_collected_amount ?? 0);
    if ($walletPortion <= 0 && $cashPortion <= 0 && (float) $booking->paid_amount > 0) {
        if ($booking->payment_method === 'wallet') {
            $walletPortion = (float) $booking->paid_amount;
        } else {
            $cashPortion = (float) $booking->paid_amount;
        }
    }
@endphp

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-5xl mx-auto px-4">

        <a href="{{ route('seller.bookings.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Bookings
        </a>

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
        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                <ul class="list-disc pl-4 mb-0">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif

        {{-- ACTION BAR --}}
        @if($canAccept || $canStart || $canComplete || $canCancel || $canReject)
            <div class="bg-white border border-gray-200 rounded-2xl p-4 mb-4" x-data="{ rejectOpen: false, cancelOpen: false }">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 mb-0.5 mt-0">Actions</p>
                        <p class="text-sm font-semibold text-gray-900 mb-0">
                            @if($canAccept) This booking needs your approval
                            @elseif($canStart) Ready to start processing
                            @elseif($canComplete) Ready to complete
                            @elseif($canCancel) Can be cancelled
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        @if($canAccept)
                            <form action="{{ route('seller.bookings.accept', $booking->reference) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    Accept Booking
                                </button>
                            </form>
                        @endif

                        @if($canReject)
                            <button type="button" @click="rejectOpen = !rejectOpen"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-red-600 bg-white border border-red-200 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reject
                            </button>
                        @endif

                        @if($canStart)
                            <form action="{{ route('seller.bookings.start', $booking->reference) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Start Processing
                                </button>
                            </form>
                        @endif

                        @if($canComplete)
                            <form action="{{ route('seller.bookings.complete', $booking->reference) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Mark this booking as completed?');">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Mark Complete
                                </button>
                            </form>
                        @endif

                        @if($canCancel)
                            <button type="button" @click="cancelOpen = !cancelOpen"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-600 bg-gray-50 border border-gray-200 hover:bg-gray-100 transition">
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>

                @if($canReject)
                    <div x-show="rejectOpen" x-cloak x-transition class="mt-4 pt-4 border-t border-gray-100">
                        <form action="{{ route('seller.bookings.reject', $booking->reference) }}" method="POST">
                            @csrf
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Rejection Reason</label>
                            <textarea name="reason" rows="2" required maxlength="500" placeholder="Why are you rejecting this booking?"
                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm resize-none focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none"></textarea>

                            @if($walletPortion > 0 || $cashPortion > 0)
                                <div class="mt-2 space-y-0.5">
                                    @if($walletPortion > 0)
                                        <p class="text-[11px] text-amber-700 mb-0">
                                            ⚠️ Rs. {{ number_format($walletPortion, 2) }} will be refunded to customer's wallet.
                                        </p>
                                    @endif
                                    @if($cashPortion > 0)
                                        <p class="text-[11px] text-amber-700 mb-0">
                                            ⚠️ Rs. {{ number_format($cashPortion, 2) }} cash refund must be handled manually.
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <div class="flex gap-2 mt-2">
                                <button type="button" @click="rejectOpen = false"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">Cancel</button>
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-red-600 hover:bg-red-700">
                                    Confirm Reject
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                @if($canCancel)
                    <div x-show="cancelOpen" x-cloak x-transition class="mt-4 pt-4 border-t border-gray-100">
                        <form action="{{ route('seller.bookings.cancel', $booking->reference) }}" method="POST">
                            @csrf
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Cancellation Reason</label>
                            <textarea name="reason" rows="2" required maxlength="500" placeholder="Why are you cancelling this booking?"
                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm resize-none focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none"></textarea>

                            @if($walletPortion > 0 || $cashPortion > 0)
                                <div class="mt-2 space-y-0.5">
                                    @if($walletPortion > 0)
                                        <p class="text-[11px] text-amber-700 mb-0">
                                            ⚠️ Rs. {{ number_format($walletPortion, 2) }} will be refunded to customer's wallet.
                                        </p>
                                    @endif
                                    @if($cashPortion > 0)
                                        <p class="text-[11px] text-amber-700 mb-0">
                                            ⚠️ Rs. {{ number_format($cashPortion, 2) }} cash refund must be handled manually.
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <div class="flex gap-2 mt-2">
                                <button type="button" @click="cancelOpen = false"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">Cancel</button>
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-red-600 hover:bg-red-700">
                                    Confirm Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @endif

        {{-- MAIN CARD --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-4">
            <div class="h-1.5
                @if($booking->status === 'completed') bg-gradient-to-r from-emerald-400 to-teal-600
                @elseif($booking->status === 'cancelled') bg-gradient-to-r from-red-400 to-red-600
                @elseif($booking->status === 'confirmed') bg-gradient-to-r from-blue-400 to-indigo-600
                @elseif($booking->status === 'processing') bg-gradient-to-r from-indigo-400 to-purple-600
                @else bg-gradient-to-r from-amber-400 to-orange-500 @endif">
            </div>

            <div class="p-6 md:p-8">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-3 mb-5 flex-wrap">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $st['class'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                {{ $st['label'] }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $pt['class'] }}">
                                {{ $pt['label'] }}
                            </span>
                        </div>
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1 mt-0 font-mono">{{ $booking->reference }}</h1>
                        <p class="text-xs text-gray-500 mb-0">
                            Received {{ $booking->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>

                {{-- Customer --}}
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-5 mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 mt-0">Customer</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-0.5 mt-0">Name</p>
                            <p class="font-semibold text-gray-900 mb-0">{{ $booking->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-0.5 mt-0">Phone</p>
                            <p class="font-semibold text-gray-900 mb-0">
                                <a href="tel:{{ $booking->customer_phone }}" class="text-blue-600 hover:underline">
                                    {{ $booking->customer_phone }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-0.5 mt-0">Email</p>
                            <p class="font-semibold text-gray-900 mb-0 truncate">
                                <a href="mailto:{{ $booking->customer_email }}" class="text-blue-600 hover:underline">
                                    {{ $booking->customer_email }}
                                </a>
                            </p>
                        </div>
                    </div>
                    @if($booking->branch)
                        <div class="mt-3 pt-3 border-t border-gray-200 text-sm">
                            <span class="text-[10px] text-gray-500 uppercase tracking-wide">Branch:</span>
                            <span class="font-semibold text-gray-900 ml-1">{{ $booking->branch->branch_name }}</span>
                        </div>
                    @endif
                </div>

                {{-- Service meta --}}
                @if(!empty($booking->meta))
                    <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-5 mb-4">
                        <p class="text-xs font-semibold text-blue-900 uppercase tracking-wide mb-2 mt-0">Booking Details</p>
                        <div class="space-y-1.5 text-xs text-blue-800">
                            @foreach($booking->meta as $key => $value)
                                @if(!empty($value))
                                    <div class="flex justify-between gap-2">
                                        <span class="capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                        <span class="font-semibold text-right">
                                            @if(is_array($value)) {{ implode(', ', $value) }}
                                            @elseif(in_array($key, ['check_in', 'check_out', 'travel_date', 'appointment_date', 'preferred_date', 'departure_date']))
                                                {{ \Carbon\Carbon::parse($value)->format('d M Y') }}
                                            @else {{ $value }} @endif
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Items --}}
                <div class="border border-gray-200 rounded-xl p-4 mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 mt-0">Items</p>
                    <div class="space-y-2">
                        @foreach($booking->items as $item)
                            <div class="flex items-center gap-3">
                                @if(!empty($item['thumbnail']))
                                    <img src="{{ Storage::url($item['thumbnail']) }}" class="w-10 h-10 rounded-lg object-cover shrink-0">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 font-medium mb-0 truncate">{{ $item['name'] ?? 'Item' }}</p>
                                    <p class="text-xs text-gray-500 mb-0">Qty: {{ $item['qty'] ?? 1 }} × Rs. {{ number_format($item['price'] ?? 0, 2) }}</p>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 mb-0 shrink-0">
                                    Rs. {{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 1), 2) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Payment breakdown --}}
                <div class="bg-emerald-50/60 border border-emerald-100 rounded-xl p-5 mb-4">
                    <p class="text-xs font-semibold text-emerald-900 uppercase tracking-wide mb-3 mt-0">Payment</p>

                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Subtotal</span>
                            <span class="text-gray-900 font-medium">Rs. {{ number_format($booking->subtotal, 2) }}</span>
                        </div>

                        @foreach(($booking->charges_breakdown ?? []) as $ch)
                            <div class="flex justify-between">
                                <span class="text-gray-700">{{ $ch['name'] }}</span>
                                <span class="text-gray-900 font-medium">Rs. {{ number_format($ch['amount'], 2) }}</span>
                            </div>
                        @endforeach

                        @foreach(($booking->tax_breakdown ?? []) as $tax)
                            <div class="flex justify-between {{ !empty($tax['is_inclusive']) ? 'text-gray-400 italic' : '' }}">
                                <span>{{ $tax['name'] }}</span>
                                <span>Rs. {{ number_format($tax['amount'], 2) }}</span>
                            </div>
                        @endforeach

                        <div class="flex justify-between font-bold text-emerald-900 pt-2 border-t border-emerald-200 mt-2">
                            <span>Total</span>
                            <span class="text-lg">Rs. {{ number_format($booking->total, 2) }}</span>
                        </div>

                        @if((float) $booking->paid_amount > 0)
                            <div class="flex justify-between text-emerald-700 font-semibold pt-1">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Paid
                                </span>
                                <span>Rs. {{ number_format($booking->paid_amount, 2) }}</span>
                            </div>
                            @if((float) $booking->wallet_paid_amount > 0)
                                <div class="flex justify-between text-[11px] text-gray-500 pl-6">
                                    <span>· via Wallet</span>
                                    <span>Rs. {{ number_format($booking->wallet_paid_amount, 2) }}</span>
                                </div>
                            @endif
                            @if((float) $booking->cash_collected_amount > 0)
                                <div class="flex justify-between text-[11px] text-gray-500 pl-6">
                                    <span>· via Cash</span>
                                    <span>Rs. {{ number_format($booking->cash_collected_amount, 2) }}</span>
                                </div>
                            @endif
                        @endif

                        @if((float) $booking->due_amount > 0 && $booking->status !== 'cancelled')
                            <div class="flex justify-between text-amber-700 font-semibold">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Remaining Due
                                </span>
                                <span>Rs. {{ number_format($booking->due_amount, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- PAYMENT COLLECTION STATUS BANNER                              --}}
                {{-- ============================================================ --}}
                <div class="mb-4">
                    @if($isFullyPaid)
                        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-start gap-3">
                            <svg class="w-6 h-6 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-emerald-900 mb-0.5 mt-0">✓ Fully Paid</p>
                                <p class="text-xs text-emerald-800 mb-0">
                                    Rs. {{ number_format($booking->total, 2) }} complete.
                                    @if($booking->payment_collected_at)
                                        Collected {{ $booking->payment_collected_at->format('d M Y, h:i A') }}.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @elseif($isPartial)
                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 flex items-start gap-3">
                            <svg class="w-6 h-6 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-amber-900 mb-0.5 mt-0">⚠️ Partially Paid</p>
                                <p class="text-xs text-amber-800 mb-1">
                                    Paid: Rs. {{ number_format($booking->paid_amount, 2) }}
                                    · <strong>Due: Rs. {{ number_format($booking->due_amount, 2) }}</strong>
                                </p>
                                @if($booking->payment_method === 'cod')
                                    <p class="text-[11px] text-amber-700 mb-0">Collect remaining cash from customer.</p>
                                @else
                                    <p class="text-[11px] text-amber-700 mb-0">
                                        Advance paid via {{ strtoupper($booking->payment_method) }} — collect remaining cash.
                                    </p>
                                @endif
                            </div>
                        </div>
                    @elseif((float) $booking->due_amount > 0 && $booking->status !== 'cancelled')
                        <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 flex items-start gap-3">
                            <svg class="w-6 h-6 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-blue-900 mb-0.5 mt-0">
                                    {{ $booking->payment_method === 'cod' ? 'Cash on Delivery' : 'Payment Due' }}
                                </p>
                                <p class="text-xs text-blue-800 mb-0">
                                    Collect <strong>Rs. {{ number_format($booking->due_amount, 2) }}</strong> from customer.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ============================================================ --}}
                {{-- CASH COLLECTION CARD                                          --}}
                {{-- ============================================================ --}}
                @if($canCollectCash)
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-2xl p-5 mb-4"
                         x-data="{ open: false }">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <p class="text-xs font-semibold text-emerald-900 uppercase tracking-wide mb-0.5 mt-0">
                                    💵 Cash Collection
                                </p>
                                <p class="text-lg font-bold text-emerald-900 mb-0">
                                    Rs. {{ number_format($booking->due_amount, 2) }} <span class="text-xs font-normal text-emerald-700">to collect</span>
                                </p>
                            </div>
                            <button type="button" @click="open = !open"
                                    class="shrink-0 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                                <span x-text="open ? 'Cancel' : 'Mark as Received'">Mark as Received</span>
                            </button>
                        </div>

                        <div x-show="open" x-cloak x-transition>
                            <form action="{{ route('seller.bookings.collect', $booking->reference) }}" method="POST" class="space-y-3">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-emerald-900 mb-1">
                                            Amount Collected (Rs.) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="amount" value="{{ (float) $booking->due_amount }}"
                                               min="0.01" max="{{ (float) $booking->due_amount }}" step="0.01" required
                                               class="w-full px-3 py-2.5 rounded-lg border border-emerald-300 text-sm font-semibold focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none bg-white">
                                        <p class="text-[11px] text-emerald-700 mt-1 mb-0">
                                            Full due: Rs. {{ number_format($booking->due_amount, 2) }} · Partial allowed
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-emerald-900 mb-1">
                                            Cash Txn Note (optional)
                                        </label>
                                        <input type="text" name="reference_note" maxlength="100"
                                               placeholder="e.g., Receipt #12345"
                                               class="w-full px-3 py-2.5 rounded-lg border border-emerald-300 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none bg-white">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-emerald-900 mb-1">Notes (optional)</label>
                                    <textarea name="notes" rows="2" maxlength="500"
                                              placeholder="Any details about this collection..."
                                              class="w-full px-3 py-2 rounded-lg border border-emerald-300 text-sm resize-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none bg-white"></textarea>
                                </div>

                                <div class="flex gap-2">
                                    <button type="button" @click="open = false"
                                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm">
                                        ✓ Confirm Cash Received
                                    </button>
                                </div>
                            </form>
                        </div>

                        @if($booking->paymentCollections->count())
                            <div class="mt-4 pt-4 border-t border-emerald-200">
                                <p class="text-[10px] font-semibold text-emerald-900 uppercase tracking-wide mb-2 mt-0">Collection History</p>
                                <div class="space-y-1.5">
                                    @foreach($booking->paymentCollections->sortByDesc('created_at') as $col)
                                        <div class="flex items-center justify-between text-xs bg-white/70 rounded-lg px-3 py-2">
                                            <div>
                                                <span class="font-semibold text-emerald-900 capitalize">{{ $col->method }}</span>
                                                <span class="text-emerald-700"> · {{ $col->collector?->name ?? 'Unknown' }}</span>
                                                <span class="text-gray-500"> · {{ $col->created_at->format('d M, h:i A') }}</span>
                                                @if($col->reference_note)
                                                    <span class="text-gray-400 italic"> · "{{ $col->reference_note }}"</span>
                                                @endif
                                            </div>
                                            <span class="font-bold text-emerald-900">Rs. {{ number_format($col->amount, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Notes --}}
                @if($booking->notes)
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 mb-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 mt-0">Notes / History</p>
                        <p class="text-sm text-gray-800 mb-0 whitespace-pre-line">{{ $booking->notes }}</p>
                    </div>
                @endif

                {{-- Wallet transaction ref --}}
                @if($booking->walletTransaction)
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-4 text-xs text-blue-800">
                        <strong>Wallet transaction:</strong>
                        <code class="font-mono font-bold bg-white px-1.5 py-0.5 rounded">{{ $booking->walletTransaction->transaction_id }}</code>
                        — {{ ucfirst($booking->walletTransaction->status) }}
                    </div>
                @endif

                                {{-- Footer --}}
                <div class="flex gap-2 flex-wrap pt-4 border-t border-gray-100">
                    <a href="{{ route('seller.bookings.invoice', $booking->reference) }}"
                       class="flex-1 min-w-[140px] px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm text-center transition inline-flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View Seller Invoice
                    </a>
                    <a href="{{ route('seller.bookings.index') }}"
                       class="flex-1 min-w-[140px] px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 text-center transition">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
