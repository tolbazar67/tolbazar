<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletRewardHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'wallet_id', 'points', 'type', 'reason',
        'reference_type', 'reference_id', 'meta',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
    public function wallet(): BelongsTo { return $this->belongsTo(Wallet::class); }
}
