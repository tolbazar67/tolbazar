<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error') — {{ config('app.name', 'TolBazar') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 flex items-center justify-center px-4">

    <div class="max-w-2xl w-full text-center">

        {{-- Logo / Brand --}}
        <div class="mb-10">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-2xl font-extrabold text-slate-800 tracking-tight">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white">T</span>
                {{ config('app.name', 'TolBazar') }}
            </a>
        </div>

        {{-- Big status code --}}
        <div class="mb-6">
            <h1 class="text-[120px] sm:text-[160px] font-extrabold leading-none bg-gradient-to-br from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                @yield('code', '500')
            </h1>
        </div>

        {{-- Title + message --}}
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-3">
            @yield('title', 'Something went wrong')
        </h2>
        <p class="text-slate-600 text-base sm:text-lg mb-10 max-w-md mx-auto">
            @yield('message', 'We are working on it. Please try again later.')
        </p>

        {{-- Action buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Go Back
            </a>
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 font-semibold shadow-lg shadow-blue-600/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Go Home
            </a>
        </div>

        {{-- Footer --}}
        <p class="mt-16 text-sm text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'TolBazar') }}. All rights reserved.
        </p>
    </div>

</body>
</html>
