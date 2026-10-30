{{-- ============================================================
     DYNAMIC BOOKING FORM — Changes based on service mode
     ============================================================ --}}
@php
    use App\Support\BookingMode;
    $btnLabel = BookingMode::label($mode);
    $isCart = ($mode === BookingMode::CART);
    $inStock = !$product->track_stock || $product->stock_quantity > 0;
@endphp

@if(!$inStock)
    {{-- Out of stock --}}
    <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-800 flex items-start gap-2">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <p class="font-semibold mb-0.5 mt-0">Currently out of stock</p>
            <p class="mb-0">This product is not available right now.</p>
        </div>
    </div>

@elseif($isCart)
    {{-- ============================================================
         CART MODE (Retail, Restaurant)
         ============================================================ --}}
    <form action="{{ route('cart.add', $product) }}" method="POST" class="space-y-3">
        @csrf

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Quantity</label>
            <div class="flex items-center gap-2">
                <button type="button" onclick="const i=this.nextElementSibling; i.value=Math.max(1,i.value-1)"
                        class="w-9 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-lg font-bold transition">−</button>
                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="99" required
                       class="w-16 text-center px-2 py-2 rounded-lg border border-gray-300 text-sm font-semibold focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                <button type="button" onclick="const i=this.previousElementSibling; i.value=Math.min(99,+i.value+1)"
                        class="w-9 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-lg font-bold transition">+</button>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Special Instructions (optional)</label>
            <textarea name="meta" rows="2" placeholder="Any customization or note..."
                      class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm resize-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">{{ old('meta') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <button type="submit" name="action" value="cart"
                    class="px-4 py-2.5 rounded-lg text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition">
                Add to Cart
            </button>
            <button type="submit" name="buy_now" value="1"
                    class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                Buy Now
            </button>
        </div>
    </form>

@elseif($mode === BookingMode::BOOKING)
    {{-- ============================================================
         HOTEL BOOKING
         ============================================================ --}}
    <form action="{{ route('booking.start') }}" method="POST" class="space-y-3">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="quantity" value="1">

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Check-in</label>
                <input type="date" name="check_in" required min="{{ now()->format('Y-m-d') }}"
                       value="{{ old('check_in') }}"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Check-out</label>
                <input type="date" name="check_out" required min="{{ now()->addDay()->format('Y-m-d') }}"
                       value="{{ old('check_out') }}"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Guests</label>
                <input type="number" name="guests" value="{{ old('guests', 2) }}" min="1" max="50" required
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Rooms</label>
                <input type="number" name="rooms" value="{{ old('rooms', 1) }}" min="1" max="20" required
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            </div>
        </div>

        <button type="submit"
                class="w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
            {{ $btnLabel }} →
        </button>
    </form>

