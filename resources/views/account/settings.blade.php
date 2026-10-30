@extends('layouts.guest')
@section('title', 'Account Settings — TolBazar')
@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-4xl mx-auto px-4">
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 mt-0">Account Settings</h1>
            <p class="text-sm text-gray-500 mb-0">Manage your account security and preferences.</p>
        </div>

        {{-- User card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6 flex items-center gap-4">
            <img src="{{ $user->avatar_url }}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow">
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 mb-0 truncate">{{ $user->name }}</p>
                <p class="text-sm text-gray-500 mb-0 truncate">{{ $user->email }}</p>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
            </span>
        </div>

        {{-- Settings grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <a href="{{ route('account.login-activity') }}"
               class="group bg-white border border-gray-200 rounded-2xl p-5 hover:border-blue-300 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                <p class="font-semibold text-gray-900 mt-4 mb-1">Login Activity</p>
                <p class="text-xs text-gray-500 mb-0">See recent logins and failed attempts.</p>
            </a>

            <a href="{{ route('account.sessions') }}"
               class="group bg-white border border-gray-200 rounded-2xl p-5 hover:border-blue-300 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700">{{ $sessionsCount }}</span>
                </div>
                <p class="font-semibold text-gray-900 mt-4 mb-1">Active Sessions</p>
                <p class="text-xs text-gray-500 mb-0">Devices currently logged into your account.</p>
            </a>

            <a href="{{ route('account.switcher') }}"
               class="group bg-white border border-gray-200 rounded-2xl p-5 hover:border-purple-300 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                <p class="font-semibold text-gray-900 mt-4 mb-1">Account Switcher</p>
                <p class="text-xs text-gray-500 mb-0">Switch between customer, seller, admin modes.</p>
            </a>

            <a href="{{ route('profile.edit') }}"
               class="group bg-white border border-gray-200 rounded-2xl p-5 hover:border-blue-300 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-600 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                <p class="font-semibold text-gray-900 mt-4 mb-1">Profile Information</p>
                <p class="text-xs text-gray-500 mb-0">Update your name, photo and bio.</p>
            </a>
            {{-- Add below the "Profile Information" card in the grid --}}
<a href="{{ route('account.change-username') }}"
   class="group bg-white border border-gray-200 rounded-2xl p-5 hover:border-blue-300 hover:shadow-md transition">
    <div class="flex items-start justify-between">
        <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </div>
    <p class="font-semibold text-gray-900 mt-4 mb-1">Change Username</p>
    <p class="text-xs text-gray-500 mb-0">Update your public @username.</p>
</a>

<a href="{{ route('account.change-email') }}"
   class="group bg-white border border-gray-200 rounded-2xl p-5 hover:border-emerald-300 hover:shadow-md transition">
    <div class="flex items-start justify-between">
        <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-600 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </div>
    <p class="font-semibold text-gray-900 mt-4 mb-1">Change Email</p>
    <p class="text-xs text-gray-500 mb-0">Update your email with OTP verification.</p>
</a>

<a href="{{ route('account.change-name') }}"
   class="group bg-white border border-gray-200 rounded-2xl p-5 hover:border-indigo-300 hover:shadow-md transition">
    <div class="flex items-start justify-between">
        <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </div>
    <p class="font-semibold text-gray-900 mt-4 mb-1">Change Name</p>
    <p class="text-xs text-gray-500 mb-0">Update your display name.</p>
</a>


<a href="{{ route('account.change-mobile') }}"
   class="group bg-white border border-gray-200 rounded-2xl p-5 hover:border-violet-300 hover:shadow-md transition">
    <div class="flex items-start justify-between">
        <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
        </div>
        <svg class="w-5 h-5 text-gray-400 group-hover:text-violet-600 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </div>
    <p class="font-semibold text-gray-900 mt-4 mb-1">Change Mobile</p>
    <p class="text-xs text-gray-500 mb-0">Update your mobile with SMS OTP.</p>
</a>
        </div>

        {{-- Danger zone --}}
        <div class="mt-8 rounded-2xl border border-red-200 bg-red-50/50 p-5">
            <div class="flex items-start gap-3 mb-4">
                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="font-semibold text-red-900 mb-0.5 mt-0">Danger Zone</p>
                    <p class="text-xs text-red-700 mb-0">These actions affect your account permanently.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('account.deactivate') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-amber-700 bg-white border border-amber-200 hover:bg-amber-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Deactivate Account
                </a>
                <a href="{{ route('account.delete') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                    Delete Account
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
