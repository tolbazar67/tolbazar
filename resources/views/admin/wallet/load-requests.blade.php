@extends('layouts.guest')
@section('title', 'Wallet Load Requests — Admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-6xl mx-auto px-4">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1 mt-0">Load Requests</h1>
                <p class="text-sm text-gray-500 mb-0">Approve or reject wallet top-up requests</p>
            </div>
            <div class="flex gap-2 text-sm">
                @foreach(['pending','approved','rejected'] as $s)
                    <a href="{{ route('admin.wallet.load-requests', ['status' => $s]) }}"
                       class="px-3 py-1.5 rounded-lg border {{ request('status','pending') === $s ? 'bg-blue-600 text-white border-blue-600' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                        {{ ucfirst($s) }}
                    </a>
                @endforeach
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="space-y-4">
            @forelse($requests as $req)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        {{-- User info --}}
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">User</p>
                            <p class="text-sm font-semibold text-gray-900 mb-0">{{ $req->user->name }}</p>
                            <p class="text-xs text-gray-500 mb-0">{{ $req->user->email }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 mb-0 font-mono">{{ $req->reference }}</p>
                        </div>

                        {{-- Amount + method --}}
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">Amount</p>
                            <p class="text-lg font-bold text-gray-900 mb-0">Rs. {{ number_format($req->amount, 2) }}</p>
                            <p class="text-xs text-gray-500 mb-0">{{ $req->paymentMethod?->name ?? '—' }}</p>
                            @if($req->sender_reference)
                                <p class="text-[10px] text-gray-400 font-mono mt-1 mb-0">Ref: {{ $req->sender_reference }}</p>
                            @endif
                        </div>

                        {{-- Screenshot --}}
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">Screenshot</p>
                            <a href="{{ Storage::url($req->screenshot_path) }}" target="_blank">
                                <img src="{{ Storage::url($req->screenshot_path) }}"
                                     alt="Proof" class="w-full h-20 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition cursor-pointer">
                            </a>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-col gap-2 justify-center">
                            <p class="text-xs text-gray-500 mb-1 mt-0">{{ $req->created_at->diffForHumans() }}</p>
                            @if($req->status === 'pending')
                                <form action="{{ route('admin.wallet.load.approve', $req->id) }}" method="POST" onsubmit="return confirm('Approve and credit Rs. {{ $req->amount }}?');">
                                    @csrf
                                    <button class="w-full px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700">✓ Approve</button>
                                </form>
                                <form action="{{ route('admin.wallet.load.reject', $req->id) }}" method="POST"
                                      onsubmit="const r = prompt('Rejection reason:'); if(!r) return false; this.reason.value=r; return true;">
                                    @csrf
                                    <input type="hidden" name="reason">
                                    <button class="w-full px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 border border-red-200 hover:bg-red-100">✗ Reject</button>
                                </form>
                            @else
                                <span class="text-[10px] font-semibold uppercase text-center py-1 rounded
                                    {{ $req->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $req->status }}
                                </span>
                                @if($req->rejection_reason)
                                    <p class="text-[10px] text-gray-500 mt-1 mb-0">Reason: {{ $req->rejection_reason }}</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if($req->note)
                        <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-600">
                            <strong>Note:</strong> {{ $req->note }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-12 text-center">
                    <p class="text-sm text-gray-500 mb-0">No {{ request('status','pending') }} requests.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
