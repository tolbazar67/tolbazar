@extends('layouts.guest')
@section('title', 'Booking ' . $booking->reference . ' — TolBazar')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

<style>[x-cloak]{display:none!important}</style>

@php
    $statusBadges = [
        'pending'    => ['label' => 'Pending',    'class' => 'bg-amber-100 text-amber-800',       'icon' => 'clock'],
        'confirmed'  => ['label' => 'Confirmed',  'class' => 'bg-blue-100 text-blue-800',         'icon' => 'check'],
        'processing' => ['label' => 'Processing', 'class' => 'bg-indigo-100 text-indigo-800',     'icon' => 'cog'],
        'completed'  => ['label' => 'Completed',  'class' => 'bg-emerald-100 text-emerald-800',   'icon' => 'check'],
        'cancelled'  => ['label' => 'Cancelled',  'class' => 'bg-red-100 text-red-800',           'icon' => 'x'],
    ];

    $paymentBadges = [
        'unpaid'   => ['label' => 'Unpaid',   'class' => 'bg-red-100 text-red-800'],
        'partial'  => ['label' => 'Partially Paid',  'class' => 'bg-amber-100 text-amber-800'],
        'paid'     => ['label' => 'Fully Paid',     'class' => 'bg-emerald-100 text-emerald-800'],
        'refunded' => ['label' => 'Refunded', 'class' => 'bg-gray-100 text-gray-800'],
    ];

    $modeLabels = [
        'cart'        => 'Cart Order',
        'booking'     => 'Hotel Booking',
        'appointment' => 'Appointment',
        'enrollment'  => 'Enrollment',
        'route'       => 'Ride Booking',
        'service'     => 'Service Request',
        'quote'       => 'Project Quote',
        'package'     => 'Travel Package',
    ];

    $st = $statusBadges[$booking->status] ?? ['label' => ucfirst($booking->status), 'class' => 'bg-gray-100 text-gray-800', 'icon' => 'info'];
    $pt = $paymentBadges[$booking->payment_status] ?? ['label' => ucfirst($booking->payment_status), 'class' => 'bg-gray-100 text-gray-800'];
    $modeLabel = $modeLabels[$booking->booking_mode] ?? ucfirst($booking->booking_mode);

    $canCancel = in_array($booking->status, ['pending', 'confirmed']);
    $canPay    = (float) $booking->due_amount > 0
                 && $booking->payment_method === 'wallet'
                 && $booking->status !== 'cancelled';
@endphp