@elseif($mode === BookingMode::APPOINTMENT)
    {{-- ============================================================
         APPOINTMENT (Healthcare, Beauty)
         ============================================================ --}}
    <form action="{{ route('booking.start') }}" method="POST" class="space-y-3">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="quantity" value="1">

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Preferred Date</label>
            <input type="date" name="appointment_date" required min="{{ now()->format('Y-m-d') }}"
                   value="{{ old('appointment_date') }}"
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Preferred Time</label>
            <select name="appointment_time" required
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="">Select time...</option>
                @foreach(['09:00','10:00','11:00','12:00','14:00','15:00','16:00','17:00','18:00'] as $t)
                    <option value="{{ $t }}" @selected(old('appointment_time') === $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
            {{ $btnLabel }} →
        </button>
    </form>

@elseif($mode === BookingMode::ENROLLMENT)
    {{-- ============================================================
         EDUCATION ENROLLMENT
         ============================================================ --}}
    <form action="{{ route('booking.start') }}" method="POST" class="space-y-3">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="quantity" value="1">

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Preferred Batch</label>
            <select name="batch" required
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="">Select batch...</option>
                <option value="morning" @selected(old('batch')==='morning')>Morning (6-9 AM)</option>
                <option value="afternoon" @selected(old('batch')==='afternoon')>Afternoon (12-3 PM)</option>
                <option value="evening" @selected(old('batch')==='evening')>Evening (5-8 PM)</option>
            </select>
        </div>

        <button type="submit"
                class="w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
            {{ $btnLabel }} →
        </button>
    </form>

@elseif($mode === BookingMode::ROUTE)
    {{-- ============================================================
         TRANSPORT ROUTE
         ============================================================ --}}
    <form action="{{ route('booking.start') }}" method="POST" class="space-y-3">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="quantity" value="1">

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Pickup Location</label>
            <input type="text" name="pickup" required placeholder="e.g. Kathmandu Airport"
                   value="{{ old('pickup') }}"
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Dropoff Location</label>
            <input type="text" name="dropoff" required placeholder="e.g. Pokhara Lakeside"
                   value="{{ old('dropoff') }}"
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Travel Date</label>
                <input type="date" name="travel_date" required min="{{ now()->format('Y-m-d') }}"
                       value="{{ old('travel_date') }}"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Passengers</label>
                <input type="number" name="passengers" value="{{ old('passengers', 1) }}" min="1" max="50" required
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            </div>
        </div>

        <button type="submit"
                class="w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
            {{ $btnLabel }} →
        </button>
    </form>

@elseif($mode === BookingMode::SERVICE)
    {{-- ============================================================
         REPAIR SERVICE
         ============================================================ --}}
    <form action="{{ route('booking.start') }}" method="POST" class="space-y-3">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="quantity" value="1">

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Describe the Issue</label>
            <textarea name="issue" rows="3" required placeholder="What's the problem?"
                      class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm resize-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">{{ old('issue') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Preferred Date</label>
            <input type="date" name="preferred_date" required min="{{ now()->format('Y-m-d') }}"
                   value="{{ old('preferred_date') }}"
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
        </div>

        <button type="submit"
                class="w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
            {{ $btnLabel }} →
        </button>
    </form>

@elseif($mode === BookingMode::QUOTE)
    {{-- ============================================================
         CONSTRUCTION QUOTE
         ============================================================ --}}
    <form action="{{ route('booking.start') }}" method="POST" class="space-y-3">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="quantity" value="1">

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Site Address</label>
            <input type="text" name="site_address" required placeholder="Where is the site?"
                   value="{{ old('site_address') }}"
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Project Size (approx)</label>
            <input type="text" name="project_size" placeholder="e.g. 1000 sq ft, 2 floors"
                   value="{{ old('project_size') }}"
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Requirements</label>
            <textarea name="requirements" rows="3" placeholder="Describe your project..."
                      class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm resize-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">{{ old('requirements') }}</textarea>
        </div>

        <button type="submit"
                class="w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
            {{ $btnLabel }} →
        </button>
    </form>

@elseif($mode === BookingMode::PACKAGE)
    {{-- ============================================================
         TRAVEL PACKAGE
         ============================================================ --}}
    <form action="{{ route('booking.start') }}" method="POST" class="space-y-3">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="quantity" value="1">

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Departure Date</label>
            <input type="date" name="departure_date" required min="{{ now()->format('Y-m-d') }}"
                   value="{{ old('departure_date') }}"
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Travelers</label>
                <input type="number" name="travelers" value="{{ old('travelers', 2) }}" min="1" max="50" required
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Room Type</label>
                <select name="room_type" required
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                    <option value="single" @selected(old('room_type')==='single')>Single</option>
                    <option value="double" @selected(old('room_type','double')==='double')>Double</option>
                    <option value="twin" @selected(old('room_type')==='twin')>Twin</option>
                    <option value="family" @selected(old('room_type')==='family')>Family</option>
                </select>
            </div>
        </div>

        <button type="submit"
                class="w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
            {{ $btnLabel }} →
        </button>
    </form>

@else
    {{-- ============================================================
         FALLBACK — simple order
         ============================================================ --}}
    <form action="{{ route('booking.start') }}" method="POST" class="space-y-3">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Quantity</label>
            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="99" required
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
        </div>

        <button type="submit"
                class="w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
            {{ $btnLabel }} →
        </button>
    </form>
@endif
