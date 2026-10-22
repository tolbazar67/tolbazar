<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Service;
use Illuminate\Validation\ValidationException;

class ProductDetailService
{

    private array $definitions = [
        'HOTEL' => [
            'model' => \App\Models\HotelRoomDetail::class,
            'fields' => [
                'room_type', 'bed_type', 'bed_count', 'max_guests',
                'room_size_sqm', 'bathroom_count', 'amenities',
                'view_type', 'check_in_time', 'check_out_time',
                'breakfast_included', 'cancellation_policy', 'extra_details',
            ],
            'rules' => [
                'room_type' => 'nullable|string|max:100',
                'bed_type' => 'nullable|string|max:100',
                'bed_count' => 'nullable|integer|min:0|max:100',
                'max_guests' => 'nullable|integer|min:1|max:1000',
                'room_size_sqm' => 'nullable|numeric|min:0',
                'bathroom_count' => 'nullable|integer|min:0|max:100',
                'amenities' => 'nullable|array',
                'view_type' => 'nullable|string|max:100',
                'check_in_time' => 'nullable|date_format:H:i',
                'check_out_time' => 'nullable|date_format:H:i',
                'breakfast_included' => 'nullable|boolean',
                'cancellation_policy' => 'nullable|string|max:5000',
                'extra_details' => 'nullable|array',
            ],
        ],

        'RETAIL' => [
            'model' => \App\Models\RetailProductDetail::class,
            'fields' => [
                'brand', 'manufacturer', 'barcode', 'model_number',
                'warranty_months', 'country_of_origin', 'weight_kg',
                'length_cm', 'width_cm', 'height_cm', 'color_options',
                'size_options', 'specifications', 'return_policy', 'extra_details',
            ],
            'rules' => [
                'brand' => 'nullable|string|max:150',
                'manufacturer' => 'nullable|string|max:150',
                'barcode' => 'nullable|string|max:100',
                'model_number' => 'nullable|string|max:100',
                'warranty_months' => 'nullable|integer|min:0|max:1200',
                'country_of_origin' => 'nullable|string|max:100',
                'weight_kg' => 'nullable|numeric|min:0',
                'length_cm' => 'nullable|numeric|min:0',
                'width_cm' => 'nullable|numeric|min:0',
                'height_cm' => 'nullable|numeric|min:0',
                'color_options' => 'nullable|array',
                'size_options' => 'nullable|array',
                'specifications' => 'nullable|array',
                'return_policy' => 'nullable|string|max:5000',
                'extra_details' => 'nullable|array',
            ],
        ],

        'RESTAURANT' => [
            'model' => \App\Models\RestaurantItemDetail::class,
            'fields' => [
                'item_type', 'preparation_minutes', 'serving_size',
                'ingredients', 'allergens', 'dietary_tags', 'spice_level',
                'is_vegetarian', 'is_vegan', 'is_gluten_free', 'calories_kcal',
                'is_customizable', 'customization_options',
                'catering_minimum_quantity', 'extra_details',
            ],
            'rules' => [
                'item_type' => 'nullable|string|max:100',
                'preparation_minutes' => 'nullable|integer|min:0|max:10000',
                'serving_size' => 'nullable|string|max:100',
                'ingredients' => 'nullable|array',
                'allergens' => 'nullable|array',
                'dietary_tags' => 'nullable|array',
                'spice_level' => 'nullable|string|max:50',
                'is_vegetarian' => 'nullable|boolean',
                'is_vegan' => 'nullable|boolean',
                'is_gluten_free' => 'nullable|boolean',
                'calories_kcal' => 'nullable|integer|min:0',
                'is_customizable' => 'nullable|boolean',
                'customization_options' => 'nullable|array',
                'catering_minimum_quantity' => 'nullable|integer|min:0',
                'extra_details' => 'nullable|array',
            ],
        ],

        'EDUCATION' => [
            'model' => \App\Models\EducationCourseDetail::class,
            'fields' => [
                'course_type', 'education_level', 'duration_hours',
                'duration_weeks', 'delivery_mode', 'language', 'instructor_name',
                'prerequisites', 'curriculum', 'learning_outcomes',
                'certificate_available', 'enrollment_limit', 'batch_start_date',
                'batch_end_date', 'class_schedule', 'extra_details',
            ],
            'rules' => [
                'course_type' => 'nullable|string|max:100',
                'education_level' => 'nullable|string|max:100',
                'duration_hours' => 'nullable|numeric|min:0',
                'duration_weeks' => 'nullable|integer|min:0',
                'delivery_mode' => 'nullable|string|max:50',
                'language' => 'nullable|string|max:100',
                'instructor_name' => 'nullable|string|max:150',
                'prerequisites' => 'nullable|string|max:5000',
                'curriculum' => 'nullable|array',
                'learning_outcomes' => 'nullable|array',
                'certificate_available' => 'nullable|boolean',
                'enrollment_limit' => 'nullable|integer|min:0',
                'batch_start_date' => 'nullable|date',
                'batch_end_date' => 'nullable|date|after_or_equal:batch_start_date',
                'class_schedule' => 'nullable|array',
                'extra_details' => 'nullable|array',
            ],
        ],

        'HEALTHCARE' => [
            'model' => \App\Models\HealthcareServiceDetail::class,
            'fields' => [
                'healthcare_type', 'provider_type', 'provider_name',
                'duration_minutes', 'appointment_required', 'consultation_mode',
                'preparation_instructions', 'follow_up_included',
                'report_delivery_time', 'age_min', 'age_max',
                'service_location', 'disclaimer', 'extra_details',
            ],
            'rules' => [
                'healthcare_type' => 'nullable|string|max:100',
                'provider_type' => 'nullable|string|max:100',
                'provider_name' => 'nullable|string|max:150',
                'duration_minutes' => 'nullable|integer|min:0',
                'appointment_required' => 'nullable|boolean',
                'consultation_mode' => 'nullable|string|max:50',
                'preparation_instructions' => 'nullable|string|max:5000',
                'follow_up_included' => 'nullable|boolean',
                'report_delivery_time' => 'nullable|string|max:100',
                'age_min' => 'nullable|integer|min:0|max:120',
                'age_max' => 'nullable|integer|min:0|max:120|gte:age_min',
                'service_location' => 'nullable|string|max:100',
                'disclaimer' => 'nullable|string|max:5000',
                'extra_details' => 'nullable|array',
            ],
        ],

        'TRANSPORT' => [
            'model' => \App\Models\TransportServiceDetail::class,
            'fields' => [
                'transport_type', 'vehicle_type', 'vehicle_class',
                'passenger_capacity', 'luggage_capacity', 'pickup_required',
                'dropoff_supported', 'route_type', 'pickup_areas', 'dropoff_areas',
                'distance_limit_km', 'waiting_time_minutes', 'driver_included',
                'extra_details',
            ],
            'rules' => [
                'transport_type' => 'nullable|string|max:100',
                'vehicle_type' => 'nullable|string|max:100',
                'vehicle_class' => 'nullable|string|max:100',
                'passenger_capacity' => 'nullable|integer|min:1',
                'luggage_capacity' => 'nullable|string|max:100',
                'pickup_required' => 'nullable|boolean',
                'dropoff_supported' => 'nullable|boolean',
                'route_type' => 'nullable|string|max:100',
                'pickup_areas' => 'nullable|array',
                'dropoff_areas' => 'nullable|array',
                'distance_limit_km' => 'nullable|numeric|min:0',
                'waiting_time_minutes' => 'nullable|integer|min:0',
                'driver_included' => 'nullable|boolean',
                'extra_details' => 'nullable|array',
            ],
        ],

        'BEAUTY' => [
            'model' => \App\Models\BeautyServiceDetail::class,
            'fields' => [
                'beauty_service_type', 'duration_minutes', 'staff_required',
                'staff_count', 'service_area', 'home_service_available',
                'appointment_required', 'gender_preference', 'products_used',
                'preparation_instructions', 'aftercare_instructions',
                'package_sessions', 'extra_details',
            ],
            'rules' => [
                'beauty_service_type' => 'nullable|string|max:100',
                'duration_minutes' => 'nullable|integer|min:0',
                'staff_required' => 'nullable|boolean',
                'staff_count' => 'nullable|integer|min:0',
                'service_area' => 'nullable|string|max:100',
                'home_service_available' => 'nullable|boolean',
                'appointment_required' => 'nullable|boolean',
                'gender_preference' => 'nullable|string|max:50',
                'products_used' => 'nullable|array',
                'preparation_instructions' => 'nullable|string|max:5000',
                'aftercare_instructions' => 'nullable|string|max:5000',
                'package_sessions' => 'nullable|integer|min:0',
                'extra_details' => 'nullable|array',
            ],
        ],

        'REPAIR' => [
            'model' => \App\Models\RepairServiceDetail::class,
            'fields' => [
                'repair_type', 'supported_brands', 'supported_models',
                'duration_minutes', 'visit_required', 'home_visit_available',
                'diagnostic_fee', 'diagnostic_fee_adjustable', 'warranty_days',
                'parts_included', 'labour_included', 'service_area',
                'service_terms', 'extra_details',
            ],
            'rules' => [
                'repair_type' => 'nullable|string|max:100',
                'supported_brands' => 'nullable|array',
                'supported_models' => 'nullable|array',
                'duration_minutes' => 'nullable|integer|min:0',
                'visit_required' => 'nullable|boolean',
                'home_visit_available' => 'nullable|boolean',
                'diagnostic_fee' => 'nullable|numeric|min:0',
                'diagnostic_fee_adjustable' => 'nullable|boolean',
                'warranty_days' => 'nullable|integer|min:0',
                'parts_included' => 'nullable|boolean',
                'labour_included' => 'nullable|boolean',
                'service_area' => 'nullable|string|max:150',
                'service_terms' => 'nullable|string|max:5000',
                'extra_details' => 'nullable|array',
            ],
        ],

        'CONSTRUCTION' => [
            'model' => \App\Models\ConstructionServiceDetail::class,
            'fields' => [
                'construction_type', 'measurement_unit', 'site_visit_required',
                'site_visit_fee', 'labour_included', 'materials_included',
                'estimated_duration_days', 'minimum_project_cost', 'service_area',
                'project_stages', 'materials_list', 'quotation_required',
                'warranty_days', 'extra_details',
            ],
            'rules' => [
                'construction_type' => 'nullable|string|max:100',
                'measurement_unit' => 'nullable|string|max:50',
                'site_visit_required' => 'nullable|boolean',
                'site_visit_fee' => 'nullable|numeric|min:0',
                'labour_included' => 'nullable|boolean',
                'materials_included' => 'nullable|boolean',
                'estimated_duration_days' => 'nullable|integer|min:0',
                'minimum_project_cost' => 'nullable|numeric|min:0',
                'service_area' => 'nullable|string|max:150',
                'project_stages' => 'nullable|array',
                'materials_list' => 'nullable|array',
                'quotation_required' => 'nullable|boolean',
                'warranty_days' => 'nullable|integer|min:0',
                'extra_details' => 'nullable|array',
            ],
        ],

        'TRAVEL' => [
            'model' => \App\Models\TravelPackageDetail::class,
            'fields' => [
                'package_type', 'destination', 'origin', 'duration_days',
                'duration_nights', 'group_size_min', 'group_size_max', 'itinerary',
                'inclusions', 'exclusions', 'accommodation_type',
                'transport_included', 'meals_included', 'departure_dates',
                'cancellation_policy', 'difficulty_level', 'extra_details',
            ],
            'rules' => [
                'package_type' => 'nullable|string|max:100',
                'destination' => 'nullable|string|max:255',
                'origin' => 'nullable|string|max:255',
                'duration_days' => 'nullable|integer|min:0',
                'duration_nights' => 'nullable|integer|min:0',
                'group_size_min' => 'nullable|integer|min:1',
                'group_size_max' => 'nullable|integer|min:1|gte:group_size_min',
                'itinerary' => 'nullable|array',
                'inclusions' => 'nullable|array',
                'exclusions' => 'nullable|array',
                'accommodation_type' => 'nullable|string|max:100',
                'transport_included' => 'nullable|boolean',
                'meals_included' => 'nullable|array',
                'departure_dates' => 'nullable|array',
                'cancellation_policy' => 'nullable|string|max:5000',
                'difficulty_level' => 'nullable|string|max:50',
                'extra_details' => 'nullable|array',
            ],
        ],
    ];

    public function rulesFor(Service $service): array
    {
        $code = strtoupper((string) $service->code);

        if (!isset($this->definitions[$code])) {
            throw ValidationException::withMessages([
                'service_id' => 'This service does not have a product-details configuration.',
            ]);
        }

        $rules = [];

        foreach ($this->definitions[$code]['rules'] as $field => $rule) {
            $rules["details.{$field}"] = $rule;
        }

        return $rules;
    }

    public function saveForProduct(Product $product, array $details): void
    {
        $code = strtoupper((string) $product->service?->code);
        $definition = $this->definitions[$code] ?? null;

        if (!$definition) {
            throw ValidationException::withMessages([
                'service_id' => 'No detail configuration exists for this service.',
            ]);
        }

        $allowed = array_flip($definition['fields']);
        $safeDetails = array_intersect_key($details, $allowed);

        $modelClass = $definition['model'];

        $modelClass::updateOrCreate(
            ['product_id' => $product->id],
            $safeDetails
        );
    }
}
