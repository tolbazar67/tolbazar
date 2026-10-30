@extends('layouts.guest')
@section('title', 'Deactivate Account — TolBazar')
@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-xl mx-auto px-4">
        <a href="{{ route('account.settings') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>

        <div class="bg-white border border-amber-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-amber-400 to-orange-500"></div>
            <div class="p-6 md:p-8">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>

                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 mt-0">Deactivate Account</h1>
                <p class="text-sm text-gray-600 mb-5">
                    Deactivating your account will <strong>hide your profile</strong> and <strong>log you out from all devices</strong>.
                    You can reactivate anytime by logging in again with your email and password.
                </p>

                <div class="rounded-xl bg-amber-50/60 border border-amber-100 p-4 mb-5 text-xs text-amber-800">
                    <p class="font-semibold mb-1">While deactivated:</p>
                    <ul class="list-disc pl-4 space-y-0.5 mb-0">
                        <li>Your listings and store will be hidden</li>
                        <li>You won't receive orders or messages</li>
                        <li>Your account data is preserved for reactivation</li>
                    </ul>
                </div>

                @if($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('account.deactivate.process') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Enter your password</label>
                        <input type="password" name="password" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    </div>
                    <label class="flex items-start gap-2 mb-5 cursor-pointer">
                        <input type="checkbox" name="confirm" value="1" required
                               class="mt-1 w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        <span class="text-sm text-gray-700">I understand my account will be hidden and I will be logged out from all devices.</span>
                    </label>

                    <div class="flex gap-2">
                        <a href="{{ route('account.settings') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">Cancel</a>
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Deactivate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
