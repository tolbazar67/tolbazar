@extends('layouts.guest')
@section('title', 'Withdraw Money — Wallet')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-3xl mx-auto px-4">

        <a href="{{ route('wallet.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Wallet
        </a>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-orange-400 to-red-600"></div>
            <div class="p-6 md:p-8">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1 mt-0">Withdraw Money</h1>
                <p class="text-sm text-gray-500 mb-5">Available balance: <strong>Rs. {{ number_format($wallet->balance, 2) }}</strong></p>

                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                        <ul class="list-disc pl-4 mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                @if(!$wallet->is_kyc_verified)
                    <div class="mb-5 rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                        <strong>KYC required:</strong> Contact admin to verify your identity before withdrawing.
                    </div>
                @endif

                @if($banks->isEmpty())
                    <div class="mb-5 rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                        No bank account added yet.
                        <a href="{{ route('wallet.bank-accounts') }}" class="font-semibold underline">Add one first →</a>
                    </div>
                @endif

                <form action="{{ route('wallet.withdraw.submit') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Amount (NPR) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount') }}" min="500" step="0.01" required
                               placeholder="1000"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none">
                        <p class="text-xs text-gray-500 mt-1 mb-0">Minimum Rs. 500 · Limits: Rs. {{ number_format($wallet->single_txn_limit, 0) }} single / Rs. {{ number_format($wallet->daily_limit, 0) }} daily</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Select Bank Account <span class="text-red-500">*</span></label>
                        <select name="bank_account_id" required
                                class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none">
                            <option value="">Choose bank...</option>
                            @foreach($banks as $b)
                                <option value="{{ $b->id }}" @selected(old('bank_account_id') == $b->id)>
                                    {{ $b->bank_name }} — {{ $b->masked_number }}
                                    @if($b->is_primary) (Primary) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Wallet PIN <span class="text-red-500">*</span></label>
                        <input type="password" name="pin" required pattern="\d{6}" maxlength="6" inputmode="numeric" placeholder="••••••"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm text-center font-mono tracking-widest focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none">
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m8-8l-8 8 8 8"/></svg>
                        Request Withdrawal
                    </button>
                </form>

                {{-- Recent --}}
                @if($myRequests->count())
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3 mt-0">Recent Withdraw Requests</h3>
                        <div class="space-y-2">
                            @foreach($myRequests as $r)
                                <div class="flex items-center justify-between gap-2 text-sm p-2.5 rounded-lg bg-gray-50">
                                    <div class="min-w-0">
                                        <span class="font-mono text-xs text-gray-600">{{ $r->reference }}</span>
                                        <span class="text-gray-500 ml-2">Rs. {{ number_format($r->amount, 2) }}</span>
                                    </div>
                                    <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded
                                        @if($r->status === 'completed' || $r->status === 'approved') bg-emerald-100 text-emerald-800
                                        @elseif($r->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-amber-100 text-amber-800 @endif">
                                        {{ $r->status }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
