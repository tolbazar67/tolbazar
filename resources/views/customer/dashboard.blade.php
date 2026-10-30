@extends('layouts.guest')

@section('title', 'My Dashboard')

@section('content')

{{-- ================================================================ --}}
{{-- WELCOME BANNER --}}
{{-- ================================================================ --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 p-6 sm:p-8 mb-6 shadow-lg shadow-blue-600/20">

    {{-- Decorative circles --}}
    <div class="absolute -top-20 -right-20 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
    <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>

    <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <p class="text-blue-100 text-sm font-medium mb-1">Welcome back 👋</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">
                Hi, {{ auth()->user()->name ?? 'Customer' }}!
            </h1>
            <p class="text-blue-100 text-sm max-w-xl">
                Manage your orders, track deliveries, and explore the latest deals — all from one place.
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-white text-blue-600 hover:bg-blue-50 px-5 py-2.5 text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Continue Shopping
            </a>
        </div>
    </div>
</div>

{{-- ================================================================ --}}
{{-- STATS CARDS --}}
{{-- ================================================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Orders --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="h-11 w-11 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+12%</span>
        </div>
        <p class="text-slate-500 text-sm">Total Orders</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_orders'] ?? 0 }}</p>
    </div>

    {{-- Pending --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="h-11 w-11 rounded-xl bg-yellow-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-slate-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['pending_orders'] ?? 0 }}</p>
    </div>

    {{-- Delivered --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="h-11 w-11 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-slate-500 text-sm">Delivered</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['delivered_orders'] ?? 0 }}</p>
    </div>

    {{-- Wallet --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="h-11 w-11 rounded-xl bg-purple-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>
        <p class="text-slate-500 text-sm">Wallet Balance</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">Rs. {{ number_format($stats['wallet_balance'] ?? 0, 2) }}</p>
    </div>

</div>

{{-- ================================================================ --}}
{{-- MAIN GRID: Orders + Sidebar --}}
{{-- ================================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT: Recent Orders (2/3) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden">

        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Recent Orders</h2>
                <p class="text-sm text-slate-500">Your latest purchase activity</p>
            </div>
            <a href="#"
               class="text-sm font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @php
            $recentOrders = $recentOrders ?? collect();
        @endphp

        @if($recentOrders->isEmpty())
            <div class="px-5 py-16 text-center">
                <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="text-slate-700 font-semibold mb-1">No orders yet</h3>
                <p class="text-sm text-slate-500 mb-5">Start shopping to see your orders here</p>
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 text-sm font-semibold transition">
                    Browse Products
                </a>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($recentOrders as $order)
                    <div class="px-5 py-4 hover:bg-slate-50 transition-colors flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-800 truncate">Order #{{ $order->order_number ?? $order->id }}</p>
                            <p class="text-sm text-slate-500">{{ $order->created_at?->format('M d, Y') ?? '' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-slate-800">Rs. {{ number_format($order->total ?? 0, 2) }}</p>
                            <span class="inline-block mt-1 text-xs font-medium px-2 py-0.5 rounded-full
                                @switch($order->status ?? 'pending')
                                    @case('delivered') bg-green-100 text-green-700 @break
                                    @case('shipped')   bg-blue-100 text-blue-700 @break
                                    @case('cancelled') bg-red-100 text-red-700 @break
                                    @default            bg-yellow-100 text-yellow-700
                                @endswitch">
                                {{ ucfirst($order->status ?? 'pending') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    {{-- RIGHT: Quick Actions + Profile (1/3) --}}
    <div class="space-y-6">

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Quick Actions</h2>

            <div class="grid grid-cols-2 gap-3">

                <a href="#"
                   class="flex flex-col items-center gap-2 p-3 rounded-xl bg-slate-50 hover:bg-blue-50 hover:text-blue-600 transition-colors text-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span class="text-xs font-semibold">My Orders</span>
                </a>

                <a href="#"
                   class="flex flex-col items-center gap-2 p-3 rounded-xl bg-slate-50 hover:bg-red-50 hover:text-red-600 transition-colors text-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span class="text-xs font-semibold">Wishlist</span>
                </a>

                <a href="#"
                   class="flex flex-col items-center gap-2 p-3 rounded-xl bg-slate-50 hover:bg-purple-50 hover:text-purple-600 transition-colors text-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-xs font-semibold">Profile</span>
                </a>

                <a href="#"
                   class="flex flex-col items-center gap-2 p-3 rounded-xl bg-slate-50 hover:bg-green-50 hover:text-green-600 transition-colors text-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-xs font-semibold">Addresses</span>
                </a>

            </div>
        </div>

        {{-- Become a Seller CTA --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/20">
            <div class="absolute -bottom-10 -right-10 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="relative">
                <div class="h-10 w-10 rounded-xl bg-white/20 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-1">Become a Seller</h3>
                <p class="text-emerald-50 text-sm mb-4">
                    Start selling on {{ config('app.name', 'TolBazar') }} and grow your business today.
                </p>
                <a href="#"
                   class="inline-flex items-center gap-2 rounded-xl bg-white text-emerald-600 hover:bg-emerald-50 px-4 py-2 text-sm font-semibold transition">
                    Apply Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Account Info --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Account</h2>

            <div class="flex items-center gap-3 mb-4">
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-800 truncate">{{ auth()->user()->name ?? 'Customer' }}</p>
                    <p class="text-sm text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 hover:border-red-200 hover:bg-red-50 hover:text-red-600 text-slate-700 px-4 py-2.5 text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Log Out
                </button>
            </form>
        </div>

    </div>

</div>

@endsection
