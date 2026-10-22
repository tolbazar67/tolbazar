<?php

namespace App\Models;

class TravelPackageDetail extends ProductDetail
{
    protected $table = 'travel_package_details';

    protected function casts(): array
    {
        return [
            'itinerary' => 'array',
            'inclusions' => 'array',
            'exclusions' => 'array',
            'transport_included' => 'boolean',
            'meals_included' => 'array',
            'departure_dates' => 'array',
            'extra_details' => 'array',
        ];
    }
}