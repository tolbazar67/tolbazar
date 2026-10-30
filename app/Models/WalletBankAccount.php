<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'account_holder_name', 'account_number',
        'bank_name', 'branch_name', 'ifsc_code', 'account_type',
        'is_primary', 'is_verified', 'verified_at', 'status',
    ];

    // 🔒 Encrypt account number at rest
    protected function casts(): array
    {
        return [
            'account_number' => 'encrypted',
            'is_primary'     => 'boolean',
            'is_verified'    => 'boolean',
            'verified_at'    => 'datetime',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function getMaskedNumberAttribute(): string
    {
        $num = (string) $this->account_number;
        $len = strlen($num);
        if ($len <= 4) return str_repeat('*', $len);
        return str_repeat('*', max(0, $len - 4)) . substr($num, -4);
    }
}
