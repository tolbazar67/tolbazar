@extends('layouts.guest')
@section('title', 'Wallet Admin — Dashboard')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-6xl mx-auto px-4">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 mt-0">Wallet Administration</h1>
            <p class="text-sm text-gray-500 mb-0">Manage all wallets, requests, and security</p>
        </div>

        {{-- Stats grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">Total Wallets</p>
                <p class="text-2xl font-bold text-gray-900 mb-0">{{ number_format($stats['total_wallets']) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">Total Balance</p>
                <p class="text-2xl font-bold text-blue-600 mb-0">Rs. {{ number_format($stats['total_balance'], 2) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">Pending Balance</p>
                <p class="text-2xl font-bold text-amber-600 mb-0">Rs. {{ number_format($stats['total_pending_balance'], 2) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">Critical Alerts (7d)</p>
                <p class="text-2xl font-bold text-red-600 mb-0">{{ $stats['critical_audits'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <a href="{{ route('admin.wallet.load-requests') }}" class="bg-white border border-gray-200 rounded-xl p-4 hover:border-emerald-300 hover:shadow-md transition group">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <span class="text-lg font-bold text-amber-600">{{ $stats['pending_loads'] }}</span>
                </div>
                <p class="text-sm font-semibold text-gray-900 mb-0">Load Requests</p>
                <p class="text-xs text-gray-500 mb-0">Pending approval</p>
            </a>
            <a href="{{ route('admin.wallet.withdraw-requests') }}" class="bg-white border border-gray-200 rounded-xl p-4 hover:border-red-300 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m8-8l-8 8 8 8"/></svg>
                    </div>
                    <span class="text-lg font-bold text-amber-600">{{ $stats['pending_withdraws'] }}</span>
                </div>
                <p class="text-sm font-semibold text-gray-900 mb-0">Withdraw Requests</p>
                <p class="text-xs text-gray-500 mb-0">Pending approval</p>
            </a>
            <a href="{{ route('admin.wallet.wallets') }}" class="bg-white border border-gray-200 rounded-xl p-4 hover:border-blue-300 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                </div>
                <p class="text-sm font-semibold text-gray-900 mb-0">All Wallets</p>
                <p class="text-xs text-gray-500 mb-0">View & manage</p>
            </a>
            <a href="{{ route('admin.wallet.audits') }}" class="bg-white border border-gray-200 rounded-xl p-4 hover:border-purple-300 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                </div>
                <p class="text-sm font-semibold text-gray-900 mb-0">Audit Logs</p>
                <p class="text-xs text-gray-500 mb-0">Fraud detection</p>
            </a>
        </div>

        {{-- Today stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">Today's Credits</p>
                <p class="text-xl font-bold text-emerald-600 mb-0">Rs. {{ number_format($stats['today_credits'], 2) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">Today's Debits</p>
                <p class="text-xl font-bold text-red-600 mb-0">Rs. {{ number_format($stats['today_debits'], 2) }}</p>
            </div>
        </div>

        {{-- Recent audits --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900 mb-0 mt-0">Recent Activity</h2>
                <a href="{{ route('admin.wallet.audits') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View all →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentAudits as $log)
                    <div class="p-4 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center
                            {{ $log->severity === 'critical' ? 'bg-red-50' : ($log->severity === 'warning' ? 'bg-amber-50' : 'bg-blue-50') }}">
                            <span class="w-2 h-2 rounded-full
                                {{ $log->severity === 'critical' ? 'bg-red-500' : ($log->severity === 'warning' ? 'bg-amber-500' : 'bg-blue-500') }}"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 mb-0 truncate">{{ $log->description }}</p>
                            <p class="text-xs text-gray-500 mb-0">
                                {{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-gray-500">No recent activity.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
