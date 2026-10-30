@extends('layouts.guest')
@section('title', 'Add Money — Wallet')

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
            <div class="h-1.5 bg-gradient-to-r from-emerald-400 to-teal-600"></div>
            <div class="p-6 md:p-8">

                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1 mt-0">Add Money to Wallet</h1>
                <p class="text-sm text-gray-500 mb-5">Send money to any account below, then upload the payment screenshot.</p>

                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                        <ul class="list-disc pl-4 mb-0">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Step 1: Choose account --}}
                <div class="mb-6">
                    <p class="text-sm font-semibold text-gray-900 mb-3 mt-0">Step 1 — Send money to one of these accounts:</p>
                    <div class="space-y-2">
                        @foreach($systemBanks as $bank)
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div>
                                        <p class="font-semibold text-gray-900 mb-0">{{ $bank->bank_name }}</p>
                                        <p class="text-xs text-gray-500 mb-0">{{ $bank->account_holder_name }}</p>
                                    </div>
                                    <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">{{ strtoupper($bank->account_type) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <code class="flex-1 px-3 py-2 bg-gray-50 rounded-lg text-sm font-mono text-gray-800 break-all">{{ $bank->account_number }}</code>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $bank->account_number }}'); this.textContent='✓ Copied'; setTimeout(()=>this.textContent='Copy', 1500)"
                                            class="px-3 py-2 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg">Copy</button>
                                </div>
                                @if($bank->branch_name || $bank->ifsc_code)
                                    <p class="text-xs text-gray-500 mt-2 mb-0">
                                        @if($bank->branch_name) Branch: {{ $bank->branch_name }} @endif
                                        @if($bank->ifsc_code) · IFSC: {{ $bank->ifsc_code }} @endif
                                    </p>
                                @endif
                                @if($bank->qr_code_image)
                                    <img src="{{ Storage::url($bank->qr_code_image) }}" alt="QR" class="mt-3 w-32 h-32 rounded-lg border border-gray-200">
                                @endif
                            </div>
                        @endforeach

                        @foreach($methods as $method)
                            @if($method->account_number)
                                <div class="rounded-xl border border-gray-200 p-4">
                                    <p class="font-semibold text-gray-900 mb-1 mt-0">{{ $method->name }}</p>
                                    <code class="text-sm font-mono text-gray-800">{{ $method->account_number }}</code>
                                    <p class="text-xs text-gray-500 mt-1 mb-0">{{ $method->account_name }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Step 2: Upload proof --}}
                <form action="{{ route('wallet.add-money.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <p class="text-sm font-semibold text-gray-900 mb-3 mt-0">Step 2 — Fill the details</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Amount (NPR) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" value="{{ old('amount') }}" min="100" step="0.01" required
                                   placeholder="1000" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Payment Method <span class="text-red-500">*</span></label>
                            <select name="payment_method_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                                <option value="">Select...</option>
                                @foreach($methods as $m)
                                    <option value="{{ $m->id }}" @selected(old('payment_method_id') == $m->id)>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Sender Reference (Txn ID from bank/eSewa)</label>
                        <input type="text" name="sender_reference" value="{{ old('sender_reference') }}" placeholder="Optional"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Payment Screenshot <span class="text-red-500">*</span></label>
                        <input type="file" name="screenshot" accept="image/*" required
                               class="block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        <p class="text-xs text-gray-500 mt-1 mb-0">PNG/JPG/WEBP · max 5MB</p>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Note (optional)</label>
                        <textarea name="note" rows="2" placeholder="Any details for admin..."
                                  class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none resize-none">{{ old('note') }}</textarea>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Submit Load Request
                    </button>
                </form>

                {{-- Recent requests --}}
                @if($myRequests->count())
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3 mt-0">Your Recent Load Requests</h3>
                        <div class="space-y-2">
                            @foreach($myRequests as $r)
                                <div class="flex items-center justify-between gap-2 text-sm p-2 rounded-lg bg-gray-50">
                                    <div class="min-w-0">
                                        <span class="font-mono text-xs text-gray-600">{{ $r->reference }}</span>
                                        <span class="text-gray-500 ml-2">Rs. {{ number_format($r->amount, 2) }}</span>
                                    </div>
                                    <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded
                                        @if($r->status === 'approved') bg-emerald-100 text-emerald-800
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
