@extends('layouts.guest')

@section('title', 'Edit Profile — TolBazar')

@section('content')
{{-- ============ LIBRARIES ============ --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: { preflight: false },
        theme: { extend: { fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] } } },
    };
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

{{-- Tom Select — multi-select --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

{{-- Tagify — hobbies chips --}}
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

{{-- Leaflet — map picker --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@php
    $countryList = [
        'Nepal', 'India', 'United States', 'United Kingdom', 'Australia',
        'Canada', 'United Arab Emirates', 'Japan', 'China', 'Germany',
    ];

    $categoriesList = [
        'Business', 'Technology', 'Education', 'Healthcare', 'Art',
        'Music', 'Sports', 'Travel', 'Food', 'Fashion',
        'Photography', 'Writing', 'Science', 'Finance', 'Marketing', 'Design',
    ];

    $languagesList = [
        'Nepali', 'Hindi', 'English', 'Chinese', 'Spanish',
        'French', 'German', 'Japanese', 'Arabic', 'Portuguese',
        'Korean', 'Russian', 'Italian', 'Bengali', 'Tamil',
    ];

    $iconList = [
        'globe'     => '🌐 Website',
        'facebook'  => '📘 Facebook',
        'instagram' => '📷 Instagram',
        'twitter'   => '🐦 Twitter / X',
        'linkedin'  => '💼 LinkedIn',
        'github'    => '💻 GitHub',
        'youtube'   => '▶️ YouTube',
        'tiktok'    => '🎵 TikTok',
        'email'     => '✉️ Email',
        'phone'     => '📞 Phone',
    ];

    $currentCategories = old('categories', $user->categories ?? []);
    $currentLanguages  = old('languages', $user->languages ?? []);
    $currentHobbies    = old('hobbies', $user->hobbies ?? []);
    $currentWebsites   = old('websites', $user->websites->toArray());
    $currentWorks      = old('works', $user->works->toArray());
    $currentEducations = old('educations', $user->educations->toArray());

    $bioWordCount = 0;
    if (!empty(old('bio', $user->bio))) {
        $bioWordCount = count(preg_split('/\s+/u', trim(strip_tags(old('bio', $user->bio)))));
    }
@endphp

<div class="min-h-screen pb-24" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-5xl mx-auto px-4 py-6 md:py-10">

        {{-- HEADER --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 mt-0">Edit Profile</h1>
                <p class="text-sm text-gray-500 mb-0">Update your personal information and profile visuals.</p>
            </div>
            <a href="{{ route('profile.me') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                View Public Profile
            </a>
        </div>

        {{-- ALERTS --}}
        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-red-800 mb-1 mt-0">Please fix the following:</p>
                    <ul class="text-sm text-red-700 list-disc pl-4 space-y-0.5 mb-0">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium text-emerald-800 mb-0">{{ session('success') }}</p>
            </div>
        @endif

        <form id="profileForm" action="{{ route('profile.update') }}" method="POST"
              x-data="editProfileForm()">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ==================== LEFT: VISUALS ==================== --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Profile Picture --}}
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-0 mt-0">Profile Picture</h3>
                        </div>
                        <div class="p-6 text-center">
                            <div class="relative inline-block">
                                <img id="avatarPreview" src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                     class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg bg-gray-100">
                                <span class="absolute bottom-1 right-1 w-8 h-8 rounded-full bg-blue-600 border-4 border-white flex items-center justify-center shadow">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                </span>
                            </div>
                            <p class="mt-4 text-sm font-semibold text-gray-900 mb-0">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 mb-0">@ {{ $user->username }}</p>

                            <form action="{{ route('profile.picture.update') }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-2">
                                @csrf
                                <label for="avatarInput"
                                       class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-blue-700 bg-blue-50 border border-blue-100 hover:bg-blue-100 cursor-pointer transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Upload New Picture
                                </label>
                                <input type="file" id="avatarInput" name="avatar" class="hidden" accept="image/*">
                                <p class="text-[11px] text-gray-500 mb-0">JPG, PNG or WEBP · max 2MB</p>
                            </form>
                            @if($user->avatar)
                                <form action="{{ route('profile.picture.remove') }}" method="POST" class="mt-2">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-full gap-2 px-4 py-2 rounded-lg text-sm font-medium text-red-600 bg-white border border-red-200 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                                        Remove Picture
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Cover Photo --}}
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-0 mt-0">Cover Photo</h3>
                        </div>
                        <div class="p-4">
                            <div class="rounded-xl overflow-hidden border border-gray-200 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600" style="aspect-ratio:16/9;">
                                <img id="coverPreview" src="{{ $user->cover_photo_url ?? '' }}" alt="Cover"
                                     class="w-full h-full object-cover {{ $user->cover_photo_url ? '' : 'hidden' }}">
                            </div>
                            <form action="{{ route('profile.cover.update') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-2">
                                @csrf
                                <label for="coverInput"
                                       class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 hover:bg-indigo-100 cursor-pointer transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Change Cover
                                </label>
                                <input type="file" id="coverInput" name="cover_photo" class="hidden" accept="image/*">
                                <p class="text-[11px] text-gray-500 mb-0">Recommended 1500×500 · max 5MB</p>
                            </form>
                            @if($user->cover_photo_url)
                                <form action="{{ route('profile.cover.remove') }}" method="POST" class="mt-2">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-full gap-2 px-4 py-2 rounded-lg text-sm font-medium text-red-600 bg-white border border-red-200 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                                        Remove Cover
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ==================== RIGHT: FORM ==================== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- SECTION: BASIC INFO --}}
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Basic Information</h3>
                                <p class="text-xs text-gray-500 mb-0">Your identity and how others see you</p>
                            </div>
                        </div>
                        <div class="p-6 space-y-5">

                            {{-- Bio — 70 word limit --}}
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-sm font-medium text-gray-700">Bio</label>
                                    <span class="text-[11px] font-medium" :class="bioWords > 70 ? 'text-red-600' : 'text-gray-500'">
                                        <span x-text="bioWords">0</span>/70 words
                                    </span>
                                </div>
                                <textarea name="bio" x-model="bio" @input="updateBioWords()"
                                          rows="3" placeholder="Write a short intro (max 70 words)..."
                                          class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-none"
                                          :class="bioWords > 70 ? 'border-red-300 ring-2 ring-red-100' : ''">{{ old('bio', $user->bio) }}</textarea>
                                <p class="text-[11px] text-gray-500 mt-1 mb-0" x-show="bioWords > 70" x-cloak>
                                    ⚠️ Exceeds 70-word limit — please shorten your bio.
                                </p>
                            </div>

                            {{-- Categories (max 2) + Relationship --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Categories <span class="text-gray-400 text-xs">(max 2)</span>
                                    </label>
                                    <select id="categoriesSelect" name="categories[]" multiple
                                            placeholder="Choose up to 2 categories...">
                                        @foreach($categoriesList as $cat)
                                            <option value="{{ $cat }}" @selected(in_array($cat, $currentCategories))>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Relationship Status</label>
                                    <select name="relationship_status"
                                            class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                        <option value="">Not specified</option>
                                        @foreach(['single'=>'Single','married'=>'Married','divorced'=>'Divorced','widowed'=>'Widowed','separated'=>'Separated'] as $v=>$l)
                                            <option value="{{ $v }}" @selected(old('relationship_status', $user->relationship_status) === $v)>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Languages --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Languages</label>
                                <select id="languagesSelect" name="languages[]" multiple
                                        placeholder="Add languages you speak...">
                                    @foreach($languagesList as $lang)
                                        <option value="{{ $lang }}" @selected(in_array($lang, $currentLanguages))>{{ $lang }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Hobbies --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Hobbies & Interests</label>
                                <input id="hobbiesInput" name="hobbies" value="{{ json_encode($currentHobbies) }}"
                                       placeholder="Type and press Enter to add (e.g., Cooking, Travel, Reading)">
                                <p class="text-[11px] text-gray-500 mt-1 mb-0">Press <kbd class="px-1.5 py-0.5 bg-gray-100 rounded text-[10px] font-mono">Enter</kbd> or <kbd class="px-1.5 py-0.5 bg-gray-100 rounded text-[10px] font-mono">,</kbd> to add a hobby.</p>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: CONTACT --}}
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Contact Information</h3>
                                <p class="text-xs text-gray-500 mb-0">How we can reach you</p>
                            </div>
                        </div>
                        <div class="p-6 space-y-5">

                            {{-- Email — readonly if set --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                                @if($user->email)
                                    <div class="flex items-center gap-2 px-3.5 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span class="flex-1 truncate">{{ $user->email }}</span>
                                        @if($user->isEmailVerified())
                                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full shrink-0">Verified</span>
                                        @else
                                            <span class="text-[10px] font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full shrink-0">Unverified</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1 mb-0">Email is registered and cannot be changed here.</p>
                                @else
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com"
                                           class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                    <p class="text-[11px] text-emerald-600 mt-1 mb-0">No email set yet — add one now.</p>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mobile Number</label>
                                    @if($user->mobile_number)
                                        <div class="flex items-center gap-2 px-3.5 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-600">
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            <span class="flex-1 truncate">{{ $user->mobile_number }}</span>
                                            @if($user->isMobileVerified())
                                                <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full shrink-0">Verified</span>
                                            @else
                                                <span class="text-[10px] font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full shrink-0">Unverified</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-1 mb-0">Mobile number cannot be changed after it is set.</p>
                                    @else
                                        <input type="text" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="+977 98XXXXXXXX"
                                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                        <p class="text-[11px] text-emerald-600 mt-1 mb-0">No mobile set yet — add one now.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Country dropdown --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Country</label>
                                <select name="country"
                                        class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                    <option value="">Select country...</option>
                                    @foreach($countryList as $c)
                                        <option value="{{ $c }}" @selected(old('country', $user->country) === $c)>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: LOCATION with MAP --}}
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-rose-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Location</h3>
                                <p class="text-xs text-gray-500 mb-0">Pin your location on the map for accuracy</p>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                                <div class="flex gap-2">
                                    <input type="text" name="address" id="addressInput" value="{{ old('address', $user->address) }}"
                                           placeholder="Street, area, city..."
                                           class="flex-1 px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                    <button type="button" id="findOnMapBtn"
                                            class="px-3.5 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm whitespace-nowrap">
                                        Find on Map
                                    </button>
                                </div>
                            </div>

                            <div id="map" class="rounded-xl border border-gray-200 overflow-hidden z-0" style="height:320px;"></div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500 uppercase mb-1">Latitude</label>
                                    <input type="text" name="latitude" id="latInput" value="{{ old('latitude', $user->latitude) }}"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-xs text-gray-700 font-mono">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500 uppercase mb-1">Longitude</label>
                                    <input type="text" name="longitude" id="lngInput" value="{{ old('longitude', $user->longitude) }}"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-xs text-gray-700 font-mono">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: WEBSITES --}}
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-cyan-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Websites & Links</h3>
                                    <p class="text-xs text-gray-500 mb-0">Add links to your social profiles or website</p>
                                </div>
                            </div>
                            <button type="button" @click="addWebsite()"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-cyan-700 bg-cyan-50 border border-cyan-200 hover:bg-cyan-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Link
                            </button>
                        </div>
                        <div class="p-6 space-y-3">
                            <template x-for="(site, i) in websites" :key="i">
                                <div class="flex gap-2 items-start">
                                    <select x-model="site.icon" :name="`websites[${i}][icon]`"
                                            class="w-32 px-2 py-2.5 rounded-lg border border-gray-300 text-xs bg-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                                        @foreach($iconList as $iconKey => $iconLabel)
                                            <option value="{{ $iconKey }}">{{ $iconLabel }}</option>
                                        @endforeach
                                    </select>
                                    <input type="url" x-model="site.url" :name="`websites[${i}][url]`"
                                           placeholder="https://example.com/profile"
                                           class="flex-1 px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none transition">
                                    <button type="button" @click="websites.splice(i, 1)"
                                            class="w-10 h-10 shrink-0 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                            <div x-show="websites.length === 0" x-cloak class="text-center py-6 text-sm text-gray-400">
                                No websites yet. Click "Add Link" to add one.
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: WORK EXPERIENCE --}}
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Work Experience</h3>
                                    <p class="text-xs text-gray-500 mb-0">Your professional background</p>
                                </div>
                            </div>
                            <button type="button" @click="addWork()"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Work
                            </button>
                        </div>
                        <div class="p-6 space-y-4">
                            <template x-for="(work, i) in works" :key="i">
                                <div class="rounded-xl border border-gray-200 p-4 bg-gray-50/50 relative">
                                    <button type="button" @click="works.splice(i, 1)"
                                            class="absolute top-3 right-3 w-7 h-7 rounded-lg text-red-600 bg-white hover:bg-red-50 border border-red-200 transition">
                                        <svg class="w-3.5 h-3.5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <div class="space-y-3 pr-10">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Job Title *</label>
                                                <input type="text" x-model="work.title" :name="`works[${i}][title]`" required
                                                       placeholder="e.g. Software Engineer"
                                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Company *</label>
                                                <input type="text" x-model="work.company" :name="`works[${i}][company]`" required
                                                       placeholder="e.g. Google"
                                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                            <textarea x-model="work.description" :name="`works[${i}][description]`" rows="2"
                                                      placeholder="What did you do?"
                                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition resize-none"></textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label>
                                                <input type="date" x-model="work.start_date" :name="`works[${i}][start_date]`"
                                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">End Date</label>
                                                <input type="date" x-model="work.end_date" :name="`works[${i}][end_date]`"
                                                       :disabled="work.is_current"
                                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition disabled:bg-gray-100">
                                            </div>
                                        </div>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="work.is_current" :name="`works[${i}][is_current]`" value="1"
                                                   class="w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                            <span class="text-xs text-gray-700">I currently work here</span>
                                        </label>
                                    </div>
                                </div>
                            </template>
                            <div x-show="works.length === 0" x-cloak class="text-center py-6 text-sm text-gray-400">
                                No work experience added. Click "Add Work" to start.
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: EDUCATION --}}
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-violet-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Education</h3>
                                    <p class="text-xs text-gray-500 mb-0">Your academic background</p>
                                </div>
                            </div>
                            <button type="button" @click="addEducation()"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-violet-700 bg-violet-50 border border-violet-200 hover:bg-violet-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Education
                            </button>
                        </div>
                        <div class="p-6 space-y-4">
                            <template x-for="(edu, i) in educations" :key="i">
                                <div class="rounded-xl border border-gray-200 p-4 bg-gray-50/50 relative">
                                    <button type="button" @click="educations.splice(i, 1)"
                                            class="absolute top-3 right-3 w-7 h-7 rounded-lg text-red-600 bg-white hover:bg-red-50 border border-red-200 transition">
                                        <svg class="w-3.5 h-3.5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <div class="space-y-3 pr-10">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">School / University *</label>
                                                <input type="text" x-model="edu.school" :name="`educations[${i}][school]`" required
                                                       placeholder="e.g. Tribhuvan University"
                                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Degree</label>
                                                <input type="text" x-model="edu.degree" :name="`educations[${i}][degree]`"
                                                       placeholder="e.g. Bachelor's"
                                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none transition">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Field of Study</label>
                                            <input type="text" x-model="edu.field_of_study" :name="`educations[${i}][field_of_study]`"
                                                   placeholder="e.g. Computer Science"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none transition">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                            <textarea x-model="edu.description" :name="`educations[${i}][description]`" rows="2"
                                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none transition resize-none"></textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label>
                                                <input type="date" x-model="edu.start_date" :name="`educations[${i}][start_date]`"
                                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">End Date</label>
                                                <input type="date" x-model="edu.end_date" :name="`educations[${i}][end_date]`"
                                                       :disabled="edu.is_current"
                                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none transition disabled:bg-gray-100">
                                            </div>
                                        </div>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="edu.is_current" :name="`educations[${i}][is_current]`" value="1"
                                                   class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                            <span class="text-xs text-gray-700">Currently studying here</span>
                                        </label>
                                    </div>
                                </div>
                            </template>
                            <div x-show="educations.length === 0" x-cloak class="text-center py-6 text-sm text-gray-400">
                                No education added. Click "Add Education" to start.
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: PERSONAL DETAILS --}}
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-pink-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 mb-0 mt-0">Personal Details</h3>
                                <p class="text-xs text-gray-500 mb-0">Optional information</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Date of Birth</label>
                                    <input type="date" name="date_of_birth"
                                           value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}"
                                           class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                                    <select name="gender"
                                            class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                        <option value="">Select...</option>
                                        @foreach(['male'=>'Male','female'=>'Female','other'=>'Other','prefer_not_to_say'=>'Prefer not to say'] as $v=>$l)
                                            <option value="{{ $v }}" @selected(old('gender', $user->gender) === $v)>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STICKY SAVE BAR --}}
                    <div class="sticky bottom-4 z-10">
                        <div class="bg-white/95 backdrop-blur border border-gray-200 rounded-2xl shadow-lg px-4 py-3 flex items-center justify-between gap-3">
                            <p class="hidden sm:block text-xs text-gray-500 mb-0">
                                <svg class="inline w-3.5 h-3.5 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                All changes apply on save.
                            </p>
                            <div class="flex items-center gap-2 ml-auto">
                                <a href="{{ route('profile.me') }}"
                                   class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                                    Cancel
                                </a>
                                <button type="submit" :disabled="bioWords > 70"
                                        class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

