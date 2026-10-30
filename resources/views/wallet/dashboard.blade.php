@extends('layouts.guest')
@section('title', 'My Wallet — TolBazar')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-5xl mx-auto px-4">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 mt-0">My Wallet</h1>
                <p class="text-sm text-gray-500 mb-0">Manage your balance and transactions</p>
            </div>
            <a href="{{ route('wallet.security') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Security
            </a>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        {{-- Balance Card --}}
        <div class="rounded-2xl p-6 md:p-8 mb-6 text-white relative overflow-hidden"
             style="background: linear-gradient(135deg,#4f46e5 0%, #7c3aed 60%, #ec4899 100%);">
            <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-20 -left-10 w-48 h-48 rounded-full bg-white/5"></div>

            <div class="relative">
                <p class="text-xs uppercase tracking-wider text-white/70 mb-2 mt-0">Available Balance</p>
                <p class="text-4xl md:text-5xl font-bold mb-1">Rs. {{ number_format($wallet->balance, 2) }}</p>
                <p class="text-xs text-white/70 mb-6">Wallet ID: #{{ str_pad($wallet->id, 8, '0', STR_PAD_LEFT) }}</p>

                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="bg-white/10 rounded-xl p-3 backdrop-blur">
                        <p class="text-[10px] uppercase tracking-wide text-white/60 mb-1 mt-0">Pending</p>
                        <p class="text-sm font-bold mb-0">Rs. {{ number_format($wallet->pending_balance, 2) }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3 backdrop-blur">
                        <p class="text-[10px] uppercase tracking-wide text-white/60 mb-1 mt-0">Reward Points</p>
                        <p class="text-sm font-bold mb-0">{{ number_format($wallet->reward_points) }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3 backdrop-blur">
                        <p class="text-[10px] uppercase tracking-wide text-white/60 mb-1 mt-0">Credit</p>
                        <p class="text-sm font-bold mb-0">Rs. {{ number_format($wallet->credit_balance, 2) }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('wallet.add-money') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-indigo-700 bg-white hover:bg-gray-100 transition shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Money
                    </a>
                    <a href="{{ route('wallet.withdraw') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-white/20 backdrop-blur border border-white/30 hover:bg-white/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m8-8l-8 8 8 8"/></svg>
                        Withdraw
                    </a>
                    <a href="{{ route('wallet.transfer') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-white/20 backdrop-blur border border-white/30 hover:bg-white/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        Transfer
                    </a>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <p class="text-xs font-medium text-gray-500 mb-0 uppercase tracking-wide">In (This month)</p>
                </div>
                <p class="text-lg font-bold text-gray-900 mb-0">Rs. {{ number_format($thisMonthIn, 2) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <p class="text-xs font-medium text-gray-500 mb-0 uppercase tracking-wide">Out (This month)</p>
                </div>
                <p class="text-lg font-bold text-gray-900 mb-0">Rs. {{ number_format($thisMonthOut, 2) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <p class="text-xs font-medium text-gray-500 mb-0 uppercase tracking-wide">Pending Loads</p>
                </div>
                <p class="text-lg font-bold text-gray-900 mb-0">{{ $pendingLoads }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    <p class="text-xs font-medium text-gray-500 mb-0 uppercase tracking-wide">Pending Withdraws</p>
                </div>
                <p class="text-lg font-bold text-gray-900 mb-0">{{ $pendingWithdraws }}</p>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900 mb-0 mt-0">Recent Transactions</h2>
                <a href="{{ route('wallet.transactions') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All →</a>
            </div>
            @forelse($recentTxns as $txn)
                <div class="p-4 flex items-center gap-3 hover:bg-gray-50/50 transition {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center {{ $txn->type === 'credit' ? 'bg-emerald-50' : 'bg-red-50' }}">
                        @if($txn->type === 'credit')
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        @else
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 mb-0 truncate">{{ $txn->description ?: $txn->category_label }}</p>
                        <p class="text-xs text-gray-500 mb-0">{{ $txn->created_at->format('d M Y, h:i A') }} · <span class="capitalize">{{ $txn->category_label }}</span></p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-semibold mb-0 {{ $txn->type === 'credit' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $txn->type === 'credit' ? '+' : '−' }} Rs. {{ number_format($txn->amount, 2) }}
                        </p>
                        <span class="inline-block mt-1 text-[10px] font-semibold uppercase
                            @if($txn->status === 'completed') text-emerald-700 bg-emerald-100
                            @elseif($txn->status === 'pending') text-amber-700 bg-amber-100
                            @else text-red-700 bg-red-100 @endif
                            px-1.5 py-0.5 rounded">
                            {{ $txn->status }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M9 8h6M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-gray-500 mb-0">No transactions yet. <a href="{{ route('wallet.add-money') }}" class="text-blue-600 font-medium">Add money</a> to start.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
