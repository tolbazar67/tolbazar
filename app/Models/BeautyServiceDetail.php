<?php

namespace App\Models;

class BeautyServiceDetail extends ProductDetail
{
    protected $table = 'beauty_service_details';

    protected function casts(): array
    {
        return [
            'staff_required' => 'boolean',
            'home_service_available' => 'boolean',
            'appointment_required' => 'boolean',
            'products_used' => 'array',
            'extra_details' => 'array',
        ];
    }
}