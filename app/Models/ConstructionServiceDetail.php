<?php

namespace App\Models;

class ConstructionServiceDetail extends ProductDetail
{
    protected $table = 'construction_service_details';

    protected function casts(): array
    {
        return [
            'site_visit_required' => 'boolean',
            'site_visit_fee' => 'decimal:2',
            'labour_included' => 'boolean',
            'materials_included' => 'boolean',
            'minimum_project_cost' => 'decimal:2',
            'quotation_required' => 'boolean',
            'project_stages' => 'array',
            'materials_list' => 'array',
            'extra_details' => 'array',
        ];
    }
}