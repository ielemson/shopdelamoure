@extends('layouts.app')

@section('PageContent')
    <section class="pb-lg-20 pb-16">

        @include('frontend.partials.breadcrumb', [
            'title' => 'Shopping Cart',
            'item' => 'Shopping Cart',
        ])

        @php
            $currency = strtoupper(session('currency', 'NGN'));
            $currencySymbol = $currency === 'USD' ? '$' : '₦';

            $cartCount = \Darryldecode\Cart\Facades\CartFacade::getTotalQuantity();
            $subtotal = \Darryldecode\Cart\Facades\CartFacade::getSubTotal();

            $shipping = 0;
            $discount = 0;
            $total = $subtotal + $shipping - $discount;
        @endphp


        <section class="container" id="cart-page">

            <div class="shopping-cart">

                <h2 class="text-center fs-2 mt-12 mb-13">
                    Shopping Cart
                </h2>


                @if ($cartItems->isNotEmpty())
                    <div class="table-responsive-md pb-8 pb-lg-10">

                        <table class="table border">

                            <thead class="bg-body-secondary">

                                <tr class="fs-15px letter-spacing-01 fw-semibold text-uppercase text-body-emphasis">

                                    <th scope="col" class="fw-semibold border-1 ps-11">
                                        Product
                                    </th>

                                    <th scope="col" class="fw-semibold border-1">
                                        Quantity
                                    </th>

                                    <th colspan="2" class="fw-semibold border-1">
                                        Total
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach ($cartItems as $item)
                                    @php
                                        $image = $item->attributes->get('image');
                                        $slug = $item->attributes->get('slug');
                                        $variantName = $item->attributes->get('variant_name');
                                        $variantOptions = $item->attributes->get('variant_options');

                                        $productUrl = $slug ? route('products.show', $slug) : '#';

                                        $lineTotal = $item->price * $item->quantity;
                                    @endphp


                                    <tr class="position-relative cart-page-row" data-cart-row="{{ $item->id }}">

                                        {{-- Product --}}
                                        <th scope="row" class="pe-5 ps-8 py-7 shop-product">

                                            <div class="d-flex align-items-center">

                                                <div class="me-7">

                                                    <a href="{{ $productUrl }}">

                                                        <img src="{{ $image ? asset($image) : asset('assets/images/products/product-placeholder.jpg') }}"
                                                            width="75" height="100" style="object-fit: cover;"
                                                            alt="{{ $item->name }}">

                                                    </a>

                                                </div>


                                                <div>

                                                    <p class="fw-500 mb-1 text-body-emphasis">

                                                        <a href="{{ $productUrl }}"
                                                            class="text-decoration-none text-reset">

                                                            {{ $item->name }}

                                                        </a>

                                                    </p>


                                                    @if ($variantName)
                                                        <p class="fs-13px text-muted mb-1">
                                                            {{ $variantName }}
                                                        </p>
                                                    @elseif ($variantOptions)
                                                        <p class="fs-13px text-muted mb-1">

                                                            @if (is_array($variantOptions))
                                                                {{ collect($variantOptions)->implode(' / ') }}
                                                            @else
                                                                {{ $variantOptions }}
                                                            @endif

                                                        </p>
                                                    @endif


                                                    <p class="card-text mb-0">

                                                        <span class="fs-15px fw-bold text-body-emphasis">

                                                            {{ $currencySymbol }}{{ number_format($item->price, 2) }}

                                                        </span>

                                                    </p>

                                                </div>

                                            </div>

                                        </th>


                                        {{-- Quantity --}}
                                        <td class="align-middle">

                                            <div class="input-group position-relative shop-quantity"
                                                data-cart-id="{{ $item->id }}">

                                                <a href="#" class="shop-down position-absolute z-index-2">

                                                    <i class="far fa-minus"></i>

                                                </a>


                                                <input name="quantity[]" type="number"
                                                    class="form-control form-control-sm px-10 py-4 fs-6 text-center border-0 cart-page-quantity"
                                                    value="{{ $item->quantity }}" min="1"
                                                    data-cart-id="{{ $item->id }}" required>


                                                <a href="#" class="shop-up position-absolute z-index-2">

                                                    <i class="far fa-plus"></i>

                                                </a>

                                            </div>

                                        </td>


                                        {{-- Line Total --}}
                                        <td class="align-middle">

                                            <p class="mb-0 text-body-emphasis fw-bold cart-line-total">

                                                {{ $currencySymbol }}{{ number_format($lineTotal, 2) }}

                                            </p>

                                        </td>


                                        {{-- Remove --}}
                                        <td class="align-middle text-end pe-8">

                                            <a href="#" class="d-block text-secondary cart-page-remove"
                                                data-cart-id="{{ $item->id }}" title="Remove product">

                                                <i class="fa fa-times"></i>

                                            </a>

                                        </td>

                                    </tr>
                                @endforeach


                                {{-- Cart Actions --}}
                                <tr>

                                    <td class="pt-5 pb-10 position-relative bg-body ps-0 left">

                                        <a href="{{ route('shop') }}" title="Continue Shopping"
                                            class="btn btn-outline-dark me-8 text-nowrap my-5">

                                            Continue Shopping

                                        </a>


                                        <button type="button" id="clear-cart-button"
                                            class="btn btn-link p-0 border-0 border-bottom border-secondary text-decoration-none rounded-0 my-5 fw-semibold">

                                            <i class="fa fa-times me-3"></i>

                                            Clear Shopping Cart

                                        </button>

                                    </td>


                                    <td colspan="3" class="text-end pt-5 pb-10 position-relative bg-body right pe-0">

                                        <button type="button" id="update-cart-button" class="btn btn-outline-dark my-5">

                                            Update Cart

                                        </button>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>


                    {{-- =========================================================
                CART SUMMARY
            ========================================================== --}}

                    <div class="row justify-content-end pt-8 pt-lg-11 pb-16 pb-lg-18">

                        {{-- Shopping Information --}}
                        <div class="col-lg-5 pt-lg-2 pt-10">

                            <h4 class="fs-24 mb-6">
                                Shopping Information
                            </h4>

                            <p class="mb-4 text-muted">
                                Shipping charges and available delivery options
                                will be calculated during checkout.
                            </p>

                            <p class="mb-0 text-muted">
                                Discounts and promotional codes can also be
                                applied at checkout when available.
                            </p>

                        </div>


                        {{-- Cart Summary --}}
                        <div class="col-lg-4 pt-lg-0 pt-11">

                            <div class="card border-0" style="box-shadow: 0 0 10px 0 rgba(0,0,0,0.1)">

                                <div class="card-body px-9 pt-6">

                                    <div class="d-flex align-items-center justify-content-between mb-5">

                                        <span>
                                            Subtotal:
                                        </span>

                                        <span class="d-block ml-auto text-body-emphasis fw-bold" data-cart-subtotal>

                                            {{ $currencySymbol }}{{ number_format($subtotal, 2) }}

                                        </span>

                                    </div>


                                    <div class="d-flex align-items-center justify-content-between mb-5">

                                        <span>
                                            Shipping:
                                        </span>

                                        <span class="d-block ml-auto text-body-emphasis fw-bold">

                                            Calculated at checkout

                                        </span>

                                    </div>


                                    @if ($discount > 0)
                                        <div class="d-flex align-items-center justify-content-between">

                                            <span>
                                                Discount:
                                            </span>

                                            <span class="d-block ml-auto text-body-emphasis fw-bold" data-cart-discount>

                                                -{{ $currencySymbol }}{{ number_format($discount, 2) }}

                                            </span>

                                        </div>
                                    @endif

                                </div>


                                <div class="card-footer bg-transparent px-0 pt-5 pb-7 mx-9">

                                    <div class="d-flex align-items-center justify-content-between fw-bold mb-7">

                                        <span class="text-secondary text-body-emphasis">
                                            Total:
                                        </span>

                                        <span class="d-block ml-auto text-body-emphasis fs-4 fw-bold" data-cart-page-total>

                                            {{ $currencySymbol }}{{ number_format($total, 2) }}

                                        </span>

                                    </div>


                                    <a href="{{ route('checkout.index') }}"
                                        class="btn w-100 btn-dark btn-hover-bg-primary btn-hover-border-primary"
                                        title="Check Out">

                                        Proceed to Checkout

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>
                @else
                    {{-- =========================================================
                EMPTY CART
            ========================================================== --}}

                    <div class="text-center pt-5 pb-18">

                        <div class="mb-6">

                            <svg class="icon icon-shopping-bag-open-light" style="width:65px;height:65px;">

                                <use xlink:href="#icon-shopping-bag-open-light"></use>

                            </svg>

                        </div>


                        <h3 class="fs-4 mb-4">
                            Your shopping cart is empty
                        </h3>


                        <p class="text-muted mb-7">

                            Browse our collection and discover something you love.

                        </p>


                        <a href="{{ route('shop') }}" class="btn btn-dark px-9">

                            Continue Shopping

                        </a>

                    </div>
                @endif

            </div>

        </section>
    @endsection
    {{-- @push('scripts')
        <script>
            async function updateCartPageItem(cartId, quantity, input = null) {

                try {

                    const response = await fetch(
                        "{{ route('cart.update') }}", {
                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',

                                'X-CSRF-TOKEN': document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .content
                            },

                            body: JSON.stringify({
                                id: cartId,
                                quantity: quantity
                            })
                        }
                    );


                    const data = await response.json();


                    if (!response.ok || !data.status) {

                        throw new Error(
                            data.message ||
                            'Unable to update cart.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Update Line Total
                    |--------------------------------------------------------------------------
                    */

                    const row = document.querySelector(
                        `[data-cart-row="${CSS.escape(cartId)}"]`
                    );

                    if (row) {

                        const lineTotal =
                            row.querySelector('.cart-line-total');

                        if (lineTotal) {

                            lineTotal.textContent =
                                formatCartMoney(data.item_total);

                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Update Page Totals
                    |--------------------------------------------------------------------------
                    */

                    updateCartPageTotals(data);


                    /*
                    |--------------------------------------------------------------------------
                    | Update Header + Sidebar
                    |--------------------------------------------------------------------------
                    */

                    updateCartUI(data);


                } catch (error) {

                    notyf.error(
                        error.message ||
                        'Unable to update cart.'
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Restore current server cart if stock validation failed
                    |--------------------------------------------------------------------------
                    */

                    if (input) {
                        window.location.reload();
                    }

                }

            }

            function formatCartMoney(value) {

                const currency = @json($currency);

                const amount =
                    Number(value || 0);


                return new Intl.NumberFormat(
                    currency === 'USD' ?
                    'en-US' :
                    'en-NG', {
                        style: 'currency',
                        currency: currency,
                        minimumFractionDigits: 2
                    }
                ).format(amount);

            }

            function updateCartPageTotals(data) {

                const subtotal =
                    document.querySelector('[data-cart-subtotal]');

                const total =
                    document.querySelector('[data-cart-page-total]');


                if (subtotal) {

                    subtotal.textContent =
                        formatCartMoney(data.subtotal);

                }


                if (total) {

                    total.textContent =
                        formatCartMoney(data.total);

                }

            }
            document.addEventListener('change', function(e) {

                const input = e.target.closest(
                    '#cart-page .cart-page-quantity'
                );

                if (!input) return;


                const wrapper =
                    input.closest('.shop-quantity');


                const cartId =
                    wrapper?.dataset.cartId;


                if (!cartId) return;


                let quantity =
                    parseInt(input.value || 1);


                if (!quantity || quantity < 1) {

                    quantity = 1;

                    input.value = 1;

                }


                updateCartPageItem(
                    cartId,
                    quantity,
                    input
                );

            });

            document.addEventListener('click', async function(e) {

                const button = e.target.closest(
                    '#cart-page .cart-page-remove'
                );

                if (!button) return;

                e.preventDefault();


                const cartId =
                    button.dataset.cartId;


                if (!cartId) return;


                try {

                    const response = await fetch(
                        "{{ route('cart.remove') }}", {
                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',

                                'X-CSRF-TOKEN': document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .content
                            },

                            body: JSON.stringify({
                                id: cartId
                            })
                        }
                    );


                    const data = await response.json();


                    if (!response.ok || !data.status) {

                        throw new Error(
                            data.message ||
                            'Unable to remove product.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Remove Row
                    |--------------------------------------------------------------------------
                    */

                    document
                        .querySelector(
                            `[data-cart-row="${CSS.escape(cartId)}"]`
                        )
                        ?.remove();


                    /*
                    |--------------------------------------------------------------------------
                    | Update UI
                    |--------------------------------------------------------------------------
                    */

                    updateCartPageTotals(data);

                    updateCartUI(data);


                    notyf.success(
                        data.message ||
                        'Product removed from cart.'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Last Product Removed
                    |--------------------------------------------------------------------------
                    */

                    if (Number(data.cart_count) === 0) {

                        window.location.reload();

                    }


                } catch (error) {

                    notyf.error(
                        error.message ||
                        'Unable to remove product.'
                    );

                }

            });

            document
                .getElementById('clear-cart-button')
                ?.addEventListener('click', async function() {

                    try {

                        const response = await fetch(
                            "{{ route('cart.clear') }}", {
                                method: 'POST',

                                headers: {
                                    'Accept': 'application/json',

                                    'X-CSRF-TOKEN': document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        .content
                                }
                            }
                        );


                        const data =
                            await response.json();


                        if (!response.ok || !data.status) {

                            throw new Error(
                                data.message ||
                                'Unable to clear cart.'
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Header Counter
                        |--------------------------------------------------------------------------
                        */

                        document
                            .querySelectorAll('[data-cart-count]')
                            .forEach(element => {

                                element.textContent = 0;

                            });


                        notyf.success(
                            data.message ||
                            'Cart cleared.'
                        );


                        window.location.reload();


                    } catch (error) {

                        notyf.error(
                            error.message ||
                            'Unable to clear cart.'
                        );

                    }

                });
        </script>
    @endpush --}}

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                /*
                |--------------------------------------------------------------------------
                | Notification Helper
                |--------------------------------------------------------------------------
                */
                let cartNotyf = null;

                function notifyCart(type, message) {
                    // Use existing global Notyf instance where available
                    if (window.notyf && typeof window.notyf[type] === 'function') {
                        window.notyf[type](message);
                        return;
                    }

                    // Otherwise create one from the Notyf library
                    if (typeof Notyf !== 'undefined') {
                        if (!cartNotyf) {
                            cartNotyf = new Notyf({
                                duration: 3500,
                                position: {
                                    x: 'right',
                                    y: 'top'
                                },
                                dismissible: true
                            });
                        }

                        cartNotyf[type](message);
                        return;
                    }

                    // Final fallback
                    alert(message);
                }


                /*
                |--------------------------------------------------------------------------
                | Format Money
                |--------------------------------------------------------------------------
                */
                function formatCartMoney(value) {
                    const currency = @json($currency);
                    const amount = Number(value || 0);

                    return new Intl.NumberFormat(
                        currency === 'USD' ? 'en-US' : 'en-NG', {
                            style: 'currency',
                            currency: currency,
                            minimumFractionDigits: 2
                        }
                    ).format(amount);
                }


                /*
                |--------------------------------------------------------------------------
                | Update Cart Page Totals
                |--------------------------------------------------------------------------
                */
                function updateCartPageTotals(data) {
                    const subtotal =
                        document.querySelector('[data-cart-subtotal]');

                    const total =
                        document.querySelector('[data-cart-page-total]');

                    const discount =
                        document.querySelector('[data-cart-discount]');

                    if (
                        subtotal &&
                        data.subtotal !== undefined
                    ) {
                        subtotal.textContent =
                            formatCartMoney(data.subtotal);
                    }

                    if (
                        total &&
                        data.total !== undefined
                    ) {
                        total.textContent =
                            formatCartMoney(data.total);
                    }

                    if (
                        discount &&
                        data.discount !== undefined
                    ) {
                        discount.textContent =
                            '-' + formatCartMoney(data.discount);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Update Single Cart Item
                |--------------------------------------------------------------------------
                */
                async function updateCartPageItem(
                    cartId,
                    quantity,
                    input = null
                ) {
                    const wrapper =
                        input?.closest('.shop-quantity');

                    if (wrapper) {
                        wrapper.dataset.updating = '1';
                    }

                    try {
                        const csrfToken =
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            )?.content;

                        const response = await fetch(
                            "{{ route('cart.update') }}", {
                                method: 'POST',

                                headers: {
                                    'Content-Type': 'application/json',

                                    'Accept': 'application/json',

                                    'X-CSRF-TOKEN': csrfToken
                                },

                                body: JSON.stringify({
                                    id: cartId,
                                    quantity: quantity
                                })
                            }
                        );

                        const data =
                            await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | Validation / Stock Error
                        |--------------------------------------------------------------------------
                        */
                        if (
                            !response.ok ||
                            !data.status
                        ) {
                            throw new Error(
                                data.message ||
                                'Unable to update cart.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Update Line Total
                        |--------------------------------------------------------------------------
                        */
                        const row =
                            document.querySelector(
                                `[data-cart-row="${CSS.escape(String(cartId))}"]`
                            );

                        if (row) {
                            const lineTotal =
                                row.querySelector(
                                    '.cart-line-total'
                                );

                            if (
                                lineTotal &&
                                data.item_total !== undefined
                            ) {
                                lineTotal.textContent =
                                    formatCartMoney(
                                        data.item_total
                                    );
                            }
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Update Summary Totals
                        |--------------------------------------------------------------------------
                        */
                        updateCartPageTotals(data);


                        /*
                        |--------------------------------------------------------------------------
                        | Update Header + Side Cart
                        |--------------------------------------------------------------------------
                        */
                        if (
                            typeof updateCartUI ===
                            'function'
                        ) {
                            updateCartUI(data);
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Notify
                        |--------------------------------------------------------------------------
                        */
                        notifyCart(
                            'success',
                            data.message ||
                            'Shopping cart updated.'
                        );

                        return data;

                    } catch (error) {

                        notifyCart(
                            'error',
                            error.message ||
                            'Unable to update cart.'
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | Server Remains Authority
                        |--------------------------------------------------------------------------
                        */
                        if (input) {
                            window.location.reload();
                        }

                        throw error;

                    } finally {

                        if (wrapper) {
                            delete wrapper.dataset.updating;
                        }
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Quantity + / -
                |--------------------------------------------------------------------------
                |
                | Capture mode is deliberate. The theme may already have .shop-up and
                | .shop-down handlers. We take control here to prevent double increments.
                |
                */
                document.addEventListener(
                    'click',
                    function(e) {

                        const plusButton =
                            e.target.closest(
                                '#cart-page .shop-up'
                            );

                        const minusButton =
                            e.target.closest(
                                '#cart-page .shop-down'
                            );

                        if (
                            !plusButton &&
                            !minusButton
                        ) {
                            return;
                        }

                        e.preventDefault();
                        e.stopImmediatePropagation();


                        const button =
                            plusButton || minusButton;

                        const wrapper =
                            button.closest(
                                '.shop-quantity'
                            );

                        if (!wrapper) return;


                        /*
                        |--------------------------------------------------------------------------
                        | Prevent Multiple Requests
                        |--------------------------------------------------------------------------
                        */
                        if (
                            wrapper.dataset.updating ===
                            '1'
                        ) {
                            return;
                        }


                        const input =
                            wrapper.querySelector(
                                '.cart-page-quantity'
                            );

                        const cartId =
                            wrapper.dataset.cartId;

                        if (
                            !input ||
                            !cartId
                        ) {
                            return;
                        }


                        let quantity =
                            parseInt(
                                input.value || 1
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Increase
                        |--------------------------------------------------------------------------
                        */
                        if (plusButton) {
                            quantity += 1;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Decrease
                        |--------------------------------------------------------------------------
                        */
                        if (minusButton) {
                            if (quantity <= 1) {
                                quantity = 1;
                                return;
                            }

                            quantity -= 1;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Update Input Immediately
                        |--------------------------------------------------------------------------
                        */
                        input.value =
                            quantity;


                        /*
                        |--------------------------------------------------------------------------
                        | Update Server + Prices
                        |--------------------------------------------------------------------------
                        */
                        updateCartPageItem(
                            cartId,
                            quantity,
                            input
                        ).catch(() => {
                            // Error already handled
                        });

                    },
                    true
                );


                /*
                |--------------------------------------------------------------------------
                | Manual Quantity Entry
                |--------------------------------------------------------------------------
                */
                document.addEventListener(
                    'change',
                    function(e) {

                        const input =
                            e.target.closest(
                                '#cart-page .cart-page-quantity'
                            );

                        if (!input) return;


                        const wrapper =
                            input.closest(
                                '.shop-quantity'
                            );

                        const cartId =
                            wrapper?.dataset.cartId;

                        if (!cartId) return;


                        let quantity =
                            parseInt(
                                input.value || 1
                            );


                        if (
                            !quantity ||
                            quantity < 1
                        ) {
                            quantity = 1;
                            input.value = 1;
                        }


                        updateCartPageItem(
                            cartId,
                            quantity,
                            input
                        ).catch(() => {
                            // Error already handled
                        });

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Update Cart Button
                |--------------------------------------------------------------------------
                */
                document
                    .getElementById(
                        'update-cart-button'
                    )
                    ?.addEventListener(
                        'click',
                        async function() {

                            const inputs =
                                document.querySelectorAll(
                                    '#cart-page .cart-page-quantity'
                                );

                            if (!inputs.length) {
                                return;
                            }


                            this.disabled = true;

                            const originalText =
                                this.textContent;

                            this.textContent =
                                'Updating...';


                            try {

                                for (
                                    const input of inputs
                                ) {

                                    const wrapper =
                                        input.closest(
                                            '.shop-quantity'
                                        );

                                    const cartId =
                                        wrapper?.dataset.cartId;

                                    if (!cartId) {
                                        continue;
                                    }


                                    let quantity =
                                        parseInt(
                                            input.value || 1
                                        );


                                    if (
                                        !quantity ||
                                        quantity < 1
                                    ) {
                                        quantity = 1;
                                        input.value = 1;
                                    }


                                    await updateCartPageItem(
                                        cartId,
                                        quantity,
                                        input
                                    );
                                }

                            } catch (error) {
                                // Already notified
                            } finally {

                                this.disabled =
                                    false;

                                this.textContent =
                                    originalText;
                            }
                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | Remove Item
                |--------------------------------------------------------------------------
                */
                document.addEventListener(
                    'click',
                    async function(e) {

                        const button =
                            e.target.closest(
                                '#cart-page .cart-page-remove'
                            );

                        if (!button) return;


                        e.preventDefault();


                        const cartId =
                            button.dataset.cartId;

                        if (!cartId) return;


                        button.style.pointerEvents =
                            'none';

                        button.style.opacity =
                            '0.5';


                        try {

                            const csrfToken =
                                document.querySelector(
                                    'meta[name="csrf-token"]'
                                )?.content;


                            const response =
                                await fetch(
                                    "{{ route('cart.remove') }}", {
                                        method: 'POST',

                                        headers: {
                                            'Content-Type': 'application/json',

                                            'Accept': 'application/json',

                                            'X-CSRF-TOKEN': csrfToken
                                        },

                                        body: JSON.stringify({
                                            id: cartId
                                        })
                                    }
                                );


                            const data =
                                await response.json();


                            if (
                                !response.ok ||
                                !data.status
                            ) {
                                throw new Error(
                                    data.message ||
                                    'Unable to remove product.'
                                );
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Remove Row
                            |--------------------------------------------------------------------------
                            */
                            document
                                .querySelector(
                                    `[data-cart-row="${CSS.escape(String(cartId))}"]`
                                )
                                ?.remove();


                            /*
                            |--------------------------------------------------------------------------
                            | Totals
                            |--------------------------------------------------------------------------
                            */
                            updateCartPageTotals(
                                data
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | Header + Sidebar
                            |--------------------------------------------------------------------------
                            */
                            if (
                                typeof updateCartUI ===
                                'function'
                            ) {
                                updateCartUI(data);
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Notify
                            |--------------------------------------------------------------------------
                            */
                            notifyCart(
                                'success',
                                data.message ||
                                'Product removed from cart.'
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | Empty Cart
                            |--------------------------------------------------------------------------
                            */
                            if (
                                Number(
                                    data.cart_count
                                ) === 0
                            ) {

                                window.location.reload();
                            }

                        } catch (error) {

                            button.style.pointerEvents =
                                '';

                            button.style.opacity =
                                '';


                            notifyCart(
                                'error',
                                error.message ||
                                'Unable to remove product.'
                            );
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Clear Cart
                |--------------------------------------------------------------------------
                */
                document
                    .getElementById(
                        'clear-cart-button'
                    )
                    ?.addEventListener(
                        'click',
                        async function() {

                            this.disabled =
                                true;


                            try {

                                const csrfToken =
                                    document.querySelector(
                                        'meta[name="csrf-token"]'
                                    )?.content;


                                const response =
                                    await fetch(
                                        "{{ route('cart.clear') }}", {
                                            method: 'POST',

                                            headers: {
                                                'Accept': 'application/json',

                                                'X-CSRF-TOKEN': csrfToken
                                            }
                                        }
                                    );


                                const data =
                                    await response.json();


                                if (
                                    !response.ok ||
                                    !data.status
                                ) {

                                    throw new Error(
                                        data.message ||
                                        'Unable to clear cart.'
                                    );
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | Header Counter
                                |--------------------------------------------------------------------------
                                */
                                document
                                    .querySelectorAll(
                                        '[data-cart-count]'
                                    )
                                    .forEach(
                                        element => {
                                            element.textContent =
                                                0;
                                        }
                                    );


                                /*
                                |--------------------------------------------------------------------------
                                | Global Cart UI
                                |--------------------------------------------------------------------------
                                */
                                if (
                                    typeof updateCartUI ===
                                    'function'
                                ) {
                                    updateCartUI(data);
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | Notify
                                |--------------------------------------------------------------------------
                                */
                                notifyCart(
                                    'success',
                                    data.message ||
                                    'Shopping cart cleared.'
                                );


                                /*
                                |--------------------------------------------------------------------------
                                | Allow Notification To Render Before Page Change
                                |--------------------------------------------------------------------------
                                */
                                setTimeout(
                                    function() {
                                        window.location.reload();
                                    },
                                    500
                                );

                            } catch (error) {

                                this.disabled =
                                    false;


                                notifyCart(
                                    'error',
                                    error.message ||
                                    'Unable to clear cart.'
                                );
                            }
                        }
                    );

            });
        </script>
    @endpush
