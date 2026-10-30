@extends('layouts.guest')
@section('title', 'Wallet Audits — Admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-6xl mx-auto px-4">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 mt-0">Wallet Audit Logs</h1>
            <p class="text-sm text-gray-500 mb-0">All security events and suspicious activity</p>
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-4 flex gap-2 flex-wrap">
            <select name="severity" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                <option value="">All Severities</option>
                <option value="info" @selected(request('severity') === 'info')>Info</option>
                <option value="warning" @selected(request('severity') === 'warning')>Warning</option>
                <option value="critical" @selected(request('severity') === 'critical')>Critical</option>
            </select>
            <select name="event_type" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                <option value="">All Events</option>
                @foreach(['pin_failed','pin_set','load_request_created','load_approved','load_rejected','withdraw_request_created','withdraw_approved','withdraw_rejected','transfer_completed','wallet_frozen','wallet_unfrozen','kyc_verified','admin_adjustment'] as $evt)
                    <option value="{{ $evt }}" @selected(request('event_type') === $evt)>{{ ucwords(str_replace('_', ' ', $evt)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700">Filter</button>
            @if(request()->hasAny(['severity','event_type']))
                <a href="{{ route('admin.wallet.audits') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">Clear</a>
            @endif
        </form>

        {{-- List --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            @forelse($logs as $log)
                <div class="p-4 flex items-start gap-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
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
                        <div class="flex items-start justify-between gap-2 mb-1 flex-wrap">
                            <p class="text-sm font-medium text-gray-900 mb-0">{{ $log->description }}</p>
                            <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded shrink-0
                                {{ $log->severity === 'critical' ? 'bg-red-100 text-red-800' : ($log->severity === 'warning' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $log->severity }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-0">
                            <span class="font-mono">{{ $log->event_type }}</span>
                            · {{ $log->user?->name ?? 'System' }} ({{ $log->user?->email ?? '—' }})
                            · {{ $log->created_at->format('d M Y, h:i A') }}
                            @if($log->ip_address) · IP: {{ $log->ip_address }} @endif
                        </p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-sm text-gray-500">No audit logs found.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
