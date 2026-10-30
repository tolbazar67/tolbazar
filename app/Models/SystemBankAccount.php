<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name', 'account_holder_name', 'account_number',
        'branch_name', 'ifsc_code', 'account_type',
        'qr_code_image', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'account_number' => 'encrypted',
            'is_active'      => 'boolean',
        ];
    }
}
