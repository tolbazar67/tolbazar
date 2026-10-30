@extends('layouts.guest')
@section('title', 'Wallet Security — TolBazar')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-3xl mx-auto px-4">

        <a href="{{ route('wallet.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Wallet
        </a>

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 mt-0">Wallet Security</h1>
            <p class="text-sm text-gray-500 mb-0">Protect your wallet with a PIN and manage limits</p>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                <ul class="list-disc pl-4 mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- Status overview --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">PIN Status</p>
                @if($wallet->hasPin())
                    <span class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Set
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-sm font-semibold text-amber-700">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Not Set
                    </span>
                @endif
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">Lock Status</p>
                @if($wallet->pin_locked_until && now()->lt($wallet->pin_locked_until))
                    <span class="text-sm font-semibold text-red-700">Locked till {{ $wallet->pin_locked_until->format('h:i A') }}</span>
                @else
                    <span class="text-sm font-semibold text-emerald-700">Unlocked</span>
                @endif
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">KYC</p>
                @if($wallet->is_kyc_verified)
                    <span class="text-sm font-semibold text-emerald-700">Verified</span>
                @else
                    <span class="text-sm font-semibold text-amber-700">Pending</span>
                @endif
            </div>
        </div>

        {{-- PIN form --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm mb-6">
            <div class="p-5 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900 mb-0 mt-0">{{ $wallet->hasPin() ? 'Change Wallet PIN' : 'Set Wallet PIN' }}</h2>
                    <p class="text-xs text-gray-500 mb-0">6-digit PIN required for all sensitive transactions</p>
                </div>
            </div>
            <form action="{{ route('wallet.security.pin') }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Your Account Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required placeholder="Confirm with account password"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">New 6-digit PIN <span class="text-red-500">*</span></label>
                        <input type="password" name="pin" required pattern="\d{6}" maxlength="6" inputmode="numeric" placeholder="••••••"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm font-mono tracking-widest text-center focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm PIN <span class="text-red-500">*</span></label>
                        <input type="password" name="pin_confirmation" required pattern="\d{6}" maxlength="6" inputmode="numeric" placeholder="••••••"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm font-mono tracking-widest text-center focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                    </div>
                </div>
                <p class="text-xs text-gray-500 mb-0">⚠️ Avoid simple PINs like 123456 or repeated digits.</p>
                <button type="submit" class="w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm">
                    {{ $wallet->hasPin() ? 'Update PIN' : 'Set PIN' }}
                </button>
            </form>
        </div>

        {{-- Limits form --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm mb-6">
            <div class="p-5 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900 mb-0 mt-0">Transaction Limits</h2>
                    <p class="text-xs text-gray-500 mb-0">Financial safety limits for your wallet</p>
                </div>
            </div>
            <form action="{{ route('wallet.security.limits') }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Account Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Single Txn (NPR)</label>
                        <input type="number" name="single_txn_limit" value="{{ old('single_txn_limit', $wallet->single_txn_limit) }}" min="1000" max="100000" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Daily Limit (NPR)</label>
                        <input type="number" name="daily_limit" value="{{ old('daily_limit', $wallet->daily_limit) }}" min="1000" max="500000" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Monthly Limit (NPR)</label>
                        <input type="number" name="monthly_limit" value="{{ old('monthly_limit', $wallet->monthly_limit) }}" min="5000" max="5000000" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                    </div>
                </div>
                <button type="submit" class="w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 shadow-sm">
                    Update Limits
                </button>
            </form>
        </div>

        {{-- Audit logs --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="p-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900 mb-0 mt-0">Recent Security Activity</h2>
                <p class="text-xs text-gray-500 mb-0">Your recent wallet security events</p>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($audits as $log)
                    <div class="p-4 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center
                            {{ $log->severity === 'critical' ? 'bg-red-50' : ($log->severity === 'warning' ? 'bg-amber-50' : 'bg-blue-50') }}">
                            @if($log->severity === 'critical')
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            @elseif($log->severity === 'warning')
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 mb-0 truncate">{{ $log->description }}</p>
                            <p class="text-xs text-gray-500 mb-0">{{ $log->created_at->format('d M Y, h:i A') }} · {{ $log->ip_address ?? 'N/A' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-gray-500">No security activity yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
