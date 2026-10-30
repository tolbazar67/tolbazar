<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Invoice {{ $booking->reference }} — TolBazar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <style>
        body { background: #f3f4f6; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .invoice-shell { box-shadow: none !important; border: none !important; margin: 0 !important; }
            @page { margin: 12mm; size: A4; }
            .no-break { page-break-inside: avoid; }
        }
    </style>
</head>
<body style="font-family:Inter,system-ui,sans-serif;">

@php
    $modeLabels = [
        'cart' => 'Cart Order', 'booking' => 'Hotel Booking',
        'appointment' => 'Appointment', 'enrollment' => 'Enrollment',
        'route' => 'Ride Booking', 'service' => 'Service Request',
        'quote' => 'Project Quote', 'package' => 'Travel Package',
    ];
    $modeLabel = $modeLabels[$booking->booking_mode] ?? ucfirst($booking->booking_mode);

    $paymentLabel = match ($booking->payment_method) {
        'cod' => 'Cash on Delivery', 'wallet' => 'TolBazar Wallet',
        'esewa' => 'eSewa', 'khalti' => 'Khalti', 'bank' => 'Bank Transfer',
        default => ucfirst((string) $booking->payment_method),
    };

    $paymentStatusColors = [
        'paid' => 'bg-emerald-100 text-emerald-800',
        'partial' => 'bg-amber-100 text-amber-800',
        'unpaid' => 'bg-red-100 text-red-800',
        'refunded' => 'bg-gray-100 text-gray-800',
    ];
    $pStatusClass = $paymentStatusColors[$booking->payment_status] ?? 'bg-gray-100 text-gray-800';

    $statusColors = [
        'pending' => 'bg-amber-100 text-amber-800',
        'confirmed' => 'bg-blue-100 text-blue-800',
        'processing' => 'bg-indigo-100 text-indigo-800',
        'completed' => 'bg-emerald-100 text-emerald-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
    $bStatusClass = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800';

    $walletPaid   = (float) ($booking->wallet_paid_amount ?? 0);
    $cashPaid     = (float) ($booking->cash_collected_amount ?? 0);
    $dueAmount    = (float) ($booking->due_amount ?? 0);
    $total        = (float) $booking->total;
    $paidAmount   = (float) $booking->paid_amount;

    // Legacy fallback
    if ($walletPaid <= 0 && $cashPaid <= 0 && $paidAmount > 0) {
        if ($booking->payment_method === 'wallet') $walletPaid = $paidAmount;
        else $cashPaid = $paidAmount;
    }

    // Seller-side earnings
    $grossAmount      = (float) ($booking->seller_gross_amount ?? 0);
    $commissionAmount = (float) ($booking->seller_commission_amount ?? $booking->commission_amount ?? 0);
    $netEarning       = (float) ($booking->seller_amount ?? 0);

    // Legacy — if snapshot fields are 0 but seller_amount > 0
    if ($grossAmount <= 0 && $paidAmount > 0) {
        $grossAmount = $paidAmount;
    }
@endphp

{{-- ================= CONTROLS (hidden on print) ================= --}}
<div class="no-print bg-white border-b border-gray-200">
    <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
        <a href="{{ route('seller.bookings.show', $booking->reference) }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Booking
        </a>
        <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print / Save as PDF
        </button>
    </div>
</div>

{{-- ================= INVOICE ================= --}}
<div class="max-w-3xl mx-auto my-6 px-4">
    <div class="invoice-shell bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="h-1.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-emerald-600"></div>

        <div class="p-8">

            {{-- ============ HEADER ============ --}}
            <div class="flex items-start justify-between gap-4 mb-8 flex-wrap">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-bold text-lg">
                            T
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900 mb-0 leading-tight">TolBazar</p>
                            <p class="text-[11px] text-gray-500 mb-0">Seller Copy — Internal</p>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wider text-gray-500 mb-1">Seller Invoice</p>
                    <p class="text-lg font-bold text-gray-900 mb-0 font-mono">{{ $booking->reference }}</p>
                    <p class="text-xs text-gray-500 mt-1 mb-0">
                        Issued: {{ $booking->created_at->format('d M Y, h:i A') }}
                    </p>
                </div>
            </div>

            {{-- ============ BILL TO / FROM ============ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-2">Customer</p>
                    <p class="text-sm font-bold text-gray-900 mb-1">{{ $booking->customer_name }}</p>
                    <p class="text-xs text-gray-600 mb-0.5">{{ $booking->customer_email }}</p>
                    <p class="text-xs text-gray-600 mb-0">{{ $booking->customer_phone }}</p>
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-700 mb-2">Your Store</p>
                    <p class="text-sm font-bold text-blue-900 mb-1">
                        {{ $booking->seller?->store_name ?? '—' }}
                    </p>
                    @if($booking->branch)
                        <p class="text-xs text-blue-700 mb-0.5">{{ $booking->branch->branch_name }}</p>
                    @endif
                    @if($booking->branch?->address ?? null)
                        <p class="text-xs text-blue-600 mb-0">{{ $booking->branch->address }}</p>
                    @endif
                </div>
            </div>

            {{-- ============ BOOKING / PAYMENT DETAILS ============ --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 pb-6 border-b border-gray-200">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-1">Booking Type</p>
                    <p class="text-sm font-semibold text-gray-900 mb-0">{{ $modeLabel }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-1">Payment Method</p>
                    <p class="text-sm font-semibold text-gray-900 mb-0">{{ $paymentLabel }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-1">Booking Status</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $bStatusClass }}">
                        {{ $booking->status }}
                    </span>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-1">Payment Status</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $pStatusClass }}">
                        {{ $booking->payment_status }}
                    </span>
                </div>
            </div>

            {{-- ============ ITEMS ============ --}}
            <div class="mb-6 no-break">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Items</p>
                <div class="overflow-hidden border border-gray-200 rounded-xl">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left text-[10px] font-semibold uppercase tracking-wider text-gray-600 px-4 py-2.5 w-10">#</th>
                                <th class="text-left text-[10px] font-semibold uppercase tracking-wider text-gray-600 px-4 py-2.5">Product / Service</th>
                                <th class="text-right text-[10px] font-semibold uppercase tracking-wider text-gray-600 px-4 py-2.5 w-20">Qty</th>
                                <th class="text-right text-[10px] font-semibold uppercase tracking-wider text-gray-600 px-4 py-2.5 w-28">Unit Price</th>
                                <th class="text-right text-[10px] font-semibold uppercase tracking-wider text-gray-600 px-4 py-2.5 w-28">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach(($booking->items ?? []) as $index => $item)
                                @php
                                    $qty = (int) ($item['qty'] ?? 1);
                                    $price = (float) ($item['price'] ?? 0);
                                    $lineTotal = $price * $qty;
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-xs text-gray-500 align-top">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 align-top">
                                        <p class="text-sm font-semibold text-gray-900 mb-0">{{ $item['name'] ?? 'Item' }}</p>
                                        @if(!empty($item['meta']['note']))
                                            <p class="text-[11px] text-gray-500 mb-0 mt-0.5 italic">{{ $item['meta']['note'] }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-700 text-right align-top">{{ $qty }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-700 text-right align-top">Rs. {{ number_format($price, 2) }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right align-top">Rs. {{ number_format($lineTotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ============ 2-COLUMN: CUSTOMER PAYMENT + YOUR EARNINGS ============ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 no-break">

                {{-- LEFT: What customer paid (invoice view) --}}
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-3">Invoice Summary</p>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Subtotal</span>
                            <span class="text-gray-900">Rs. {{ number_format($booking->subtotal, 2) }}</span>
                        </div>
                        @foreach(($booking->charges_breakdown ?? []) as $ch)
                            <div class="flex justify-between">
                                <span class="text-gray-700">{{ $ch['name'] ?? 'Charge' }}</span>
                                <span class="text-gray-900">Rs. {{ number_format($ch['amount'] ?? 0, 2) }}</span>
                            </div>
                        @endforeach
                        @foreach(($booking->tax_breakdown ?? []) as $tax)
                            <div class="flex justify-between {{ !empty($tax['is_inclusive']) ? 'text-gray-400 italic' : 'text-gray-700' }}">
                                <span>{{ $tax['name'] ?? 'Tax' }}</span>
                                <span>Rs. {{ number_format($tax['amount'] ?? 0, 2) }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between pt-2 border-t border-gray-200 mt-2">
                            <span class="text-base font-bold text-gray-900">Total</span>
                            <span class="text-base font-bold text-gray-900">Rs. {{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: What seller gets --}}
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-xl p-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-emerald-900 mb-3">Your Earnings</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-emerald-800">Gross (Customer Paid)</span>
                            <span class="font-semibold text-emerald-900">Rs. {{ number_format($grossAmount, 2) }}</span>
                        </div>
                        @if($commissionAmount > 0)
                            <div class="flex justify-between">
                                <span class="text-amber-800">Commission</span>
                                <span class="font-semibold text-amber-700">− Rs. {{ number_format($commissionAmount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-emerald-200 mt-2">
                            <span class="text-base font-bold text-emerald-900">Net Earned</span>
                            <span class="text-base font-bold text-emerald-700">Rs. {{ number_format($netEarning, 2) }}</span>
                        </div>

                        <div class="mt-3 pt-3 border-t border-emerald-200">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-emerald-800 mb-1.5">Customer Payment Split</p>
                            <div class="space-y-1 text-xs">
                                @if($walletPaid > 0)
                                    <div class="flex justify-between">
                                        <span class="text-emerald-700">Wallet Paid</span>
                                        <span class="font-semibold text-emerald-900">Rs. {{ number_format($walletPaid, 2) }}</span>
                                    </div>
                                @endif
                                @if($cashPaid > 0)
                                    <div class="flex justify-between">
                                        <span class="text-emerald-700">Cash Collected</span>
                                        <span class="font-semibold text-emerald-900">Rs. {{ number_format($cashPaid, 2) }}</span>
                                    </div>
                                @endif
                                @if($dueAmount > 0 && $booking->status !== 'cancelled')
                                    <div class="flex justify-between">
                                        <span class="text-amber-800 font-semibold">Still Owed by Customer</span>
                                        <span class="font-bold text-amber-700">Rs. {{ number_format($dueAmount, 2) }}</span>
                                    </div>
                                @endif
                                @if($walletPaid <= 0 && $cashPaid <= 0 && $dueAmount <= 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">No payments recorded</span>
                                        <span class="text-gray-500">Rs. 0.00</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ CASH COLLECTION HISTORY ============ --}}
            @if($booking->paymentCollections && $booking->paymentCollections->count() > 0)
                <div class="bg-white border border-gray-200 rounded-xl p-4 mb-6 no-break">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-2">Payment Collection History</p>
                    <div class="space-y-1.5">
                        @foreach($booking->paymentCollections->sortByDesc('created_at') as $col)
                            <div class="flex items-center justify-between text-xs bg-gray-50 rounded-lg px-3 py-2">
                                <div class="flex-1 min-w-0">
                                    <span class="font-semibold text-gray-900 capitalize">{{ $col->method }}</span>
                                    <span class="text-gray-600"> · {{ $col->collector?->name ?? 'System' }}</span>
                                    <span class="text-gray-500"> · {{ $col->created_at->format('d M Y, h:i A') }}</span>
                                    @if($col->reference_note)
                                        <span class="text-gray-400 italic"> · "{{ $col->reference_note }}"</span>
                                    @endif
                                </div>
                                <span class="font-bold text-gray-900 shrink-0">Rs. {{ number_format($col->amount, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ============ BOOKING-SPECIFIC META ============ --}}
            @if(!empty($booking->meta))
                <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-4 mb-6 no-break">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-900 mb-2">Booking Details</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                        @foreach($booking->meta as $key => $value)
                            @if(!empty($value))
                                <div class="flex justify-between gap-2 py-0.5">
                                    <span class="text-blue-800 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                    <span class="font-semibold text-blue-900 text-right">
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

            {{-- ============ NOTES ============ --}}
            @if($booking->notes)
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 mb-6 no-break">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Notes / History</p>
                    <p class="text-xs text-gray-800 mb-0 whitespace-pre-line">{{ $booking->notes }}</p>
                </div>
            @endif

            {{-- ============ FOOTER ============ --}}
            <div class="pt-6 border-t border-gray-200 flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Seller copy — for internal use only.</p>
                    <p class="text-xs text-gray-500 mb-0">
                        Reference: <code class="font-mono font-bold text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded">{{ $booking->reference }}</code>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-0.5">Generated</p>
                    <p class="text-xs text-gray-600 mb-0">{{ now()->format('d M Y, h:i A') }}</p>
                </div>
            </div>

        </div>
    </div>

    <p class="text-center text-[11px] text-gray-400 mt-4 mb-0 no-print">
        This invoice is system-generated and does not require a signature.
    </p>
</div>

</body>
</html>
