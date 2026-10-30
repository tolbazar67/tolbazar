@extends('layouts.guest')

@section('title', 'Cancel Deletion — TolBazar')

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
        <div class="bg-white border border-emerald-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-emerald-400 to-emerald-600"></div>
            <div class="p-6 md:p-8">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>

                <h1 class="text-xl font-bold text-gray-900 mb-2 mt-0">Cancel Account Deletion</h1>
                <p class="text-sm text-gray-600 mb-5">
                    Hi <strong>{{ $user->name }}</strong>, your account is scheduled for deletion
                    @if($user->deletion_scheduled_at)
                        on <strong>{{ $user->deletion_scheduled_at->format('d M Y') }}</strong>.
                    @else
                        soon.
                    @endif
                    Cancel it now to keep your account and log in.
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

                <form action="{{ route('account.cancel-deletion') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <input type="password" name="password" required autofocus
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none transition">
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('login') }}"
                           class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Cancel Deletion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
