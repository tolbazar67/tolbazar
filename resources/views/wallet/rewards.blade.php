@extends('layouts.guest')
@section('title', 'Rewards — Wallet')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-3xl mx-auto px-4">

        <a href="{{ route('wallet.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Wallet
        </a>

        <h1 class="text-2xl font-bold text-gray-900 mb-6 mt-0">Reward Points</h1>

        {{-- Points banner --}}
        <div class="rounded-2xl p-6 mb-6 text-white" style="background:linear-gradient(135deg,#f59e0b 0%,#ef4444 100%);">
            <p class="text-xs uppercase tracking-wider text-white/80 mb-1 mt-0">Your Balance</p>
            <p class="text-4xl font-bold mb-0">{{ number_format($wallet->reward_points) }} <span class="text-lg font-medium">points</span></p>
            <p class="text-xs text-white/80 mt-2 mb-0">Redeemable on purchases · 100 points = Rs. 1</p>
        </div>

        {{-- History --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="p-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900 mb-0 mt-0">Reward History</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($history as $h)
                    <div class="p-4 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ $h->points > 0 ? 'bg-emerald-50' : 'bg-red-50' }}">
                            <svg class="w-4 h-4 {{ $h->points > 0 ? 'text-emerald-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 mb-0">{{ $h->reason ?: ucfirst($h->type) }}</p>
                            <p class="text-xs text-gray-500 mb-0">{{ $h->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <p class="text-sm font-bold mb-0 {{ $h->points > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $h->points > 0 ? '+' : '' }}{{ number_format($h->points) }}
                        </p>
                    </div>
                @empty
                    <div class="p-10 text-center text-sm text-gray-500">No rewards yet.</div>
                @endforelse
            </div>
        </div>

        <div class="mt-4">{{ $history->links() }}</div>
    </div>
</div>
@endsection
