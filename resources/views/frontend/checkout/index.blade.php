@extends('layouts.app')

@section('meta_title', 'Checkout | Dela Moure Luxury Fragrances')

@section('meta_description', 'Complete your Dela Moure order securely. Review your cart, enter your delivery details and
    pay securely with Paystack.')

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
        | Previously Selected Values
        |--------------------------------------------------------------------------
        */

        $selectedCountryId = old(
            'country_id',
            optional($defaultAddress)->country_id ?? optional($countries->first())->id,
        );

        $selectedStateId = old('state_id', optional($defaultAddress)->state_id);

        $selectedShippingRateId = old('shipping_rate_id');
    @endphp


    <section class="container pb-14 pb-lg-19">

        <div class="text-center mb-10">

            <h2 class="mb-3">
                Checkout
            </h2>

            <p class="text-body mb-0">
                Complete your delivery information and proceed to secure payment.
            </p>

        </div>


        {{-- ==========================================================
        CUSTOMER / GUEST CHECKOUT STATUS
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

                                No account is required to complete your purchase.
                                Enter your delivery details below and proceed securely to payment.

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

                                You are checking out with your Dela Moure account.
                                Your available customer details have been pre-filled below.

                            </p>

                        </div>

                    </div>

                </div>

            @endguest

        </div>


        {{-- VALIDATION ERRORS --}}
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


        {{-- SESSION ERROR --}}
        @if (session('error'))
            <div class="alert alert-danger mb-8">

                {{ session('error') }}

            </div>
        @endif


        {{-- SESSION SUCCESS --}}
        @if (session('success'))
            <div class="alert alert-success mb-8">

                {{ session('success') }}

            </div>
        @endif



        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">

            @csrf


            {{-- PAYSTACK ONLY --}}
            <input type="hidden" name="payment_method" value="paystack">


            {{-- STANDARD DELIVERY --}}
            <input type="hidden" name="delivery_method" value="standard">



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


                            {{-- SUBTOTAL --}}
                            <div class="d-flex align-items-center mb-3">

                                <span>
                                    Subtotal:
                                </span>

                                <span class="ms-auto text-body-emphasis fw-semibold" id="checkoutSubtotal">

                                    {{ $currencySymbol }}{{ number_format($subtotal, 2) }}

                                </span>

                            </div>


                            {{-- SHIPPING --}}
                            <div class="d-flex align-items-center">

                                <span>
                                    Shipping:
                                </span>

                                <span class="ms-auto text-body-emphasis fw-semibold" id="checkoutShipping">

                                    Select delivery zone

                                </span>

                            </div>


                            {{-- SHIPPING MESSAGE --}}
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
                SHIPPING INFORMATION
                ========================================================== --}}

                <div class="col-lg-8 order-lg-first pe-xl-20 pe-lg-6">

                    <div class="checkout">

                        <h4 class="fs-4 mb-2">
                            Shipping Information
                        </h4>

                        <p class="text-body fs-14px mb-8">

                            Please provide the details required to deliver your order.

                        </p>



                        {{-- NAME --}}
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



                        {{-- CONTACT --}}
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

                                    @guest

                                        <small class="text-muted d-block mt-2">

                                            Your order confirmation will be associated with this email address.

                                        </small>

                                    @endguest

                                </div>


                                <div class="col-md-6">

                                    <input type="tel" class="form-control" name="phone"
                                        value="{{ old('phone', optional($defaultAddress)->phone) }}"
                                        placeholder="Phone Number" autocomplete="tel" required>

                                </div>

                            </div>

                        </div>



                        {{-- STREET ADDRESS --}}
                        <div class="mb-7">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                Street Address

                                <span class="text-danger">*</span>

                            </label>


                            <input type="text" class="form-control" name="street_address"
                                value="{{ old('street_address', optional($defaultAddress)->street_address) }}"
                                placeholder="House number and street name" autocomplete="street-address" required>

                        </div>



                        <div class="row">

                            <div class="col-md-6">

                                {{-- COUNTRY --}}
                                <div class="mb-7">

                                    <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                        Country

                                        <span class="text-danger">*</span>

                                    </label>


                                    <select name="country_id" id="checkoutCountry" class="form-select"
                                        autocomplete="country" required>

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


                            <div class="col-md-6">

                                {{-- STATE --}}
                                <div class="mb-7">

                                    <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                        State

                                        <span class="text-danger">*</span>

                                    </label>


                                    <select name="state_id" id="checkoutState" class="form-select"
                                        data-selected="{{ $selectedStateId }}" autocomplete="address-level1" required>

                                        <option value="">
                                            Select State
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>



                        {{-- ==================================================
                        DELIVERY ZONE
                        ================================================== --}}

                        <div class="mb-7">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                Delivery Zone

                                <span class="text-danger">*</span>

                            </label>


                            <select name="shipping_rate_id" id="checkoutShippingZone" class="form-select"
                                data-selected="{{ $selectedShippingRateId }}" disabled required>

                                <option value="">
                                    Select delivery zone
                                </option>

                            </select>


                            <small id="zoneHelpText" class="text-muted d-block mt-2">

                                Select your state first to see available delivery zones.

                            </small>



                            {{-- COVERED AREAS --}}
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



                        {{-- DELIVERY NOTE --}}
                        <div class="mb-10">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">

                                Delivery Notes

                                <span class="text-body fw-normal">
                                    (Optional)
                                </span>

                            </label>


                            <textarea name="delivery_note" class="form-control" rows="4" placeholder="Special delivery instructions...">{{ old('delivery_note', optional($defaultAddress)->delivery_note) }}</textarea>

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

                                    You will be redirected to Paystack's secure payment page
                                    after your order has been created.

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

                                                You can complete this order as a guest.
                                                You will also be able to create an account later
                                                using the same email address.

                                            </span>

                                        </div>

                                    </div>

                                </div>

                            @endguest



                            {{-- TERMS --}}
                            <div class="form-check mb-7">

                                <input class="form-check-input" type="checkbox" name="terms" value="1"
                                    id="checkoutTerms" required>


                                <label class="form-check-label" for="checkoutTerms">

                                    I agree to the terms and conditions.

                                </label>

                            </div>



                            {{-- SUBMIT --}}
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
        /*
            |--------------------------------------------------------------------------
            | Checkout Customer / Guest Status
            |--------------------------------------------------------------------------
            */

        .checkout-customer-status>div {
            border-color: rgba(0, 0, 0, .1) !important;
        }


        /*
            |--------------------------------------------------------------------------
            | Payment Method
            |--------------------------------------------------------------------------
            */

        .payment-method-card {
            transition: all .2s ease;
        }


        /*
            |--------------------------------------------------------------------------
            | Guest Checkout Note
            |--------------------------------------------------------------------------
            */

        .guest-checkout-note {
            background: #faf9f7;
            border-color: rgba(0, 0, 0, .08) !important;
        }


        /*
            |--------------------------------------------------------------------------
            | Shipping Zone Areas
            |--------------------------------------------------------------------------
            */

        #zoneCoverageAreas .zone-area-badge {
            display: inline-block;
            padding: 0.35rem 0.65rem;
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

            const countrySelect =
                document.getElementById('checkoutCountry');

            const stateSelect =
                document.getElementById('checkoutState');

            const zoneSelect =
                document.getElementById('checkoutShippingZone');


            const zoneHelpText =
                document.getElementById('zoneHelpText');

            const zoneCoverageBox =
                document.getElementById('zoneCoverageBox');

            const zoneCoverageTitle =
                document.getElementById('zoneCoverageTitle');

            const zoneCoverageAreas =
                document.getElementById('zoneCoverageAreas');


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

            const checkoutForm =
                document.getElementById('checkoutForm');


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
                Number(
                    @json((float) $subtotal)
                );


            /*
            |--------------------------------------------------------------------------
            | Previously Selected Values
            |--------------------------------------------------------------------------
            */

            const selectedState =
                stateSelect.dataset.selected;

            const selectedShippingRate =
                zoneSelect.dataset.selected;


            /*
            |--------------------------------------------------------------------------
            | Runtime Data
            |--------------------------------------------------------------------------
            */

            let shippingAmount = 0;

            let shippingReady = false;

            let shippingZones = [];


            /*
            |--------------------------------------------------------------------------
            | URLs
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
            | Currency Formatter
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
            | Reset Coverage
            |--------------------------------------------------------------------------
            */

            function resetCoverage() {

                zoneCoverageBox.classList.add(
                    'd-none'
                );

                zoneCoverageTitle.textContent =
                    'Delivery Zone';

                zoneCoverageAreas.innerHTML =
                    '';

            }


            /*
            |--------------------------------------------------------------------------
            | Reset Shipping
            |--------------------------------------------------------------------------
            */

            function resetShipping(
                message = 'Select delivery zone'
            ) {

                shippingAmount = 0;

                shippingReady = false;


                shippingDisplay.textContent =
                    message;


                totalDisplay.textContent =
                    formatMoney(
                        subtotal
                    );


                placeOrderBtn.disabled =
                    true;


                locationNotice.textContent =
                    'Select your delivery state and zone to calculate shipping.';


                shippingMessage.classList.add(
                    'd-none'
                );

                shippingMessage.classList.remove(
                    'text-danger'
                );

                shippingMessage.textContent =
                    '';

            }


            /*
            |--------------------------------------------------------------------------
            | Reset Shipping Zones
            |--------------------------------------------------------------------------
            */

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
            | Load States
            |--------------------------------------------------------------------------
            */

            async function loadStates(
                countryId,
                selectedStateId = null
            ) {

                stateSelect.disabled =
                    true;


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


                    states.forEach(
                        state => {

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

                        }
                    );


                    stateSelect.disabled =
                        false;


                    /*
                    |--------------------------------------------------------------------------
                    | Restore Existing State
                    |--------------------------------------------------------------------------
                    */

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
                        false;


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


                    /*
                    |--------------------------------------------------------------------------
                    | No Delivery Zones
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !Array.isArray(
                            shippingZones
                        ) ||
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


                    /*
                    |--------------------------------------------------------------------------
                    | Populate Delivery Zones
                    |--------------------------------------------------------------------------
                    */

                    shippingZones.forEach(
                        zone => {

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

                        }
                    );


                    zoneSelect.disabled =
                        false;


                    zoneHelpText.textContent =
                        'Choose the zone that covers your delivery location.';


                    /*
                    |--------------------------------------------------------------------------
                    | Restore Zone After Validation Error
                    |--------------------------------------------------------------------------
                    */

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
            | Display Zone Coverage
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

                    areas.forEach(
                        area => {

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

                        }
                    );

                }


                zoneCoverageBox.classList.remove(
                    'd-none'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Load Selected Shipping Rate
            |--------------------------------------------------------------------------
            */

            async function loadShippingRate(
                shippingRateId
            ) {

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


                shippingMessage.classList.add(
                    'd-none'
                );


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


                    /*
                    |--------------------------------------------------------------------------
                    | Refresh Coverage
                    |--------------------------------------------------------------------------
                    */

                    if (
                        data.zone_name
                    ) {

                        zoneCoverageTitle.textContent =
                            data.zone_name;

                    }


                    if (
                        Array.isArray(
                            data.areas
                        )
                    ) {

                        zoneCoverageAreas.innerHTML =
                            '';


                        data.areas.forEach(
                            area => {

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

                            }
                        );


                        zoneCoverageBox.classList.remove(
                            'd-none'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | NGN Shipping
                    |--------------------------------------------------------------------------
                    */

                    if (
                        currency === 'NGN'
                    ) {

                        shippingAmount =
                            Number(
                                data.shipping_cost
                            );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | USD Shipping
                    |--------------------------------------------------------------------------
                    */
                    else if (
                        currency === 'USD'
                    ) {

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


                    /*
                    |--------------------------------------------------------------------------
                    | Validate Amount
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !Number.isFinite(
                            shippingAmount
                        )
                    ) {

                        throw new Error(
                            'Invalid shipping amount.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Update Summary
                    |--------------------------------------------------------------------------
                    */

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

                    shippingAmount =
                        0;


                    shippingReady =
                        false;


                    shippingDisplay.innerHTML =
                        '<span class="text-danger">Unavailable</span>';


                    totalDisplay.textContent =
                        formatMoney(
                            subtotal
                        );


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
            | Country Change
            |--------------------------------------------------------------------------
            */

            countrySelect.addEventListener(
                'change',
                function() {

                    resetZones();


                    loadStates(
                        this.value
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | State Change
            |--------------------------------------------------------------------------
            */

            stateSelect.addEventListener(
                'change',
                async function() {

                    const stateId =
                        this.value;


                    resetZones();


                    if (!stateId) {

                        return;

                    }


                    await loadShippingZones(
                        stateId
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Delivery Zone Change
            |--------------------------------------------------------------------------
            */

            zoneSelect.addEventListener(
                'change',
                async function() {

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
            | Prevent Checkout Without Shipping
            |--------------------------------------------------------------------------
            |
            | This checks shipping only.
            |
            | There is intentionally NO authentication check here.
            | Both guests and logged-in customers may proceed.
            |
            */

            checkoutForm.addEventListener(
                'submit',
                function(event) {

                    if (
                        !zoneSelect.value ||
                        !shippingReady
                    ) {

                        event.preventDefault();


                        shippingMessage.textContent =
                            'Please select your delivery zone and allow shipping to be calculated.';


                        shippingMessage.classList.remove(
                            'd-none'
                        );


                        shippingMessage.classList.add(
                            'text-danger'
                        );


                        locationNotice.textContent =
                            'Select a valid delivery zone before proceeding to payment.';

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Initial Country
            |--------------------------------------------------------------------------
            */

            if (
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
