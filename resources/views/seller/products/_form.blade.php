@php
    $isEdit = isset($product) && $product->exists;
    $selectedServiceId = old('service_id', $product->service_id ?? request('service_id'));

    $detailRecord = null;

    if ($isEdit && $product->service) {
        $detailMap = [
            'HOTEL' => 'hotelRoomDetail',
            'RETAIL' => 'retailProductDetail',
            'RESTAURANT' => 'restaurantItemDetail',
            'EDUCATION' => 'educationCourseDetail',
            'HEALTHCARE' => 'healthcareServiceDetail',
            'TRANSPORT' => 'transportServiceDetail',
            'BEAUTY' => 'beautyServiceDetail',
            'REPAIR' => 'repairServiceDetail',
            'CONSTRUCTION' => 'constructionServiceDetail',
            'TRAVEL' => 'travelPackageDetail',
        ];

        $relation = $detailMap[strtoupper($product->service->code ?? '')] ?? null;

        if ($relation && method_exists($product, $relation)) {
            $detailRecord = $product->{$relation};
        }
    }

    $detailsOld = old('details', []);

    $detailValue = function ($key, $default = null) use ($detailsOld, $detailRecord) {
        return data_get(
            $detailsOld,
            $key,
            data_get($detailRecord, $key, $default)
        );
    };

    /*
    |--------------------------------------------------------------------------
    | Service-specific field definitions
    |--------------------------------------------------------------------------
    */

    $detailDefinitions = [
        'HOTEL' => [
            'title' => 'Hotel & Accommodation Details',
            'fields' => [
                ['room_type', 'Room type', 'text'],
                ['bed_type', 'Bed type', 'text'],
                ['bed_count', 'Bed count', 'number', 'min' => 0],
                ['max_guests', 'Maximum guests', 'number', 'min' => 1],
                ['room_size_sqm', 'Room size (sqm)', 'number', 'step' => '0.01', 'min' => 0],
                ['bathroom_count', 'Bathroom count', 'number', 'min' => 0],
                ['amenities', 'Amenities (one per line)', 'textarea', 'array' => true],
                ['view_type', 'View type', 'text'],
                ['check_in_time', 'Check-in time', 'time'],
                ['check_out_time', 'Check-out time', 'time'],
                ['breakfast_included', 'Breakfast included', 'boolean'],
                ['cancellation_policy', 'Cancellation policy', 'textarea'],
                ['extra_details', 'Extra details (JSON object)', 'json'],
            ],
        ],

        'RETAIL' => [
            'title' => 'Retail Product Details',
            'fields' => [
                ['brand', 'Brand', 'text'],
                ['manufacturer', 'Manufacturer', 'text'],
                ['barcode', 'Barcode', 'text'],
                ['model_number', 'Model number', 'text'],
                ['warranty_months', 'Warranty (months)', 'number', 'min' => 0],
                ['country_of_origin', 'Country of origin', 'text'],
                ['weight_kg', 'Weight (kg)', 'number', 'step' => '0.001', 'min' => 0],
                ['length_cm', 'Length (cm)', 'number', 'step' => '0.01', 'min' => 0],
                ['width_cm', 'Width (cm)', 'number', 'step' => '0.01', 'min' => 0],
                ['height_cm', 'Height (cm)', 'number', 'step' => '0.01', 'min' => 0],
                ['color_options', 'Color options (one per line)', 'textarea', 'array' => true],
                ['size_options', 'Size options (one per line)', 'textarea', 'array' => true],
                ['specifications', 'Specifications (JSON object)', 'json'],
                ['return_policy', 'Return policy', 'textarea'],
                ['extra_details', 'Extra details (JSON object)', 'json'],
            ],
        ],

        'RESTAURANT' => [
            'title' => 'Restaurant & Food Details',
            'fields' => [
                ['item_type', 'Item type', 'text'],
                ['preparation_minutes', 'Preparation time (minutes)', 'number', 'min' => 0],
                ['serving_size', 'Serving size', 'text'],
                ['ingredients', 'Ingredients (one per line)', 'textarea', 'array' => true],
                ['allergens', 'Allergens (one per line)', 'textarea', 'array' => true],
                ['dietary_tags', 'Dietary tags (one per line)', 'textarea', 'array' => true],
                ['spice_level', 'Spice level', 'text'],
                ['is_vegetarian', 'Vegetarian', 'boolean'],
                ['is_vegan', 'Vegan', 'boolean'],
                ['is_gluten_free', 'Gluten free', 'boolean'],
                ['calories_kcal', 'Calories (kcal)', 'number', 'min' => 0],
                ['is_customizable', 'Customizable', 'boolean'],
                ['customization_options', 'Customization options (JSON)', 'json'],
                ['catering_minimum_quantity', 'Catering minimum quantity', 'number', 'min' => 0],
                ['extra_details', 'Extra details (JSON object)', 'json'],
            ],
        ],

        'EDUCATION' => [
            'title' => 'Education & Course Details',
            'fields' => [
                ['course_type', 'Course type', 'text'],
                ['education_level', 'Education level', 'text'],
                ['duration_hours', 'Duration (hours)', 'number', 'step' => '0.01', 'min' => 0],
                ['duration_weeks', 'Duration (weeks)', 'number', 'min' => 0],
                ['delivery_mode', 'Delivery mode', 'text'],
                ['language', 'Language', 'text'],
                ['instructor_name', 'Instructor name', 'text'],
                ['prerequisites', 'Prerequisites', 'textarea'],
                ['curriculum', 'Curriculum (one item per line)', 'textarea', 'array' => true],
                ['learning_outcomes', 'Learning outcomes (one per line)', 'textarea', 'array' => true],
                ['certificate_available', 'Certificate available', 'boolean'],
                ['enrollment_limit', 'Enrollment limit', 'number', 'min' => 0],
                ['batch_start_date', 'Batch start date', 'date'],
                ['batch_end_date', 'Batch end date', 'date'],
                ['class_schedule', 'Class schedule (JSON)', 'json'],
                ['extra_details', 'Extra details (JSON object)', 'json'],
            ],
        ],

        'HEALTHCARE' => [
            'title' => 'Healthcare & Medical Details',
            'fields' => [
                ['healthcare_type', 'Healthcare type', 'text'],
                ['provider_type', 'Provider type', 'text'],
                ['provider_name', 'Provider name', 'text'],
                ['duration_minutes', 'Duration (minutes)', 'number', 'min' => 0],
                ['appointment_required', 'Appointment required', 'boolean'],
                ['consultation_mode', 'Consultation mode', 'text'],
                ['preparation_instructions', 'Preparation instructions', 'textarea'],
                ['follow_up_included', 'Follow-up included', 'boolean'],
                ['report_delivery_time', 'Report delivery time', 'text'],
                ['age_min', 'Minimum age', 'number', 'min' => 0],
                ['age_max', 'Maximum age', 'number', 'min' => 0],
                ['service_location', 'Service location', 'text'],
                ['disclaimer', 'Disclaimer', 'textarea'],
                ['extra_details', 'Extra details (JSON object)', 'json'],
            ],
        ],

        'TRANSPORT' => [
            'title' => 'Transport Details',
            'fields' => [
                ['transport_type', 'Transport type', 'text'],
                ['vehicle_type', 'Vehicle type', 'text'],
                ['vehicle_class', 'Vehicle class', 'text'],
                ['passenger_capacity', 'Passenger capacity', 'number', 'min' => 1],
                ['luggage_capacity', 'Luggage capacity', 'text'],
                ['pickup_required', 'Pickup required', 'boolean'],
                ['dropoff_supported', 'Drop-off supported', 'boolean'],
                ['route_type', 'Route type', 'text'],
                ['pickup_areas', 'Pickup areas (one per line)', 'textarea', 'array' => true],
                ['dropoff_areas', 'Drop-off areas (one per line)', 'textarea', 'array' => true],
                ['distance_limit_km', 'Distance limit (km)', 'number', 'step' => '0.01', 'min' => 0],
                ['waiting_time_minutes', 'Waiting time (minutes)', 'number', 'min' => 0],
                ['driver_included', 'Driver included', 'boolean'],
                ['extra_details', 'Extra details (JSON object)', 'json'],
            ],
        ],

        'BEAUTY' => [
            'title' => 'Beauty & Personal Care Details',
            'fields' => [
                ['beauty_service_type', 'Beauty service type', 'text'],
                ['duration_minutes', 'Duration (minutes)', 'number', 'min' => 0],
                ['staff_required', 'Staff required', 'boolean'],
                ['staff_count', 'Staff count', 'number', 'min' => 0],
                ['service_area', 'Service area', 'text'],
                ['home_service_available', 'Home service available', 'boolean'],
                ['appointment_required', 'Appointment required', 'boolean'],
                ['gender_preference', 'Gender preference', 'text'],
                ['products_used', 'Products used (one per line)', 'textarea', 'array' => true],
                ['preparation_instructions', 'Preparation instructions', 'textarea'],
                ['aftercare_instructions', 'Aftercare instructions', 'textarea'],
                ['package_sessions', 'Package sessions', 'number', 'min' => 0],
                ['extra_details', 'Extra details (JSON object)', 'json'],
            ],
        ],

        'REPAIR' => [
            'title' => 'Repair & Maintenance Details',
            'fields' => [
                ['repair_type', 'Repair type', 'text'],
                ['supported_brands', 'Supported brands (one per line)', 'textarea', 'array' => true],
                ['supported_models', 'Supported models (one per line)', 'textarea', 'array' => true],
                ['duration_minutes', 'Duration (minutes)', 'number', 'min' => 0],
                ['visit_required', 'Visit required', 'boolean'],
                ['home_visit_available', 'Home visit available', 'boolean'],
                ['diagnostic_fee', 'Diagnostic fee', 'number', 'step' => '0.01', 'min' => 0],
                ['diagnostic_fee_adjustable', 'Diagnostic fee adjustable', 'boolean'],
                ['warranty_days', 'Warranty (days)', 'number', 'min' => 0],
                ['parts_included', 'Parts included', 'boolean'],
                ['labour_included', 'Labour included', 'boolean'],
                ['service_area', 'Service area', 'text'],
                ['service_terms', 'Service terms', 'textarea'],
                ['extra_details', 'Extra details (JSON object)', 'json'],
            ],
        ],

        'CONSTRUCTION' => [
            'title' => 'Construction & Home Service Details',
            'fields' => [
                ['construction_type', 'Construction type', 'text'],
                ['measurement_unit', 'Measurement unit', 'text'],
                ['site_visit_required', 'Site visit required', 'boolean'],
                ['site_visit_fee', 'Site visit fee', 'number', 'step' => '0.01', 'min' => 0],
                ['labour_included', 'Labour included', 'boolean'],
                ['materials_included', 'Materials included', 'boolean'],
                ['estimated_duration_days', 'Estimated duration (days)', 'number', 'min' => 0],
                ['minimum_project_cost', 'Minimum project cost', 'number', 'step' => '0.01', 'min' => 0],
                ['service_area', 'Service area', 'text'],
                ['project_stages', 'Project stages (one per line)', 'textarea', 'array' => true],
                ['materials_list', 'Materials list (one per line)', 'textarea', 'array' => true],
                ['quotation_required', 'Quotation required', 'boolean'],
                ['warranty_days', 'Warranty (days)', 'number', 'min' => 0],
                ['extra_details', 'Extra details (JSON object)', 'json'],
            ],
        ],

        'TRAVEL' => [
            'title' => 'Travel & Tourism Details',
            'fields' => [
                ['package_type', 'Package type', 'text'],
                ['destination', 'Destination', 'text'],
                ['origin', 'Origin', 'text'],
                ['duration_days', 'Duration (days)', 'number', 'min' => 0],
                ['duration_nights', 'Duration (nights)', 'number', 'min' => 0],
                ['group_size_min', 'Minimum group size', 'number', 'min' => 1],
                ['group_size_max', 'Maximum group size', 'number', 'min' => 1],
                ['itinerary', 'Itinerary (one item per line)', 'textarea', 'array' => true],
                ['inclusions', 'Inclusions (one per line)', 'textarea', 'array' => true],
                ['exclusions', 'Exclusions (one per line)', 'textarea', 'array' => true],
                ['accommodation_type', 'Accommodation type', 'text'],
                ['transport_included', 'Transport included', 'boolean'],
                ['meals_included', 'Meals included (one per line)', 'textarea', 'array' => true],
                ['departure_dates', 'Departure dates (one date per line)', 'textarea', 'array' => true],
                ['cancellation_policy', 'Cancellation policy', 'textarea'],
                ['difficulty_level', 'Difficulty level', 'text'],
                ['extra_details', 'Extra details (JSON object)', 'json'],
            ],
        ],
    ];

    $serviceCodes = [];

    foreach ($services as $svc) {
        $serviceCodes[$svc->id] = strtoupper((string) $svc->code);
    }
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please correct the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Branch & Service --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Branch & Service</h5>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="branch_id">Branch <span class="text-danger">*</span></label>

                <select
                    id="branch_id"
                    name="branch_id"
                    class="form-control"
                    required
                    {{ $isEdit ? 'disabled' : '' }}
                >
                    @foreach ($branches as $branchOption)
                        <option
                            value="{{ $branchOption->id }}"
                            @selected((string) old('branch_id', $branch->id ?? '') === (string) $branchOption->id)
                        >
                            {{ $branchOption->name ?? ('Branch #' . $branchOption->id) }}
                        </option>
                    @endforeach
                </select>

                @if ($isEdit)
                    <input type="hidden" name="branch_id" value="{{ $product->branch_id }}">
                @endif

                <small class="text-muted">Products are scoped to the selected branch.</small>
            </div>

            <div class="col-md-6 form-group">
                <label for="service_id">Service type <span class="text-danger">*</span></label>

                <select
                    id="service_id"
                    name="service_id"
                    class="form-control"
                    required
                    {{ $isEdit ? 'disabled' : '' }}
                >
                    @foreach ($services as $serviceOption)
                        <option
                            value="{{ $serviceOption->id }}"
                            data-code="{{ strtoupper($serviceOption->code) }}"
                            @selected((string) $selectedServiceId === (string) $serviceOption->id)
                        >
                            {{ $serviceOption->name ?? $serviceOption->label ?? $serviceOption->code }}
                        </option>
                    @endforeach
                </select>

                @if ($isEdit)
                    <input type="hidden" name="service_id" value="{{ $product->service_id }}">
                @endif

                <small class="text-muted">
                    {{ $isEdit ? 'Service type cannot be changed after creation.' : 'Service-specific fields update automatically.' }}
                </small>
            </div>
        </div>
    </div>
