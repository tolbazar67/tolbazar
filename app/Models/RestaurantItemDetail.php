<?php

namespace App\Models;

class RestaurantItemDetail extends ProductDetail
{
    protected $table = 'restaurant_item_details';

    protected function casts(): array
    {
        return [
            'ingredients' => 'array',
            'allergens' => 'array',
            'dietary_tags' => 'array',
            'customization_options' => 'array',
            'extra_details' => 'array',
            'is_vegetarian' => 'boolean',
            'is_vegan' => 'boolean',
            'is_gluten_free' => 'boolean',
            'is_customizable' => 'boolean',
        ];
    }
}