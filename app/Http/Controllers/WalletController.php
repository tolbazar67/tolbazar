<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletBankAccount;
use App\Models\WalletLoadRequest;
use App\Models\WalletWithdrawRequest;
use App\Models\WalletTransaction;
use App\Models\WalletPaymentMethod;
use App\Models\SystemBankAccount;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    private function wallet()
    {
        return $this->walletService->getOrCreate(Auth::user());
    }

    // ============================================================
    // DASHBOARD
    // ============================================================
    public function dashboard()
    {
        $wallet = $this->wallet();
        $recentTxns = $wallet->transactions()->latest()->limit(10)->get();

        $pendingLoads    = $wallet->loadRequests()->where('status', 'pending')->count();
        $pendingWithdraws = $wallet->withdrawRequests()->where('status', 'pending')->count();

        $thisMonthIn  = $wallet->transactions()->where('type', 'credit')->whereMonth('created_at', now()->month)->sum('amount');
        $thisMonthOut = $wallet->transactions()->where('type', 'debit')->whereMonth('created_at', now()->month)->sum('amount');

        return view('wallet.dashboard', compact(
            'wallet', 'recentTxns',
            'pendingLoads', 'pendingWithdraws',
            'thisMonthIn', 'thisMonthOut'
        ));
    }

    // ============================================================
    // STATEMENT / TRANSACTIONS
    // ============================================================
    public function transactions(Request $request)
    {
        $wallet = $this->wallet();
        $query = $wallet->transactions();

        if ($request->filled('type') && in_array($request->type, ['credit', 'debit'])) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status') && in_array($request->status, ['completed', 'pending', 'failed', 'reversed'])) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        return view('wallet.transactions', compact('wallet', 'transactions'));
    }

    // ============================================================
    // ADD MONEY (LOAD)
    // ============================================================
    public function addMoney()
    {
        $wallet = $this->wallet();
        $methods = WalletPaymentMethod::active()->orderBy('sort_order')->get();
        $systemBanks = SystemBankAccount::where('is_active', 1)->orderBy('sort_order')->get();
        $myRequests = $wallet->loadRequests()->latest()->limit(10)->get();

        return view('wallet.add-money', compact('wallet', 'methods', 'systemBanks', 'myRequests'));
    }

    public function submitLoad(Request $request)
    {
        $wallet = $this->wallet();

        $request->validate([
            'amount' => 'required|numeric|min:100|max:' . ($wallet->single_txn_limit ?? 25000),
            'payment_method_id' => 'required|exists:wallet_payment_methods,id',
            'system_bank_account_id' => 'nullable|exists:system_bank_accounts,id',
            'screenshot' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sender_reference' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $path = $request->file('screenshot')->store('wallet/loads/' . now()->format('Y/m'), 'public');

            $load = WalletLoadRequest::create([
                'reference'               => $this->walletService->generateRef('LD'),
                'user_id'                 => Auth::id(),
                'wallet_id'               => $wallet->id,
                'payment_method_id'       => $request->payment_method_id,
                'system_bank_account_id'  => $request->system_bank_account_id,
                'amount'                  => $request->amount,
                'screenshot_path'         => $path,
                'sender_reference'        => $request->sender_reference,
                'note'                    => $request->note,
                'status'                  => 'pending',
            ]);

            $this->walletService->audit(Auth::user(), 'load_request_created', 'info', "Load request {$load->reference} for Rs. {$load->amount}");

            return redirect()->route('wallet.add-money')->with('success', "Load request submitted (Ref: {$load->reference}). You'll be notified once approved.");
        } catch (\Throwable $e) {
            \Log::error('Load request failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['screenshot' => 'Failed to submit. Please try again.']);
        }
    }

    // ============================================================
    // WITHDRAW
    // ============================================================
    public function withdraw()
    {
        $wallet = $this->wallet();
        $banks = WalletBankAccount::where('user_id', Auth::id())->where('status', 'active')->get();
        $myRequests = $wallet->withdrawRequests()->latest()->limit(10)->get();

        return view('wallet.withdraw', compact('wallet', 'banks', 'myRequests'));
    }

    public function submitWithdraw(Request $request)
    {
        $wallet = $this->wallet();

        if ($wallet->status !== 'active') {
            return back()->withErrors(['amount' => 'Wallet is not active.']);
        }
        if (!$wallet->is_kyc_verified) {
            return back()->withErrors(['amount' => 'KYC verification required for withdrawals.']);
        }

        $request->validate([
            'amount'          => 'required|numeric|min:500',
            'bank_account_id' => 'required|exists:wallet_bank_accounts,id',
            'pin'             => 'required|digits:6',
        ]);

        // PIN check
        if (!$wallet->verifyPin($request->pin)) {
            $this->walletService->audit(Auth::user(), 'pin_failed', 'critical', 'Wrong PIN on withdrawal');
            return back()->withErrors(['pin' => 'Incorrect PIN.']);
        }

        $bank = WalletBankAccount::where('id', $request->bank_account_id)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        $amount = (float) $request->amount;
        $fee = 0; // Free withdrawals, adjust as needed
        $net = $amount - $fee;

        // Limits
        $check = $this->walletService->checkLimits($wallet, $amount, 'debit');
        if (!$check['ok']) {
            return back()->withErrors(['amount' => $check['reason']]);
        }

        // Balance check
        if ($amount > (float) $wallet->balance) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        try {
            DB::transaction(function () use ($wallet, $bank, $amount, $fee, $net) {
                // Lock the amount (debit to pending)
                $locked = Wallet::lockForUpdate()->find($wallet->id);
                $before = (float) $locked->balance;

                if ($amount > $before) {
                    throw new \RuntimeException('Insufficient balance.');
                }

                $locked->decrement('balance', $amount);
                $locked->increment('pending_balance', $amount);

                $txn = WalletTransaction::create([
                    'transaction_id' => $this->walletService->generateTxnId(),
                    'wallet_id'      => $locked->id,
                    'user_id'        => $locked->user_id,
                    'type'           => 'debit',
                    'category'       => 'withdraw',
                    'amount'         => $amount,
                    'fee'            => $fee,
                    'balance_before' => $before,
                    'balance_after'  => $before - $amount,
                    'description'    => "Withdrawal request to {$bank->bank_name}",
                    'status'         => 'pending',
                    'ip_address'     => request()->ip(),
                    'user_agent'     => substr((string) request()->userAgent(), 0, 500),
                ]);

                $wr = WalletWithdrawRequest::create([
                    'reference'            => $this->walletService->generateRef('WD'),
                    'user_id'              => $locked->user_id,
                    'wallet_id'            => $locked->id,
                    'bank_account_id'      => $bank->id,
                    'amount'               => $amount,
                    'fee'                  => $fee,
                    'net_amount'           => $net,
                    'status'               => 'pending',
                    'wallet_transaction_id'=> $txn->id,
                ]);

                $this->walletService->audit(
                    Auth::user(),
                    'withdraw_request_created',
                    'info',
                    "Withdraw {$wr->reference} for Rs. {$amount}"
                );
            });

            return redirect()->route('wallet.withdraw')->with('success', 'Withdrawal request submitted. It will be processed within 24 hours.');
        } catch (\Throwable $e) {
            \Log::error('Withdraw failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['amount' => $e->getMessage()]);
        }
    }

    // ============================================================
    // TRANSFER MONEY
    // ============================================================
    public function transfer()
    {
        $wallet = $this->wallet();
        return view('wallet.transfer', compact('wallet'));
    }

    public function submitTransfer(Request $request)
    {
        $wallet = $this->wallet();

        $request->validate([
            'recipient' => 'required|string', // username/email/mobile
            'amount'    => 'required|numeric|min:10',
            'pin'       => 'required|digits:6',
            'note'      => 'nullable|string|max:200',
        ]);

        if (!$wallet->verifyPin($request->pin)) {
            $this->walletService->audit(Auth::user(), 'pin_failed', 'critical', 'Wrong PIN on transfer');
            return back()->withErrors(['pin' => 'Incorrect PIN.']);
        }

        // Find recipient
        $recipient = User::where('username', $request->recipient)
            ->orWhere('email', $request->recipient)
            ->orWhere('mobile_number', $request->recipient)
            ->first();

        if (!$recipient) {
            return back()->withErrors(['recipient' => 'Recipient not found.']);
        }
        if ($recipient->id === Auth::id()) {
            return back()->withErrors(['recipient' => 'You cannot transfer to yourself.']);
        }

        $recipientWallet = $this->walletService->getOrCreate($recipient);
        $amount = (float) $request->amount;

        if ($amount > (float) $wallet->balance) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        $check = $this->walletService->checkLimits($wallet, $amount, 'debit');
        if (!$check['ok']) {
            return back()->withErrors(['amount' => $check['reason']]);
        }

        try {
            $this->walletService->transfer($wallet, $recipientWallet, $amount, $request->note);

            $this->walletService->audit(
                Auth::user(),
                'transfer_completed',
                'info',
                "Transferred Rs. {$amount} to @{$recipient->username}"
            );

            return redirect()->route('wallet.dashboard')
                ->with('success', "Successfully transferred Rs. " . number_format($amount, 2) . " to @{$recipient->username}");
        } catch (\Throwable $e) {
            \Log::error('Transfer failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['amount' => $e->getMessage()]);
        }
    }

    // ============================================================
    // BANK ACCOUNTS
    // ============================================================
    public function bankAccounts()
    {
        $banks = WalletBankAccount::where('user_id', Auth::id())->orderByDesc('is_primary')->get();
        return view('wallet.bank-accounts', compact('banks'));
    }

    public function storeBankAccount(Request $request)
    {
        $request->validate([
            'account_holder_name' => 'required|string|max:150',
            'account_number'      => 'required|string|min:6|max:30',
            'bank_name'           => 'required|string|max:150',
            'branch_name'         => 'nullable|string|max:150',
            'ifsc_code'           => 'nullable|string|max:50',
            'account_type'        => 'required|in:savings,current',
            'is_primary'          => 'nullable|boolean',
        ]);

        // Max 5 accounts
        if (WalletBankAccount::where('user_id', Auth::id())->count() >= 5) {
            return back()->withErrors(['account_number' => 'Maximum 5 bank accounts allowed.']);
        }

        $isPrimary = $request->boolean('is_primary') && !WalletBankAccount::where('user_id', Auth::id())->where('is_primary', 1)->exists();

        $bank = WalletBankAccount::create([
            'user_id'             => Auth::id(),
            'account_holder_name' => $request->account_holder_name,
            'account_number'      => $request->account_number,
            'bank_name'           => $request->bank_name,
            'branch_name'         => $request->branch_name,
            'ifsc_code'           => $request->ifsc_code,
            'account_type'        => $request->account_type,
            'is_primary'          => $isPrimary,
            'is_verified'         => false,
            'status'              => 'active',
        ]);

        $this->walletService->audit(Auth::user(), 'bank_added', 'info', "Bank {$bank->bank_name} added");

        return back()->with('success', 'Bank account added. Verification pending.');
    }

    public function deleteBankAccount($id)
    {
        $bank = WalletBankAccount::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Check no pending withdrawals
        $pending = WalletWithdrawRequest::where('bank_account_id', $bank->id)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();
        if ($pending) {
            return back()->withErrors(['bank' => 'Cannot delete — pending withdrawal in progress.']);
        }

        $bank->delete();
        return back()->with('success', 'Bank account removed.');
    }

    public function setPrimaryBank($id)
    {
        $bank = WalletBankAccount::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        WalletBankAccount::where('user_id', Auth::id())->update(['is_primary' => false]);
        $bank->update(['is_primary' => true]);

        return back()->with('success', 'Primary bank updated.');
    }

    // ============================================================
    // SECURITY — PIN, LIMITS
    // ============================================================
    public function security()
    {
        $wallet = $this->wallet();
        $audits = $wallet->auditLogs()->latest()->limit(20)->get();

        return view('wallet.security', compact('wallet', 'audits'));
    }

    public function setPin(Request $request)
    {
        $wallet = $this->wallet();

        $request->validate([
            'password' => 'required|string',
            'pin'      => 'required|digits:6|confirmed',
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Password incorrect.']);
        }

        // Prevent simple PINs
        $weakPins = ['000000','111111','222222','333333','444444','555555','666666','777777','888888','999999','123456','654321'];
        if (in_array($request->pin, $weakPins)) {
            return back()->withErrors(['pin' => 'Too simple. Choose a stronger PIN.']);
        }

        $wallet->update([
            'pin_hash'     => Hash::make($request->pin),
            'pin_set_at'   => now(),
            'pin_failed_attempts' => 0,
            'pin_locked_until'    => null,
        ]);

        $this->walletService->audit(Auth::user(), 'pin_set', 'warning', 'Wallet PIN created/updated');

        return back()->with('success', 'Wallet PIN set successfully.');
    }

    public function changeLimits(Request $request)
    {
        $wallet = $this->wallet();

        $request->validate([
            'password'         => 'required|string',
            'single_txn_limit' => 'required|numeric|min:1000|max:100000',
            'daily_limit'      => 'required|numeric|min:1000|max:500000',
            'monthly_limit'    => 'required|numeric|min:5000|max:5000000',
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Password incorrect.']);
        }

        $wallet->update([
            'single_txn_limit' => $request->single_txn_limit,
            'daily_limit'      => $request->daily_limit,
            'monthly_limit'    => $request->monthly_limit,
        ]);

        return back()->with('success', 'Limits updated.');
    }

    // ============================================================
    // REWARDS
    // ============================================================
    public function rewards()
    {
        $wallet = $this->wallet();
        $history = $wallet->rewardHistory()->latest()->paginate(20);

        return view('wallet.rewards', compact('wallet', 'history'));
    }

    // ============================================================
    // LOAD & WITHDRAW LISTS
    // ============================================================
    public function loadRequests()
    {
        $wallet = $this->wallet();
        $requests = $wallet->loadRequests()->latest()->paginate(15);
        return view('wallet.load-requests', compact('wallet', 'requests'));
    }

    public function withdrawRequests()
    {
        $wallet = $this->wallet();
        $requests = $wallet->withdrawRequests()->latest()->paginate(15);
        return view('wallet.withdraw-requests', compact('wallet', 'requests'));
    }
}
