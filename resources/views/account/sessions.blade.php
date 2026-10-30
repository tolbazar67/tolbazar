@extends('layouts.guest')
@section('title', 'Active Sessions — TolBazar')
@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('account.settings') }}" class="text-gray-500 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 mb-0 mt-0">Active Sessions</h1>
                <p class="text-xs text-gray-500 mb-0">Devices currently logged into your account.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 flex items-center gap-2 text-sm text-emerald-800">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Logout all button --}}
        <div class="flex items-center justify-between mb-4 p-4 bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div>
                <p class="font-semibold text-gray-900 mb-0.5 mt-0 text-sm">
                    {{ $sessions->count() }} active {{ Str::plural('session', $sessions->count()) }}
                </p>
                <p class="text-xs text-gray-500 mb-0">Review and manage each device below.</p>
            </div>
            <form action="{{ route('account.sessions.logout-all') }}" method="POST"
                  onsubmit="return confirm('Logout all other devices? You will remain logged in here.');">
                @csrf
                <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout All Devices
                </button>
            </form>
        </div>

        <div class="space-y-3">
            @foreach($sessions as $session)
                <div class="bg-white border {{ $session->is_current ? 'border-emerald-300 ring-2 ring-emerald-100' : 'border-gray-200' }} rounded-2xl p-4 md:p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                            @if($session->device_type === 'mobile')
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            @elseif($session->device_type === 'tablet')
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            @else
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <p class="font-semibold text-gray-900 mb-0">
                                    {{ $session->browser }} on {{ $session->platform }}
                                </p>
                                @if($session->is_current)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> This Device
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mb-0.5">IP: {{ $session->ip_address ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500 mb-0">
                                Last active: {{ $session->last_seen->diffForHumans() }}
                            </p>
                        </div>

                        @if(!$session->is_current)
                            <form action="{{ route('account.sessions.destroy', $session->id) }}" method="POST"
                                  onsubmit="return confirm('Logout this device?');">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Logout
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
