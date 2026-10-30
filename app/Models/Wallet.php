<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'balance', 'pending_balance', 'credit_balance', 'gift_balance',
        'reward_points', 'total_earned', 'total_spent', 'currency', 'status',
        'is_kyc_verified', 'kyc_verified_at', 'pin_hash', 'pin_set_at',
        'pin_failed_attempts', 'pin_locked_until',
        'daily_limit', 'monthly_limit', 'single_txn_limit',
    ];

    protected $hidden = ['pin_hash'];

    protected function casts(): array
    {
        return [
            'balance'         => 'decimal:2',
            'pending_balance' => 'decimal:2',
            'credit_balance'  => 'decimal:2',
            'gift_balance'    => 'decimal:2',
            'total_earned'    => 'decimal:2',
            'total_spent'     => 'decimal:2',
            'daily_limit'     => 'decimal:2',
            'monthly_limit'   => 'decimal:2',
            'single_txn_limit'=> 'decimal:2',
            'is_kyc_verified' => 'boolean',
            'kyc_verified_at' => 'datetime',
            'pin_set_at'      => 'datetime',
            'pin_locked_until'=> 'datetime',
        ];
    }

    // ==== Relationships ====

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(WalletBankAccount::class, 'user_id', 'user_id');
    }

    public function loadRequests(): HasMany
    {
        return $this->hasMany(WalletLoadRequest::class);
    }

    public function withdrawRequests(): HasMany
    {
        return $this->hasMany(WalletWithdrawRequest::class);
    }

    public function rewardHistory(): HasMany
    {
        return $this->hasMany(WalletRewardHistory::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(WalletAuditLog::class);
    }

    // ==== Helpers ====

    public function hasPin(): bool
    {
        return !is_null($this->pin_hash);
    }

    public function verifyPin(string $pin): bool
    {
        if (!$this->hasPin()) return false;
        if ($this->pin_locked_until && now()->lt($this->pin_locked_until)) return false;

        if (Hash::check($pin, $this->pin_hash)) {
            $this->update(['pin_failed_attempts' => 0, 'pin_locked_until' => null]);
            return true;
        }

        $attempts = $this->pin_failed_attempts + 1;
        $update = ['pin_failed_attempts' => $attempts];

        if ($attempts >= 5) {
            $update['pin_locked_until'] = now()->addMinutes(30);
        }

        $this->update($update);
        return false;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function totalUsable(): float
    {
        return (float) $this->balance + (float) $this->credit_balance + (float) $this->gift_balance;
    }
}
