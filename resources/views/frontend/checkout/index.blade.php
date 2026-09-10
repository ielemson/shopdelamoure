@extends('layouts.app')

@section('meta_title', 'Checkout | Dela Moure Luxury Fragrances')

@section('meta_description', 'Complete your Dela Moure order securely. Choose delivery or pickup and pay securely with
    Paystack.')

@section('PageContent')

    @include('frontend.partials.breadcrumb', [
        'title' => 'Checkout',
        'item' => 'Checkout',
    ])

    @php
        $currency = strtoupper(session('currency', 'NGN'));
        $currencySymbol = $currency === 'USD' ? '$' : '₦';

        $cart = \Darryldecode\Cart\Facades\CartFacade::getContent();
        $subtotal = (float) \Darryldecode\Cart\Facades\CartFacade::getSubTotal();

        $shipping = 0;
        $total = $subtotal;

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
        | Pickup
        |--------------------------------------------------------------------------
        */

        $hasPickupLocations = isset($pickupLocations) && $pickupLocations->isNotEmpty();

        $defaultPickupLocation = $hasPickupLocations
            ? ($pickupLocations->firstWhere('is_default', true) ?:
            $pickupLocations->first())
            : null;

        $selectedPickupLocationId = old('pickup_location_id', $defaultPickupLocation?->id);

        $selectedDeliveryMethod = old('delivery_method', 'shipping');

        if ($selectedDeliveryMethod === 'pickup' && !$hasPickupLocations) {
            $selectedDeliveryMethod = 'shipping';
        }
    @endphp


    <section class="container pb-14 pb-lg-19">

        <div class="text-center mb-10">

            <h2 class="mb-3">
                Checkout
            </h2>

            <p class="text-body mb-0">
                Choose how you would like to receive your order and proceed to secure payment.
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
                                Welcome back{{ auth()->user()->name ? ', ' . auth()->user()->name : '' }}
                            </h5>

                            <p class="text-body fs-14px mb-0">
                                Your available customer details have been pre-filled below.
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
                        <li>{{ $error }}</li>
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
                                    $image =
                                        $item->attributes->get('image') ?:
                                        asset('assets/images/products/product-placeholder.jpg');
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


                            {{-- Delivery / Pickup --}}
                            <div class="d-flex align-items-center">

                                <span id="checkoutShippingLabel">
                                    Shipping:
                                </span>

                                <span class="ms-auto text-body-emphasis fw-semibold" id="checkoutShipping">

                                    Select delivery zone

                                </span>

                            </div>


                            <div id="shippingMessage" class="small mt-3 d-none">
                            </div>

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
                            Choose delivery to your address or collect your order
                            from a Dela Moure pickup location.
                        </p>


                        {{-- ==================================================
                            SHIP / PICKUP SELECTOR
                        ================================================== --}}

                        <div class="mb-8">

                            <label class="mb-4 fs-13px letter-spacing-01 fw-semibold text-uppercase">
                                How would you like to receive your order?
                            </label>


                            <div class="row g-3">

                                {{-- Ship --}}
                                <div class="col-md-6">

                                    <input type="radio" class="btn-check delivery-method-input" name="delivery_method"
                                        id="deliveryShipping" value="shipping" autocomplete="off"
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
                                <div class="col-md-6">

                                    <input type="radio" class="btn-check delivery-method-input" name="delivery_method"
                                        id="deliveryPickup" value="pickup" autocomplete="off"
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

                            </div>

                        </div>


                        {{-- ==================================================
                            CUSTOMER DETAILS
                        ================================================== --}}

                        <div class="mb-7">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                Name
                                <span class="text-danger">*</span>

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


                        {{-- Contact --}}
                        <div class="mb-7">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                Contact Information
                                <span class="text-danger">*</span>

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

                        <div id="shippingFields">

                            {{-- Street --}}
                            <div class="mb-7">

                                <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                    Street Address
                                    <span class="text-danger">*</span>

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
                                            <span class="text-danger">*</span>

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
                                            <span class="text-danger">*</span>

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


                            {{-- Delivery Zone --}}
                            <div class="mb-7">

                                <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                    Delivery Zone
                                    <span class="text-danger">*</span>

                                </label>


                                <select name="shipping_rate_id" id="checkoutShippingZone"
                                    class="form-select shipping-required" data-selected="{{ $selectedShippingRateId }}"
                                    disabled>

                                    <option value="">
                                        Select delivery zone
                                    </option>

                                </select>


                                <small id="zoneHelpText" class="text-muted d-block mt-2">

                                    Select your state first to see available delivery zones.

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

                                    <div id="zoneCoverageAreas" class="d-flex flex-wrap gap-2">
                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- ==================================================
                            PICKUP
                        ================================================== --}}

                        <div id="pickupFields" class="d-none mb-8">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                Pickup Location
                                <span class="text-danger">*</span>

                            </label>


                            @forelse ($pickupLocations as $pickupLocation)
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


                            <div class="alert alert-light border mt-4 mb-0 fs-14px">

                                <i class="fa-solid fa-circle-info me-2"></i>

                                You will be notified when your order is ready for collection.
                                Please do not visit the pickup location until your order
                                has been confirmed ready.

                            </div>

                        </div>


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

                                                You can complete this order as a guest
                                                and create an account later using the
                                                same email address.

                                            </span>

                                        </div>

                                    </div>

                                </div>

                            @endguest


                            {{-- Terms --}}
                            <div class="form-check mb-7">

                                <input class="form-check-input" type="checkbox" name="terms" value="1"
                                    id="checkoutTerms" {{ old('terms') ? 'checked' : '' }} required>


                                <label class="form-check-label" for="checkoutTerms">

                                    I agree to the terms and conditions.

                                </label>

                            </div>


                            {{-- Submit --}}
                            <button type="submit" id="placeOrderBtn"
                                class="btn btn-dark btn-hover-bg-primary btn-hover-border-primary px-11 py-5" disabled>

                                <i class="far fa-lock me-2"></i>

                                Proceed to Secure Payment

                            </button>


                            <div id="checkoutLocationNotice" class="small text-muted mt-3">

                                Select your delivery state and zone to calculate shipping.

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

            let shippingAmount = 0;

            let shippingReady = false;

            let shippingZones = [];


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
                    'Shipping:';

                shippingDisplay.textContent =
                    message;

                totalDisplay.textContent =
                    formatMoney(subtotal);

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

                totalDisplay.textContent =
                    formatMoney(subtotal);

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

                shippingFields.classList.remove(
                    'd-none'
                );

                pickupFields.classList.add(
                    'd-none'
                );


                pickupRadios.forEach(function(input) {
                    input.disabled = true;
                });


                streetAddress.disabled = false;
                streetAddress.required = true;

                countrySelect.disabled = false;
                countrySelect.required = true;

                stateSelect.disabled = false;
                stateSelect.required = true;

                zoneSelect.required = true;


                deliveryNoteLabel.innerHTML =
                    'Delivery Notes <span class="text-body fw-normal">(Optional)</span>';

                deliveryNote.placeholder =
                    'Special delivery instructions...';


                shippingLabel.textContent =
                    'Shipping:';


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
                    zone.areas :
                    [];


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

                if (deliveryMethod() !== 'shipping') {
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
                        'Shipping:';

                    shippingDisplay.textContent =
                        formatMoney(
                            shippingAmount
                        );

                    totalDisplay.textContent =
                        formatMoney(
                            subtotal +
                            shippingAmount
                        );


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

                    totalDisplay.textContent =
                        formatMoney(subtotal);

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
            | Submit Guard
            |--------------------------------------------------------------------------
            */

            checkoutForm.addEventListener(
                'submit',
                function(event) {

                    const method =
                        deliveryMethod();


                    /*
                    |--------------------------------------------------------------------------
                    | Shipping
                    |--------------------------------------------------------------------------
                    */

                    if (method === 'shipping') {

                        if (
                            !countrySelect.value ||
                            !stateSelect.value ||
                            !zoneSelect.value ||
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


            /*
            |--------------------------------------------------------------------------
            | Initial State
            |--------------------------------------------------------------------------
            */

            applyDeliveryMethod();


            if (
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
