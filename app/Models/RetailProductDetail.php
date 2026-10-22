<?php

namespace App\Models;

class RetailProductDetail extends ProductDetail
{
    protected $table = 'retail_product_details';

    protected function casts(): array
    {
        return [
            'color_options' => 'array',
            'size_options' => 'array',
            'specifications' => 'array',
            'extra_details' => 'array',
        ];
    }
}