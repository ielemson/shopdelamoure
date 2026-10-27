@extends('layouts.app')

@section('meta_title', 'Checkout | Dela Moure Luxury Fragrances')

@section('meta_description',
    'Complete your Dela Moure order securely. Choose delivery or pickup and pay securely with
    Paystack.')

@section('PageContent')

    @include('frontend.partials.breadcrumb', [
        'title' => 'Checkout',
        'item' => 'Checkout',
    ])

    @php

        /*
        |--------------------------------------------------------------------------
        | Currency / Cart
        |--------------------------------------------------------------------------
        */

        $currency = 'NGN';

        $currencySymbol = $currency === 'USD' ? '$' : '₦';

        $cart = \Darryldecode\Cart\Facades\CartFacade::getContent();

        $subtotal = (float) \Darryldecode\Cart\Facades\CartFacade::getSubTotal(false);
        $hasWholesale = $cart->contains(fn($item) => $item->attributes->get('purchase_type') === 'wholesale');

        $shipping = 0;

        /*
        |--------------------------------------------------------------------------
        | Coupon
        |--------------------------------------------------------------------------
        |
        | Coupon validation/calculation remains server-side.
        | The checkout page only reads the already validated coupon session.
        |
        */

        $coupon = $hasWholesale ? null : session('coupon');

        $couponDiscount = (float) data_get($coupon, 'discount_amount', 0);

        $couponDiscount = max(0, min($couponDiscount, $subtotal));

        $total = max(0, $subtotal - $couponDiscount);

        /*
        |--------------------------------------------------------------------------
        | Existing Shipping Values
        |--------------------------------------------------------------------------
        */

        $selectedCountryId = old(
            'country_id',
            optional($defaultAddress)->country_id ?? optional($countries->first())->id,
        );

        $selectedStateId = old('state_id', optional($defaultAddress)->state_id);

        $selectedShippingRateId = old('shipping_rate_id');

        /*
        |--------------------------------------------------------------------------
        | Store Pickup Master Setting
        |--------------------------------------------------------------------------
        */

        $websiteSetting = \App\Models\WebsiteSetting::query()->first();

        $storePickupEnabled = (bool) ($websiteSetting?->enable_store_pickup ?? true);

        $terminalService = app(\App\Services\TerminalCheckoutService::class);
        $terminalMode = $websiteSetting && $terminalService->available($websiteSetting);
        $terminalPreview = $terminalMode && $terminalService->preview();
        $deliveryProvider = $websiteSetting?->delivery_provider ?? 'local';
        $localDeliveryAvailable = $deliveryProvider === 'local';
        $homeDeliveryAvailable = $localDeliveryAvailable || $terminalMode;

        /*
        |--------------------------------------------------------------------------
        | Pickup Locations
        |--------------------------------------------------------------------------
        */

        $pickupLocationsCollection = $pickupLocations ?? collect();

        $hasPickupLocations = $storePickupEnabled && $pickupLocationsCollection->isNotEmpty();

        $defaultPickupLocation = $hasPickupLocations
            ? ($pickupLocationsCollection->firstWhere('is_default', true) ?:
            $pickupLocationsCollection->first())
            : null;

        $selectedPickupLocationId = old('pickup_location_id', $defaultPickupLocation?->id);

        /*
        |--------------------------------------------------------------------------
        | Delivery Method
        |--------------------------------------------------------------------------
        */

        $selectedDeliveryMethod = old('delivery_method', 'shipping');

        /*
        |--------------------------------------------------------------------------
        | Force Shipping When Pickup Is Unavailable
        |--------------------------------------------------------------------------
        */

        if (!$homeDeliveryAvailable && $hasPickupLocations) {
            $selectedDeliveryMethod = 'pickup';
        }

        if (!$storePickupEnabled || ($selectedDeliveryMethod === 'pickup' && !$hasPickupLocations)) {
            $selectedDeliveryMethod = 'shipping';
        }
    @endphp



    <section class="container pb-14 pb-lg-19">

        <div class="text-center mb-10">

            <h2 class="mb-3">
                Checkout
            </h2>

            <p class="text-body mb-0">

                @if ($storePickupEnabled)
                    Choose how you would like to receive your order
                    and proceed to secure payment.
                @else
                    Enter your delivery information and proceed
                    to secure payment.
                @endif

            </p>

        </div>



        {{-- ==========================================================
            CUSTOMER STATUS
        ========================================================== --}}

        <div class="checkout-customer-status mb-9">

            @guest

                <div class="border p-5 p-md-6 bg-light">

                    <div class="d-md-flex align-items-center justify-content-between">

                        <div class="pe-md-5">

                            <div class="d-flex align-items-center mb-2">

                                <i class="far fa-user me-3 fs-5"></i>

                                <h5 class="mb-0">
                                    Guest Checkout
                                </h5>

                            </div>


                            <p class="text-body mb-md-0 mb-4 fs-14px">

                                No account is required. Enter your details below
                                and proceed securely to payment.

                            </p>

                        </div>


                        <div class="flex-shrink-0">

                            <span class="fs-14px text-body">
                                Already have an account?
                            </span>

                            <a href="{{ route('login') }}"
                                class="fw-semibold text-body-emphasis text-decoration-underline ms-1">
                                Sign In
                            </a>

                        </div>

                    </div>

                </div>
            @else
                <div class="border p-5 p-md-6 bg-light">

                    <div class="d-flex align-items-center">

                        <div class="me-4">

                            <i class="far fa-user-circle fs-2"></i>

                        </div>


                        <div>

                            <h5 class="mb-1">

                                Welcome
                                back{{ auth()->user()->name ? ', ' . auth()->user()->name : '' }}

                            </h5>


                            <p class="text-body fs-14px mb-0">

                                Your available customer details have been
                                pre-filled below.

                            </p>

                        </div>

                    </div>

                </div>

            @endguest

        </div>



        {{-- ==========================================================
            ERRORS
        ========================================================== --}}

        @if ($errors->any())

            <div class="alert alert-danger mb-8">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach

                </ul>

            </div>

        @endif



        @if (session('error'))
            <div class="alert alert-danger mb-8">

                {{ session('error') }}

            </div>
        @endif



        @if (session('success'))
            <div class="alert alert-success mb-8">

                {{ session('success') }}

            </div>
        @endif



        @if ($hasWholesale)
            <div class="alert alert-info" role="status">
                Your order contains wholesale items. Prices and payment are in NGN.
                Volume prices are based on the quantities shown below. Promotional codes are unavailable for this order.
                <a href="{{ route('cart.index') }}" class="alert-link">Review quantities in your cart</a>.
            </div>
        @endif
        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">

            @csrf


            <input type="hidden" name="payment_method" value="paystack">



            <div class="row">


                {{-- ==========================================================
                    ORDER SUMMARY
                ========================================================== --}}

                <div class="col-lg-4 pb-lg-0 pb-14 order-lg-last">

                    <div class="card border-0 rounded-0 shadow">

                        <div class="card-header px-0 mx-8 bg-transparent py-8">

                            <h4 class="fs-4 mb-8">
                                Order Summary
                            </h4>


                            @forelse ($cart as $item)
                                @php

                                    $imagePath = $item->attributes->get('image');
                                    $image = $imagePath
                                        ? asset($imagePath)
                                        : asset('assets/images/products/product-placeholder.jpg');
                                    $isWholesale = $item->attributes->get('purchase_type') === 'wholesale';
                                    $minimum = max(1, (int) $item->attributes->get('wholesale_min_quantity', 1));
                                    $mixed =
                                        $isWholesale && (bool) $item->attributes->get('wholesale_allow_mixed_variants');
                                    $groupQuantity = $cart
                                        ->filter(
                                            fn($line) => $line->attributes->get('purchase_type') === 'wholesale' &&
                                                (int) $line->attributes->get('product_id') ===
                                                    (int) $item->attributes->get('product_id'),
                                        )
                                        ->sum('quantity');

                                @endphp


                                <div class="d-flex w-100 mb-7">

                                    <div class="me-5 flex-shrink-0">

                                        <img src="{{ $image }}" width="60" height="80"
                                            class="object-fit-cover" alt="{{ $item->name }}">

                                    </div>


                                    <div class="d-flex flex-grow-1">

                                        <div class="pe-3">

                                            <p class="mb-1 text-body-emphasis fw-semibold">

                                                {{ $item->name }}

                                                <span class="text-body fw-normal">

                                                    ×{{ $item->quantity }}

                                                </span>

                                            </p>


                                            <span
                                                class="badge {{ $isWholesale ? 'bg-primary' : 'bg-secondary' }} mb-2">{{ $isWholesale ? 'Wholesale' : 'Retail' }}</span>
                                            <p class="small text-muted mb-1">₦{{ number_format($item->price, 2) }} NGN /
                                                unit</p>
                                            @if ($isWholesale)
                                                <p class="small text-muted mb-1">
                                                    Minimum {{ $minimum }} units
                                                    {{ $mixed ? 'across eligible options of this product' : ($item->attributes->get('variant_id') ? 'per option' : 'of this product') }}.
                                                </p>
                                                @if ($mixed)
                                                    <p class="small text-muted mb-1">Combined wholesale quantity:
                                                        {{ $groupQuantity }}.</p>
                                                @endif
                                            @endif
                                            @if ($item->attributes->get('variant_name'))
                                                <p class="fs-14px text-body mb-0">

                                                    {{ $item->attributes->get('variant_name') }}

                                                </p>
                                            @endif

                                        </div>


                                        <div class="ms-auto text-end">

                                            <p class="fs-14px text-body-emphasis mb-0 fw-bold">

                                                {{ $currencySymbol }}{{ number_format($item->price * $item->quantity, 2) }}

                                            </p>

                                        </div>

                                    </div>

                                </div>


                            @empty

                                <p class="text-body">

                                    Your cart is empty.

                                </p>
                            @endforelse

                        </div>



                        <div class="card-body px-8 py-7">


                            {{-- Subtotal --}}

                            <div class="d-flex align-items-center mb-3">

                                <span>
                                    Subtotal:
                                </span>


                                <span class="ms-auto text-body-emphasis fw-semibold" id="checkoutSubtotal">

                                    {{ $currencySymbol }}{{ number_format($subtotal, 2) }}

                                </span>

                            </div>



                            {{-- Coupon Discount --}}

                            <div id="checkoutCouponDiscountRow"
                                class="d-flex align-items-center mb-3 {{ $couponDiscount > 0 ? '' : 'd-none' }}">

                                <span>
                                    Coupon Discount
                                    <span id="checkoutCouponCodeLabel" class="text-body fw-normal">
                                        @if (!empty($coupon['code']))
                                            ({{ $coupon['code'] }})
                                        @endif
                                    </span>
                                </span>

                                <span class="ms-auto text-success fw-semibold" id="checkoutCouponDiscount">

                                    -{{ $currencySymbol }}{{ number_format($couponDiscount, 2) }}

                                </span>

                            </div>


                            @unless ($hasWholesale)
                                {{-- Coupon --}}

                                <div class="checkout-coupon-box border-top pt-5 mb-5">

                                    <label for="checkoutCouponCode"
                                        class="mb-3 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                        Coupon Code

                                    </label>


                                    <div id="couponEntryBox" class="{{ $couponDiscount > 0 ? 'd-none' : '' }}">

                                        <div class="input-group">

                                            <input type="text" class="form-control text-uppercase" id="checkoutCouponCode"
                                                placeholder="Enter coupon code" autocomplete="off">

                                            <button type="button" class="btn btn-outline-dark" id="applyCouponBtn">

                                                Apply

                                            </button>

                                        </div>

                                    </div>


                                    <div id="couponAppliedBox" class="{{ $couponDiscount > 0 ? '' : 'd-none' }}">

                                        <div
                                            class="d-flex align-items-center justify-content-between gap-3 border bg-light p-4">

                                            <div>

                                                <div class="fw-semibold text-body-emphasis" id="appliedCouponCode">

                                                    {{ $coupon['code'] ?? '' }}

                                                </div>

                                                {{-- <div class="small text-success">
                                                Coupon applied
                                            </div> --}}

                                            </div>


                                            <button type="button" class="btn btn-sm btn-outline-danger" id="removeCouponBtn">

                                                Remove

                                            </button>

                                        </div>

                                    </div>


                                    <div id="couponMessage" class="small mt-3 d-none">
                                    </div>

                                </div>
                            @endunless

                            {{-- Delivery / Pickup --}}

                            <div class="d-flex align-items-center">

                                <span id="checkoutShippingLabel">

                                    Delivery & Handling:

                                </span>


                                <span class="ms-auto text-body-emphasis fw-semibold" id="checkoutShipping">

                                    Select delivery zone

                                </span>

                            </div>


                            <div id="shippingMessage" class="small mt-3 d-none"></div>

                        </div>



                        <div class="card-footer bg-transparent py-6 px-0 mx-8">

                            <div class="d-flex align-items-center fw-bold">

                                <span class="text-body-emphasis">

                                    Total

                                </span>


                                <span class="ms-auto text-body-emphasis fs-4" id="checkoutTotal">

                                    {{ $currencySymbol }}{{ number_format($total, 2) }}

                                </span>

                            </div>

                        </div>

                    </div>

                </div>



                {{-- ==========================================================
                    DELIVERY
                ========================================================== --}}

                <div class="col-lg-8 order-lg-first pe-xl-20 pe-lg-6">

                    <div class="checkout">

                        <h4 class="fs-4 mb-2">

                            Delivery Information

                        </h4>


                        <p class="text-body fs-14px mb-7">

                            @if ($storePickupEnabled)
                                Choose delivery to your address or collect
                                your order from a Dela Moure pickup location.
                            @else
                                Enter your delivery details to have your
                                order delivered to your address.
                            @endif

                        </p>



                        {{-- ==================================================
                            DELIVERY METHOD SELECTOR
                        ================================================== --}}

                        @unless ($homeDeliveryAvailable)
                            <div class="alert alert-warning" role="status">
                                Home delivery is temporarily unavailable.
                                @if ($hasPickupLocations)
                                    Please select store pickup to continue.
                                @else
                                    Please contact us for assistance with your order.
                                @endif
                            </div>
                        @endunless

                        <div class="mb-8">

                            <label class="mb-4 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                How would you like to receive your order?

                            </label>


                            <div class="row g-3">


                                {{-- Shipping --}}

                                <div class="{{ $storePickupEnabled ? 'col-md-6' : 'col-12' }}">

                                    <input type="radio" class="btn-check delivery-method-input" name="delivery_method"
                                        id="deliveryShipping" value="shipping" autocomplete="off"
                                        @disabled(!$homeDeliveryAvailable)
                                        {{ $selectedDeliveryMethod === 'shipping' ? 'checked' : '' }}>


                                    <label for="deliveryShipping" class="delivery-method-card border w-100 p-5">

                                        <div class="d-flex align-items-center">

                                            <div class="delivery-method-icon me-4">

                                                <i class="fa-solid fa-truck-fast"></i>

                                            </div>


                                            <div>

                                                <strong class="d-block fs-5">

                                                    Ship

                                                </strong>


                                                <span class="text-body fs-14px">

                                                    Deliver to your address

                                                </span>

                                            </div>

                                        </div>

                                    </label>

                                </div>



                                {{-- Pickup --}}

                                @if ($storePickupEnabled)
                                    <div class="col-md-6">

                                        <input type="radio" class="btn-check delivery-method-input"
                                            name="delivery_method" id="deliveryPickup" value="pickup" autocomplete="off"
                                            {{ $selectedDeliveryMethod === 'pickup' ? 'checked' : '' }}
                                            {{ !$hasPickupLocations ? 'disabled' : '' }}>


                                        <label for="deliveryPickup"
                                            class="delivery-method-card border w-100 p-5
                                            {{ !$hasPickupLocations ? 'opacity-50' : '' }}">

                                            <div class="d-flex align-items-center">

                                                <div class="delivery-method-icon me-4">

                                                    <i class="fa-solid fa-location-dot"></i>

                                                </div>


                                                <div>

                                                    <strong class="d-block fs-5">

                                                        Pickup

                                                    </strong>


                                                    <span class="text-body fs-14px">

                                                        {{ $hasPickupLocations ? 'Collect from Dela Moure' : 'Currently unavailable' }}

                                                    </span>

                                                </div>

                                            </div>

                                        </label>

                                    </div>
                                @endif

                            </div>

                        </div>



                        {{-- ==================================================
                            CUSTOMER DETAILS
                        ================================================== --}}

                        <div class="mb-7">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                Name

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <div class="row">

                                <div class="col-md-6 mb-md-0 mb-7">

                                    <input type="text" class="form-control" name="first_name"
                                        value="{{ old('first_name', optional($defaultAddress)->first_name) }}"
                                        placeholder="First Name" autocomplete="given-name" required>

                                </div>


                                <div class="col-md-6">

                                    <input type="text" class="form-control" name="last_name"
                                        value="{{ old('last_name', optional($defaultAddress)->last_name) }}"
                                        placeholder="Last Name" autocomplete="family-name" required>

                                </div>

                            </div>

                        </div>



                        {{-- ==================================================
                            CONTACT INFORMATION
                        ================================================== --}}

                        <div class="mb-7">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                Contact Information

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <div class="row">

                                <div class="col-md-6 mb-md-0 mb-7">

                                    <input type="email" class="form-control" name="email"
                                        value="{{ old('email', optional($defaultAddress)->email ?? optional(auth()->user())->email) }}"
                                        placeholder="Email Address" autocomplete="email" required>

                                </div>


                                <div class="col-md-6">

                                    <input type="tel" class="form-control" name="phone"
                                        value="{{ old('phone', optional($defaultAddress)->phone) }}"
                                        placeholder="Phone Number" autocomplete="tel" required>

                                </div>

                            </div>

                        </div>



                        {{-- ==================================================
                            SHIPPING FIELDS
                        ================================================== --}}

                        @if($terminalPreview)
                            <div class="alert alert-warning">Sandbox checkout preview. Service selection updates the total; payment is disabled for home delivery. Preview ends after 30 minutes. Return to Admin → Terminal Checkout to end it sooner.</div>
                        @endif
                        <div id="shippingFields">


                            {{-- Street --}}

                            <div class="mb-7">

                                <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                    Street Address

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <input type="text" class="form-control shipping-required" name="street_address"
                                    id="checkoutStreetAddress"
                                    value="{{ old('street_address', optional($defaultAddress)->street_address) }}"
                                    placeholder="House number and street name" autocomplete="street-address">

                            </div>



                            <div class="row">


                                {{-- Country --}}

                                <div class="col-md-6">

                                    <div class="mb-7">

                                        <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                            Country

                                            <span class="text-danger">
                                                *
                                            </span>

                                        </label>


                                        <select name="country_id" id="checkoutCountry"
                                            class="form-select shipping-required" autocomplete="country">

                                            <option value="">

                                                Select Country

                                            </option>


                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ (string) $selectedCountryId === (string) $country->id ? 'selected' : '' }}>

                                                    {{ $country->name }}

                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div>



                                {{-- State --}}

                                <div class="col-md-6">

                                    <div class="mb-7">

                                        <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                            State

                                            <span class="text-danger">
                                                *
                                            </span>

                                        </label>


                                        <select name="state_id" id="checkoutState" class="form-select shipping-required"
                                            data-selected="{{ $selectedStateId }}" autocomplete="address-level1">

                                            <option value="">

                                                Select State

                                            </option>

                                        </select>

                                    </div>

                                </div>

                            </div>



                            @include('frontend.checkout.terminal-fields')
                            {{-- Delivery Zone --}}

                            <div class="mb-7">

                                <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                    Delivery Zone

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <select name="shipping_rate_id" id="checkoutShippingZone"
                                    class="form-select shipping-required" data-selected="{{ $selectedShippingRateId }}"
                                    disabled>

                                    <option value="">

                                        Select delivery zone

                                    </option>

                                </select>


                                <small id="zoneHelpText" class="text-muted d-block mt-2">

                                    Select your state first to see available
                                    delivery zones.

                                </small>



                                {{-- Covered Areas --}}

                                <div id="zoneCoverageBox" class="border bg-light p-4 mt-4 d-none">

                                    <div class="mb-2">

                                        <strong id="zoneCoverageTitle">

                                            Delivery Zone

                                        </strong>

                                    </div>


                                    <div class="text-body fs-14px mb-3">

                                        This delivery zone covers:

                                    </div>


                                    <div id="zoneCoverageAreas" class="d-flex flex-wrap gap-2"></div>

                                </div>

                            </div>

                        </div>



                        {{-- ==================================================
                            PICKUP
                        ================================================== --}}

                        @if ($storePickupEnabled)

                            <div id="pickupFields" class="d-none mb-8">

                                <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                    Pickup Location

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>



                                @forelse ($pickupLocationsCollection as $pickupLocation)
                                    <div class="mb-3">

                                        <input type="radio" class="btn-check pickup-location-input"
                                            name="pickup_location_id" id="pickupLocation{{ $pickupLocation->id }}"
                                            value="{{ $pickupLocation->id }}" autocomplete="off"
                                            {{ (string) $selectedPickupLocationId === (string) $pickupLocation->id ? 'checked' : '' }}>


                                        <label for="pickupLocation{{ $pickupLocation->id }}"
                                            class="pickup-location-card border w-100 p-5">

                                            <div class="d-flex justify-content-between gap-4">

                                                <div>

                                                    <div class="d-flex align-items-center mb-2">

                                                        <i class="fa-solid fa-location-dot me-3"></i>


                                                        <strong class="fs-5">

                                                            {{ $pickupLocation->name }}

                                                        </strong>

                                                    </div>


                                                    <div class="text-body fs-14px mb-2">

                                                        {{ $pickupLocation->address }}


                                                        @if ($pickupLocation->state?->name)
                                                            <br>

                                                            {{ $pickupLocation->state->name }}
                                                        @endif

                                                    </div>



                                                    @if ($pickupLocation->opening_hours)
                                                        <div class="small text-muted mb-1">

                                                            <i class="far fa-clock me-1"></i>

                                                            {{ $pickupLocation->opening_hours }}

                                                        </div>
                                                    @endif



                                                    @if ($pickupLocation->pickup_time)
                                                        <div class="small text-muted">

                                                            <i class="far fa-circle-check me-1"></i>

                                                            {{ $pickupLocation->pickup_time }}

                                                        </div>
                                                    @endif

                                                </div>



                                                <div class="text-end">

                                                    <span class="badge bg-success">

                                                        FREE

                                                    </span>


                                                    @if ($pickupLocation->is_default)
                                                        <div class="small text-muted mt-2">

                                                            Main Pickup

                                                        </div>
                                                    @endif

                                                </div>

                                            </div>

                                        </label>

                                    </div>


                                @empty


                                    <div class="alert alert-light border mb-0">

                                        Pickup is currently unavailable.

                                    </div>
                                @endforelse



                                @if ($hasPickupLocations)
                                    <div class="alert alert-light border mt-4 mb-0 fs-14px">

                                        <i class="fa-solid fa-circle-info me-2"></i>

                                        You will be notified when your order
                                        is ready for collection.

                                        Please do not visit the pickup location
                                        until your order has been confirmed ready.

                                    </div>
                                @endif

                            </div>

                        @endif



                        {{-- ==================================================
                            NOTE
                        ================================================== --}}

                        <div class="mb-10">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase"
                                id="deliveryNoteLabel">

                                Delivery Notes

                                <span class="text-body fw-normal">

                                    (Optional)

                                </span>

                            </label>


                            <textarea name="delivery_note" class="form-control" rows="4" id="deliveryNote"
                                placeholder="Special delivery instructions...">{{ old('delivery_note', optional($defaultAddress)->delivery_note) }}</textarea>

                        </div>



                        {{-- ==================================================
                            PAYMENT
                        ================================================== --}}

                        <div class="checkout">

                            <h4 class="fs-4 mb-3">

                                Payment

                            </h4>


                            <p class="text-body mb-7">

                                Complete your order securely with Paystack.

                            </p>



                            <div class="payment-method-card border p-6 mb-7">

                                <div class="d-flex align-items-center mb-3">

                                    <i class="far fa-credit-card fs-2 me-4"></i>


                                    <strong class="fs-5">

                                        Pay with Paystack

                                    </strong>

                                </div>


                                <p class="text-body fs-14px mb-0">

                                    You will be redirected to Paystack's secure
                                    payment page after your order has been created.

                                </p>

                            </div>



                            @guest

                                <div class="guest-checkout-note border p-5 mb-7">

                                    <div class="d-flex">

                                        <i class="far fa-check-circle me-3 mt-1"></i>


                                        <div>

                                            <strong class="d-block mb-1">

                                                Continue without an account

                                            </strong>


                                            <span class="text-body fs-14px">

                                                You can complete this order as
                                                a guest and create an account
                                                later using the same email address.

                                            </span>

                                        </div>

                                    </div>

                                </div>

                            @endguest



                            {{-- Terms --}}

                            <div class="form-check mb-7">
                                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox"
                                    name="terms" value="1" id="checkoutTerms" {{ old('terms') ? 'checked' : '' }}
                                    required>

                                <label class="form-check-label" for="checkoutTerms">
                                    I have read and agree to the
                                    <button type="button"
                                        class="btn btn-link text-black text-decoration-underline p-0 border-0 align-baseline"
                                        data-bs-toggle="modal" data-bs-target="#termsModal">
                                        Terms and Conditions
                                    </button>.
                                </label>

                                @error('terms')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>



                            {{-- Submit --}}

                            <button type="submit" id="placeOrderBtn"
                                class="btn btn-dark btn-hover-bg-primary btn-hover-border-primary px-11 py-5" disabled>

                                <i class="far fa-lock me-2"></i>

                                Proceed to Secure Payment

                            </button>



                            <div id="checkoutLocationNotice" class="small text-muted mt-3">

                                Select your delivery state and zone
                                to calculate shipping.

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </section>



    <style>
        .checkout-customer-status>div {
            border-color: rgba(0, 0, 0, .1) !important;
        }


        .payment-method-card {
            transition: all .2s ease;
        }


        .guest-checkout-note {
            background: #faf9f7;
            border-color: rgba(0, 0, 0, .08) !important;
        }



        /*
                                |--------------------------------------------------------------------------
                                | Ship / Pickup
                                |--------------------------------------------------------------------------
                                */

        .delivery-method-card,
        .pickup-location-card {

            display: block;

            cursor: pointer;

            transition: all .2s ease;

            background: #fff;
        }


        .delivery-method-card:hover,
        .pickup-location-card:hover {

            border-color: #4A2C20 !important;
        }


        .delivery-method-input:checked+.delivery-method-card,
        .pickup-location-input:checked+.pickup-location-card {

            border: 2px solid #4A2C20 !important;

            background: #faf7f3;
        }


        .delivery-method-icon {

            width: 45px;

            height: 45px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #F8F4EC;

            color: #4A2C20;

            flex-shrink: 0;
        }



        /*
                                |--------------------------------------------------------------------------
                                | Disabled Pickup
                                |--------------------------------------------------------------------------
                                */

        .delivery-method-input:disabled+.delivery-method-card {

            cursor: not-allowed;

            opacity: .55;

        }



        /*
                                |--------------------------------------------------------------------------
                                | Shipping Areas
                                |--------------------------------------------------------------------------
                                */

        #zoneCoverageAreas .zone-area-badge {

            display: inline-block;

            padding: .35rem .65rem;

            border: 1px solid var(--bs-border-color);

            background: #fff;

            font-size: 13px;
        }




        /*
                                |--------------------------------------------------------------------------
                                | Coupon
                                |--------------------------------------------------------------------------
                                */

        .checkout-coupon-box .input-group .form-control {

            min-width: 0;
        }


        .checkout-coupon-box .btn {

            white-space: nowrap;
        }
    </style>

