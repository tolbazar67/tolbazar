@extends('layouts.guest')
@section('title', 'Booking Confirmed — TolBazar')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-2xl mx-auto px-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-emerald-400 to-teal-600"></div>

            <div class="p-8">

                {{-- Success header --}}
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-emerald-50 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2 mt-0">Booking Confirmed!</h1>
                    <p class="text-sm text-gray-500 mb-0">
                        Reference:
                        <code class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded">{{ $booking->reference }}</code>
                    </p>
                </div>

                {{-- Customer details --}}
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
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                            @if($booking->status === 'completed') bg-emerald-100 text-emerald-800
                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-amber-100 text-amber-800 @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>

                {{-- Service-specific details --}}
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
                @if(!empty($booking->items))
                    <div class="border border-gray-200 rounded-xl p-4 mb-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 mt-0">Items</p>
                        <div class="space-y-2">
                            @foreach($booking->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">
                                        {{ $item['name'] ?? 'Item' }}
                                        @if(($item['qty'] ?? 1) > 1)
                                            <span class="text-xs text-gray-500">× {{ $item['qty'] }}</span>
                                        @endif
                                    </span>
                                    <span class="font-semibold text-gray-900">
                                        Rs. {{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 1), 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Payment breakdown --}}
                <div class="bg-emerald-50/60 border border-emerald-100 rounded-xl p-5 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-emerald-900 uppercase tracking-wide mb-0 mt-0">Payment Breakdown</p>
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full
                            @if($booking->payment_status === 'paid') bg-emerald-200 text-emerald-900
                            @elseif($booking->payment_status === 'partial') bg-amber-200 text-amber-900
                            @else bg-red-200 text-red-900 @endif">
                            {{ strtoupper($booking->payment_status) }}
                        </span>
                    </div>

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

                        {{-- Paid / Due --}}
                        @if((float) $booking->paid_amount > 0)
                            <div class="flex justify-between text-emerald-700 font-semibold pt-1">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Paid via Wallet
                                </span>
                                <span>Rs. {{ number_format($booking->paid_amount, 2) }}</span>
                            </div>
                        @endif

                        @if((float) $booking->due_amount > 0)
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

                {{-- Payment status message --}}
                @if($booking->payment_status === 'partial')
                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 mb-4">
                        <strong>Advance payment received.</strong>
                        Rs. {{ number_format($booking->due_amount, 2) }} remains due — please pay on delivery / at the service location.
                    </div>
                @elseif($booking->payment_status === 'unpaid' && $booking->payment_method === 'cod')
                    <div class="rounded-xl bg-blue-50 border border-blue-200 p-3 text-xs text-blue-800 mb-4">
                        <strong>Cash on Delivery.</strong>
                        Please have Rs. {{ number_format($booking->total, 2) }} ready when your order is delivered.
                    </div>
                @elseif($booking->payment_status === 'paid' && $booking->paid_amount > 0)
                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800 mb-4">
                        <strong>Fully paid from wallet.</strong>
                        No further payment needed. Thank you!
                    </div>
                @endif

                {{-- Info --}}
                <div class="rounded-xl bg-gray-50 border border-gray-200 p-3 text-xs text-gray-700 mb-5">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    We'll notify you by email once the seller confirms your booking. Keep your reference number safe.
                </div>

                {{-- Actions --}}
                <div class="flex gap-2">
                    <a href="{{ route('services.index') }}"
                       class="flex-1 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 text-center transition">
                        Continue Shopping
                    </a>
                    <a href="{{ route('home') }}"
                       class="flex-1 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm text-center transition">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
