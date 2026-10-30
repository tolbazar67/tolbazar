@extends('layouts.guest')
@section('title', 'Bank Accounts — Wallet')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-3xl mx-auto px-4">

        <a href="{{ route('wallet.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Wallet
        </a>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1 mt-0">Bank Accounts</h1>
                <p class="text-sm text-gray-500 mb-0">Manage withdrawal destinations</p>
            </div>
            <button type="button" onclick="document.getElementById('addBankForm').classList.toggle('hidden')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Bank
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        {{-- Add form --}}
        <div id="addBankForm" class="hidden bg-white border border-gray-200 rounded-2xl shadow-sm p-5 mb-4">
            <h2 class="text-sm font-semibold text-gray-900 mb-3 mt-0">Add New Bank Account</h2>
            <form action="{{ route('wallet.bank-accounts.store') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Account Holder Name *</label>
                        <input type="text" name="account_holder_name" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bank Name *</label>
                        <input type="text" name="bank_name" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Account Number *</label>
                        <input type="text" name="account_number" required minlength="6" maxlength="30"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Branch</label>
                        <input type="text" name="branch_name"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">IFSC Code</label>
                        <input type="text" name="ifsc_code"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Account Type *</label>
                        <select name="account_type" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                            <option value="savings">Savings</option>
                            <option value="current">Current</option>
                        </select>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_primary" value="1" class="w-4 h-4 rounded border-gray-300 text-blue-600">
                    Set as primary account
                </label>
                <button type="submit" class="w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700">
                    Save Bank Account
                </button>
            </form>
        </div>

        {{-- Bank list --}}
        <div class="space-y-3">
            @forelse($banks as $bank)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <p class="font-semibold text-gray-900 mb-0">{{ $bank->bank_name }}</p>
                                @if($bank->is_primary)
                                    <span class="text-[10px] font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full uppercase">Primary</span>
                                @endif
                                @if($bank->is_verified)
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full uppercase">✓ Verified</span>
                                @else
                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full uppercase">Pending</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mb-0">{{ $bank->account_holder_name }}</p>
                        </div>
                        <span class="text-[10px] font-semibold uppercase text-gray-500">{{ $bank->account_type }}</span>
                    </div>

                    <div class="flex items-center gap-2 py-2 px-3 bg-gray-50 rounded-lg mb-3">
                        <code class="flex-1 text-sm font-mono text-gray-800">{{ $bank->masked_number }}</code>
                        @if($bank->branch_name)
                            <span class="text-xs text-gray-500">{{ $bank->branch_name }}</span>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        @if(!$bank->is_primary)
                            <form action="{{ route('wallet.bank-accounts.primary', $bank->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button class="w-full px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100">
                                    Set Primary
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('wallet.bank-accounts.delete', $bank->id) }}" method="POST" class="flex-1"
                              onsubmit="return confirm('Delete this bank account?')">
                            @csrf @method('DELETE')
                            <button class="w-full px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 border border-red-200 hover:bg-red-100">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-12 text-center">
                    <p class="text-sm text-gray-500 mb-0">No bank accounts added yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
