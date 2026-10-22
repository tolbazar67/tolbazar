<?php

namespace App\Models;

class TransportServiceDetail extends ProductDetail
{
    protected $table = 'transport_service_details';

    protected function casts(): array
    {
        return [
            'pickup_required' => 'boolean',
            'dropoff_supported' => 'boolean',
            'driver_included' => 'boolean',
            'pickup_areas' => 'array',
            'dropoff_areas' => 'array',
            'extra_details' => 'array',
        ];
    }
}