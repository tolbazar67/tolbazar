@extends('layouts.guest')
@section('title', 'Cash Collections — Seller')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-5xl mx-auto px-4">

        <a href="{{ route('seller.payments.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Payments
        </a>

        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 mt-0">Cash Collections</h1>
            <p class="text-sm text-gray-500 mb-0">History of all cash received for COD bookings</p>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-4 text-white">
                <p class="text-[10px] uppercase tracking-wider text-white/70 mb-1 mt-0">Total Collected</p>
                <p class="text-xl font-bold mb-0">Rs. {{ number_format($stats['total_collected'], 0) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-[10px] uppercase tracking-wider text-gray-500 mb-1 mt-0">Today</p>
                <p class="text-xl font-bold text-gray-900 mb-0">Rs. {{ number_format($stats['today_collected'], 0) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-[10px] uppercase tracking-wider text-gray-500 mb-1 mt-0">This Month</p>
                <p class="text-xl font-bold text-gray-900 mb-0">Rs. {{ number_format($stats['month_collected'], 0) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-[10px] uppercase tracking-wider text-gray-500 mb-1 mt-0">Count</p>
                <p class="text-xl font-bold text-gray-900 mb-0">{{ $stats['count'] }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-white border border-gray-200 rounded-2xl p-4 mb-4 flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search reference, booking, customer..."
                   class="flex-1 min-w-[200px] px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">
            <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700">Filter</button>
            @if(request()->hasAny(['q', 'from', 'to']))
                <a href="{{ route('seller.payments.collections') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">Clear</a>
            @endif
        </form>

        {{-- List --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            @forelse($collections as $col)
                <div class="p-4 flex items-center gap-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }} hover:bg-gray-50/50">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 mb-0.5 truncate">
                            {{ $col->booking?->customer_name ?? 'Unknown' }}
                            <span class="text-xs text-gray-400 font-normal">·</span>
                            <code class="text-[11px] font-mono text-gray-500">{{ $col->booking?->reference ?? '—' }}</code>
                        </p>
                        <p class="text-xs text-gray-500 mb-0">
                            Collected by <strong>{{ $col->collector?->name ?? 'Unknown' }}</strong>
                            · {{ $col->created_at->format('d M Y, h:i A') }}
                            @if($col->reference_note) · <span class="italic">"{{ $col->reference_note }}"</span> @endif
                        </p>
                        <p class="text-[10px] text-gray-400 mb-0 font-mono">{{ $col->reference }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-emerald-700 mb-0">+ Rs. {{ number_format($col->amount, 2) }}</p>
                        <p class="text-[10px] text-gray-500 mb-0">Cash</p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-sm text-gray-500 mb-0">No cash collections yet.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $collections->links() }}</div>
    </div>
</div>
@endsection
