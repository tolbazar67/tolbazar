@extends('layouts.guest')
@section('title', 'All Services — TolBazar')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Hero --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2 mt-0">Explore All Services</h1>
            <p class="text-gray-500 mb-0">Discover products & services from 10+ categories on TolBazar</p>
        </div>

        {{-- Services grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @php
                $colors = [
                    'HOTEL'        => ['bg'=>'bg-blue-50',    'text'=>'text-blue-600',    'path'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    'RETAIL'       => ['bg'=>'bg-emerald-50', 'text'=>'text-emerald-600', 'path'=>'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
                    'RESTAURANT'   => ['bg'=>'bg-rose-50',    'text'=>'text-rose-600',    'path'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                    'EDUCATION'    => ['bg'=>'bg-violet-50',  'text'=>'text-violet-600',  'path'=>'M12 14l9-5-9-5-9 5 9 5zM12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
                    'HEALTHCARE'   => ['bg'=>'bg-red-50',     'text'=>'text-red-600',     'path'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                    'TRANSPORT'    => ['bg'=>'bg-amber-50',   'text'=>'text-amber-600',   'path'=>'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1'],
                    'BEAUTY'       => ['bg'=>'bg-pink-50',    'text'=>'text-pink-600',    'path'=>'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01'],
                    'REPAIR'       => ['bg'=>'bg-slate-50',   'text'=>'text-slate-600',   'path'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                    'CONSTRUCTION' => ['bg'=>'bg-orange-50',  'text'=>'text-orange-600',  'path'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    'TRAVEL'       => ['bg'=>'bg-teal-50',    'text'=>'text-teal-600',    'path'=>'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064'],
                ];
            @endphp

            @forelse($services as $s)
                @php $c = $colors[$s->code] ?? ['bg'=>'bg-gray-50','text'=>'text-gray-600','path'=>'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4']; @endphp
                <a href="{{ route('services.products', $s->slug) }}"
                   class="group bg-white border border-gray-200 rounded-2xl p-5 hover:border-blue-300 hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['path'] }}"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1 mt-0 group-hover:text-blue-700">{{ $s->name }}</h3>
                    <p class="text-xs text-gray-500 mb-0">{{ $s->product_count }} {{ \Illuminate\Support\Str::plural('product', $s->product_count) }}</p>
                </a>
            @empty
                <div class="col-span-full bg-white border border-gray-200 rounded-2xl p-12 text-center">
                    <p class="text-sm text-gray-500 mb-0">No services available yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