</div>

{{-- Basic information --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Basic Information</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <label for="name">Product / Service name <span class="text-danger">*</span></label>
            <input
                id="name"
                name="name"
                class="form-control"
                maxlength="255"
                required
                value="{{ old('name', $product->name ?? '') }}"
                placeholder="e.g. Deluxe Double Room"
            >
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label for="sku">SKU / Reference code</label>
                <input
                    id="sku"
                    name="sku"
                    class="form-control"
                    maxlength="100"
                    value="{{ old('sku', $product->sku ?? '') }}"
                >
            </div>

            <div class="col-md-6 form-group">
                <label for="unit">Unit <span class="text-danger">*</span></label>
                <input
                    id="unit"
                    name="unit"
                    class="form-control"
                    maxlength="50"
                    required
                    value="{{ old('unit', $product->unit ?? 'piece') }}"
                    placeholder="piece, night, person, session"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="short_description">Short description</label>
            <input
                id="short_description"
                name="short_description"
                class="form-control"
                maxlength="500"
                value="{{ old('short_description', $product->short_description ?? '') }}"
            >
        </div>

        <div class="form-group mb-0">
            <label for="description">Full description</label>
            <textarea
                id="description"
                name="description"
                rows="5"
                class="form-control"
                maxlength="10000"
            >{{ old('description', $product->description ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- Pricing and inventory --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Pricing & Inventory</h5>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-4 form-group">
                <label for="price">Price (NPR) <span class="text-danger">*</span></label>
                <input
                    id="price"
                    type="number"
                    step="0.01"
                    min="0"
                    name="price"
                    class="form-control"
                    required
                    value="{{ old('price', $product->price ?? '') }}"
                >
            </div>

            <div class="col-md-4 form-group">
                <label for="compare_price">Compare price (NPR)</label>
                <input
                    id="compare_price"
                    type="number"
                    step="0.01"
                    min="0"
                    name="compare_price"
                    class="form-control"
                    value="{{ old('compare_price', $product->compare_price ?? '') }}"
                >
            </div>

            <div class="col-md-4 form-group">
                <label for="cost_price">Cost price (NPR)</label>
                <input
                    id="cost_price"
                    type="number"
                    step="0.01"
                    min="0"
                    name="cost_price"
                    class="form-control"
                    value="{{ old('cost_price', $product->cost_price ?? '') }}"
                >
            </div>

            <div class="col-md-4 form-group">
                <label for="tax_percent">Tax (%)</label>
                <input
                    id="tax_percent"
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    name="tax_percent"
                    class="form-control"
                    value="{{ old('tax_percent', $product->tax_percent ?? 0) }}"
                >
            </div>

            <div class="col-md-4 form-group">
                <label for="stock_quantity">Stock quantity <span class="text-danger">*</span></label>
                <input
                    id="stock_quantity"
                    type="number"
                    min="0"
                    step="1"
                    name="stock_quantity"
                    class="form-control"
                    required
                    value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}"
                >
            </div>

            <div class="col-md-4 form-group">
                <label for="min_order_qty">Minimum order quantity</label>
                <input
                    id="min_order_qty"
                    type="number"
                    min="1"
                    name="min_order_qty"
                    class="form-control"
                    value="{{ old('min_order_qty', $product->min_order_qty ?? 1) }}"
                >
            </div>

            <div class="col-md-4 form-group">
                <label for="max_order_qty">Maximum order quantity</label>
                <input
                    id="max_order_qty"
                    type="number"
                    min="1"
                    name="max_order_qty"
                    class="form-control"
                    value="{{ old('max_order_qty', $product->max_order_qty ?? '') }}"
                >
            </div>
        </div>

        <input type="hidden" name="track_stock" value="0">

        <div class="custom-control custom-checkbox">
            <input
                class="custom-control-input"
                type="checkbox"
                id="track_stock"
                name="track_stock"
                value="1"
                @checked(old('track_stock', $product->track_stock ?? true))
            >
            <label class="custom-control-label" for="track_stock">
                Track stock quantity
            </label>
        </div>
    </div>
</div>

{{-- Dynamic service details --}}
<div class="card border-0 shadow-sm mb-4" id="service-details-card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-1" id="service-details-title">Service-specific details</h5>
        <small class="text-muted">
            Fill in the details relevant to the selected service.
        </small>
    </div>

    <div class="card-body">
        <div id="service-details-empty" class="alert alert-info d-none">
            No detail fields are configured for this service. Check the service code and ProductDetailService configuration.
        </div>

        @foreach ($detailDefinitions as $code => $definition)
            <div class="service-detail-panel row" data-service-code="{{ $code }}" hidden>
                @foreach ($definition['fields'] as $field)
                    @php
                        $key = $field[0];
                        $label = $field[1];
                        $type = $field[2];
                        $value = $detailValue($key);

                        if (!empty($field['array']) && is_array($value)) {
                            $value = implode("\n", $value);
                        }

                        if ($type === 'json' && is_array($value)) {
                            $value = json_encode(
                                $value,
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                            );
                        }
                    @endphp

                    <div class="{{ in_array($type, ['textarea', 'json']) ? 'col-12' : 'col-md-6' }} form-group">
                        <label for="details_{{ $code }}_{{ $key }}">
                            {{ $label }}
                        </label>

                        @if ($type === 'boolean')
                            <input
                                type="hidden"
                                name="details[{{ $key }}]"
                                value="0"
                            >

                            <div class="custom-control custom-checkbox">
                                <input
                                    class="custom-control-input"
                                    type="checkbox"
                                    id="details_{{ $code }}_{{ $key }}"
                                    name="details[{{ $key }}]"
                                    value="1"
                                    @checked((bool) $value)
                                >

                                <label
                                    class="custom-control-label"
                                    for="details_{{ $code }}_{{ $key }}"
                                >
                                    Yes
                                </label>
                            </div>

                        @elseif ($type === 'textarea')
                            <textarea
                                id="details_{{ $code }}_{{ $key }}"
                                name="details[{{ $key }}]"
                                rows="3"
                                class="form-control"
                                data-array="{{ !empty($field['array']) ? '1' : '0' }}"
                            >{{ is_scalar($value) ? $value : '' }}</textarea>

                            @if (!empty($field['array']))
                                <small class="text-muted">
                                    Enter one value per line. Each line will be saved as an array item.
                                </small>
                            @endif

                        @elseif ($type === 'json')
                            <textarea
                                id="details_{{ $code }}_{{ $key }}"
                                name="details[{{ $key }}]"
                                rows="4"
                                class="form-control json-detail"
                                placeholder='{"key":"value"}'
                            >{{ is_string($value) ? $value : '' }}</textarea>

                            <small class="text-muted">
                                Enter a valid JSON object or array. Leave blank if not needed.
                            </small>

                        @else
                            <input
                                id="details_{{ $code }}_{{ $key }}"
                                name="details[{{ $key }}]"
                                type="{{ $type }}"
                                class="form-control"
                                value="{{ is_scalar($value) ? $value : '' }}"
                                @if (isset($field['min'])) min="{{ $field['min'] }}" @endif
                                @if (isset($field['step'])) step="{{ $field['step'] }}" @endif
                            >
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

{{-- Images --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Images</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <label for="thumbnail">Thumbnail image</label>
            <input
                type="file"
                id="thumbnail"
                name="thumbnail"
                class="form-control-file"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <small class="text-muted">JPG, PNG or WEBP; maximum 2 MB.</small>

            @if ($isEdit && $product->thumbnail)
                <div class="mt-3">
                    <img
                        src="{{ asset('storage/' . $product->thumbnail) }}"
                        alt="Product thumbnail"
                        style="max-width:180px;max-height:140px;object-fit:cover"
                        class="rounded border"
                    >

                    <div class="custom-control custom-checkbox mt-2">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="remove_thumbnail"
                            name="remove_thumbnail"
                            value="1"
                        >

                        <label class="custom-control-label" for="remove_thumbnail">
                            Remove current thumbnail
                        </label>
                    </div>
                </div>
            @endif
        </div>

        <div class="form-group">
            <label for="gallery">Gallery images (up to 5 per submission)</label>
            <input
                type="file"
                id="gallery"
                name="gallery[]"
                class="form-control-file"
                accept=".jpg,.jpeg,.png,.webp"
                multiple
            >

            <small class="text-muted">Each image must be 2 MB or smaller.</small>
        </div>

        @if ($isEdit && $product->images->isNotEmpty())
            <div class="row">
                @foreach ($product->images as $image)
                    <div class="col-6 col-md-3 mb-3">
                        <img
                            src="{{ asset('storage/' . $image->image_path) }}"
                            alt="Gallery image"
                            class="img-fluid rounded border"
                            style="height:120px;width:100%;object-fit:cover"
                        >

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger mt-2"
                            onclick="if(confirm('Delete this image?')) document.getElementById('delete-image-{{ $image->id }}').submit()"
                        >
                            Delete image
                        </button>

                        <form
                            id="delete-image-{{ $image->id }}"
                            method="POST"
                            action="{{ route('seller.products.images.delete', [$product->id, $image->id]) }}"
                            class="d-none"
                        >
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Publishing and SEO --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Publishing & SEO</h5>
    </div>

    <div class="card-body">
        <div class="custom-control custom-checkbox mb-3">
            <input type="hidden" name="is_active" value="0">

            <input
                class="custom-control-input"
                type="checkbox"
                id="is_active"
                name="is_active"
                value="1"
                @checked(old('is_active', $product->is_active ?? true))
            >

            <label class="custom-control-label" for="is_active">
                Product is active
            </label>
        </div>

        <div class="form-group">
            <label for="meta_title">SEO title</label>
            <input
                id="meta_title"
                name="meta_title"
                maxlength="255"
                class="form-control"
                value="{{ old('meta_title', $product->meta_title ?? '') }}"
            >
        </div>

        <div class="form-group">
            <label for="meta_description">SEO description</label>
            <textarea
                id="meta_description"
                name="meta_description"
                maxlength="500"
                rows="2"
                class="form-control"
            >{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
        </div>

        <div class="form-group mb-0">
            <label for="tags">Tags (comma-separated)</label>
            <input
                id="tags"
                name="tags"
                maxlength="500"
                class="form-control"
                value="{{ old('tags', $product->tags ?? '') }}"
                placeholder="premium, popular, local"
            >
        </div>

        <div class="alert alert-light border mt-3 mb-0">
            <i class="fas fa-lock mr-1"></i>
            Featured status is managed separately from the product form.
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    const serviceSelect = document.getElementById('service_id');
    const panels = Array.from(document.querySelectorAll('.service-detail-panel'));
    const title = document.getElementById('service-details-title');
    const empty = document.getElementById('service-details-empty');
    const form = document.querySelector('form.product-form');

    const titles = @json(
        collect($detailDefinitions)->mapWithKeys(
            fn ($definition, $code) => [$code => $definition['title']]
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Show fields for selected service only
    |--------------------------------------------------------------------------
    */

    function renderServiceDetails() {
        if (!serviceSelect) return;

        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        const code = selectedOption
            ? (selectedOption.dataset.code || '').toUpperCase()
            : '';

        let found = false;

        panels.forEach(function (panel) {
            const visible = panel.dataset.serviceCode === code;

            panel.hidden = !visible;

            panel.querySelectorAll('input, textarea, select, button').forEach(function (input) {
                input.disabled = !visible;
            });

            if (visible) found = true;
        });

        if (title) {
            title.textContent = titles[code] || 'Service-specific details';
        }

        if (empty) {
            empty.classList.toggle('d-none', found);
        }
    }

    if (serviceSelect) {
        serviceSelect.addEventListener('change', renderServiceDetails);
        renderServiceDetails();
    }

    /*
    |--------------------------------------------------------------------------
    | Convert nested JSON values into Laravel form inputs
    |--------------------------------------------------------------------------
    |
    | Example:
    | {"color":"red","specs":{"weight":"1kg"}}
    |
    | becomes:
    | details[field][color] = red
    | details[field][specs][weight] = 1kg
    |--------------------------------------------------------------------------
    */

    function appendNestedInputs(formElement, baseName, value) {
        if (Array.isArray(value)) {
            value.forEach(function (item, index) {
                appendNestedInputs(formElement, baseName + '[' + index + ']', item);
            });

            return;
        }

        if (value !== null && typeof value === 'object') {
            Object.keys(value).forEach(function (key) {
                appendNestedInputs(
                    formElement,
                    baseName + '[' + key + ']',
                    value[key]
                );
            });

            return;
        }

        const hidden = document.createElement('input');

        hidden.type = 'hidden';
        hidden.name = baseName;
        hidden.value = value === null ? '' : String(value);
        hidden.dataset.generatedDetailInput = '1';

        formElement.appendChild(hidden);
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare arrays and JSON before submit
    |--------------------------------------------------------------------------
    */

    if (form) {
        form.addEventListener('submit', function (event) {
            const activePanel = panels.find(function (panel) {
                return !panel.hidden;
            });

            if (!activePanel) return;

            // Remove generated fields if submission is retried.
            form.querySelectorAll('[data-generated-detail-input="1"]').forEach(function (input) {
                input.remove();
            });

            /*
            |--------------------------------------------------------------------------
            | 1. Validate JSON fields and submit them as nested arrays
            |--------------------------------------------------------------------------
            */

            const jsonFields = Array.from(
                activePanel.querySelectorAll('textarea.json-detail')
            );

            for (const field of jsonFields) {
                const rawValue = field.value.trim();

                if (rawValue === '') {
                    field.disabled = true;
                    continue;
                }

                let parsedValue;

                try {
                    parsedValue = JSON.parse(rawValue);
                } catch (error) {
                    event.preventDefault();
                    field.disabled = false;
                    field.focus();

                    alert('Invalid JSON. Please correct: ' + field.id);
                    return;
                }

                if (
                    parsedValue === null ||
                    typeof parsedValue !== 'object'
                ) {
                    event.preventDefault();
                    field.disabled = false;
                    field.focus();

                    alert('Please enter a JSON object or array: ' + field.id);
                    return;
                }

                const match = field.name.match(/^details\[([^\]]+)\]$/);

                if (!match) {
                    event.preventDefault();
                    alert('Unexpected JSON field name: ' + field.name);
                    return;
                }

                const key = match[1];

                // The JSON textarea itself must not submit as a string.
                field.disabled = true;

                appendNestedInputs(
                    form,
                    'details[' + key + ']',
                    parsedValue
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Convert multiline textareas into real arrays
            |--------------------------------------------------------------------------
            |
            | Face Wash
            | Moisturizer
            |
            | becomes details[products_used][] for each line.
            |--------------------------------------------------------------------------
            */

            activePanel.querySelectorAll('textarea[data-array="1"]').forEach(function (field) {
                const match = field.name.match(/^details\[([^\]]+)\]$/);

                if (!match) return;

                const key = match[1];

                const values = field.value
                    .split(/\r?\n/)
                    .map(function (item) {
                        return item.trim();
                    })
                    .filter(function (item) {
                        return item !== '';
                    });

                // Avoid submitting the textarea's original string.
                field.disabled = true;

                values.forEach(function (value) {
                    const hidden = document.createElement('input');

                    hidden.type = 'hidden';
                    hidden.name = 'details[' + key + '][]';
                    hidden.value = value;
                    hidden.dataset.generatedDetailInput = '1';

                    form.appendChild(hidden);
                });
            });
        });
    }
})();
</script>
@endpush