@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | Elements
            |--------------------------------------------------------------------------
            */

            const checkoutForm =
                document.getElementById('checkoutForm');

            const shippingRadio =
                document.getElementById('deliveryShipping');

            const pickupRadio =
                document.getElementById('deliveryPickup');

            const shippingFields =
                document.getElementById('shippingFields');

            const pickupFields =
                document.getElementById('pickupFields');

            const pickupRadios =
                document.querySelectorAll('.pickup-location-input');

            const countrySelect =
                document.getElementById('checkoutCountry');

            const stateSelect =
                document.getElementById('checkoutState');

            const zoneSelect =
                document.getElementById('checkoutShippingZone');

            const streetAddress =
                document.getElementById('checkoutStreetAddress');

            const zoneHelpText =
                document.getElementById('zoneHelpText');

            const zoneCoverageBox =
                document.getElementById('zoneCoverageBox');

            const zoneCoverageTitle =
                document.getElementById('zoneCoverageTitle');

            const zoneCoverageAreas =
                document.getElementById('zoneCoverageAreas');

            const shippingLabel =
                document.getElementById('checkoutShippingLabel');

            const shippingDisplay =
                document.getElementById('checkoutShipping');

            const shippingMessage =
                document.getElementById('shippingMessage');

            const totalDisplay =
                document.getElementById('checkoutTotal');

            const placeOrderBtn =
                document.getElementById('placeOrderBtn');

            const locationNotice =
                document.getElementById('checkoutLocationNotice');

            const deliveryNoteLabel =
                document.getElementById('deliveryNoteLabel');

            const deliveryNote =
                document.getElementById('deliveryNote');


            /*
            |--------------------------------------------------------------------------
            | Coupon Elements
            |--------------------------------------------------------------------------
            */

            const couponDiscountRow =
                document.getElementById('checkoutCouponDiscountRow');

            const couponDiscountDisplay =
                document.getElementById('checkoutCouponDiscount');

            const couponCodeLabel =
                document.getElementById('checkoutCouponCodeLabel');

            const couponEntryBox =
                document.getElementById('couponEntryBox');

            const couponAppliedBox =
                document.getElementById('couponAppliedBox');

            const couponInput =
                document.getElementById('checkoutCouponCode');

            const appliedCouponCode =
                document.getElementById('appliedCouponCode');

            const applyCouponBtn =
                document.getElementById('applyCouponBtn');

            const removeCouponBtn =
                document.getElementById('removeCouponBtn');

            const couponMessage =
                document.getElementById('couponMessage');


            const csrfToken =
                checkoutForm.querySelector(
                    'input[name="_token"]'
                ).value;


            /*
            |--------------------------------------------------------------------------
            | Currency
            |--------------------------------------------------------------------------
            */

            const currency =
                @json($currency);

            const currencySymbol =
                @json($currencySymbol);

            const subtotal =
                Number(@json((float) $subtotal));


            /*
            |--------------------------------------------------------------------------
            | Existing Selection
            |--------------------------------------------------------------------------
            */

            const selectedState =
                stateSelect.dataset.selected;

            const selectedShippingRate =
                zoneSelect.dataset.selected;


            /*
            |--------------------------------------------------------------------------
            | Runtime
            |--------------------------------------------------------------------------
            */

            const localDeliveryAvailable = @json($homeDeliveryAvailable);
            const terminalMode = @json((bool) $terminalMode);
            const terminalPreview = @json((bool) $terminalPreview);

            let shippingAmount = 0;

            let shippingReady = false;

            let shippingZones = [];

            /*
            |--------------------------------------------------------------------------
            | Coupon Runtime
            |--------------------------------------------------------------------------
            */

            let couponDiscountAmount =
                Number(@json((float) $couponDiscount));


            /*
            |--------------------------------------------------------------------------
            | Routes
            |--------------------------------------------------------------------------
            */

            const statesUrl =
                @json(route('checkout.states', [
                        'country' => '__COUNTRY__',
                    ]));

            const shippingZonesUrl =
                @json(route('checkout.shipping-zones', [
                        'stateId' => '__STATE__',
                    ]));

            const shippingRateUrl =
                @json(route('checkout.shipping-rate'));

            const couponApplyUrl =
                @json(route('coupon.apply'));

            const couponRemoveUrl =
                @json(route('coupon.remove'));


            /*
            |--------------------------------------------------------------------------
            | Current Delivery Method
            |--------------------------------------------------------------------------
            */

            function deliveryMethod() {

                return document.querySelector(
                    'input[name="delivery_method"]:checked'
                )?.value || 'shipping';

            }


            /*
            |--------------------------------------------------------------------------
            | Currency
            |--------------------------------------------------------------------------
            */

            function formatMoney(amount) {

                return currencySymbol +
                    Number(amount).toLocaleString(
                        currency === 'USD' ?
                        'en-US' :
                        'en-NG', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Checkout Total
            |--------------------------------------------------------------------------
            */

            function discountedSubtotal() {

                return Math.max(
                    0,
                    subtotal - couponDiscountAmount
                );

            }


            function refreshCheckoutTotal() {

                totalDisplay.textContent =
                    formatMoney(
                        discountedSubtotal() +
                        shippingAmount
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Coverage
            |--------------------------------------------------------------------------
            */

            function resetCoverage() {

                zoneCoverageBox.classList.add('d-none');

                zoneCoverageTitle.textContent =
                    'Delivery Zone';

                zoneCoverageAreas.innerHTML =
                    '';

            }


            /*
            |--------------------------------------------------------------------------
            | Messages
            |--------------------------------------------------------------------------
            */

            function clearShippingMessage() {

                shippingMessage.classList.add('d-none');

                shippingMessage.classList.remove(
                    'text-danger'
                );

                shippingMessage.textContent = '';

            }


            /*
            |--------------------------------------------------------------------------
            | Shipping Reset
            |--------------------------------------------------------------------------
            */

            function resetShipping(
                message = 'Select delivery zone'
            ) {

                if (deliveryMethod() !== 'shipping') {
                    return;
                }

                shippingAmount = 0;

                shippingReady = false;

                shippingLabel.textContent =
                    'Delivery & Handling:';

                shippingDisplay.textContent =
                    message;

                refreshCheckoutTotal();

                placeOrderBtn.disabled =
                    true;

                locationNotice.textContent =
                    'Select your delivery state and zone to calculate shipping.';

                clearShippingMessage();

            }


            function resetZones() {

                shippingZones = [];

                zoneSelect.innerHTML =
                    '<option value="">Select delivery zone</option>';

                zoneSelect.disabled =
                    true;

                zoneHelpText.textContent =
                    'Select your state first to see available delivery zones.';

                resetCoverage();

                resetShipping();

            }


            /*
            |--------------------------------------------------------------------------
            | Pickup State
            |--------------------------------------------------------------------------
            */

            function setPickupSummary() {

                shippingAmount = 0;

                shippingReady = true;

                shippingLabel.textContent =
                    'Pickup:';

                shippingDisplay.innerHTML =
                    '<span class="text-success">FREE</span>';

                refreshCheckoutTotal();

                clearShippingMessage();

                const selectedPickup =
                    document.querySelector(
                        '.pickup-location-input:checked'
                    );

                placeOrderBtn.disabled = !selectedPickup;

                locationNotice.textContent =
                    selectedPickup ?
                    'Free pickup selected. You will be notified when your order is ready.' :
                    'Please select a pickup location.';

            }


            /*
            |--------------------------------------------------------------------------
            | Delivery Method UI
            |--------------------------------------------------------------------------
            */

            function applyDeliveryMethod() {

                const method =
                    deliveryMethod();

                if (terminalMode) {
                    for (const id of ['terminalCity', 'terminalZip', 'terminalRate', 'terminalQuoteToken']) {
                        const input = document.getElementById(id);
                        if (input && method === 'pickup') { input.disabled = true; input.required = false; }
                    }
                    const zipInput = document.getElementById('terminalZip');
                    if (zipInput && method === 'shipping') { zipInput.disabled = false; zipInput.required = !terminalPreview; }
                }


                /*
                |--------------------------------------------------------------------------
                | Pickup
                |--------------------------------------------------------------------------
                */

                if (method === 'pickup') {

                    shippingFields.classList.add(
                        'd-none'
                    );

                    pickupFields.classList.remove(
                        'd-none'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Disable Shipping Inputs
                    |--------------------------------------------------------------------------
                    */

                    streetAddress.required = false;
                    streetAddress.disabled = true;

                    countrySelect.required = false;
                    countrySelect.disabled = true;

                    stateSelect.required = false;
                    stateSelect.disabled = true;

                    zoneSelect.required = false;
                    zoneSelect.disabled = true;


                    /*
                    |--------------------------------------------------------------------------
                    | Enable Pickup
                    |--------------------------------------------------------------------------
                    */

                    pickupRadios.forEach(function(input) {
                        input.disabled = false;
                    });


                    deliveryNoteLabel.innerHTML =
                        'Pickup Notes <span class="text-body fw-normal">(Optional)</span>';

                    deliveryNote.placeholder =
                        'Special pickup instructions...';


                    resetCoverage();

                    setPickupSummary();

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Shipping
                |--------------------------------------------------------------------------
                */

                if (!localDeliveryAvailable) {
                    shippingFields.classList.add('d-none');
                    pickupFields?.classList.add('d-none');
                    shippingFields.querySelectorAll('input, select, textarea').forEach(input => {
                        input.disabled = true;
                        input.required = false;
                    });
                    pickupRadios.forEach(input => input.disabled = true);
                    shippingAmount = 0;
                    shippingReady = false;
                    shippingDisplay.textContent = 'Unavailable';
                    locationNotice.textContent = 'Home delivery is temporarily unavailable.';
                    placeOrderBtn.disabled = true;
                    refreshCheckoutTotal();
                    return;
                }

                shippingFields.classList.remove(
                    'd-none'
                );

                if (pickupFields) {

                    pickupFields.classList.add(
                        'd-none'
                    );

                }


                pickupRadios.forEach(function(input) {
                    input.disabled = true;
                });


                streetAddress.disabled = false;
                streetAddress.required = true;

                countrySelect.disabled = false;
                countrySelect.required = true;

                stateSelect.disabled = false;
                stateSelect.required = true;

                zoneSelect.required = !terminalMode;
                if (terminalMode) {
                    zoneSelect.disabled = true;
                    document.getElementById('terminalCity').disabled = false;
                }


                deliveryNoteLabel.innerHTML =
                    'Delivery Notes <span class="text-body fw-normal">(Optional)</span>';

                deliveryNote.placeholder =
                    'Special delivery instructions...';


                shippingLabel.textContent =
                    'Delivery & Handling:';


                /*
                |--------------------------------------------------------------------------
                | Restore Shipping State
                |--------------------------------------------------------------------------
                */

                if (
                    countrySelect.value &&
                    stateSelect.value &&
                    zoneSelect.value
                ) {

                    loadShippingRate(
                        zoneSelect.value
                    );

                } else {

                    resetShipping();

                }
            }


            /*
            |--------------------------------------------------------------------------
            | Load States
            |--------------------------------------------------------------------------
            */

            async function loadStates(
                countryId,
                selectedStateId = null
            ) {

                if (deliveryMethod() !== 'shipping') {
                    return;
                }


                stateSelect.disabled = true;

                stateSelect.innerHTML =
                    '<option value="">Loading states...</option>';

                resetZones();


                if (!countryId) {

                    stateSelect.innerHTML =
                        '<option value="">Select State</option>';

                    stateSelect.disabled =
                        false;

                    return;
                }


                try {

                    const response =
                        await fetch(
                            statesUrl.replace(
                                '__COUNTRY__',
                                countryId
                            )
                        );


                    if (!response.ok) {

                        throw new Error(
                            'Unable to load states.'
                        );

                    }


                    const states =
                        await response.json();


                    stateSelect.innerHTML =
                        '<option value="">Select State</option>';


                    states.forEach(function(state) {

                        const option =
                            document.createElement(
                                'option'
                            );

                        option.value =
                            state.id;

                        option.textContent =
                            state.name;


                        if (
                            selectedStateId &&
                            String(selectedStateId) ===
                            String(state.id)
                        ) {

                            option.selected =
                                true;

                        }


                        stateSelect.appendChild(
                            option
                        );

                    });


                    if (deliveryMethod() !== 'shipping') {
                        return;
                    }


                    stateSelect.disabled =
                        false;


                    if (
                        selectedStateId &&
                        stateSelect.value
                    ) {

                        await loadShippingZones(
                            selectedStateId,
                            selectedShippingRate
                        );

                    }

                } catch (error) {

                    console.error(error);

                    stateSelect.innerHTML =
                        '<option value="">Unable to load states</option>';

                    stateSelect.disabled =
                        deliveryMethod() !== 'shipping';

                    resetZones();

                }
            }


            /*
            |--------------------------------------------------------------------------
            | Load Shipping Zones
            |--------------------------------------------------------------------------
            */

            async function loadShippingZones(
                stateId,
                selectedRateId = null
            ) {

                if (deliveryMethod() !== 'shipping') {
                    return;
                }


                if (terminalMode) { await loadTerminalCities(); return; }
                resetZones();


                if (!stateId) {
                    return;
                }


                zoneSelect.innerHTML =
                    '<option value="">Loading delivery zones...</option>';

                zoneHelpText.textContent =
                    'Loading available delivery zones...';


                try {

                    const response =
                        await fetch(
                            shippingZonesUrl.replace(
                                '__STATE__',
                                stateId
                            )
                        );


                    if (!response.ok) {

                        throw new Error(
                            'Unable to load delivery zones.'
                        );

                    }


                    shippingZones =
                        await response.json();


                    zoneSelect.innerHTML =
                        '<option value="">Select delivery zone</option>';


                    if (
                        !Array.isArray(shippingZones) ||
                        shippingZones.length === 0
                    ) {

                        zoneSelect.innerHTML =
                            '<option value="">No delivery zones available</option>';

                        zoneSelect.disabled =
                            true;

                        zoneHelpText.textContent =
                            'Delivery is not currently configured for this state.';

                        shippingDisplay.innerHTML =
                            '<span class="text-danger">Unavailable</span>';

                        locationNotice.textContent =
                            'Delivery is not currently available for this state.';

                        return;
                    }


                    shippingZones.forEach(function(zone) {

                        const option =
                            document.createElement(
                                'option'
                            );

                        option.value =
                            zone.id;

                        option.textContent =
                            zone.zone_name;


                        if (
                            selectedRateId &&
                            String(selectedRateId) ===
                            String(zone.id)
                        ) {

                            option.selected =
                                true;

                        }


                        zoneSelect.appendChild(
                            option
                        );

                    });


                    if (deliveryMethod() !== 'shipping') {
                        return;
                    }


                    zoneSelect.disabled =
                        false;

                    zoneHelpText.textContent =
                        'Choose the zone that covers your delivery location.';


                    if (
                        selectedRateId &&
                        zoneSelect.value
                    ) {

                        displayZoneCoverage(
                            selectedRateId
                        );

                        await loadShippingRate(
                            selectedRateId
                        );

                    }

                } catch (error) {

                    console.error(error);

                    zoneSelect.innerHTML =
                        '<option value="">Unable to load delivery zones</option>';

                    zoneSelect.disabled =
                        true;

                    zoneHelpText.textContent =
                        'Unable to load delivery zones.';

                    shippingDisplay.innerHTML =
                        '<span class="text-danger">Unavailable</span>';

                    locationNotice.textContent =
                        'Unable to retrieve delivery zones at this time.';

                }
            }


            /*
            |--------------------------------------------------------------------------
            | Coverage
            |--------------------------------------------------------------------------
            */

            function displayZoneCoverage(
                shippingRateId
            ) {

                resetCoverage();


                const zone =
                    shippingZones.find(
                        item =>
                        String(item.id) ===
                        String(shippingRateId)
                    );


                if (!zone) {
                    return;
                }


                zoneCoverageTitle.textContent =
                    zone.zone_name;


                const areas =
                    Array.isArray(zone.areas) ?
                    zone.areas : [];


                if (areas.length === 0) {

                    zoneCoverageAreas.innerHTML =
                        '<span class="text-muted">No covered areas specified.</span>';

                } else {

                    areas.forEach(function(area) {

                        const badge =
                            document.createElement(
                                'span'
                            );

                        badge.className =
                            'zone-area-badge';

                        badge.textContent =
                            area;

                        zoneCoverageAreas.appendChild(
                            badge
                        );

                    });
                }


                zoneCoverageBox.classList.remove(
                    'd-none'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Shipping Rate
            |--------------------------------------------------------------------------
            */

            async function loadShippingRate(
                shippingRateId
            ) {

                if (terminalMode || !localDeliveryAvailable || deliveryMethod() !== 'shipping') {
                    return;
                }


                const countryId =
                    countrySelect.value;

                const stateId =
                    stateSelect.value;


                shippingReady =
                    false;

                placeOrderBtn.disabled =
                    true;

                shippingDisplay.textContent =
                    'Calculating...';

                clearShippingMessage();


                if (
                    !countryId ||
                    !stateId ||
                    !shippingRateId
                ) {

                    resetShipping();

                    return;
                }


                try {

                    const response =
                        await fetch(
                            shippingRateUrl, {
                                method: 'POST',

                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },

                                body: JSON.stringify({
                                    country_id: countryId,
                                    state_id: stateId,
                                    shipping_rate_id: shippingRateId
                                })
                            }
                        );


                    const data =
                        await response.json();


                    if (!response.ok) {

                        throw new Error(
                            data.message ||
                            'Shipping is unavailable for this delivery zone.'
                        );

                    }


                    if (deliveryMethod() !== 'shipping') {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Coverage
                    |--------------------------------------------------------------------------
                    */

                    if (data.zone_name) {

                        zoneCoverageTitle.textContent =
                            data.zone_name;

                    }


                    if (Array.isArray(data.areas)) {

                        zoneCoverageAreas.innerHTML =
                            '';


                        data.areas.forEach(function(area) {

                            const badge =
                                document.createElement(
                                    'span'
                                );

                            badge.className =
                                'zone-area-badge';

                            badge.textContent =
                                area;

                            zoneCoverageAreas.appendChild(
                                badge
                            );

                        });


                        zoneCoverageBox.classList.remove(
                            'd-none'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Shipping Amount
                    |--------------------------------------------------------------------------
                    */

                    if (currency === 'NGN') {

                        shippingAmount =
                            Number(
                                data.shipping_cost
                            );

                    } else if (currency === 'USD') {

                        if (
                            typeof data.converted_shipping_cost ===
                            'undefined'
                        ) {

                            throw new Error(
                                'USD shipping conversion is not yet available.'
                            );

                        }


                        shippingAmount =
                            Number(
                                data.converted_shipping_cost
                            );
                    }


                    if (!Number.isFinite(shippingAmount)) {

                        throw new Error(
                            'Invalid shipping amount.'
                        );

                    }


                    shippingLabel.textContent =
                        'Delivery & Handling:';

                    shippingDisplay.textContent =
                        formatMoney(
                            shippingAmount
                        );

                    refreshCheckoutTotal();


                    shippingReady =
                        true;

                    placeOrderBtn.disabled =
                        false;

                    locationNotice.textContent =
                        'Shipping has been calculated for your selected delivery zone.';

                } catch (error) {

                    shippingAmount = 0;

                    shippingReady = false;

                    shippingDisplay.innerHTML =
                        '<span class="text-danger">Unavailable</span>';

                    refreshCheckoutTotal();

                    placeOrderBtn.disabled =
                        true;

                    shippingMessage.textContent =
                        error.message;

                    shippingMessage.classList.remove(
                        'd-none'
                    );

                    shippingMessage.classList.add(
                        'text-danger'
                    );

                    locationNotice.textContent =
                        'Please select an available delivery zone.';

                }
            }


            /*
            |--------------------------------------------------------------------------
            | Method Change
            |--------------------------------------------------------------------------
            */

            shippingRadio.addEventListener(
                'change',
                function() {

                    if (!this.checked) {
                        return;
                    }


                    applyDeliveryMethod();


                    if (countrySelect.value) {

                        loadStates(
                            countrySelect.value,
                            selectedState
                        );

                    }
                }
            );


            if (pickupRadio) {

                pickupRadio.addEventListener(
                    'change',
                    function() {

                        if (!this.checked) {
                            return;
                        }

                        applyDeliveryMethod();

                    }
                );
            }


            pickupRadios.forEach(function(input) {

                input.addEventListener(
                    'change',
                    function() {

                        if (
                            this.checked &&
                            deliveryMethod() === 'pickup'
                        ) {

                            setPickupSummary();

                        }
                    }
                );

            });


            /*
            |--------------------------------------------------------------------------
            | Country
            |--------------------------------------------------------------------------
            */

            countrySelect.addEventListener(
                'change',
                function() {

                    if (deliveryMethod() !== 'shipping') {
                        return;
                    }

                    resetZones();

                    loadStates(
                        this.value
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | State
            |--------------------------------------------------------------------------
            */

            stateSelect.addEventListener(
                'change',
                async function() {

                    if (deliveryMethod() !== 'shipping') {
                        return;
                    }


                    resetZones();


                    if (!this.value) {
                        return;
                    }


                    await loadShippingZones(
                        this.value
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Zone
            |--------------------------------------------------------------------------
            */

            zoneSelect.addEventListener(
                'change',
                async function() {

                    if (deliveryMethod() !== 'shipping') {
                        return;
                    }


                    const shippingRateId =
                        this.value;


                    resetShipping();

                    resetCoverage();


                    if (!shippingRateId) {
                        return;
                    }


                    displayZoneCoverage(
                        shippingRateId
                    );


                    await loadShippingRate(
                        shippingRateId
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Coupon Helpers
            |--------------------------------------------------------------------------
            */

            function showCouponMessage(
                message,
                type = 'success'
            ) {

                if (!couponMessage) {
                    return;
                }

                couponMessage.textContent =
                    message || '';

                couponMessage.classList.remove(
                    'd-none',
                    'text-success',
                    'text-danger'
                );

                couponMessage.classList.add(
                    type === 'error' ?
                    'text-danger' :
                    'text-success'
                );

            }


            function clearCouponMessage() {

                if (!couponMessage) {
                    return;
                }

                couponMessage.textContent = '';

                couponMessage.classList.add(
                    'd-none'
                );

                couponMessage.classList.remove(
                    'text-success',
                    'text-danger'
                );

            }


            function setCouponLoading(
                button,
                loading,
                defaultText
            ) {

                if (!button) {
                    return;
                }

                button.disabled =
                    loading;

                button.innerHTML =
                    loading ?
                    '<span class="spinner-border spinner-border-sm me-2"></span>Processing...' :
                    defaultText;

            }


            function applyCouponToSummary(coupon) {

                couponDiscountAmount =
                    Number(
                        coupon.discount_amount || 0
                    );

                if (
                    !Number.isFinite(
                        couponDiscountAmount
                    )
                ) {

                    couponDiscountAmount = 0;

                }


                couponDiscountAmount =
                    Math.max(
                        0,
                        Math.min(
                            couponDiscountAmount,
                            subtotal
                        )
                    );


                if (couponDiscountDisplay) {

                    couponDiscountDisplay.textContent =
                        '-' +
                        formatMoney(
                            couponDiscountAmount
                        );

                }


                if (couponCodeLabel) {

                    couponCodeLabel.textContent =
                        coupon.code ?
                        '(' + coupon.code + ')' :
                        '';

                }


                if (appliedCouponCode) {

                    appliedCouponCode.textContent =
                        coupon.code || '';

                }


                if (couponDiscountRow) {

                    couponDiscountRow.classList.remove(
                        'd-none'
                    );

                }


                if (couponEntryBox) {

                    couponEntryBox.classList.add(
                        'd-none'
                    );

                }


                if (couponAppliedBox) {

                    couponAppliedBox.classList.remove(
                        'd-none'
                    );

                }


                refreshCheckoutTotal();

            }


            function removeCouponFromSummary() {

                couponDiscountAmount = 0;


                if (couponDiscountDisplay) {

                    couponDiscountDisplay.textContent =
                        '-' +
                        formatMoney(0);

                }


                if (couponCodeLabel) {

                    couponCodeLabel.textContent = '';

                }


                if (appliedCouponCode) {

                    appliedCouponCode.textContent = '';

                }


                if (couponDiscountRow) {

                    couponDiscountRow.classList.add(
                        'd-none'
                    );

                }


                if (couponAppliedBox) {

                    couponAppliedBox.classList.add(
                        'd-none'
                    );

                }


                if (couponEntryBox) {

                    couponEntryBox.classList.remove(
                        'd-none'
                    );

                }


                if (couponInput) {

                    couponInput.value = '';

                }


                refreshCheckoutTotal();

            }


            async function parseCouponResponse(
                response
            ) {

                let data = {};

                try {

                    data = await response.json();

                } catch (error) {

                    throw new Error(
                        'Unable to process the coupon request.'
                    );

                }


                if (!response.ok) {

                    let message =
                        data.message ||
                        'Unable to process this coupon.';


                    if (data.errors) {

                        const firstError =
                            Object.values(
                                data.errors
                            )
                            .flat()
                            .find(Boolean);

                        if (firstError) {
                            message = firstError;
                        }

                    }


                    throw new Error(message);

                }


                return data;

            }


            /*
            |--------------------------------------------------------------------------
            | Apply Coupon
            |--------------------------------------------------------------------------
            */

            if (
                applyCouponBtn &&
                couponInput
            ) {

                applyCouponBtn.addEventListener(
                    'click',
                    async function() {

                        clearCouponMessage();


                        const code =
                            couponInput.value
                            .trim()
                            .toUpperCase();


                        if (!code) {

                            showCouponMessage(
                                'Enter a coupon code.',
                                'error'
                            );

                            couponInput.focus();

                            return;
                        }


                        setCouponLoading(
                            applyCouponBtn,
                            true,
                            'Apply'
                        );


                        try {

                            const response =
                                await fetch(
                                    couponApplyUrl, {
                                        method: 'POST',

                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken
                                        },

                                        body: JSON.stringify({
                                            coupon_code: code
                                        })
                                    }
                                );


                            const data =
                                await parseCouponResponse(
                                    response
                                );


                            if (
                                !data.coupon ||
                                typeof data.coupon.discount_amount ===
                                'undefined'
                            ) {

                                throw new Error(
                                    'The coupon response is incomplete.'
                                );

                            }


                            applyCouponToSummary(
                                data.coupon
                            );


                            showCouponMessage(
                                data.message ||
                                'Coupon applied successfully.'
                            );

                        } catch (error) {

                            showCouponMessage(
                                error.message ||
                                'Unable to apply coupon.',
                                'error'
                            );

                        } finally {

                            setCouponLoading(
                                applyCouponBtn,
                                false,
                                'Apply'
                            );

                        }

                    }
                );


                couponInput.addEventListener(
                    'keydown',
                    function(event) {

                        if (event.key !== 'Enter') {
                            return;
                        }

                        event.preventDefault();

                        applyCouponBtn.click();

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Remove Coupon
            |--------------------------------------------------------------------------
            */

            if (removeCouponBtn) {

                removeCouponBtn.addEventListener(
                    'click',
                    async function() {

                        clearCouponMessage();


                        setCouponLoading(
                            removeCouponBtn,
                            true,
                            'Remove'
                        );


                        try {

                            const response =
                                await fetch(
                                    couponRemoveUrl, {
                                        method: 'POST',

                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken
                                        }
                                    }
                                );


                            const data =
                                await parseCouponResponse(
                                    response
                                );


                            removeCouponFromSummary();


                            showCouponMessage(
                                data.message ||
                                'Coupon removed successfully.'
                            );

                        } catch (error) {

                            showCouponMessage(
                                error.message ||
                                'Unable to remove coupon.',
                                'error'
                            );

                        } finally {

                            setCouponLoading(
                                removeCouponBtn,
                                false,
                                'Remove'
                            );

                        }

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Submit Guard
            |--------------------------------------------------------------------------
            */

            let checkoutCartChanged = false;
            document.addEventListener('cart:updated', function() {
                checkoutCartChanged = true;
                placeOrderBtn.disabled = true;
                if (document.getElementById('checkoutCartChangedNotice')) return;
                const notice = document.createElement('div');
                notice.id = 'checkoutCartChangedNotice';
                notice.className = 'alert alert-warning';
                notice.setAttribute('role', 'alert');
                notice.appendChild(document.createTextNode(
                    'Your cart changed. Refresh checkout to review the updated quantities and total before paying. '
                    ));
                const refresh = document.createElement('button');
                refresh.type = 'button';
                refresh.className = 'btn btn-sm btn-outline-dark';
                refresh.textContent = 'Refresh checkout';
                refresh.addEventListener('click', () => window.location.reload());
                notice.appendChild(refresh);
                checkoutForm.prepend(notice);
            });

            checkoutForm.addEventListener(
                'submit',
                function(event) {
                    if (checkoutCartChanged) {
                        event.preventDefault();
                        document.getElementById('checkoutCartChangedNotice')?.scrollIntoView({
                            block: 'center'
                        });
                        return;
                    }

                    const method =
                        deliveryMethod();


                    /*
                    |--------------------------------------------------------------------------
                    | Shipping
                    |--------------------------------------------------------------------------
                    */

                    if (method === 'shipping') {
                        if (terminalMode && terminalPreview) {
                            event.preventDefault();
                            terminalMessage('Sandbox preview only. No order or payment was created.');
                            return;
                        }
                        if (!localDeliveryAvailable) {
                            event.preventDefault();
                            shippingMessage.textContent = 'Home delivery is temporarily unavailable.';
                            shippingMessage.classList.remove('d-none');
                            shippingMessage.classList.add('text-danger');
                            return;
                        }

                        if (
                            !countrySelect.value ||
                            !stateSelect.value ||
                            (!terminalMode && !zoneSelect.value) ||
                            !streetAddress.value.trim() ||
                            !shippingReady
                        ) {

                            event.preventDefault();


                            shippingMessage.textContent =
                                'Please complete your shipping address and select a valid delivery zone.';

                            shippingMessage.classList.remove(
                                'd-none'
                            );

                            shippingMessage.classList.add(
                                'text-danger'
                            );


                            locationNotice.textContent =
                                'Complete your delivery details before proceeding to payment.';

                            return;
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Pickup
                    |--------------------------------------------------------------------------
                    */

                    if (method === 'pickup') {

                        const selectedPickup =
                            document.querySelector(
                                '.pickup-location-input:checked'
                            );


                        if (!selectedPickup) {

                            event.preventDefault();


                            shippingMessage.textContent =
                                'Please select a pickup location.';

                            shippingMessage.classList.remove(
                                'd-none'
                            );

                            shippingMessage.classList.add(
                                'text-danger'
                            );


                            locationNotice.textContent =
                                'Select a pickup location before proceeding to payment.';

                            return;
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Prevent Double Click
                    |--------------------------------------------------------------------------
                    */

                    placeOrderBtn.disabled =
                        true;

                    placeOrderBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

                }
            );


            // Terminal Africa uses the existing checkout summary and coupon calculation.
            let terminalRequestVersion = 0;
            let terminalRates = [];
            let terminalExpiryTimer;
            const terminalCity = document.getElementById('terminalCity');
            const terminalZip = document.getElementById('terminalZip');
            const terminalRate = document.getElementById('terminalRate');
            const terminalToken = document.getElementById('terminalQuoteToken');
            const terminalGetRates = document.getElementById('terminalGetRates');
            const terminalNotice = document.getElementById('terminalNotice');
            function terminalMessage(message) {
                if (terminalNotice) terminalNotice.textContent = message;
            }
            function invalidateTerminal() {
                if (!terminalMode) return;
                terminalRequestVersion++;
                clearTimeout(terminalExpiryTimer);
                terminalRates = [];
                terminalToken.value = '';
                terminalRate.replaceChildren(new Option('Request rates first', ''));
                terminalRate.disabled = true;
                if (deliveryMethod() === 'shipping') resetShipping('Request delivery rates');
                terminalMessage('');
            }
            async function terminalJSON(response) {
                let data;
                try { data = await response.json(); }
                catch (_) { throw new Error('Unable to load delivery services. Please try again.'); }
                if (!response.ok) {
                    const first = data.errors ? Object.values(data.errors).flat().find(Boolean) : null;
                    throw new Error(first || data.message || 'Unable to load delivery services.');
                }
                return data;
            }
            async function loadTerminalCities() {
                if (!terminalMode) return;
                invalidateTerminal();
                const version = terminalRequestVersion;
                terminalCity.replaceChildren(new Option('Loading cities...', ''));
                terminalCity.disabled = true;
                if (!stateSelect.value || deliveryMethod() !== 'shipping') {
                    terminalCity.replaceChildren(new Option('Select your state first', ''));
                    return;
                }
                try {
                    const url = new URL(@json(route('checkout.terminal.cities')), window.location.origin);
                    url.searchParams.set('state_id', stateSelect.value);
                    const data = await terminalJSON(await fetch(url, {headers: {'Accept': 'application/json'}}));
                    if (version !== terminalRequestVersion || deliveryMethod() !== 'shipping') return;
                    terminalCity.replaceChildren(new Option('Select delivery city', ''));
                    for (const city of data.cities) terminalCity.add(new Option(city, city));
                    terminalCity.disabled = false;
                } catch (error) {
                    if (version !== terminalRequestVersion) return;
                    terminalCity.replaceChildren(new Option('Unable to load cities', ''));
                    terminalMessage(error.message);
                }
            }
            if (terminalMode) {
                // Keep local zone elements in the DOM because existing local JS references them.
                zoneSelect.closest('.mb-7').hidden = true;
                zoneSelect.required = false;
                zoneSelect.disabled = true;
                for (const input of [streetAddress, terminalCity, terminalZip]) {
                    input.addEventListener('input', invalidateTerminal);
                    input.addEventListener('change', invalidateTerminal);
                }
                countrySelect.addEventListener('change', () => {
                    invalidateTerminal();
                    terminalCity.replaceChildren(new Option('Select your state first', ''));
                    terminalCity.disabled = true;
                });
                // Existing state listener calls loadShippingZones, which delegates to loadTerminalCities.

                document.querySelectorAll('.delivery-method-input').forEach(input => {
                    input.addEventListener('change', () => {
                        invalidateTerminal();
                        const shipping = deliveryMethod() === 'shipping';
                        terminalCity.disabled = !shipping;
                        terminalCity.required = shipping;
                        terminalZip.disabled = !shipping;
                        terminalZip.required = shipping && !terminalPreview;
                        terminalGetRates.disabled = !shipping;
                        if (shipping && stateSelect.value) loadTerminalCities();
                    });
                });
                terminalGetRates.addEventListener('click', async () => {
                    invalidateTerminal();
                    const version = terminalRequestVersion;
                    if (!countrySelect.value || !stateSelect.value || !terminalCity.value || !streetAddress.value.trim() || (!terminalPreview && !terminalZip.value.trim())) {
                        terminalMessage('Complete your street address, state, city and postal code first.');
                        return;
                    }
                    terminalGetRates.disabled = true;
                    terminalGetRates.textContent = 'Loading delivery rates…';
                    terminalMessage('Fetching carrier rates. This can take up to 90 seconds.');
                    try {
                        const data = await terminalJSON(await fetch(@json(route('checkout.terminal.rates')), {
                            method: 'POST', headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                            body: JSON.stringify({
                                country_id: countrySelect.value, state_id: stateSelect.value,
                                terminal_city: terminalCity.value, terminal_zip: terminalZip.value,
                                street_address: streetAddress.value
                            })
                        }));
                        if (version !== terminalRequestVersion || deliveryMethod() !== 'shipping') return;
                        terminalRates = data.rates;
                        terminalToken.value = data.quote_token;
                        terminalRate.replaceChildren(new Option('Select delivery service', ''));
                        for (const rate of terminalRates) {
                            terminalRate.add(new Option(rate.carrier + ' / ' + rate.service + ' — ' + formatMoney(rate.total) + ' (' + rate.estimate + ')', rate.id));
                        }
                        terminalRate.disabled = terminalRates.length === 0;
                        terminalMessage(terminalRates.length
                            ? (terminalPreview ? 'Sandbox estimates only. Product eligibility is unverified. Payment is disabled.' : 'Choose a delivery service. Rates expire in five minutes.')
                            : 'No delivery services are available for this parcel and destination.');
                        terminalExpiryTimer = setTimeout(() => {
                            invalidateTerminal(); terminalMessage('Rates expired. Please request delivery rates again.');
                        }, data.expires_in * 1000);
                    } catch (error) {
                        if (version === terminalRequestVersion) terminalMessage(error.message);
                    } finally {
                        terminalGetRates.disabled = deliveryMethod() !== 'shipping';
                        terminalGetRates.textContent = 'Get delivery rates';
                    }
                });
                terminalRate.addEventListener('change', () => {
                    const rate = terminalRates.find(rate => rate.id === terminalRate.value);
                    shippingAmount = rate ? Number(rate.total) : 0;
                    shippingReady = Boolean(rate && terminalToken.value);
                    shippingDisplay.textContent = rate ? formatMoney(shippingAmount) : 'Select delivery service';
                    refreshCheckoutTotal();
                    placeOrderBtn.disabled = !shippingReady || terminalPreview || checkoutCartChanged;
                    locationNotice.textContent = rate ? rate.carrier + ' / ' + rate.service + (terminalPreview ? ' — sandbox preview' : '') : 'Select a delivery service.';
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Initial State
            |--------------------------------------------------------------------------
            */

            applyDeliveryMethod();


            if (
                (localDeliveryAvailable || terminalMode) &&
                deliveryMethod() === 'shipping' &&
                countrySelect.value
            ) {

                loadStates(
                    countrySelect.value,
                    selectedState
                );

            }

        });
    </script>
@endpush
