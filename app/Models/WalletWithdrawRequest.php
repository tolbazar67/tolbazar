<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletWithdrawRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'user_id', 'wallet_id', 'bank_account_id',
        'amount', 'fee', 'net_amount', 'status',
        'reviewed_by', 'reviewed_at', 'rejection_reason',
        'proof_image', 'bank_reference', 'wallet_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'fee'         => 'decimal:2',
            'net_amount'  => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function wallet(): BelongsTo { return $this->belongsTo(Wallet::class); }
    public function bankAccount(): BelongsTo { return $this->belongsTo(WalletBankAccount::class, 'bank_account_id'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function scopePending($q) { return $q->where('status', 'pending'); }
}
