@extends('layouts.guest')
@section('title', 'Load Requests — Wallet')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-3xl mx-auto px-4">

        <a href="{{ route('wallet.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Wallet
        </a>

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-0 mt-0">Load Requests</h1>
            <a href="{{ route('wallet.add-money') }}" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700">+ New Load</a>
        </div>

        <div class="space-y-2">
            @forelse($requests as $r)
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <code class="text-xs font-mono text-gray-600">{{ $r->reference }}</code>
                            <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded
                                @if($r->status === 'approved') bg-emerald-100 text-emerald-800
                                @elseif($r->status === 'rejected') bg-red-100 text-red-800
                                @else bg-amber-100 text-amber-800 @endif">
                                {{ $r->status }}
                            </span>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 mb-0">Rs. {{ number_format($r->amount, 2) }}</p>
                        <p class="text-xs text-gray-500 mb-0">{{ $r->created_at->format('d M Y, h:i A') }}</p>
                        @if($r->rejection_reason)
                            <p class="text-xs text-red-600 mt-1 mb-0">Reason: {{ $r->rejection_reason }}</p>
                        @endif
                    </div>
                    <a href="{{ Storage::url($r->screenshot_path) }}" target="_blank">
                        <img src="{{ Storage::url($r->screenshot_path) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                    </a>
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-xl p-10 text-center text-sm text-gray-500">
                    No load requests yet.
                </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
