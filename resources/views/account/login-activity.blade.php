@extends('layouts.guest')
@section('title', 'Login Activity — TolBazar')
@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('account.settings') }}" class="text-gray-500 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 mb-0 mt-0">Login Activity</h1>
                <p class="text-xs text-gray-500 mb-0">Track where and when your account was accessed.</p>
            </div>
        </div>

        {{-- Filter tabs --}}
        <div class="flex gap-2 mb-4">
            @foreach(['all'=>'All','success'=>'Successful','failed'=>'Failed'] as $key=>$label)
                <a href="{{ route('account.login-activity', ['filter'=>$key]) }}"
                   class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-medium border transition
                          {{ $filter === $key ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            @forelse($activities as $activity)
                <div class="p-4 md:p-5 {{ !$loop->last ? 'border-b border-gray-100' : '' }} flex items-start gap-3 md:gap-4">
                    <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center
                        @if($activity->status === 'success') bg-emerald-50
                        @elseif($activity->status === 'failed') bg-red-50
                        @else bg-gray-100 @endif">
                        @if($activity->status === 'success')
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($activity->status === 'failed')
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <p class="text-sm font-semibold text-gray-900 mb-0 truncate">
                                {{ $activity->status_label }}
                                @if($activity->failure_reason)
                                    <span class="text-xs font-normal text-red-600">— {{ $activity->failure_reason }}</span>
                                @endif
                            </p>
                            <span class="text-xs text-gray-500 shrink-0">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mb-1">
                            <strong>{{ $activity->browser }}</strong> on {{ $activity->platform }}
                            <span class="text-gray-400 mx-1">·</span>
                            <span class="capitalize">{{ $activity->device_type }}</span>
                        </p>
                        <p class="text-xs text-gray-500 mb-0">
                            IP: {{ $activity->ip_address ?? 'Unknown' }}
                            <span class="text-gray-400 mx-1">·</span>
                            {{ $activity->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg class="w-14 h-14 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium text-gray-700 mb-1">No activity yet</p>
                    <p class="text-xs text-gray-500 mb-0">Login activities will appear here.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $activities->links() }}</div>
    </div>
</div>
@endsection
