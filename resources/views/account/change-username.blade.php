@extends('layouts.guest')

@section('title', 'Change Username — TolBazar')

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
            <div class="h-1.5 bg-gradient-to-r from-blue-400 to-indigo-600"></div>

            <div class="p-6 md:p-8">

                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>

                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 mt-0">Change Username</h1>
                <p class="text-sm text-gray-600 mb-5">
                    Your username appears in your public profile URL.
                    Current URL: <code class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-800 text-xs font-mono">/u/{{ $user->username }}</code>
                </p>

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

                {{-- Warning box --}}
                <div class="rounded-xl bg-amber-50/60 border border-amber-100 p-4 mb-5 text-xs text-amber-800 flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold mb-0.5 mt-0">Please note:</p>
                        <ul class="list-disc pl-4 space-y-0.5 mb-0">
                            <li>Your public profile URL will change — old links will not work</li>
                            <li>Your password is required to confirm this change</li>
                        </ul>
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('account.change-username.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            New Username <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-mono">@</span>
                            <input type="text"
                                   name="username"
                                   value="{{ old('username', $user->username) }}"
                                   required
                                   minlength="3"
                                   maxlength="30"
                                   pattern="[A-Za-z0-9_\-]+"
                                   title="Only letters, numbers, hyphens and underscores"
                                   class="w-full pl-8 pr-3.5 py-2.5 rounded-lg border border-gray-300 text-sm font-mono focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1 mb-0">
                            3–30 characters · letters, numbers, hyphens (-), underscores (_)
                        </p>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Current Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required
                               placeholder="Enter your password to confirm"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('account.settings') }}"
                           class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Update Username
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
