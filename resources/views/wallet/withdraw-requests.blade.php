@extends('layouts.guest')
@section('title', 'Withdraw Requests — Wallet')

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
            <h1 class="text-2xl font-bold text-gray-900 mb-0 mt-0">Withdraw Requests</h1>
            <a href="{{ route('wallet.withdraw') }}" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700">+ New Withdraw</a>
        </div>

        <div class="space-y-2">
            @forelse($requests as $r)
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <code class="text-xs font-mono text-gray-600">{{ $r->reference }}</code>
                            <p class="text-sm font-semibold text-gray-900 mb-0 mt-1">Rs. {{ number_format($r->amount, 2) }}</p>
                        </div>
                        <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded
                            @if(in_array($r->status, ['completed','approved'])) bg-emerald-100 text-emerald-800
                            @elseif($r->status === 'rejected') bg-red-100 text-red-800
                            @else bg-amber-100 text-amber-800 @endif">
                            {{ $r->status }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-0">To: {{ $r->bankAccount?->bank_name }} — {{ $r->bankAccount?->masked_number }}</p>
                    <p class="text-xs text-gray-400 mb-0">{{ $r->created_at->format('d M Y, h:i A') }}</p>
                    @if($r->rejection_reason)
                        <p class="text-xs text-red-600 mt-1 mb-0">Reason: {{ $r->rejection_reason }}</p>
                    @endif
                    @if($r->proof_image)
                        <a href="{{ Storage::url($r->proof_image) }}" target="_blank" class="inline-block mt-2">
                            <img src="{{ Storage::url($r->proof_image) }}" class="w-20 h-16 object-cover rounded border border-gray-200">
                        </a>
                    @endif
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-xl p-10 text-center text-sm text-gray-500">
                    No withdraw requests yet.
                </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