<div class="min-h-screen py-8" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-3xl mx-auto px-4">

        {{-- Back --}}
        <a href="{{ route('my-bookings.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to My Bookings
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

        {{-- Main card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-4">

            {{-- Header strip --}}
            <div class="h-1.5
                @if($booking->status === 'completed') bg-gradient-to-r from-emerald-400 to-teal-600
                @elseif($booking->status === 'cancelled') bg-gradient-to-r from-red-400 to-red-600
                @elseif($booking->status === 'confirmed') bg-gradient-to-r from-blue-400 to-indigo-600
                @else bg-gradient-to-r from-amber-400 to-orange-500 @endif">
            </div>

            <div class="p-6 md:p-8">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-3 mb-5 flex-wrap">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0">{{ $modeLabel }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $st['class'] }}">
                                {{ $st['label'] }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $pt['class'] }}">
                                {{ $pt['label'] }}
                            </span>
                        </div>
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1 mt-0">
                            <span class="font-mono">{{ $booking->reference }}</span>
                        </h1>
                        <p class="text-xs text-gray-500 mb-0">
                            Booked on {{ $booking->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>

                    @if($canCancel)
                        <form action="{{ route('my-bookings.cancel', $booking->reference) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to cancel this booking?{{ (float) $booking->paid_amount > 0 && $booking->payment_method === 'wallet' ? ' Rs. ' . number_format($booking->paid_amount, 2) . ' will be refunded to your wallet.' : '' }}');">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel Booking
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Customer --}}
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-5 mb-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Customer</span>
                        <span class="font-medium text-gray-900">{{ $booking->customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Email</span>
                        <span class="font-medium text-gray-900 text-right truncate">{{ $booking->customer_email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Phone</span>
                        <span class="font-medium text-gray-900">{{ $booking->customer_phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Payment Method</span>
                        <span class="font-medium text-gray-900 uppercase">{{ $booking->payment_method }}</span>
                    </div>
                    @if($booking->branch)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Branch</span>
                            <span class="font-medium text-gray-900 text-right">
                                {{ $booking->branch->branch_name }}
                                @if($booking->seller) · {{ $booking->seller->store_name }} @endif
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Service specific meta --}}
                @if(!empty($booking->meta))
                    <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-5 mb-4">
                        <p class="text-xs font-semibold text-blue-900 uppercase tracking-wide mb-2 mt-0">Booking Details</p>
                        <div class="space-y-1.5 text-xs text-blue-800">
                            @foreach($booking->meta as $key => $value)
                                @if(!empty($value))
                                    <div class="flex justify-between gap-2">
                                        <span class="capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                        <span class="font-semibold text-right">
                                            @if(is_array($value))
                                                {{ implode(', ', $value) }}
                                            @elseif(in_array($key, ['check_in', 'check_out', 'travel_date', 'appointment_date', 'preferred_date', 'departure_date']))
                                                {{ \Carbon\Carbon::parse($value)->format('d M Y') }}
                                            @else
                                                {{ $value }}
                                            @endif
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
                                    <img src="{{ Storage::url($item['thumbnail']) }}"
                                         class="w-10 h-10 rounded-lg object-cover shrink-0" alt="">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 font-medium mb-0 truncate">{{ $item['name'] ?? 'Item' }}</p>
                                    <p class="text-xs text-gray-500 mb-0">
                                        Qty: {{ $item['qty'] ?? 1 }} × Rs. {{ number_format($item['price'] ?? 0, 2) }}
                                    </p>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 mb-0 shrink-0">
                                    Rs. {{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 1), 2) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Financial breakdown --}}
                <div class="bg-emerald-50/60 border border-emerald-100 rounded-xl p-5 mb-4">
                    <p class="text-xs font-semibold text-emerald-900 uppercase tracking-wide mb-3 mt-0">Payment Breakdown</p>

                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Subtotal</span>
                            <span class="text-gray-900 font-medium">Rs. {{ number_format($booking->subtotal, 2) }}</span>
                        </div>

                        @foreach(($booking->charges_breakdown ?? []) as $ch)
                            <div class="flex justify-between">
                                <span class="text-gray-700 flex items-center gap-1">
                                    {{ $ch['name'] }}
                                    @if(($ch['type'] ?? '') === 'percentage')
                                        <span class="text-[11px] text-gray-500">({{ $ch['rate'] }}%)</span>
                                    @endif
                                </span>
                                <span class="text-gray-900 font-medium">Rs. {{ number_format($ch['amount'], 2) }}</span>
                            </div>
                        @endforeach

                        @foreach(($booking->tax_breakdown ?? []) as $tax)
                            <div class="flex justify-between {{ !empty($tax['is_inclusive']) ? 'text-gray-400 italic' : '' }}">
                                <span>
                                    {{ $tax['name'] }}
                                    @if(($tax['type'] ?? '') === 'percentage')
                                        <span class="text-[11px] text-gray-500">({{ $tax['rate'] }}%)</span>
                                    @endif
                                </span>
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

                {{-- Notes --}}
                @if($booking->notes)
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 mb-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 mt-0">Notes</p>
                        <p class="text-sm text-gray-800 mb-0">{{ $booking->notes }}</p>
                    </div>
                @endif

                {{-- Pay remaining block --}}
                @if($canPay)
                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 mb-4"
                         x-data="{ open: false }">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <p class="text-sm font-semibold text-amber-900 mb-0.5 mt-0">Remaining payment due</p>
                                <p class="text-xs text-amber-800 mb-0">
                                    Rs. {{ number_format($booking->due_amount, 2) }} — pay now from your wallet
                                </p>
                            </div>
                            <button type="button" @click="open = !open"
                                    class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 transition">
                                <span x-text="open ? 'Cancel' : 'Pay Now'">Pay Now</span>
                            </button>
                        </div>

                        <div x-show="open" x-cloak x-transition>
                            <form action="{{ route('my-bookings.pay-remaining', $booking->reference) }}" method="POST">
                                @csrf
                                <label class="block text-xs font-medium text-amber-900 mb-1.5">
                                    Enter Wallet PIN to confirm
                                </label>
                                <div class="flex gap-2">
                                    <input type="password" name="wallet_pin"
                                           maxlength="6" pattern="\d{6}" inputmode="numeric" required
                                           placeholder="••••••"
                                           class="flex-1 px-3 py-2 rounded-lg border border-amber-300 text-sm text-center font-mono tracking-widest focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 transition shadow-sm">
                                        Pay Rs. {{ number_format($booking->due_amount, 2) }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Status info banner --}}
                @if($booking->status === 'cancelled')
                    <div class="rounded-xl bg-red-50 border border-red-200 p-3 text-xs text-red-800 mb-4">
                        <strong>This booking has been cancelled.</strong>
                        @if($booking->payment_status === 'refunded')
                            Your paid amount has been refunded to your wallet.
                        @endif
                    </div>
                @elseif($booking->payment_status === 'partial')
                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 mb-4">
                        <strong>Advance payment received.</strong>
                        Rs. {{ number_format($booking->due_amount, 2) }} remains due — please complete the payment.
                    </div>
                @elseif($booking->payment_status === 'unpaid' && $booking->payment_method === 'cod')
                    <div class="rounded-xl bg-blue-50 border border-blue-200 p-3 text-xs text-blue-800 mb-4">
                        <strong>Cash on Delivery.</strong>
                        Please have Rs. {{ number_format($booking->total, 2) }} ready when your order is delivered.
                    </div>
                @elseif($booking->payment_status === 'paid' && (float) $booking->paid_amount > 0)
                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800 mb-4">
                        <strong>Fully paid.</strong> Thank you!
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('my-bookings.invoice', $booking->reference) }}"
                       class="flex-1 min-w-[140px] px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm text-center transition inline-flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View Invoice
                    </a>
                    <a href="{{ route('my-bookings.index') }}"
                       class="flex-1 min-w-[140px] px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 text-center transition">
                        Back to List
                    </a>
                    <a href="{{ route('services.index') }}"
                       class="flex-1 min-w-[140px] px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm text-center transition">
                        Book Again
                    </a>
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="text-center text-xs text-gray-500">
            Need help? Contact support with your reference
            <code class="font-mono font-bold text-gray-800 bg-gray-100 px-1.5 py-0.5 rounded">{{ $booking->reference }}</code>
        </div>
    </div>
</div>
@endsection
