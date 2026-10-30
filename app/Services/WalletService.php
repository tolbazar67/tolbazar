<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletAuditLog;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    /**
     * Get or create wallet for a user.
     */
    public function getOrCreate(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance'      => 0,
                'currency'     => 'NPR',
                'status'       => 'active',
            ]
        );
    }

    /**
     * Credit money to wallet.
     */
    public function credit(
        Wallet $wallet,
        float $amount,
        string $category,
        ?string $description = null,
        $reference = null,
        array $meta = []
    ): WalletTransaction {
        return $this->recordTransaction($wallet, 'credit', $category, $amount, $description, $reference, $meta);
    }

    /**
     * Debit money from wallet — blocks negative balance.
     */
    public function debit(
        Wallet $wallet,
        float $amount,
        string $category,
        ?string $description = null,
        $reference = null,
        array $meta = []
    ): WalletTransaction {
        return $this->recordTransaction($wallet, 'debit', $category, $amount, $description, $reference, $meta);
    }

    /**
     * Debit money — ALLOWS NEGATIVE BALANCE.
     * Used for cash collectors (riders/delivery boys) who physically
     * hold customer cash and owe it to the system until settlement.
     */
    public function debitAllowNegative(
        Wallet $wallet,
        float $amount,
        string $category,
        ?string $description = null,
        $reference = null,
        array $meta = []
    ): WalletTransaction {
        return $this->recordTransaction(
            $wallet, 'debit', $category, $amount, $description, $reference, $meta, true
        );
    }

    /**
     * Core transaction recorder — atomic + locked.
     * ✅ FIXED: $allowNegative now passed into the DB::transaction closure.
     */
    private function recordTransaction(
        Wallet $wallet,
        string $type,
        string $category,
        float $amount,
        ?string $description,
        $reference,
        array $meta,
        bool $allowNegative = false
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive.');
        }

        return DB::transaction(function () use ($wallet, $type, $category, $amount, $description, $reference, $meta, $allowNegative) {
            /** @var Wallet $locked */
            $locked = Wallet::lockForUpdate()->find($wallet->id);

            if (!$locked->isActive()) {
                throw new \RuntimeException('Wallet is not active.');
            }

            $before = (float) $locked->balance;
            $after  = $type === 'credit' ? $before + $amount : $before - $amount;

            if ($type === 'debit' && $after < 0 && ! $allowNegative) {
                throw new \RuntimeException('Insufficient wallet balance.');
            }

            $locked->update([
                'balance'      => $after,
                'total_earned' => $type === 'credit' ? $locked->total_earned + $amount : $locked->total_earned,
                'total_spent'  => $type === 'debit'  ? $locked->total_spent + $amount  : $locked->total_spent,
            ]);

            return WalletTransaction::create([
                'transaction_id' => $this->generateTxnId(),
                'wallet_id'      => $locked->id,
                'user_id'        => $locked->user_id,
                'type'           => $type,
                'category'       => $category,
                'amount'         => $amount,
                'balance_before' => $before,
                'balance_after'  => $after,
                'description'    => $description,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id'   => $reference?->id,
                'status'         => 'completed',
                'meta'           => $meta,
                'ip_address'     => request()?->ip(),
                'user_agent'     => substr((string) request()?->userAgent(), 0, 500),
                'completed_at'   => now(),
            ]);
        });
    }

    /**
     * Transfer between two wallets.
     */
    public function transfer(Wallet $from, Wallet $to, float $amount, ?string $note = null): array
    {
        if ($from->id === $to->id) {
            throw new \RuntimeException('Cannot transfer to same wallet.');
        }

        return DB::transaction(function () use ($from, $to, $amount, $note) {
            $debit  = $this->debit($from, $amount, 'transfer_out', $note ?: "Transfer to wallet #{$to->id}");
            $credit = $this->credit($to, $amount, 'transfer_in', $note ?: "Transfer from wallet #{$from->id}");

            return ['debit' => $debit, 'credit' => $credit];
        });
    }

    /**
     * Validate wallet transaction against limits.
     */
    public function checkLimits(Wallet $wallet, float $amount, string $type = 'debit'): array
    {
        if ($amount > (float) $wallet->single_txn_limit) {
            return ['ok' => false, 'reason' => 'Exceeds single transaction limit of ' . number_format($wallet->single_txn_limit, 2)];
        }

        $dailyTotal = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('type', $type)
            ->where('status', 'completed')
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');

        if (($dailyTotal + $amount) > (float) $wallet->daily_limit) {
            return ['ok' => false, 'reason' => 'Exceeds daily ' . $type . ' limit of ' . number_format($wallet->daily_limit, 2)];
        }

        $monthlyTotal = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('type', $type)
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        if (($monthlyTotal + $amount) > (float) $wallet->monthly_limit) {
            return ['ok' => false, 'reason' => 'Exceeds monthly ' . $type . ' limit'];
        }

        return ['ok' => true, 'reason' => null];
    }

    /**
     * Log a security event.
     */
    public function audit(
        ?User $user,
        string $eventType,
        string $severity = 'info',
        string $description = '',
        array $meta = []
    ): void {
        try {
            WalletAuditLog::create([
                'user_id'     => $user?->id,
                'wallet_id'   => $user?->wallet?->id,
                'event_type'  => $eventType,
                'severity'    => $severity,
                'description' => $description,
                'ip_address'  => request()?->ip(),
                'user_agent'  => substr((string) request()?->userAgent(), 0, 500),
                'meta'        => $meta,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Wallet audit failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Award reward points.
     */
    public function addRewardPoints(Wallet $wallet, int $points, string $type = 'earned', ?string $reason = null, $reference = null): void
    {
        DB::transaction(function () use ($wallet, $points, $type, $reason, $reference) {
            $locked = Wallet::lockForUpdate()->find($wallet->id);
            $locked->increment('reward_points', $points);

            \App\Models\WalletRewardHistory::create([
                'user_id'        => $locked->user_id,
                'wallet_id'      => $locked->id,
                'points'         => $points,
                'type'           => $type,
                'reason'         => $reason,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id'   => $reference?->id,
            ]);
        });
    }

    /**
     * Generate unique transaction ID.
     */
    public function generateTxnId(): string
    {
        return 'TXN' . strtoupper(Str::random(4)) . now()->format('ymdHis') . rand(100, 999);
    }

    /**
     * Generate unique reference for load/withdraw requests.
     */
    public function generateRef(string $prefix = 'WL'): string
    {
        return $prefix . strtoupper(Str::random(4)) . now()->format('ymdHis') . rand(100, 999);
    }
}
