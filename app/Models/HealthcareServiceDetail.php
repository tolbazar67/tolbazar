<?php

namespace App\Models;

class HealthcareServiceDetail extends ProductDetail
{
    protected $table = 'healthcare_service_details';

    protected function casts(): array
    {
        return [
            'appointment_required' => 'boolean',
            'follow_up_included' => 'boolean',
            'extra_details' => 'array',
        ];
    }
}