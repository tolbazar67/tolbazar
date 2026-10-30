@extends('layouts.guest')

@section('title', 'Reactivate Account — TolBazar')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: { preflight: false },
        theme: { extend: { fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] } } },
    };
</script>

<div class="min-h-screen py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-md mx-auto px-4">
        <div class="bg-white border border-blue-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-blue-400 to-indigo-600"></div>
            <div class="p-6 md:p-8">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>

                <h1 class="text-xl font-bold text-gray-900 mb-2 mt-0">Reactivate Your Account</h1>
                <p class="text-sm text-gray-600 mb-5">
                    Welcome back, <strong>{{ $user->name }}</strong>. Your account was deactivated.
                    Confirm your password below to reactivate it and log in.
                </p>

                @if(session('info'))
                    <div class="mb-4 rounded-lg bg-blue-50 border border-blue-200 p-3 text-sm text-blue-800">
                        {{ session('info') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('account.reactivate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <input type="password" name="password" required autofocus
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('login') }}"
                           class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Reactivate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
