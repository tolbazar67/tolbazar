<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Welcome') — {{ config('app.name', 'TolBazar') }}</title>

    {{-- Favicon (optional — replace with your own) --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234f46e5'%3E%3Cpath d='M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'/%3E%3C/svg%3E">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CDN (development only — use Vite build in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    <style>
        html, body { height: 100%; }
        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Smooth fade-in for the card */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.35s ease-out both;
        }

        /* Subtle floating gradient orbs in background */
        .bg-orb {
            position: fixed;
            border-radius: 9999px;
            filter: blur(80px);
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
        }

        /* Input autofill fix (Chrome yellow tint) */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
            -webkit-text-fill-color: #111827 !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 text-gray-900">

    {{-- ==================== DECORATIVE BACKGROUND ORBS ==================== --}}
    <div class="bg-orb" style="width: 420px; height: 420px; background: #a5b4fc; top: -120px; left: -120px;"></div>
    <div class="bg-orb" style="width: 380px; height: 380px; background: #d8b4fe; bottom: -120px; right: -120px;"></div>
    <div class="bg-orb" style="width: 260px; height: 260px; background: #c7d2fe; top: 40%; right: 10%; opacity: 0.25;"></div>

    {{-- ==================== MAIN CONTENT ==================== --}}
    <div class="relative z-10 min-h-screen flex flex-col">

        {{-- ==================== TOP BRAND BAR ==================== --}}
        <header class="w-full px-6 py-5">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-md shadow-indigo-500/25 group-hover:scale-105 transition">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="text-lg font-extrabold tracking-tight text-gray-900">
                    {{ config('app.name', 'TolBazar') }}
                </span>
            </a>
        </header>

        {{-- ==================== PAGE CONTENT ==================== --}}
        <main class="flex-1 flex items-center justify-center px-4 py-6">
            <div class="w-full animate-fade-in-up">
                @yield('content')
            </div>
        </main>

        {{-- ==================== FOOTER ==================== --}}
        <footer class="w-full px-6 py-5 text-center">
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-xs text-gray-500">
                <span>&copy; {{ date('Y') }} {{ config('app.name', 'TolBazar') }}</span>
                <span class="hidden sm:inline text-gray-300">•</span>
                <a href="#" class="hover:text-indigo-600 transition">Privacy</a>
                <span class="text-gray-300">•</span>
                <a href="#" class="hover:text-indigo-600 transition">Terms</a>
                <span class="text-gray-300">•</span>
                <a href="#" class="hover:text-indigo-600 transition">Support</a>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>