{{-- ============ SCRIPTS ============ --}}
<script>
    // ---------- Alpine component ----------
    function editProfileForm() {
        return {
            bio: @json(old('bio', $user->bio ?? '')),
            bioWords: {{ $bioWordCount }},
            websites: @json($currentWebsites).map(w => ({ icon: w.icon || 'globe', url: w.url || '' })),
            works: @json($currentWorks).map(w => ({
                title: w.title || '',
                company: w.company || '',
                description: w.description || '',
                start_date: w.start_date ? (w.start_date.split('T')[0] ?? w.start_date) : '',
                end_date: w.end_date ? (w.end_date.split('T')[0] ?? w.end_date) : '',
                is_current: !!w.is_current,
            })),
            educations: @json($currentEducations).map(e => ({
                school: e.school || '',
                degree: e.degree || '',
                field_of_study: e.field_of_study || '',
                description: e.description || '',
                start_date: e.start_date ? (e.start_date.split('T')[0] ?? e.start_date) : '',
                end_date: e.end_date ? (e.end_date.split('T')[0] ?? e.end_date) : '',
                is_current: !!e.is_current,
            })),

            updateBioWords() {
                const text = (this.bio || '').trim();
                this.bioWords = text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;
            },

            addWebsite() {
                if (this.websites.length >= 10) return alert('Max 10 websites.');
                this.websites.push({ icon: 'globe', url: '' });
            },
            addWork() {
                if (this.works.length >= 15) return alert('Max 15 work entries.');
                this.works.push({
                    title: '', company: '', description: '',
                    start_date: '', end_date: '', is_current: false,
                });
            },
            addEducation() {
                if (this.educations.length >= 15) return alert('Max 15 education entries.');
                this.educations.push({
                    school: '', degree: '', field_of_study: '', description: '',
                    start_date: '', end_date: '', is_current: false,
                });
            },
        };
    }

    // ---------- Tom Select: Categories (max 2) ----------
    document.addEventListener('DOMContentLoaded', function () {
        new TomSelect('#categoriesSelect', {
            maxItems: 2,
            plugins: ['remove_button', 'clear_button'],
            placeholder: 'Choose up to 2...',
            render: {
                no_results: function () {
                    return '<div class="no-results">No categories found</div>';
                }
            }
        });

        // ---------- Tom Select: Languages ----------
        new TomSelect('#languagesSelect', {
            plugins: ['remove_button', 'clear_button'],
            placeholder: 'Add languages you speak...',
        });

        // ---------- Tagify: Hobbies ----------
        var hobbiesInput = document.getElementById('hobbiesInput');
        if (hobbiesInput) {
            new Tagify(hobbiesInput, {
                delimiters: ',|\\n',
                maxTags: 20,
                dropdown: { enabled: 0 },
                originalInputValueFormat: (valuesArr) =>
                    JSON.stringify(valuesArr.map(v => v.value))
            });
        }

        // ---------- Avatar & Cover live preview ----------
        const avatarInput = document.getElementById('avatarInput');
        const avatarPreview = document.getElementById('avatarPreview');
        if (avatarInput && avatarPreview) {
            avatarInput.addEventListener('change', e => {
                const file = e.target.files?.[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = ev => { avatarPreview.src = ev.target.result; };
                reader.readAsDataURL(file);
                setTimeout(() => avatarInput.form.submit(), 400);
            });
        }
        const coverInput = document.getElementById('coverInput');
        const coverPreview = document.getElementById('coverPreview');
        if (coverInput && coverPreview) {
            coverInput.addEventListener('change', e => {
                const file = e.target.files?.[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = ev => {
                    coverPreview.src = ev.target.result;
                    coverPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
                setTimeout(() => coverInput.form.submit(), 400);
            });
        }

        // ---------- LEAFLET MAP ----------
        const latInput = document.getElementById('latInput');
        const lngInput = document.getElementById('lngInput');
        const addressInput = document.getElementById('addressInput');

        let initLat = parseFloat(latInput.value);
        let initLng = parseFloat(lngInput.value);
        if (isNaN(initLat) || isNaN(initLng)) {
            initLat = 27.7172; initLng = 85.3240; // Kathmandu
        }

        const map = L.map('map').setView([initLat, initLng], latInput.value ? 15 : 7);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap',
        }).addTo(map);

        let marker = null;

        function setMarker(lat, lng) {
            if (marker) marker.setLatLng([lat, lng]);
            else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', () => {
                    const pos = marker.getLatLng();
                    updateCoords(pos.lat, pos.lng);
                    reverseGeocode(pos.lat, pos.lng);
                });
            }
            updateCoords(lat, lng);
        }

        function updateCoords(lat, lng) {
            latInput.value = parseFloat(lat).toFixed(7);
            lngInput.value = parseFloat(lng).toFixed(7);
        }

        // Initialize marker if coords exist
        if (latInput.value && lngInput.value) {
            setMarker(parseFloat(latInput.value), parseFloat(lngInput.value));
        }

        // Map click → pin
        map.on('click', e => {
            setMarker(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });

        // "Find on Map" from address input
        document.getElementById('findOnMapBtn').addEventListener('click', () => {
            const addr = addressInput.value.trim();
            if (!addr) return alert('Enter an address first.');

            fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(addr)}`)
                .then(r => r.json())
                .then(results => {
                    if (!results.length) return alert('Address not found. Try more detail.');
                    const { lat, lon } = results[0];
                    setMarker(parseFloat(lat), parseFloat(lon));
                    map.setView([parseFloat(lat), parseFloat(lon)], 15);
                })
                .catch(() => alert('Could not search address.'));
        });

        function reverseGeocode(lat, lng) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(r => r.json())
                .then(res => {
                    if (res?.display_name && !addressInput.value) {
                        addressInput.value = res.display_name;
                    }
                })
                .catch(() => {});
        }
    });
</script>

<style>
    [x-cloak] { display: none !important; }

    /* Tom Select Tailwind styling */
    .ts-wrapper.single .ts-control,
    .ts-wrapper.multi .ts-control {
        min-height: 42px;
        padding: 6px 12px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        background: #fff;
        font-size: 0.875rem;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .ts-wrapper.multi .ts-control > div {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        border-radius: 0.375rem;
        padding: 2px 8px;
        margin: 2px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .ts-wrapper.multi .ts-control > div .remove {
        border-left-color: #bfdbfe;
    }
    .ts-dropdown {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,.1);
        font-size: 0.875rem;
        margin-top: 4px;
    }
    .ts-dropdown .option {
        padding: 8px 12px;
    }
    .ts-dropdown .option.active {
        background: #eff6ff;
        color: #1e40af;
    }

    /* Tagify Tailwind styling */
    .tagify {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 6px 10px !important;
        min-height: 42px !important;
    }
    .tagify.tagify--focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
    .tagify__tag {
        background: #eff6ff !important;
        color: #1e40af !important;
        border: 1px solid #bfdbfe !important;
        border-radius: 0.375rem !important;
    }
    .tagify__tag__removeBtn {
        color: #1e40af !important;
    }

    /* Leaflet z-index fix */
    #map { position: relative; z-index: 0; }
    .leaflet-container { font-family: inherit; }
</style>
@endsection
