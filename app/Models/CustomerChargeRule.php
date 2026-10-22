<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerChargeRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'category_id',
        'name',
        'charge_code',
        'charge_type',
        'rate',
        'currency',
        'calculation_basis',
        'min_charge',
        'max_charge',
        'is_taxable',
        'is_refundable',
        'starts_at',
        'ends_at',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'rate'          => 'decimal:4',
            'min_charge'    => 'decimal:2',
            'max_charge'    => 'decimal:2',
            'is_taxable'    => 'boolean',
            'is_refundable' => 'boolean',
            'is_active'     => 'boolean',
            'starts_at'     => 'datetime',
            'ends_at'       => 'datetime',
            'settings'      => 'array',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            ServiceCategory::class,
            'category_id'
        );
    }

    public function scopeActive($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            });
    }
}