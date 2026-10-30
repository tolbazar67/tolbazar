@extends('layouts.guest')

@section('title', 'Change Name — TolBazar')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = { corePlugins: { preflight: false }, theme: { extend: { fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] } } } };
</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-xl mx-auto px-4">

        {{-- Back link --}}
        <a href="{{ route('account.settings') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Settings
        </a>

        {{-- Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-indigo-400 to-purple-600"></div>

            <div class="p-6 md:p-8">

                <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>

                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 mt-0">Change Name</h1>
                <p class="text-sm text-gray-600 mb-5">
                    Your name is how you appear across TolBazar — on your profile, reviews, and interactions with other users.
                </p>

                {{-- Current name preview --}}
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 mb-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Current Name</p>
                    <div class="flex items-center gap-3">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                             class="w-10 h-10 rounded-full object-cover border-2 border-white shadow">
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-900 mb-0 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 mb-0">@ {{ $user->username }}</p>
                        </div>
                    </div>
                </div>

                {{-- Alerts --}}
                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800 flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                        <ul class="list-disc pl-4 mb-0 space-y-0.5">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Info box --}}
                <div class="rounded-xl bg-indigo-50/60 border border-indigo-100 p-4 mb-5 text-xs text-indigo-800 flex items-start gap-2">
                    <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold mb-0.5 mt-0">Please note:</p>
                        <ul class="list-disc pl-4 space-y-0.5 mb-0">
                            <li>Your name appears publicly on your profile and interactions</li>
                            <li>Your password is required to confirm this change</li>
                            <li>Change is immediate and reflected everywhere</li>
                        </ul>
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('account.change-name.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            New Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $user->name) }}"
                               required
                               minlength="2"
                               maxlength="100"
                               autocomplete="name"
                               placeholder="Your full name"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                        <p class="text-[11px] text-gray-500 mt-1 mb-0">
                            2–100 characters · letters, spaces, dots, hyphens, apostrophes allowed
                        </p>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Current Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="Enter your password to confirm"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('account.settings') }}"
                           class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Update Name
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
