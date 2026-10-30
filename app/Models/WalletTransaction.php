<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id', 'wallet_id', 'user_id', 'type', 'category',
        'amount', 'fee', 'balance_before', 'balance_after',
        'description', 'reference_type', 'reference_id',
        'status', 'meta', 'ip_address', 'user_agent', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'         => 'decimal:2',
            'fee'            => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after'  => 'decimal:2',
            'meta'           => 'array',
            'completed_at'   => 'datetime',
        ];
    }

    public function wallet(): BelongsTo { return $this->belongsTo(Wallet::class); }
    public function user(): BelongsTo   { return $this->belongsTo(User::class); }

    public function scopeCompleted($q) { return $q->where('status', 'completed'); }
    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeFailed($q)    { return $q->where('status', 'failed'); }
    public function scopeCredits($q)   { return $q->where('type', 'credit'); }
    public function scopeDebits($q)    { return $q->where('type', 'debit'); }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'success',
            'pending'   => 'warning',
            'failed'    => 'danger',
            'reversed'  => 'secondary',
            default     => 'secondary',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->category));
    }
}
