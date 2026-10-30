<?php

namespace App\Support;

class BookingMode
{
    public const CART        = 'cart';
    public const BOOKING     = 'booking';
    public const ENROLLMENT  = 'enrollment';
    public const APPOINTMENT = 'appointment';
    public const ROUTE       = 'route';
    public const SERVICE     = 'service';
    public const QUOTE       = 'quote';
    public const PACKAGE     = 'package';

    /**
     * Map service code → booking mode.
     */
    public static function for(?string $serviceCode): string
    {
        return match (strtoupper((string) $serviceCode)) {
            'HOTEL'        => self::BOOKING,
            'RETAIL'       => self::CART,
            'RESTAURANT'   => self::CART,
            'EDUCATION'    => self::ENROLLMENT,
            'HEALTHCARE'   => self::APPOINTMENT,
            'TRANSPORT'    => self::ROUTE,
            'BEAUTY'       => self::APPOINTMENT,
            'REPAIR'       => self::SERVICE,
            'CONSTRUCTION' => self::QUOTE,
            'TRAVEL'       => self::PACKAGE,
            default        => self::CART,
        };
    }

    public static function isCart(string $mode): bool
    {
        return in_array($mode, [self::CART], true);
    }

    public static function label(string $mode): string
    {
        return match ($mode) {
            self::CART        => 'Add to Cart',
            self::BOOKING     => 'Book Now',
            self::ENROLLMENT  => 'Enroll Now',
            self::APPOINTMENT => 'Book Appointment',
            self::ROUTE       => 'Book Ride',
            self::SERVICE     => 'Request Service',
            self::QUOTE       => 'Get Quote',
            self::PACKAGE     => 'Book Package',
            default           => 'Order Now',
        };
    }
}
