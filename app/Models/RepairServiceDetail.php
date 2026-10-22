<?php

namespace App\Models;

class RepairServiceDetail extends ProductDetail
{
    protected $table = 'repair_service_details';

    protected function casts(): array
    {
        return [
            'supported_brands' => 'array',
            'supported_models' => 'array',
            'visit_required' => 'boolean',
            'home_visit_available' => 'boolean',
            'diagnostic_fee_adjustable' => 'boolean',
            'parts_included' => 'boolean',
            'labour_included' => 'boolean',
            'diagnostic_fee' => 'decimal:2',
            'extra_details' => 'array',
        ];
    }
}