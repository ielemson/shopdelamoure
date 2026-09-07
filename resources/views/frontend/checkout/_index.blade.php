<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Check Out | {{ $setting?->website_name }}</title>
    <meta name="keywords"
        content="contact us, customer support, help center, {{ strtolower($setting?->website_name ?? '') }}, phone number, email address, customer service">
    <meta name="description"
        content="Get in touch with {{ $setting?->website_name }}. Contact our customer support team for inquiries, orders, product information, and assistance.">
    <meta name="author" content="{{ $setting?->website_name }}">
    <link rel="icon"
        href="{{ !empty($setting?->favicon) ? asset('storage/' . $setting->favicon) : asset('pages/assets/images/favicon/favicon.png') }}"
        sizes="32x32">
    <link rel="apple-touch-icon"
        href="{{ !empty($setting?->favicon) ? asset('storage/' . $setting->favicon) : asset('pages/assets/images/favicon/favicon.png') }}">
    <meta name="msapplication-TileImage"
        content="{{ !empty($setting?->favicon) ? asset('storage/' . $setting->favicon) : asset('pages/assets/images/favicon/favicon.png') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Contact Us | {{ $setting?->website_name }}">
    <meta property="og:description"
        content="Reach out to {{ $setting?->website_name }} for support, inquiries, and assistance.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $setting?->website_name }}">
    @if (!empty($setting?->logo))
        <meta property="og:image" content="{{ asset('storage/' . $setting->logo) }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Contact Us | {{ $setting?->website_name }}">
    <meta name="twitter:description"
        content="Contact {{ $setting?->website_name }} for product inquiries, customer support, and assistance.">
    @if (!empty($setting?->logo))
        <meta name="twitter:image" content="{{ asset('storage/' . $setting->logo) }}">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('pages/assets/css/vendor/ecicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/countdowntimer.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/slick.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/bootstrap.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('pages/assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/responsive.css') }}" />
    <link rel="stylesheet" id="bg-switcher-css" href="{{ asset('pages/assets/css/backgrounds/bg-4.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>
<style>
</style>

<body class="contact_us_page">
    <div id="ec-overlay">
        <div class="ec-ellipsis">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    @include('frontend.partials.header.pages-header')
    @include('frontend.partials.cart.side-cart')
    @include('frontend.partials.breadcrumb', ['title' => 'Checkout'])

    @php
        use Darryldecode\Cart\Facades\CartFacade as Cart;

        $cartItems = Cart::getContent();
        $subtotal = Cart::getSubTotal();
        $delivery = 0;
        $discount = 0;
        $total = Cart::getTotal() + $delivery - $discount;
    @endphp
    @php
        $profile = auth()->user()?->customerProfile;
    @endphp
    <section class="ec-page-content section-space-p">
        <div class="container">
            @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

            <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm" data-parsley-validate>
                @csrf

                <div class="row">
                    <div class="ec-checkout-leftside col-lg-8 col-md-12">
                        <div class="ec-checkout-content">
                            <div class="ec-checkout-inner">

                                {{-- STEP 1: SHIPPING DETAILS --}}
                                <div class="ec-checkout-wrap margin-bottom-30 padding-bottom-3 checkout-step-content"
                                    id="step-1">
                                    <div class="ec-checkout-block ec-check-bill">
                                        <h3 class="ec-checkout-title">Shipping Details</h3>

                                        <div class="ec-bl-block-content">
                                            <div class="ec-check-bill-form">
                                                <div class="row">

                                                    <div class="col-md-6">
                                                        <span class="ec-bill-wrap">
                                                            <label>First Name*</label>
                                                            <input type="text" name="first_name"
                                                                value="{{ old('first_name', $profile->first_name ?? '') }}"
                                                                placeholder="Enter your first name" required
                                                                data-parsley-required-message="First name is required">
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <span class="ec-bill-wrap">
                                                            <label>Last Name*</label>
                                                            <input type="text" name="last_name"
                                                                value="{{ old('last_name', $profile->last_name ?? '') }}"
                                                                placeholder="Enter your last name" required
                                                                data-parsley-required-message="Last name is required">
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <span class="ec-bill-wrap">
                                                            <label>Email Address*</label>
                                                            <input type="email" name="email"
                                                                value="{{ old('email', auth()->user()->email ?? '') }}"
                                                                placeholder="you@example.com" required
                                                                data-parsley-type="email"
                                                                data-parsley-required-message="Email address is required"
                                                                data-parsley-type-message="Please enter a valid email address">
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <span class="ec-bill-wrap">
                                                            <label>Phone Number*</label>
                                                            <input type="tel" name="phone"
                                                                value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                                                placeholder="08067407355" required
                                                                data-parsley-pattern="^[0-9+\-\s()]+$"
                                                                data-parsley-required-message="Phone number is required"
                                                                data-parsley-pattern-message="Enter a valid phone number">
                                                        </span>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <span class="ec-bill-wrap">
                                                            <label>Delivery Address*</label>
                                                            <input type="text" name="street_address"
                                                                value="{{ old('street_address', $profile->street_address ?? '') }}"
                                                                placeholder="House number and street name" required
                                                                data-parsley-required-message="Street address is required">
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <span class="ec-bill-wrap">
                                                            <label>Country*</label>
                                                            <span class="ec-bl-select-inner">
                                                                <select name="country_id" id="ec-select-country"
                                                                    class="ec-bill-select" required
                                                                    data-parsley-required-message="Please select a country">
                                                                    <option value="">Select Country</option>
                                                                    @foreach ($countries as $country)
                                                                        <option value="{{ $country->id }}"
                                                                            {{ old('country_id', $profile->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                                                            {{ $country->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </span>
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <span class="ec-bill-wrap">
                                                            <label>Region / State*</label>
                                                            <span class="ec-bl-select-inner">
                                                                <select name="state_id" id="ec-select-state"
                                                                    class="ec-bill-select" required
                                                                    data-selected-state="{{ old('state_id', $profile->state_id ?? '') }}"
                                                                    data-parsley-required-message="Please select a state">
                                                                    <option value="">Select Region / State
                                                                    </option>
                                                                </select>
                                                            </span>
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <span class="ec-bill-wrap">
                                                            <label>City*</label>
                                                            <input type="text" name="city" id="ec-select-city"
                                                                class="ec-bill-select" required
                                                                data-selected-city="{{ old('city_id', $profile->city_id ?? '') }}"
                                                                data-parsley-required-message="Please select a city">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <span class="ec-bill-wrap">
                                                            <label>Post Code</label>
                                                            <input type="text" name="postal_code"
                                                                value="{{ old('postal_code', $profile->postal_code ?? '') }}"
                                                                placeholder="Post Code">
                                                        </span>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <span class="ec-bill-wrap">
                                                            <label>Order Notes</label>
                                                            <textarea name="delivery_note" placeholder="Add comments about your order">{{ old('delivery_note') }}</textarea>
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <span class="ec-check-order-btn">
                                        <button type="button" class="btn btn-primary next-step" data-next="2">
                                            Continue to Delivery
                                        </button>
                                    </span>
                                </div>

                                {{-- STEP 2: DELIVERY / PAYMENT METHOD --}}
                                <div class="ec-checkout-wrap margin-bottom-30 checkout-step-content d-none"
                                    id="step-2">

                                    <div class="ec-checkout-block">
                                        <h3 class="ec-checkout-title">Delivery Method</h3>

                                        <div class="ec-bl-block-content">
                                            <p class="ec-del-desc mb-3">
                                                Please select the preferred delivery method for this order.
                                            </p>

                                            <div class="checkout-option-grid">
                                                <label class="checkout-method-card active">
                                                    <input type="radio" name="delivery_method" value="store_pickup"
                                                        checked>
                                                    <div class="checkout-method-content">
                                                        <div>
                                                            <strong>Store Pickup</strong>
                                                            <p>Pick up from our store — Ready in 24 hours</p>
                                                        </div>
                                                        <span class="checkout-price">FREE</span>
                                                    </div>
                                                </label>

                                                <label class="checkout-method-card">
                                                    <input type="radio" name="delivery_method"
                                                        value="doorstep_delivery">
                                                    <div class="checkout-method-content">
                                                        <div>
                                                            <strong>Doorstep Delivery</strong>
                                                            <p>We will contact you with the delivery cost</p>
                                                        </div>
                                                        <span class="checkout-price">At a Cost</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-checkout-block mt-4">
                                        <h3 class="ec-checkout-title">Payment Plan</h3>

                                        <div class="ec-bl-block-content">
                                            <p class="ec-pay-desc mb-3">This order is paid in full now.</p>

                                            <label class="checkout-method-card active full-width">
                                                <input type="radio" name="payment_plan" value="full_payment"
                                                    checked>
                                                <div class="checkout-method-content">
                                                    <div>
                                                        <strong>Pay full amount now</strong>
                                                        <p>The 50% deposit only applies to preorder items.</p>
                                                    </div>
                                                    <span
                                                        class="checkout-price">₦{{ number_format($total, 2) }}</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ec-checkout-block mt-4">
                                        <h3 class="ec-checkout-title">Payment Method</h3>

                                        <div class="ec-bl-block-content">
                                            <p class="ec-pay-desc mb-3">Choose how you would like to pay.</p>

                                            <div class="checkout-option-grid">
                                                <label class="checkout-method-card active">
                                                    <input type="radio" name="payment_method" value="paystack"
                                                        checked>
                                                    <div class="checkout-method-content">
                                                        <div>
                                                            <strong>
                                                                Paystack Payment
                                                                <small class="checkout-badge">Recommended</small>
                                                            </strong>
                                                            <p>Pay securely using card, transfer, USSD, or bank account.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </label>

                                                <label class="checkout-method-card">
                                                    <input type="radio" name="payment_method"
                                                        value="bank_transfer">
                                                    <div class="checkout-method-content">
                                                        <div>
                                                            <strong>Bank Transfer</strong>
                                                            <p>Make payment directly into our company bank account.</p>
                                                        </div>
                                                    </div>
                                                </label>

                                                <label class="checkout-method-card full-width">
                                                    <input type="radio" name="payment_method"
                                                        value="cash_on_delivery">
                                                    <div class="checkout-method-content">
                                                        <div>
                                                            <strong>Cash on Delivery</strong>
                                                            <p>Available only in selected locations.</p>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="ec-check-order-btn d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary prev-step" data-prev="1">
                                            Back
                                        </button>
                                        <button type="button" class="btn btn-primary next-step" data-next="3">
                                            Continue to Payment
                                        </button>
                                    </span>
                                </div>
                                {{-- STEP 3: REVIEW & PAYMENT --}}
                                <div class="ec-checkout-wrap margin-bottom-30 checkout-step-content d-none"
                                    id="step-3">

                                    <div class="ec-checkout-block">
                                        <h3 class="ec-checkout-title">Review & Confirm Order</h3>

                                        <div class="ec-bl-block-content">
                                            <div class="checkout-review-card">

                                                <div class="checkout-review-header">
                                                    <div class="checkout-review-icon">
                                                        <i class="fas fa-lock"></i>
                                                    </div>

                                                    <div>
                                                        <h5>Confirm Your Order</h5>
                                                        <p>Please review your selections before completing your order.
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="checkout-review-summary">
                                                    <div class="review-item">
                                                        <span>Delivery Method</span>
                                                        <strong id="reviewDelivery">Store Pickup</strong>
                                                    </div>

                                                    <div class="review-item">
                                                        <span>Payment Method</span>
                                                        <strong id="reviewPayment">Paystack Payment</strong>
                                                    </div>

                                                    <div class="review-item total">
                                                        <span>Total Amount</span>
                                                        <strong>₦{{ number_format($total, 2) }}</strong>
                                                    </div>
                                                </div>

                                                <div class="checkout-security-notice">
                                                    <i class="fas fa-shield-alt"></i>
                                                    <span>Your order and payment details are handled securely.</span>
                                                </div>

                                                <div class="checkout-terms-box">
                                                    <label class="checkout-terms-label" for="terms">
                                                        <input type="checkbox" id="terms" name="terms"
                                                            value="1" required
                                                            data-parsley-required-message="You must accept the terms and conditions">

                                                        <span class="terms-check-icon">
                                                            <i class="fas fa-check"></i>
                                                        </span>

                                                        <span class="terms-text">
                                                            I confirm that my order details are correct and I agree to
                                                            the
                                                            <a href="#" target="_blank">Terms & Conditions</a>.
                                                        </span>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <span class="ec-check-order-btn d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary prev-step" data-prev="2">
                                            Back
                                        </button>

                                        <button type="submit" class="btn btn-primary btn-lg checkout-complete-btn">
                                            Complete Order
                                        </button>
                                    </span>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- RIGHT SUMMARY --}}
                    <div class="ec-checkout-rightside col-lg-4 col-md-12">
                        <div class="ec-sidebar-wrap">
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title">
                                    <h3 class="ec-sidebar-title">Summary</h3>
                                </div>
                                <div class="ec-sb-block-content">
                                    <div class="ec-checkout-summary">
                                        @foreach ($cartItems as $item)
                                            <div>
                                                <span class="text-left">{{ $item->name }} ×
                                                    {{ $item->quantity }}</span>
                                                <span
                                                    class="text-right">₦{{ number_format($item->price * $item->quantity, 2) }}</span>
                                            </div>
                                        @endforeach
                                        <div>
                                            <span class="text-left">Sub-Total</span>
                                            <span class="text-right">₦{{ number_format($subtotal, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-left">Delivery Charges</span>
                                            <span class="text-right">To be confirmed</span>
                                        </div>
                                        <div>
                                            <span class="text-left">Coupon Discount</span>
                                            <span
                                                class="text-right">₦{{ number_format($couponDiscount ?? 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-left">Apply Coupon</span>
                                            <span class="text-right">
                                                <a href="javascript:void(0)" class="ec-checkout-coupan">Apply
                                                    Coupon</a>
                                            </span>
                                        </div>
                                        <div class="ec-checkout-coupan-content">
                                            <div class="ec-checkout-coupan-form">
                                                <input class="ec-coupan" type="text"
                                                    placeholder="Enter Your Coupon Code" id="coupon_code"
                                                    value="" disabled>
                                                <button class="ec-coupan-btn button btn-primary" type="button"
                                                    disabled>
                                                    Apply
                                                </button>
                                            </div>
                                        </div>
                                        <div class="ec-checkout-summary-total">
                                            <span class="text-left">Total Amount</span>
                                            <span class="text-right">₦{{ number_format($total, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    @include('frontend.partials.page-footer')
    <script src="{{ asset('pages/assets/js/vendor/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/bootstrap.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/jquery-migrate-3.3.0.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/modernizr-3.11.2.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/countdowntimer.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/scrollup.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/jquery.zoom.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/slick.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/infiniteslidev2.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/jquery.sticky-sidebar.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/index.js') }}"></script>
    <script src="{{ asset('pages/assets/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
    @include('frontend.partials.cart_scripts')

    <script>
        $(document).ready(function() {

            function showCheckoutStep(step) {
                $('.checkout-step-content').addClass('d-none');
                $('#step-' + step).removeClass('d-none');

                if (step == 3) {
                    updateReviewSummary();
                }

                $('html, body').animate({
                    scrollTop: $('#checkoutForm').offset().top - 120
                }, 300);
            }

            function updateReviewSummary() {
                let delivery = $('input[name="delivery_method"]:checked')
                    .closest('.checkout-method-card')
                    .find('strong')
                    .first()
                    .text()
                    .trim();

                let payment = $('input[name="payment_method"]:checked')
                    .closest('.checkout-method-card')
                    .find('strong')
                    .first()
                    .text()
                    .replace('Recommended', '')
                    .trim();

                $('#reviewDelivery').text(delivery || 'Not selected');
                $('#reviewPayment').text(payment || 'Not selected');
            }

            function updateContinueButton() {
                let paymentMethod = $('input[name="payment_method"]:checked').val();

                if (paymentMethod === 'paystack') {
                    $('#continuePaymentBtn').text('Pay with Paystack');
                } else if (paymentMethod === 'bank_transfer') {
                    $('#continuePaymentBtn').text('Continue with Bank Transfer');
                } else if (paymentMethod === 'cash_on_delivery') {
                    $('#continuePaymentBtn').text('Confirm Cash on Delivery');
                }
            }

            $('.next-step').on('click', function(e) {
                e.preventDefault();

                let currentStep = $(this).closest('.checkout-step-content');
                currentStep.attr('data-parsley-validate', '');

                let stepParsley = currentStep.parsley();

                if (stepParsley.validate() === true) {
                    showCheckoutStep($(this).data('next'));
                }
            });

            $('.prev-step').on('click', function(e) {
                e.preventDefault();
                showCheckoutStep($(this).data('prev'));
            });

            $('#checkoutForm').on('submit', function(e) {

                console.log('Checkout form submitted');

                if ($(this).parsley().validate() !== true) {
                    e.preventDefault();
                    return false;
                }

            });

            $(document).on('click', '.checkout-method-card', function() {
                let radio = $(this).find('input[type="radio"]');

                radio.prop('checked', true).trigger('change');
            });

            $(document).on('change', '.checkout-method-card input[type="radio"]', function() {
                let name = $(this).attr('name');

                $('input[name="' + name + '"]')
                    .closest('.checkout-method-card')
                    .removeClass('active');

                $(this)
                    .closest('.checkout-method-card')
                    .addClass('active');

                updateContinueButton();
                updateReviewSummary();
            });

            $('#ec-select-country').on('change', function() {
                let countryId = $(this).val();

                $('#ec-select-state').html('<option value="">Loading...</option>');
                $('#ec-select-city').html('<option value="">Select City</option>');

                if (!countryId) {
                    $('#ec-select-state').html('<option value="">Select Region / State</option>');
                    return;
                }

                $.get('/checkout/states/' + countryId, function(states) {
                    $('#ec-select-state').html('<option value="">Select Region / State</option>');

                    $.each(states, function(index, state) {
                        $('#ec-select-state').append(
                            `<option value="${state.id}">${state.name}</option>`
                        );
                    });

                    let selectedState = $('#ec-select-state').data('selected-state');

                    if (selectedState) {
                        $('#ec-select-state').val(selectedState).trigger('change');
                    }
                });
            });

            $('#ec-select-state').on('change', function() {
                let stateId = $(this).val();

                $('#ec-select-city').html('<option value="">Loading...</option>');

                if (!stateId) {
                    $('#ec-select-city').html('<option value="">Select City</option>');
                    return;
                }

                $.get('/checkout/cities/' + stateId, function(cities) {
                    $('#ec-select-city').html('<option value="">Select City</option>');

                    $.each(cities, function(index, city) {
                        $('#ec-select-city').append(
                            `<option value="${city.id}">${city.name}</option>`
                        );
                    });

                    let selectedCity = $('#ec-select-city').data('selected-city');

                    if (selectedCity) {
                        $('#ec-select-city').val(selectedCity);
                    }
                });
            });

            if ($('#ec-select-country').val()) {
                $('#ec-select-country').trigger('change');
            }

            updateContinueButton();
            updateReviewSummary();
        });
    </script>

</body>

</html>
