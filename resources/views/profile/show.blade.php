@extends('layouts.guest')

@section('title', ($isOwnProfile ? 'My Profile' : $user->name . "'s Profile") . ' — TolBazar')

@section('content')
{{-- Tailwind + Preline --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: { preflight: false },
        theme: { extend: { fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] } } },
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/preline@2.5.1/dist/preline.js"></script>

{{-- Leaflet map (only if coordinates exist) --}}
@if($user->latitude && $user->longitude)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif

@php
    $websiteIcons = [
        'globe'     => ['bg' => 'bg-blue-50',    'color' => 'text-blue-600',    'path' => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0z M3.6 9h16.8 M3.6 15h16.8 M12 3a15 15 0 010 18 M12 3a15 15 0 000 18'],
        'facebook'  => ['bg' => 'bg-blue-50',    'color' => 'text-blue-600',    'path' => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z'],
        'instagram' => ['bg' => 'bg-pink-50',    'color' => 'text-pink-600',    'path' => 'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5z'],
        'twitter'   => ['bg' => 'bg-sky-50',     'color' => 'text-sky-600',     'path' => 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z'],
        'linkedin'  => ['bg' => 'bg-blue-50',    'color' => 'text-blue-700',    'path' => 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z'],
        'github'    => ['bg' => 'bg-gray-100',   'color' => 'text-gray-800',    'path' => 'M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 00-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0020 4.77 5.07 5.07 0 0019.91 1S18.73.65 16 2.48a13.38 13.38 0 00-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 005 4.77a5.44 5.44 0 00-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 009 18.13V22'],
        'youtube'   => ['bg' => 'bg-red-50',     'color' => 'text-red-600',     'path' => 'M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 2A29 29 0 001 11.75a29 29 0 00.46 5.33A2.78 2.78 0 003.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 001.94-2 29 29 0 00.46-5.25 29 29 0 00-.46-5.33z M9.75 15.02l5.75-3.27-5.75-3.27v6.54z'],
        'tiktok'    => ['bg' => 'bg-gray-100',   'color' => 'text-gray-800',    'path' => 'M9 12a4 4 0 104 4V4a5 5 0 005 5'],
        'email'     => ['bg' => 'bg-emerald-50', 'color' => 'text-emerald-600', 'path' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        'phone'     => ['bg' => 'bg-violet-50',  'color' => 'text-violet-600',  'path' => 'M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z'],
    ];

    $relationshipMeta = [
        'single'    => ['emoji' => '💙', 'bg' => 'bg-blue-50',    'color' => 'text-blue-700',    'label' => 'Single'],
        'married'   => ['emoji' => '💍', 'bg' => 'bg-rose-50',    'color' => 'text-rose-700',    'label' => 'Married'],
        'divorced'  => ['emoji' => '💔', 'bg' => 'bg-amber-50',   'color' => 'text-amber-700',   'label' => 'Divorced'],
        'widowed'   => ['emoji' => '🕊️', 'bg' => 'bg-slate-50',   'color' => 'text-slate-700',   'label' => 'Widowed'],
        'separated' => ['emoji' => '🔗', 'bg' => 'bg-orange-50',  'color' => 'text-orange-700',  'label' => 'Separated'],
    ];
@endphp

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc; font-family: Inter, system-ui, sans-serif;">
    <div class="max-w-5xl mx-auto px-4">

        {{-- ============ COVER PHOTO ============ --}}
        <div class="relative">
            <div class="h-52 md:h-72 w-full rounded-2xl overflow-hidden"
                 style="background: linear-gradient(135deg,#2563eb 0%, #4f46e5 50%, #7c3aed 100%);">
                @if($user->cover_photo_url)
                    <img src="{{ $user->cover_photo_url }}" alt="Cover" class="w-full h-full object-cover">
                @endif
            </div>

            @if($isOwnProfile)
                <div class="absolute top-4 right-4 flex items-center gap-2">
                    <form action="{{ route('profile.cover.update') }}" method="POST" enctype="multipart/form-data" class="inline">
                        @csrf
                        <label for="coverInput"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs md:text-sm font-medium rounded-lg bg-white/95 text-gray-800 shadow-md hover:bg-white cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="hidden sm:inline">Change Cover</span>
                        </label>
                        <input type="file" id="coverInput" name="cover_photo" class="hidden" accept="image/*" onchange="this.form.submit()">
                    </form>
                    @if($user->cover_photo_url)
                        <form action="{{ route('profile.cover.remove') }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" title="Remove cover"
                                    class="inline-flex items-center justify-center w-8 h-8 md:w-9 md:h-9 rounded-lg bg-white/95 text-red-600 shadow-md hover:bg-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        {{-- ============ MAIN GRID ============ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 -mt-14 md:-mt-16 relative">

            {{-- ==================== LEFT COLUMN ==================== --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- PROFILE CARD --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 text-center">
                    <div class="-mt-20 md:-mt-24 mb-4 flex justify-center">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                             class="w-28 h-28 md:w-32 md:h-32 rounded-full border-4 border-white shadow-xl object-cover bg-white">
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-0 mt-0">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1 mb-0">@ {{ $user->username }}</p>

                    {{-- Relationship status pill --}}
                    @if($user->relationship_status && isset($relationshipMeta[$user->relationship_status]))
                        @php $rel = $relationshipMeta[$user->relationship_status]; @endphp
                        <div class="mt-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $rel['bg'] }} {{ $rel['color'] }}">
                                <span>{{ $rel['emoji'] }}</span>
                                {{ $rel['label'] }}
                            </span>
                        </div>
                    @endif

                    {{-- Verification badges --}}
                    <div class="flex flex-wrap justify-center gap-2 mt-4">
                        @if($user->isEmailVerified())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Email
                            </span>
                        @endif
                        @if($user->isMobileVerified())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Mobile
                            </span>
                        @endif
                    </div>

                    @if($user->bio)
                        <p class="text-sm text-gray-600 mt-4 px-2 leading-relaxed">{{ $user->bio }}</p>
                    @endif

                    {{-- Edit buttons --}}
                    @if($isOwnProfile)
                        <div class="mt-6 space-y-2">
                            <a href="{{ route('profile.edit') }}"
                               class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Profile
                            </a>
                            <a href="{{ route('verification.center') }}"
                               class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Verification Center
                            </a>
                        </div>
                    @endif
                </div>

                {{-- INTRO / CONTACT CARD --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4 mt-0 uppercase tracking-wide">Intro</h3>

                    <div class="space-y-3 text-sm">
                        {{-- Email --}}
                        @if($user->email)
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-gray-700 truncate">{{ $user->email }}</span>
                            </div>
                        @endif

                        {{-- Mobile --}}
                        @if($user->mobile_number)
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-gray-700">{{ $user->mobile_number }}</span>
                            </div>
                        @endif

                        {{-- Phone --}}
                        @if($user->phone)
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                                </div>
                                <span class="text-gray-700">{{ $user->phone }}</span>
                            </div>
                        @endif

                        {{-- Location --}}
                        @if($user->location || $user->country)
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <span class="text-gray-700 truncate">
                                    {{ collect([$user->location, $user->country])->filter()->implode(', ') }}
                                </span>
                            </div>
                        @endif

                        {{-- Joined --}}
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="text-gray-700">Joined {{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>

                    {{-- Websites --}}
                    @if($user->websites->count())
                        <div class="mt-5 pt-5 border-t border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2.5">Links</p>
                            <div class="space-y-1.5">
                                @foreach($user->websites as $site)
                                    @php $meta = $websiteIcons[$site->icon] ?? $websiteIcons['globe']; @endphp
                                    <a href="{{ $site->url }}" target="_blank" rel="noopener"
                                       class="flex items-center gap-2 {{ $meta['bg'] }} {{ $meta['color'] }} rounded-lg px-3 py-2 text-sm font-medium hover:opacity-80 transition">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $meta['path'] }}"/>
                                        </svg>
                                        <span class="truncate">{{ parse_url($site->url, PHP_URL_HOST) ?? $site->url }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- LANGUAGES & HOBBIES --}}
                @if(!empty($user->languages) || !empty($user->hobbies))
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

                        @if(!empty($user->languages))
                            <div class="{{ !empty($user->hobbies) ? 'mb-5' : '' }}">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3 mt-0 uppercase tracking-wide">Languages</h3>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($user->languages as $lang)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $lang }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!empty($user->hobbies))
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-3 mt-0 uppercase tracking-wide">Hobbies</h3>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($user->hobbies as $hobby)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                            # {{ $hobby }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

            </div>

            {{-- ==================== RIGHT COLUMN ==================== --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- ABOUT / CATEGORIES --}}
                @if(!empty($user->categories) || $user->bio)
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                        <div class="flex items-center gap-2.5 mb-4">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">About</h3>
                        </div>

                        @if($user->bio)
                            <p class="text-sm text-gray-700 leading-relaxed mb-4">{{ $user->bio }}</p>
                        @endif

                        @if(!empty($user->categories))
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Categories</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($user->categories as $cat)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 border border-blue-100">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            {{ $cat }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- WORK EXPERIENCE --}}
                @if($user->works->count())
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                        <div class="flex items-center gap-2.5 mb-5">
                            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Work Experience</h3>
                        </div>

                        <div class="space-y-4">
                            @foreach($user->works as $work)
                                <div class="flex gap-4">
                                    <div class="shrink-0">
                                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-sm">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                        <div class="flex items-start justify-between gap-2 mb-1">
                                            <div class="min-w-0 flex-1">
                                                <p class="font-semibold text-gray-900 mb-0 truncate">{{ $work->title }}</p>
                                                <p class="text-sm text-amber-700 font-medium mb-0 truncate">{{ $work->company }}</p>
                                            </div>
                                            @if($work->is_current)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 shrink-0">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Now
                                                </span>
                                            @endif
                                        </div>

                                        @if($work->start_date)
                                            <p class="text-xs text-gray-500 mb-1.5">
                                                {{ $work->start_date->format('M Y') }}
                                                –
                                                {{ $work->is_current ? 'Present' : ($work->end_date ? $work->end_date->format('M Y') : 'N/A') }}
                                            </p>
                                        @endif

                                        @if($work->description)
                                            <p class="text-sm text-gray-600 leading-relaxed mb-0">{{ $work->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- EDUCATION --}}
                @if($user->educations->count())
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                        <div class="flex items-center gap-2.5 mb-5">
                            <div class="w-9 h-9 rounded-lg bg-violet-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Education</h3>
                        </div>

                        <div class="space-y-4">
                            @foreach($user->educations as $edu)
                                <div class="flex gap-4">
                                    <div class="shrink-0">
                                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center shadow-sm">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                        <div class="flex items-start justify-between gap-2 mb-1">
                                            <div class="min-w-0 flex-1">
                                                <p class="font-semibold text-gray-900 mb-0 truncate">{{ $edu->school }}</p>
                                                @if($edu->degree || $edu->field_of_study)
                                                    <p class="text-sm text-violet-700 font-medium mb-0 truncate">
                                                        {{ collect([$edu->degree, $edu->field_of_study])->filter()->implode(' · ') }}
                                                    </p>
                                                @endif
                                            </div>
                                            @if($edu->is_current)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 shrink-0">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Now
                                                </span>
                                            @endif
                                        </div>

                                        @if($edu->start_date)
                                            <p class="text-xs text-gray-500 mb-1.5">
                                                {{ $edu->start_date->format('M Y') }}
                                                –
                                                {{ $edu->is_current ? 'Present' : ($edu->end_date ? $edu->end_date->format('M Y') : 'N/A') }}
                                            </p>
                                        @endif

                                        @if($edu->description)
                                            <p class="text-sm text-gray-600 leading-relaxed mb-0">{{ $edu->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- LOCATION WITH MAP --}}
                @if($user->address || ($user->latitude && $user->longitude))
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                        <div class="flex items-center gap-2.5 mb-4">
                            <div class="w-9 h-9 rounded-lg bg-rose-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Location</h3>
                        </div>

                        @if($user->address)
                            <div class="flex items-start gap-2.5 mb-4">
                                <svg class="w-4 h-4 text-rose-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <div>
                                    <p class="text-sm text-gray-800 font-medium mb-0">{{ $user->address }}</p>
                                    @if($user->country)
                                        <p class="text-xs text-gray-500 mb-0 mt-0.5">{{ $user->country }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($user->latitude && $user->longitude)
                            <div id="profileMap" class="rounded-xl overflow-hidden border border-gray-200 z-0" style="height: 240px;"></div>
                            <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                                <span>
                                    <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ number_format($user->latitude, 5) }}, {{ number_format($user->longitude, 5) }}
                                </span>
                                <a href="https://www.openstreetmap.org/?mlat={{ $user->latitude }}&mlon={{ $user->longitude }}#map=15/{{ $user->latitude }}/{{ $user->longitude }}"
                                   target="_blank" rel="noopener"
                                   class="text-blue-600 hover:text-blue-700 font-medium">
                                    Open in Maps →
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ACCOUNT OVERVIEW --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center gap-2.5 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Account Overview</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Role</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 capitalize">
                                {{ $user->display_role }}
                            </span>
                        </div>
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Member Since</p>
                            <p class="text-sm font-semibold text-gray-900 mb-0">{{ $user->created_at->format('d M Y') }}</p>
                        </div>

                        @if($user->isSellerOwner() && $user->sellerProfile)
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 md:col-span-2">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Store</p>
                                <p class="text-sm font-semibold text-gray-900 mb-0">{{ $user->sellerProfile->store_name }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- VERIFICATION STATUS --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center gap-2.5 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Verification Status</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- Email --}}
                        <div class="flex items-center justify-between p-3.5 rounded-xl border {{ $user->isEmailVerified() ? 'border-emerald-200 bg-emerald-50/60' : 'border-amber-200 bg-amber-50/60' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 {{ $user->isEmailVerified() ? 'text-emerald-600' : 'text-amber-600' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 mb-0">Email</p>
                                    <p class="text-xs text-gray-500 truncate mb-0">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold shrink-0 {{ $user->isEmailVerified() ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $user->isEmailVerified() ? 'Verified' : 'Pending' }}
                            </span>
                        </div>

                        {{-- Mobile --}}
                        <div class="flex items-center justify-between p-3.5 rounded-xl border {{ $user->isMobileVerified() ? 'border-emerald-200 bg-emerald-50/60' : 'border-amber-200 bg-amber-50/60' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 {{ $user->isMobileVerified() ? 'text-emerald-600' : 'text-amber-600' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 mb-0">Mobile</p>
                                    <p class="text-xs text-gray-500 truncate mb-0">{{ $user->mobile_number ?: 'Not set' }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold shrink-0 {{ $user->isMobileVerified() ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $user->isMobileVerified() ? 'Verified' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ============ MAP SCRIPT ============ --}}
@if($user->latitude && $user->longitude)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $user->latitude }};
        const lng = {{ $user->longitude }};

        const map = L.map('profileMap', {
            scrollWheelZoom: false,
            dragging: !L.Browser.mobile,
        }).setView([lat, lng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19,
        }).addTo(map);

        // Custom colored marker
        const customIcon = L.divIcon({
            html: `<div style="background:#ef4444;width:20px;height:20px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10],
            className: '',
        });

        L.marker([lat, lng], { icon: customIcon }).addTo(map);
    });
</script>
<style>
    #profileMap { position: relative; z-index: 0; }
    .leaflet-container { font-family: inherit; border-radius: 0.75rem; }
</style>
@endif
@endsection
