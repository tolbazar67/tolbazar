@extends('layouts.guest')

@section('title', 'Verification Center — TolBazar')

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

@php
    $verifiedCount = collect([$user->isEmailVerified(), $user->isMobileVerified()])->filter()->count();
    $totalCount = 2;
    $progressPct = (int) round(($verifiedCount / $totalCount) * 100);
    $allVerified = $user->isFullyVerified();
@endphp

<div class="min-h-screen pb-12" style="background:#f8fafc; font-family: Inter, system-ui, sans-serif;">
    <div class="max-w-4xl mx-auto px-4 py-6 md:py-10">

        {{-- ============ HERO / PROGRESS HEADER ============ --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 md:p-8 mb-6 shadow-lg">
            {{-- decorative circles --}}
            <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-20 -left-10 w-48 h-48 rounded-full bg-white/5"></div>

            <div class="relative flex flex-col md:flex-row items-center gap-6">
                {{-- Circular progress --}}
                <div class="relative shrink-0">
                    <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="42" stroke="rgba(255,255,255,0.2)" stroke-width="8" fill="none"/>
                        <circle cx="50" cy="50" r="42" stroke="white" stroke-width="8" fill="none"
                                stroke-linecap="round"
                                stroke-dasharray="{{ 2 * 3.14159 * 42 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 42 * (1 - $progressPct / 100) }}"
                                class="transition-all duration-700"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-white">
                        <span class="text-2xl font-bold leading-none">{{ $verifiedCount }}/{{ $totalCount }}</span>
                        <span class="text-[10px] uppercase tracking-wider mt-1 opacity-80">Verified</span>
                    </div>
                </div>

                <div class="text-center md:text-left flex-1">
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 mt-0">
                        @if($allVerified)
                            You're fully verified 🎉
                        @else
                            Account Verification
                        @endif
                    </h1>
                    <p class="text-blue-100 text-sm mb-0">
                        @if($allVerified)
                            Your email and mobile are both verified. Enjoy full access to TolBazar features.
                        @else
                            Verify your email and mobile to unlock all features and build trust.
                        @endif
                    </p>
                </div>

                {{-- Quick status pills --}}
                <div class="flex md:flex-col gap-2 md:gap-2 flex-wrap justify-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $user->isEmailVerified() ? 'bg-emerald-400/20 text-emerald-50 border border-emerald-300/40' : 'bg-amber-400/20 text-amber-50 border border-amber-300/40' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $user->isEmailVerified() ? 'bg-emerald-300' : 'bg-amber-300' }}"></span>
                        Email
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $user->isMobileVerified() ? 'bg-emerald-400/20 text-emerald-50 border border-emerald-300/40' : 'bg-amber-400/20 text-amber-50 border border-amber-300/40' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $user->isMobileVerified() ? 'bg-emerald-300' : 'bg-amber-300' }}"></span>
                        Mobile
                    </span>
                </div>
            </div>
        </div>

        {{-- ============ ALERTS ============ --}}
        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="flex-1">
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
        @if(session('info'))
            <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium text-blue-800 mb-0">{{ session('info') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ==================== EMAIL CARD ==================== --}}
            <div class="bg-white border {{ $user->isEmailVerified() ? 'border-emerald-200' : 'border-gray-200' }} rounded-2xl shadow-sm overflow-hidden flex flex-col">
                {{-- Card top strip --}}
                <div class="h-1.5 {{ $user->isEmailVerified() ? 'bg-gradient-to-r from-emerald-400 to-emerald-600' : 'bg-gradient-to-r from-amber-400 to-orange-500' }}"></div>

                <div class="p-6 flex-1 flex flex-col">
                    {{-- Header row --}}
                    <div class="flex items-start justify-between gap-3 mb-5">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-xl {{ $user->isEmailVerified() ? 'bg-emerald-50' : 'bg-amber-50' }} flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 {{ $user->isEmailVerified() ? 'text-emerald-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-semibold text-gray-900 mb-0.5 mt-0">Email Address</h3>
                                <p class="text-sm text-gray-500 truncate mb-0">{{ $user->email }}</p>
                            </div>
                        </div>
                        @if($user->isEmailVerified())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 shrink-0">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Pending
                            </span>
                        @endif
                    </div>

                    <div class="flex-1">
                        @if($user->isEmailVerified())
                            <div class="rounded-xl bg-emerald-50/60 border border-emerald-100 p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    <div>
                                        <p class="text-sm font-medium text-emerald-900 mb-0.5 mt-0">Your email is verified</p>
                                        <p class="text-xs text-emerald-700 mb-0">
                                            Verified on {{ $user->email_verified_at->format('d M Y, h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl bg-amber-50/60 border border-amber-100 p-4 mb-4">
                                <p class="text-sm text-amber-900 mb-0">
                                    <strong>Not yet verified.</strong> We'll send a 6-digit OTP to your email inbox. It expires in 10 minutes.
                                </p>
                            </div>

                            <form action="{{ route('verification.email.resend') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    Send Verification OTP
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ==================== MOBILE CARD ==================== --}}
            <div class="bg-white border {{ $user->isMobileVerified() ? 'border-emerald-200' : 'border-gray-200' }} rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="h-1.5 {{ $user->isMobileVerified() ? 'bg-gradient-to-r from-emerald-400 to-emerald-600' : 'bg-gradient-to-r from-blue-400 to-indigo-600' }}"></div>

                <div class="p-6 flex-1 flex flex-col">
                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-3 mb-5">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-xl {{ $user->isMobileVerified() ? 'bg-emerald-50' : 'bg-blue-50' }} flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 {{ $user->isMobileVerified() ? 'text-emerald-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-semibold text-gray-900 mb-0.5 mt-0">Mobile Number</h3>
                                <p class="text-sm text-gray-500 truncate mb-0">
                                    {{ $user->mobile_number ?: 'No mobile number set' }}
                                </p>
                            </div>
                        </div>
                        @if($user->isMobileVerified())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 shrink-0">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Pending
                            </span>
                        @endif
                    </div>

                    <div class="flex-1">
                        @if($user->isMobileVerified())
                            <div class="rounded-xl bg-emerald-50/60 border border-emerald-100 p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    <div>
                                        <p class="text-sm font-medium text-emerald-900 mb-0.5 mt-0">Your mobile is verified</p>
                                        <p class="text-xs text-emerald-700 mb-0">
                                            Verified on {{ $user->mobile_verified_at->format('d M Y, h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Step indicator --}}
                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex items-center gap-1.5">
                                    <span id="step1Badge" class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold">1</span>
                                    <span class="text-xs font-medium text-gray-700">Send OTP</span>
                                </div>
                                <div class="flex-1 h-px bg-gray-200"></div>
                                <div class="flex items-center gap-1.5">
                                    <span id="step2Badge" class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 text-gray-500 text-xs font-bold">2</span>
                                    <span class="text-xs font-medium text-gray-500">Verify</span>
                                </div>
                            </div>

                            {{-- Step 1: send OTP --}}
                            <form action="{{ route('verification.mobile.send') }}" method="POST" class="mb-3">
                                @csrf
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mobile Number</label>
                                <div class="flex gap-2">
                                    <input type="text" name="mobile_number"
                                           value="{{ old('mobile_number', $user->mobile_number) }}"
                                           placeholder="+977 98XXXXXXXX" required
                                           class="flex-1 px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                    <button type="submit"
                                            class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm whitespace-nowrap">
                                        Send
                                    </button>
                                </div>
                            </form>

                            {{-- Step 2: verify OTP --}}
                            <form action="{{ route('verification.mobile.verify') }}" method="POST" id="otpForm">
                                @csrf
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Enter 6-digit OTP</label>
                                <div class="flex gap-1.5 md:gap-2 mb-3" id="otpInputs">
                                    @for($i = 0; $i < 6; $i++)
                                        <input type="text" inputmode="numeric" maxlength="1"
                                               pattern="\d*"
                                               class="otp-digit w-full aspect-square text-center text-lg md:text-xl font-bold text-gray-900 bg-white border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition"
                                               data-index="{{ $i }}" autocomplete="one-time-code">
                                    @endfor
                                </div>
                                <input type="hidden" name="mobile_otp" id="mobileOtpHidden" value="{{ old('mobile_otp') }}">

                                <button type="submit"
                                        class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Verify OTP
                                </button>
                            </form>

                            <p class="text-[11px] text-gray-500 mt-3 mb-0 text-center">
                                Didn't receive? Wait <span id="countdown" class="font-semibold text-gray-700">60</span>s to resend.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ============ INFO FOOTER ============ --}}
        <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50/50 p-5">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-blue-900 mb-1 mt-0">Why verify your account?</p>
                    <ul class="text-xs text-blue-800 list-disc pl-4 space-y-0.5 mb-0">
                        <li>Unlock all buying, selling and delivery features</li>
                        <li>Prevent unauthorized access to your account</li>
                        <li>Receive important order and security notifications</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ============ SCRIPTS: OTP auto-advance, paste, countdown ============ --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const digits = Array.from(document.querySelectorAll('.otp-digit'));
        const hidden = document.getElementById('mobileOtpHidden');
        const form   = document.getElementById('otpForm');

        if (!digits.length) return;

        // Pre-fill from old value if any
        if (hidden && hidden.value) {
            [...hidden.value].forEach((d, i) => { if (digits[i]) digits[i].value = d; });
        }

        function sync() {
            if (hidden) hidden.value = digits.map(d => d.value || '').join('');
        }

        digits.forEach((input, i) => {
            input.addEventListener('input', (e) => {
                const val = e.target.value.replace(/\D/g, '');
                e.target.value = val.slice(-1);

                if (val && i < digits.length - 1) {
                    digits[i + 1].focus();
                    digits[i + 1].select();
                }
                sync();

                // Auto submit when all 6 filled
                if (digits.every(d => d.value.length === 1) && form) {
                    setTimeout(() => form.submit(), 250);
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && i > 0) {
                    digits[i - 1].focus();
                    digits[i - 1].select();
                }
                if (e.key === 'ArrowLeft' && i > 0) digits[i - 1].focus();
                if (e.key === 'ArrowRight' && i < digits.length - 1) digits[i + 1].focus();
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                const clean = text.replace(/\D/g, '').slice(0, 6);
                [...clean].forEach((ch, idx) => {
                    if (digits[i + idx]) digits[i + idx].value = ch;
                });
                const nextEmpty = digits.findIndex(d => !d.value);
                (digits[nextEmpty >= 0 ? nextEmpty : digits.length - 1]).focus();
                sync();
            });

            input.addEventListener('focus', (e) => e.target.select());
        });

        // Countdown timer for resend
        const cdEl = document.getElementById('countdown');
        if (cdEl) {
            let seconds = 60;
            const timer = setInterval(() => {
                seconds--;
                if (seconds <= 0) {
                    clearInterval(timer);
                    cdEl.textContent = '0';
                    cdEl.classList.add('text-emerald-600');
                    cdEl.classList.remove('text-gray-700');
                } else {
                    cdEl.textContent = seconds;
                }
            }, 1000);
        }
    });
</script>
@endsection
