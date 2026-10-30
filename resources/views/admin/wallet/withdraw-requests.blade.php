@extends('layouts.guest')
@section('title', 'Withdraw Requests — Admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-6xl mx-auto px-4">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1 mt-0">Withdraw Requests</h1>
                <p class="text-sm text-gray-500 mb-0">Approve or reject user withdrawal requests</p>
            </div>
            <div class="flex gap-2 text-sm">
                @foreach(['pending','completed','rejected'] as $s)
                    <a href="{{ route('admin.wallet.withdraw-requests', ['status' => $s]) }}"
                       class="px-3 py-1.5 rounded-lg border {{ request('status','pending') === $s ? 'bg-blue-600 text-white border-blue-600' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                        {{ ucfirst($s) }}
                    </a>
                @endforeach
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <div class="space-y-3">
            @forelse($requests as $req)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">User</p>
                            <p class="text-sm font-semibold text-gray-900 mb-0">{{ $req->user->name }}</p>
                            <p class="text-xs text-gray-500 mb-0">{{ $req->user->email }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 mb-0 font-mono">{{ $req->reference }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 mt-0">Amount</p>
                            <p class="text-lg font-bold text-gray-900 mb-0">Rs. {{ number_format($req->amount, 2) }}</p>
                            <p class="text-xs text-gray-500 mb-0">Fee: Rs. {{ number_format($req->fee, 2) }} · Net: Rs. {{ number_format($req->net_amount, 2) }}</p>
                            <p class="text-xs text-gray-700 mb-0 mt-1">
                                <strong>To:</strong> {{ $req->bankAccount?->bank_name }} — {{ $req->bankAccount?->masked_number }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-2">
                            <p class="text-xs text-gray-500 mb-1 mt-0">{{ $req->created_at->diffForHumans() }}</p>
                            @if(in_array($req->status, ['pending', 'processing']))
                                <button type="button" onclick="document.getElementById('approveForm{{ $req->id }}').classList.toggle('hidden')"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700">
                                    ✓ Approve with Proof
                                </button>
                                <form action="{{ route('admin.wallet.withdraw.reject', $req->id) }}" method="POST"
                                      onsubmit="const r = prompt('Rejection reason:'); if(!r) return false; this.reason.value=r; return true;">
                                    @csrf
                                    <input type="hidden" name="reason">
                                    <button class="w-full px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 border border-red-200 hover:bg-red-100">✗ Reject</button>
                                </form>
                            @else
                                <span class="text-center text-[10px] font-semibold uppercase py-1 rounded
                                    {{ $req->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $req->status }}
                                </span>
                                @if($req->rejection_reason)
                                    <p class="text-[10px] text-gray-500 mt-1 mb-0">Reason: {{ $req->rejection_reason }}</p>
                                @endif
                                @if($req->proof_image)
                                    <a href="{{ Storage::url($req->proof_image) }}" target="_blank" class="text-[10px] text-blue-600 underline">View proof</a>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Approve form (hidden) --}}
                    <form id="approveForm{{ $req->id }}" action="{{ route('admin.wallet.withdraw.approve', $req->id) }}"
                          method="POST" enctype="multipart/form-data" class="hidden mt-4 pt-4 border-t border-gray-100">
                        @csrf
                        <p class="text-xs font-semibold text-gray-700 mb-2 mt-0">Upload bank transfer proof:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <input type="file" name="proof_image" accept="image/*" required
                                   class="block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                            <input type="text" name="bank_reference" placeholder="Bank txn reference (optional)"
                                   class="px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        </div>
                        <button type="submit" class="mt-2 w-full px-4 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700">
                            Confirm Approve & Complete
                        </button>
                    </form>
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
