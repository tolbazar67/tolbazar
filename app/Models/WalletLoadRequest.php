<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletLoadRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'user_id', 'wallet_id', 'payment_method_id',
        'system_bank_account_id', 'amount', 'screenshot_path',
        'note', 'sender_reference', 'status',
        'reviewed_by', 'reviewed_at', 'rejection_reason', 'wallet_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function wallet(): BelongsTo   { return $this->belongsTo(Wallet::class); }
    public function paymentMethod(): BelongsTo { return $this->belongsTo(WalletPaymentMethod::class, 'payment_method_id'); }
    public function systemBank(): BelongsTo { return $this->belongsTo(SystemBankAccount::class, 'system_bank_account_id'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function scopePending($q) { return $q->where('status', 'pending'); }
}
