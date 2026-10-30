<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'user_id', 'customer_name', 'customer_email', 'customer_phone',
        'service_code', 'booking_mode', 'seller_id', 'branch_id',
        'items', 'subtotal', 'tax_amount', 'charges_amount',
        'tax_breakdown', 'charges_breakdown', 'total',
        'paid_amount', 'due_amount', 'wallet_transaction_id',
        'status', 'payment_status', 'payment_method', 'meta', 'notes', 'seller_amount', 'commission_amount', 'seller_transaction_id',
        'cash_collected_amount', 'wallet_paid_amount',
'payment_collected_at', 'payment_collected_by', 'seller_gross_amount', 'seller_commission_amount',
    ];

    protected function casts(): array
    {
        return [
            'items'             => 'array',
            'meta'              => 'array',
            'tax_breakdown'     => 'array',
            'charges_breakdown' => 'array',
            'subtotal'          => 'decimal:2',
            'tax_amount'        => 'decimal:2',
            'charges_amount'    => 'decimal:2',
            'total'             => 'decimal:2',
            'paid_amount'       => 'decimal:2',
            'due_amount'        => 'decimal:2',
            'seller_amount'     => 'decimal:2',
'commission_amount' => 'decimal:2',
'cash_collected_amount' => 'decimal:2',
'wallet_paid_amount'    => 'decimal:2',
'payment_collected_at'  => 'datetime',
'seller_gross_amount'     => 'decimal:2',
'seller_commission_amount'=> 'decimal:2',
        ];
    }

    // ============ Relationships ============

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(SellerBranch::class);
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'wallet_transaction_id');
    }

    // ============ Payment status helpers ============

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPartial(): bool
    {
        return $this->payment_status === 'partial';
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'warning',
            'confirmed'  => 'info',
            'processing' => 'primary',
            'completed'  => 'success',
            'cancelled'  => 'danger',
            default      => 'secondary',
        };
    }

    public function getPaymentBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'     => 'success',
            'partial'  => 'warning',
            'unpaid'   => 'danger',
            'refunded' => 'secondary',
            default    => 'secondary',
        };
    }

    public function paymentCollections(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(\App\Models\PaymentCollection::class);
}

public function collector(): \Illuminate\Database\Eloquent\Relations\BelongsTo
{
    return $this->belongsTo(\App\Models\User::class, 'payment_collected_by');
}

    public function isFullyPaid(): bool
    {
        return (float) $this->due_amount <= 0
            && (float) $this->paid_amount >= (float) $this->total;
    }

    public function hasDue(): bool
    {
        return (float) $this->due_amount > 0;
    }

    public function isCashPayment(): bool
    {
        return $this->payment_method === 'cod';
    }

    public function isCashCollectionPending(): bool
    {
        return $this->payment_method === 'cod'
            && (float) $this->due_amount > 0
            && in_array($this->status, ['processing', 'completed']);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cod'    => 'Cash on Delivery',
            'wallet' => 'TolBazar Wallet',
            'esewa'  => 'eSewa',
            'khalti' => 'Khalti',
            'bank'   => 'Bank Transfer',
            default  => ucfirst((string) $this->payment_method),
        };
    }
}
