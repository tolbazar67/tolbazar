@extends('layouts.guest')

@section('title', 'Account Switcher — TolBazar')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: { preflight: false },
        theme: { extend: { fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] } } },
    };
</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-3xl mx-auto px-4">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('account.settings') }}" class="text-gray-500 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 mb-0 mt-0">Account Switcher</h1>
                <p class="text-xs text-gray-500 mb-0">Manage accounts on this device and your current mode.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif
        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 p-3 text-sm text-blue-800">
                {{ session('info') }}
            </div>
        @endif

        {{-- ========================================================= --}}
        {{-- SECTION 1: SAVED ACCOUNTS ON THIS DEVICE --}}
        {{-- ========================================================= --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 mb-0 mt-0">Accounts on this Device</h2>
                        <p class="text-xs text-gray-500 mb-0">
                            {{ $savedAccounts->count() }} {{ \Illuminate\Support\Str::plural('account', $savedAccounts->count()) }} saved — switch without re-entering password
                        </p>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($savedAccounts as $sa)
                    @php $acc = $sa->user; $isCurrent = Auth::id() === $acc->id; @endphp
                    <div class="p-4 flex items-center gap-4 hover:bg-gray-50/50 transition">
                        <img src="{{ $acc->avatar_url }}"
                             alt="{{ $acc->name }}"
                             class="w-14 h-14 rounded-full object-cover border-2 {{ $isCurrent ? 'border-emerald-400 ring-2 ring-emerald-100' : 'border-gray-200' }} shrink-0">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-semibold text-gray-900 mb-0 truncate">{{ $acc->name }}</p>
                                @if($isCurrent)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        CURRENT
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mb-0 truncate">{{ $acc->email }}</p>
                            <p class="text-[11px] text-gray-400 mb-0">
                                Last used {{ $sa->last_used_at?->diffForHumans() ?? 'just now' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if(!$isCurrent)
                                <form action="{{ route('account.accounts.switch', $acc->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        Switch
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('account.accounts.remove', $acc->id) }}" method="POST"
                                  onsubmit="return confirm('{{ $isCurrent ? 'This will log you out and remove this account from this device.' : 'Remove this account from this device?' }}');">
                                @csrf @method('DELETE')
                                <button type="submit" title="Remove from this device"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <p class="text-sm text-gray-500 mb-0">Only your current account is saved on this device.</p>
                    </div>
                @endforelse
            </div>

            {{-- Add another account --}}
            <div class="p-4 bg-gray-50/60 border-t border-gray-100">
                <form action="{{ route('account.accounts.add') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-blue-700 bg-white border-2 border-dashed border-blue-200 hover:bg-blue-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Another Account
                    </button>
                </form>
                <p class="text-[11px] text-gray-500 text-center mt-2 mb-0">
                    You'll be logged out of this account, but it stays saved here for quick switching.
                </p>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- SECTION 2: SWITCH MODE (same account, different role) --}}
        {{-- ========================================================= --}}
        @if(!empty($modes))
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 mb-0 mt-0">Switch Mode</h2>
                        <p class="text-xs text-gray-500 mb-0">Change how you use TolBazar with this account.</p>
                    </div>
                </div>

                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($modes as $m)
                        <form action="{{ route('account.switch', $m['mode']) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left bg-white border {{ $user->active_mode === $m['mode'] ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-200 hover:border-blue-300 hover:shadow-md' }} rounded-xl p-4 transition group">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shadow shrink-0">
                                        @if($m['mode'] === 'customer')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        @elseif($m['mode'] === 'seller')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        @elseif($m['mode'] === 'admin')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 mb-0.5 mt-0 text-sm">{{ $m['label'] }}</p>
                                        <p class="text-xs text-gray-500 mb-0 truncate">
                                            @if($m['mode'] === 'customer') Shop & browse products
                                            @elseif($m['mode'] === 'seller') Manage your store
                                            @elseif($m['mode'] === 'admin') Manage the platform
                                            @else Handle deliveries
                                            @endif
                                        </p>
                                    </div>
                                    @if($user->active_mode === $m['mode'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 shrink-0">CURRENT</span>
                                    @endif
                                </div>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
