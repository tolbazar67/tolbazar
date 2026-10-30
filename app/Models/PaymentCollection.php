<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'booking_id', 'collected_by', 'method',
        'amount', 'reference_note', 'notes', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'meta'   => 'array',
        ];
    }

    // ============ Relationships ============

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    // ============ Helpers ============

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'cash'          => 'Cash',
            'wallet'        => 'Wallet',
            'online'        => 'Online',
            'bank_transfer' => 'Bank Transfer',
            default         => ucfirst((string) $this->method),
        };
    }
}
