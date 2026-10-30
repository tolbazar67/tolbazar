<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Global charge rules apply garne ki nai?
    |--------------------------------------------------------------------------
    |
    | false = Seller ko aafno rule matra apply (STRICT MODE — recommended)
    | true  = Global rules (seller_id NULL) pani fallback ma apply
    |
    */
    'apply_global_rules' => env('CHARGES_APPLY_GLOBAL', false),

    /*
    |--------------------------------------------------------------------------
    | Global tax rules (ServiceTaxRule) apply garne ki nai?
    |--------------------------------------------------------------------------
    */
    'apply_global_tax' => env('TAX_APPLY_GLOBAL', false),
];
