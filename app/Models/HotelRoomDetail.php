<?php

namespace App\Models;

class HotelRoomDetail extends ProductDetail
{
    protected $table = 'hotel_room_details';

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'extra_details' => 'array',
            'breakfast_included' => 'boolean',
        ];
    }
}