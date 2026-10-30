<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletPaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'type', 'logo', 'account_number',
        'account_name', 'is_active', 'sort_order', 'config',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'config'    => 'array',
        ];
    }

    public function scopeActive($q) { return $q->where('is_active', 1); }
}
