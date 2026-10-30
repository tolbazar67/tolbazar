@extends('layouts.seller')
@section('title', 'Pending Cash Collection — Seller')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

@php
    $statusColors = [
        'pending'    => ['bg' => 'bg-amber-100',   'text' => 'text-amber-800',   'label' => 'Pending'],
        'confirmed'  => ['bg' => 'bg-blue-100',    'text' => 'text-blue-800',    'label' => 'Confirmed'],
        'processing' => ['bg' => 'bg-indigo-100',  'text' => 'text-indigo-800',  'label' => 'Processing'],
        'completed'  => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Completed'],
    ];
@endphp

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-5xl mx-auto px-4">

        <a href="{{ route('seller.payments.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Payments
        </a>

        <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 mt-0">Pending Cash Collection</h1>
                <p class="text-sm text-gray-500 mb-0">All COD bookings with cash due — collect from customer</p>
            </div>
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl px-5 py-3 text-white">
                <p class="text-[10px] uppercase tracking-wider text-white/70 mb-0.5 mt-0">Total to Collect</p>
                <p class="text-xl font-bold mb-0">Rs. {{ number_format($totalDue, 2) }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        {{-- Status filter chips --}}
        <div class="flex flex-wrap gap-2 mb-3">
            <a href="{{ route('seller.payments.pending-cod', array_filter(['q' => request('q')])) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border
                      {{ !request('status') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                All <span class="opacity-70">({{ array_sum($statusCounts) }})</span>
            </a>
            @foreach($statusCounts as $key => $count)
                @php $sc = $statusColors[$key]; @endphp
                <a href="{{ route('seller.payments.pending-cod', array_filter(['status' => $key, 'q' => request('q')])) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border
                          {{ request('status') === $key ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                    {{ $sc['label'] }} <span class="opacity-70">({{ $count }})</span>
                </a>
            @endforeach
        </div>

        {{-- Search --}}
        <form method="GET" class="bg-white border border-gray-200 rounded-2xl p-4 mb-4 flex gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search booking, customer, phone..."
                   class="flex-1 px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700">Search</button>
            @if(request()->hasAny(['q', 'status']))
                <a href="{{ route('seller.payments.pending-cod') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">Clear</a>
            @endif
        </form>

        @if($pending->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center">
                <svg class="w-16 h-16 text-emerald-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-semibold text-emerald-700 mb-1">🎉 No pending COD collections</p>
                <p class="text-xs text-gray-500 mb-0">
                    {{ request()->hasAny(['q', 'status']) ? 'Try clearing filters' : 'All COD bookings are fully paid' }}
                </p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($pending as $b)
                    @php
                        $sc = $statusColors[$b->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($b->status)];
                        $canCollect = in_array($b->status, ['processing', 'completed']);
                    @endphp
                    <div class="bg-white border border-amber-200 bg-amber-50/20 rounded-2xl p-4 hover:shadow-md transition">
                        <div class="flex items-start gap-3 flex-wrap">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $sc['bg'] }} {{ $sc['text'] }}">
                                        {{ $sc['label'] }}
                                    </span>
                                    <span class="text-xs font-mono text-gray-500">{{ $b->reference }}</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 mb-0.5 truncate">
                                    {{ $b->customer_name }}
                                </p>
                                <p class="text-xs text-gray-500 mb-1">
                                    <a href="tel:{{ $b->customer_phone }}" class="text-blue-600 hover:underline">📞 {{ $b->customer_phone }}</a>
                                    @if($b->branch) · {{ $b->branch->branch_name }} @endif
                                </p>
                                <p class="text-[11px] text-gray-400 mb-0">
                                    Booked {{ $b->created_at->diffForHumans() }}
                                    @if((float) $b->paid_amount > 0)
                                        · Advance: Rs. {{ number_format($b->paid_amount, 2) }}
                                    @endif
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <p class="text-[10px] uppercase tracking-wider text-gray-500 mb-0.5 mt-0">To Collect</p>
                                <p class="text-lg font-bold text-amber-700 mb-0">Rs. {{ number_format($b->due_amount, 2) }}</p>
                                <p class="text-[10px] text-gray-500 mb-0">Total: Rs. {{ number_format($b->total, 2) }}</p>
                            </div>

                            <a href="{{ route('seller.bookings.show', $b->reference) }}"
                               class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold text-white
                                      {{ $canCollect ? 'bg-amber-600 hover:bg-amber-700' : 'bg-gray-500 hover:bg-gray-600' }} shrink-0 transition">
                                {{ $canCollect ? 'Collect' : 'Manage' }}
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                        @if(!$canCollect)
                            <p class="text-[11px] text-amber-700 mt-2 pl-13 mb-0">
                                ⚠️ Mark booking as <strong>Processing</strong> or <strong>Completed</strong> first, then collect cash.
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4">{{ $pending->links() }}</div>
        @endif
    </div>
</div>
@endsection
