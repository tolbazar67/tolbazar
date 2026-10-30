<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletLoadRequest;
use App\Models\WalletWithdrawRequest;
use App\Models\WalletTransaction;
use App\Models\WalletAuditLog;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletAdminController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    // ============================================================
    // DASHBOARD
    // ============================================================
    public function dashboard()
    {
        $stats = [
            'total_wallets'      => Wallet::count(),
            'total_balance'      => Wallet::sum('balance'),
            'total_pending_balance' => Wallet::sum('pending_balance'),
            'pending_loads'      => WalletLoadRequest::where('status', 'pending')->count(),
            'pending_withdraws'  => WalletWithdrawRequest::where('status', 'pending')->count(),
            'today_credits'      => WalletTransaction::completed()->credits()->whereDate('created_at', today())->sum('amount'),
            'today_debits'       => WalletTransaction::completed()->debits()->whereDate('created_at', today())->sum('amount'),
            'critical_audits'    => WalletAuditLog::critical()->whereDate('created_at', '>=', now()->subDays(7))->count(),
        ];

        $recentLoads = WalletLoadRequest::pending()->with('user')->latest()->limit(5)->get();
        $recentWithdraws = WalletWithdrawRequest::pending()->with('user')->latest()->limit(5)->get();
        $recentAudits = WalletAuditLog::with('user')->latest()->limit(10)->get();

        return view('admin.wallet.dashboard', compact('stats', 'recentLoads', 'recentWithdraws', 'recentAudits'));
    }

    // ============================================================
    // LOAD REQUESTS (screenshot approval)
    // ============================================================
    public function loadRequests(Request $request)
    {
        $query = WalletLoadRequest::with(['user', 'paymentMethod', 'systemBank']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        $requests = $query->latest()->paginate(20);

        return view('admin.wallet.load-requests', compact('requests'));
    }

    public function approveLoad($id)
    {
        $load = WalletLoadRequest::where('status', 'pending')->findOrFail($id);

        try {
            DB::transaction(function () use ($load) {
                $wallet = Wallet::lockForUpdate()->find($load->wallet_id);

                $txn = $this->walletService->credit(
                    $wallet,
                    (float) $load->amount,
                    'load',
                    "Load approved — Ref: {$load->reference}",
                    $load,
                    ['screenshot' => $load->screenshot_path, 'approved_by' => Auth::id()]
                );

                $load->update([
                    'status'                => 'approved',
                    'reviewed_by'           => Auth::id(),
                    'reviewed_at'           => now(),
                    'wallet_transaction_id' => $txn->id,
                ]);

                $this->walletService->audit(
                    $load->user,
                    'load_approved',
                    'info',
                    "Admin approved load {$load->reference} for Rs. {$load->amount}"
                );
            });

            return back()->with('success', "Load {$load->reference} approved and credited.");
        } catch (\Throwable $e) {
            \Log::error('Approve load failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['load' => 'Failed to approve: ' . $e->getMessage()]);
        }
    }

    public function rejectLoad(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $load = WalletLoadRequest::where('status', 'pending')->findOrFail($id);

        $load->update([
            'status'           => 'rejected',
            'reviewed_by'      => Auth::id(),
            'reviewed_at'      => now(),
            'rejection_reason' => $request->reason,
        ]);

        $this->walletService->audit(
            $load->user,
            'load_rejected',
            'warning',
            "Load {$load->reference} rejected: {$request->reason}"
        );

        return back()->with('success', "Load {$load->reference} rejected.");
    }

    // ============================================================
    // WITHDRAW REQUESTS
    // ============================================================
    public function withdrawRequests(Request $request)
    {
        $query = WalletWithdrawRequest::with(['user', 'bankAccount']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        $requests = $query->latest()->paginate(20);

        return view('admin.wallet.withdraw-requests', compact('requests'));
    }

    public function approveWithdraw(Request $request, $id)
    {
        $request->validate([
            'proof_image'    => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'bank_reference' => 'nullable|string|max:100',
        ]);

        $wd = WalletWithdrawRequest::whereIn('status', ['pending', 'processing'])->findOrFail($id);

        try {
            $path = $request->file('proof_image')->store('wallet/withdraws/' . now()->format('Y/m'), 'public');

            DB::transaction(function () use ($wd, $path, $request) {
                $wallet = Wallet::lockForUpdate()->find($wd->wallet_id);

                // Release pending
                $wallet->decrement('pending_balance', (float) $wd->amount);

                // Update txn
                if ($wd->wallet_transaction_id) {
                    WalletTransaction::where('id', $wd->wallet_transaction_id)->update([
                        'status'       => 'completed',
                        'completed_at' => now(),
                        'description'  => "Withdraw approved — Ref: {$wd->reference}",
                    ]);
                }

                $wd->update([
                    'status'         => 'completed',
                    'reviewed_by'    => Auth::id(),
                    'reviewed_at'    => now(),
                    'proof_image'    => $path,
                    'bank_reference' => $request->bank_reference,
                ]);

                $this->walletService->audit(
                    $wd->user,
                    'withdraw_approved',
                    'info',
                    "Withdraw {$wd->reference} approved by admin"
                );
            });

            return back()->with('success', "Withdraw {$wd->reference} approved.");
        } catch (\Throwable $e) {
            \Log::error('Approve withdraw failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['withdraw' => 'Failed: ' . $e->getMessage()]);
        }
    }

    public function rejectWithdraw(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $wd = WalletWithdrawRequest::whereIn('status', ['pending', 'processing'])->findOrFail($id);

        try {
            DB::transaction(function () use ($wd, $request) {
                $wallet = Wallet::lockForUpdate()->find($wd->wallet_id);

                // Refund pending → balance
                $wallet->decrement('pending_balance', (float) $wd->amount);
                $wallet->increment('balance', (float) $wd->amount);

                if ($wd->wallet_transaction_id) {
                    WalletTransaction::where('id', $wd->wallet_transaction_id)->update([
                        'status'      => 'failed',
                        'description' => "Withdraw rejected — Ref: {$wd->reference}",
                    ]);
                }

                $wd->update([
                    'status'           => 'rejected',
                    'reviewed_by'      => Auth::id(),
                    'reviewed_at'      => now(),
                    'rejection_reason' => $request->reason,
                ]);

                $this->walletService->audit(
                    $wd->user,
                    'withdraw_rejected',
                    'warning',
                    "Withdraw {$wd->reference} rejected: {$request->reason}"
                );
            });

            return back()->with('success', "Withdraw {$wd->reference} rejected and refunded.");
        } catch (\Throwable $e) {
            return back()->withErrors(['withdraw' => 'Failed: ' . $e->getMessage()]);
        }
    }

    // ============================================================
    // ADJUSTMENT (manual credit/debit)
    // ============================================================
    public function adjustWallet(Request $request, User $user)
    {
        $request->validate([
            'type'        => 'required|in:credit,debit',
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:500',
        ]);

        $wallet = $this->walletService->getOrCreate($user);

        try {
            if ($request->type === 'credit') {
                $this->walletService->credit($wallet, (float) $request->amount, 'adjustment', $request->description);
            } else {
                $this->walletService->debit($wallet, (float) $request->amount, 'adjustment', $request->description);
            }

            $this->walletService->audit(
                $user,
                'admin_adjustment',
                'warning',
                "Admin {$request->type} of Rs. {$request->amount}: {$request->description}"
            );

            return back()->with('success', "Wallet adjusted ({$request->type} Rs. {$request->amount}).");
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }
    }

    // ============================================================
    // AUDIT LOGS
    // ============================================================
    public function audits(Request $request)
    {
        $query = WalletAuditLog::with('user')->latest();

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        $logs = $query->paginate(30);
        return view('admin.wallet.audits', compact('logs'));
    }

    // ============================================================
    // ALL WALLETS
    // ============================================================
    public function wallets(Request $request)
    {
        $query = Wallet::with('user');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('user', fn($qq) => $qq->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('username', 'like', "%{$q}%"));
        }

        $wallets = $query->latest()->paginate(20);
        return view('admin.wallet.wallets', compact('wallets'));
    }

    public function freezeWallet($id)
    {
        $wallet = Wallet::findOrFail($id);
        $wallet->update(['status' => 'frozen']);
        $this->walletService->audit($wallet->user, 'wallet_frozen', 'warning', "Wallet frozen by admin");
        return back()->with('success', 'Wallet frozen.');
    }

    public function unfreezeWallet($id)
    {
        $wallet = Wallet::findOrFail($id);
        $wallet->update(['status' => 'active']);
        $this->walletService->audit($wallet->user, 'wallet_unfrozen', 'info', "Wallet unfrozen by admin");
        return back()->with('success', 'Wallet activated.');
    }

    public function verifyKyc($id)
    {
        $wallet = Wallet::findOrFail($id);
        $wallet->update(['is_kyc_verified' => true, 'kyc_verified_at' => now()]);
        $this->walletService->audit($wallet->user, 'kyc_verified', 'info', "KYC verified by admin");
        return back()->with('success', 'KYC verified.');
    }
}
