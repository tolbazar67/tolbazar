@extends('layouts.guest')

@section('title', 'Change Mobile Number — TolBazar')

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
            <div class="h-1.5 bg-gradient-to-r from-violet-400 to-purple-600"></div>

            <div class="p-6 md:p-8">

                <div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>

                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 mt-0">Change Mobile Number</h1>
                <p class="text-sm text-gray-600 mb-5">
                    Current mobile number:
                    @if($user->mobile_number)
                        <strong>{{ $user->mobile_number }}</strong>
                        @if($user->isMobileVerified())
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 ml-1">
                                Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800 ml-1">
                                Unverified
                            </span>
                        @endif
                    @else
                        <em class="text-gray-400">Not set</em>
                    @endif
                </p>

                {{-- Alerts --}}
                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800 flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('info'))
                    <div class="mb-4 rounded-lg bg-blue-50 border border-blue-200 p-3 text-sm text-blue-800">{{ session('info') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                        <ul class="list-disc pl-4 mb-0 space-y-0.5">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                @if($user->pending_mobile)
                    {{-- ========================================================= --}}
                    {{-- STATE 2: OTP VERIFICATION --}}
                    {{-- ========================================================= --}}
                    <div class="rounded-xl bg-violet-50/60 border border-violet-100 p-4 mb-5">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-violet-900 mb-0.5 mt-0">OTP sent to</p>
                                <p class="text-sm text-violet-800 font-semibold mb-0 truncate">{{ $user->pending_mobile }}</p>
                                @if($user->mobile_change_otp_expires_at)
                                    <p class="text-[11px] text-violet-700 mt-1 mb-0">
                                        Expires {{ $user->mobile_change_otp_expires_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('account.change-mobile.verify') }}" method="POST" class="mb-3">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Enter 6-digit OTP
                        </label>
                        <div class="flex gap-1.5 md:gap-2 mb-3" id="otpInputs">
                            @for($i = 0; $i < 6; $i++)
                                <input type="text" inputmode="numeric" maxlength="1" pattern="\d*"
                                       class="otp-digit w-full aspect-square text-center text-lg md:text-xl font-bold text-gray-900 bg-white border-2 border-gray-300 rounded-lg focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none transition"
                                       data-index="{{ $i }}" autocomplete="one-time-code">
                            @endfor
                        </div>
                        <input type="hidden" name="otp" id="otpHidden">

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-violet-600 hover:bg-violet-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Verify & Update Mobile
                        </button>
                    </form>

                    <div class="flex gap-2">
                        <form action="{{ route('account.change-mobile.resend') }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium text-violet-700 bg-violet-50 border border-violet-200 hover:bg-violet-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Resend OTP
                            </button>
                        </form>
                        <form action="{{ route('account.change-mobile.cancel') }}" method="POST" class="flex-1"
                              onsubmit="return confirm('Cancel this mobile change?');">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium text-red-600 bg-white border border-red-200 hover:bg-red-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Cancel Change
                            </button>
                        </form>
                    </div>

                @else
                    {{-- ========================================================= --}}
                    {{-- STATE 1: REQUEST NEW MOBILE --}}
                    {{-- ========================================================= --}}
                    <div class="rounded-xl bg-amber-50/60 border border-amber-100 p-4 mb-5 text-xs text-amber-800 flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <p class="font-semibold mb-0.5 mt-0">Verify your new mobile</p>
                            <p class="mb-0">We'll send a 6-digit OTP via SMS to your new mobile number. You'll need to enter it to complete the change.</p>
                        </div>
                    </div>

                    <form action="{{ route('account.change-mobile.send') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                New Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel"
                                   name="mobile_number"
                                   value="{{ old('mobile_number') }}"
                                   required
                                   minlength="7"
                                   maxlength="30"
                                   placeholder="+977 98XXXXXXXX"
                                   autocomplete="tel"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none transition">
                            <p class="text-[11px] text-gray-500 mt-1 mb-0">
                                Include country code if outside Nepal (e.g. +91 for India)
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
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none transition">
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('account.settings') }}"
                               class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-violet-600 hover:bg-violet-700 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Send OTP
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- OTP auto-advance + auto-submit --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const digits = Array.from(document.querySelectorAll('.otp-digit'));
    const hidden = document.getElementById('otpHidden');
    if (!digits.length) return;

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
            if (digits.every(d => d.value.length === 1)) {
                setTimeout(() => input.form && input.form.submit(), 250);
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && i > 0) {
                digits[i - 1].focus();
                digits[i - 1].select();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            [...text].forEach((ch, idx) => {
                if (digits[i + idx]) digits[i + idx].value = ch;
            });
            const next = digits.findIndex(d => !d.value);
            (digits[next >= 0 ? next : digits.length - 1]).focus();
            sync();
        });

        input.addEventListener('focus', (e) => e.target.select());
    });
});
</script>
@endsection
