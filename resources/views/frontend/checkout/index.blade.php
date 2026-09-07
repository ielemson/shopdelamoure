@extends('layouts.app')

@section('meta_title', 'Checkout | Dela Moure Luxury Fragrances')

@section('meta_description', 'Complete your Dela Moure order securely. Review your cart, enter your delivery details and
    choose your preferred payment or WhatsApp ordering option.')

    {{-- @section('meta_image', asset('assets/images/others/social-share.jpg')) --}}

@section('PageContent')

    @include('frontend.partials.breadcrumb', [
        'title' => 'Checkout',
        'item' => 'Checkout',
    ])

    @php
        $currency = strtoupper(session('currency', 'NGN'));
        $currencySymbol = $currency === 'USD' ? '$' : '₦';

        $cart = \Darryldecode\Cart\Facades\CartFacade::getContent();
        $subtotal = \Darryldecode\Cart\Facades\CartFacade::getSubTotal();

        $shipping = 0;
        $total = $subtotal + $shipping;
    @endphp

    <section class="container pb-14 pb-lg-19">

        <div class="text-center mb-10">
            <h2 class="mb-3">Checkout</h2>
            <p class="text-body mb-0">
                Complete your delivery information and choose how you would like to order.
            </p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mb-8">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
            @csrf

            <div class="row">

                {{-- Order Summary --}}
                <div class="col-lg-4 pb-lg-0 pb-14 order-lg-last">

                    <div class="card border-0 rounded-0 shadow">

                        <div class="card-header px-0 mx-8 bg-transparent py-8">
                            <h4 class="fs-4 mb-8">Order Summary</h4>

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

                                <p class="text-body">Your cart is empty.</p>
                            @endforelse

                        </div>

                        <div class="card-body px-8 py-7">

                            <div class="d-flex align-items-center mb-3">
                                <span>Subtotal:</span>

                                <span class="ms-auto text-body-emphasis fw-semibold">
                                    {{ $currencySymbol }}{{ number_format($subtotal, 2) }}
                                </span>
                            </div>

                            <div class="d-flex align-items-center">
                                <span>Shipping:</span>

                                <span class="ms-auto text-body-emphasis fw-semibold">
                                    {{ $shipping > 0 ? $currencySymbol . number_format($shipping, 2) : 'Calculated later' }}
                                </span>
                            </div>

                        </div>

                        <div class="card-footer bg-transparent py-6 px-0 mx-8">

                            <div class="d-flex align-items-center fw-bold">

                                <span class="text-body-emphasis">
                                    Total
                                </span>

                                <span class="ms-auto text-body-emphasis fs-4">
                                    {{ $currencySymbol }}{{ number_format($total, 2) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Checkout Form --}}
                <div class="col-lg-8 order-lg-first pe-xl-20 pe-lg-6">

                    <div class="checkout">

                        <h4 class="fs-4 mb-8">
                            Shipping Information
                        </h4>

                        {{-- Name --}}
                        <div class="mb-7">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">
                                Name
                            </label>

                            <div class="row">

                                <div class="col-md-6 mb-md-0 mb-7">
                                    <input type="text" class="form-control" name="first_name"
                                        value="{{ old('first_name') }}" placeholder="First Name" required>
                                </div>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="last_name"
                                        value="{{ old('last_name') }}" placeholder="Last Name" required>
                                </div>

                            </div>
                        </div>


                        {{-- Contact --}}
                        <div class="mb-7">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">
                                Contact Information
                            </label>

                            <div class="row">

                                <div class="col-md-6 mb-md-0 mb-7">
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                                        placeholder="Email Address" required>
                                </div>

                                <div class="col-md-6">
                                    <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}"
                                        placeholder="Phone Number" required>
                                </div>

                            </div>
                        </div>


                        {{-- Address --}}
                        <div class="mb-7">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">
                                Street Address
                            </label>

                            <input type="text" class="form-control" name="address" value="{{ old('address') }}"
                                placeholder="House number and street name" required>

                        </div>


                        {{-- City / State --}}
                        <div class="mb-7">

                            <div class="row">

                                <div class="col-md-6 mb-md-0 mb-7">

                                    <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">
                                        City
                                    </label>

                                    <input type="text" class="form-control" name="city" value="{{ old('city') }}"
                                        required>

                                </div>

                                <div class="col-md-6">

                                    <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">
                                        State
                                    </label>

                                    <input type="text" class="form-control" name="state" value="{{ old('state') }}"
                                        required>

                                </div>

                            </div>
                        </div>


                        {{-- Country --}}
                        <div class="mb-7">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">
                                Country
                            </label>

                            <select name="country" class="form-select" required>
                                <option value="">Select Country</option>

                                <option value="Nigeria" {{ old('country') === 'Nigeria' ? 'selected' : '' }}>
                                    Nigeria
                                </option>
                            </select>

                        </div>


                        {{-- Order Note --}}
                        <div class="mb-10">

                            <label class="mb-5 fs-13px letter-spacing-01 fw-semibold text-uppercase">
                                Order Notes
                                <span class="text-body fw-normal">
                                    (Optional)
                                </span>
                            </label>

                            <textarea name="order_note" class="form-control" rows="4"
                                placeholder="Special instructions about your order...">{{ old('order_note') }}</textarea>

                        </div>


                        {{-- Payment Method --}}
                        <div class="checkout">

                            <h4 class="fs-4 mb-3">
                                How would you like to order?
                            </h4>

                            <p class="text-body mb-7">
                                Choose one of the options below to complete your order.
                            </p>


                            <div class="row g-4 mb-7">

                                {{-- WhatsApp --}}
                                <div class="col-md-6">

                                    <label class="payment-option d-block h-100">

                                        <input type="radio" name="payment_method" value="whatsapp"
                                            class="payment-method-input d-none" required>

                                        <span class="payment-method-card d-block border p-6 h-100">

                                            <span class="d-flex align-items-center mb-3">

                                                <i class="fab fa-whatsapp fs-2 me-4"></i>

                                                <strong class="fs-5">
                                                    Order via WhatsApp
                                                </strong>

                                            </span>

                                            <span class="text-body fs-14px d-block">
                                                Send your order directly to Delamoure via WhatsApp and continue with our
                                                sales team.
                                            </span>

                                        </span>

                                    </label>

                                </div>


                                {{-- Paystack --}}
                                <div class="col-md-6">

                                    <label class="payment-option d-block h-100">

                                        <input type="radio" name="payment_method" value="paystack"
                                            class="payment-method-input d-none" required>

                                        <span class="payment-method-card d-block border p-6 h-100">

                                            <span class="d-flex align-items-center mb-3">

                                                <i class="far fa-credit-card fs-2 me-4"></i>

                                                <strong class="fs-5">
                                                    Pay with Paystack
                                                </strong>

                                            </span>

                                            <span class="text-body fs-14px d-block">
                                                Pay securely online using Paystack.
                                            </span>

                                        </span>

                                    </label>

                                </div>

                            </div>


                            {{-- WhatsApp notice --}}
                            <div id="whatsappPaymentInfo" class="alert alert-light border mb-7 d-none">

                                <div class="d-flex">

                                    <i class="fab fa-whatsapp fs-3 me-4 mt-1"></i>

                                    <div>
                                        <strong>Order via WhatsApp</strong>

                                        <p class="mb-0 mt-1">
                                            Your order details will be prepared and you will
                                            be redirected to WhatsApp to complete the order.
                                        </p>
                                    </div>

                                </div>

                            </div>


                            {{-- Paystack notice --}}
                            <div id="paystackPaymentInfo" class="alert alert-light border mb-7 d-none">

                                <div class="d-flex">

                                    <i class="far fa-shield-check fs-3 me-4 mt-1"></i>

                                    <div>
                                        <strong>Secure Paystack Payment</strong>

                                        <p class="mb-0 mt-1">
                                            After placing your order, you will be redirected
                                            to Paystack's secure checkout page to complete payment.
                                        </p>
                                    </div>

                                </div>

                            </div>


                            <button type="submit" id="placeOrderBtn"
                                class="btn btn-dark btn-hover-bg-primary btn-hover-border-primary px-11 py-5" disabled>
                                Select Order Method
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </section>

    <style>
        .payment-method-card {
            cursor: pointer;
            transition: all .2s ease;
        }

        .payment-method-card:hover {
            border-color: var(--bs-primary) !important;
        }

        .payment-method-input:checked+.payment-method-card {
            border-color: var(--bs-primary) !important;
            box-shadow: 0 0 0 1px var(--bs-primary);
        }
    </style>

@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const methods = document.querySelectorAll('.payment-method-input');
            const button = document.getElementById('placeOrderBtn');

            const whatsappInfo = document.getElementById('whatsappPaymentInfo');
            const paystackInfo = document.getElementById('paystackPaymentInfo');

            methods.forEach(method => {

                method.addEventListener('change', function() {

                    whatsappInfo.classList.add('d-none');
                    paystackInfo.classList.add('d-none');

                    button.disabled = false;

                    if (this.value === 'whatsapp') {

                        whatsappInfo.classList.remove('d-none');

                        button.innerHTML =
                            '<i class="fab fa-whatsapp me-2"></i> Continue with WhatsApp';

                    }

                    if (this.value === 'paystack') {

                        paystackInfo.classList.remove('d-none');

                        button.innerHTML =
                            '<i class="far fa-lock me-2"></i> Proceed to Secure Payment';

                    }

                });

            });

        });
    </script>
@endpush
