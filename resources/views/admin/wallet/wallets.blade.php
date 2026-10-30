@extends('layouts.guest')
@section('title', 'All Wallets — Admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-6xl mx-auto px-4">

        <div class="mb-6 flex items-center justify-between gap-3">
            <h1 class="text-2xl font-bold text-gray-900 mb-0 mt-0">All Wallets</h1>
            <form method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search user..."
                       class="px-3 py-2 rounded-lg border border-gray-300 text-sm w-64">
                <button class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700">Search</button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            @forelse($wallets as $w)
                <div class="p-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex items-start gap-4">
                        <img src="{{ $w->user?->avatar_url }}" class="w-10 h-10 rounded-full object-cover shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <p class="font-semibold text-gray-900 mb-0">{{ $w->user?->name ?? 'Unknown' }}</p>
                                <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded
                                    @if($w->status === 'active') bg-emerald-100 text-emerald-800
                                    @elseif($w->status === 'frozen') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ $w->status }}
                                </span>
                                @if($w->is_kyc_verified)
                                    <span class="text-[10px] font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full uppercase">KYC</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mb-1">{{ $w->user?->email }}</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2">
                                <div>
                                    <p class="text-[10px] text-gray-500 uppercase mb-0">Balance</p>
                                    <p class="text-sm font-bold text-gray-900 mb-0">Rs. {{ number_format($w->balance, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 uppercase mb-0">Pending</p>
                                    <p class="text-sm font-semibold text-amber-600 mb-0">Rs. {{ number_format($w->pending_balance, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 uppercase mb-0">Points</p>
                                    <p class="text-sm font-semibold text-purple-600 mb-0">{{ number_format($w->reward_points) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 uppercase mb-0">ID</p>
                                    <p class="text-xs font-mono text-gray-700 mb-0">#{{ $w->id }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 shrink-0">
                            @if($w->status === 'active')
                                <form action="{{ route('admin.wallet.freeze', $w->id) }}" method="POST" onsubmit="return confirm('Freeze this wallet?')">
                                    @csrf
                                    <button class="w-full px-3 py-1 rounded-lg text-xs font-semibold text-red-600 bg-red-50 border border-red-200 hover:bg-red-100">Freeze</button>
                                </form>
                            @else
                                <form action="{{ route('admin.wallet.unfreeze', $w->id) }}" method="POST">
                                    @csrf
                                    <button class="w-full px-3 py-1 rounded-lg text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100">Unfreeze</button>
                                </form>
                            @endif
                            @if(!$w->is_kyc_verified)
                                <form action="{{ route('admin.wallet.kyc', $w->id) }}" method="POST">
                                    @csrf
                                    <button class="w-full px-3 py-1 rounded-lg text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 hover:bg-blue-100">Verify KYC</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-sm text-gray-500 mb-0">No wallets found.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $wallets->links() }}</div>
    </div>
</div>
@endsection
