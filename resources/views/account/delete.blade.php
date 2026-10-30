@extends('layouts.guest')
@section('title', 'Delete Account — TolBazar')
@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-xl mx-auto px-4">
        <a href="{{ route('account.settings') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>

        <div class="bg-white border border-red-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-red-500 to-red-600"></div>
            <div class="p-6 md:p-8">
                <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>

                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 mt-0">Delete Account</h1>
                <p class="text-sm text-gray-600 mb-5">
                    Once deleted, your account and data will be permanently removed. This action cannot be undone.
                </p>

                <div class="rounded-xl bg-red-50/60 border border-red-100 p-4 mb-5 text-xs text-red-800">
                    <p class="font-semibold mb-1">What happens:</p>
                    <ul class="list-disc pl-4 space-y-0.5 mb-0">
                        <li>All active sessions are immediately logged out</li>
                        <li>Your profile, listings, and store data will be removed</li>
                        <li><strong>30-day grace period</strong> — you can cancel by logging in again</li>
                        <li>After 30 days, data is permanently deleted</li>
                    </ul>
                </div>

                @if($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('account.delete.process') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <input type="password" name="password" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Type <code class="px-1.5 py-0.5 rounded bg-red-100 text-red-700 font-mono">DELETE</code> to confirm
                        </label>
                        <input type="text" name="confirmation" required
                               placeholder="DELETE"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm font-mono focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition">
                    </div>
                    <label class="flex items-start gap-2 mb-5 cursor-pointer">
                        <input type="checkbox" name="understand" value="1" required
                               class="mt-1 w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-700">I understand my account will be scheduled for permanent deletion.</span>
                    </label>

                    <div class="flex gap-2">
                        <a href="{{ route('account.settings') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">Cancel</a>
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                            Delete Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
