<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'user_id', 'product_id', 'quantity', 'unit_price', 'meta'];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'meta'       => 'array',
        ];
    }

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }

    public function getLineTotalAttribute(): float
    {
        return (float) $this->unit_price * $this->quantity;
    }
}
