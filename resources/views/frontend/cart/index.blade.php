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
            $subtotal = \Darryldecode\Cart\Facades\CartFacade::getSubTotal(false);
            $hasWholesale = $cartItems->contains(fn($item) => $item->attributes->get('purchase_type') === 'wholesale');
            $cartError = session('error');
            $shipping = 0;
            $discount = 0;
            $total = $subtotal + $shipping - $discount;
        @endphp
        <section class="container" id="cart-page">
            <div class="shopping-cart">
                <h2 class="text-center fs-2 mt-12 mb-13">
                    Shopping Cart
                </h2>
                <div id="cart-page-feedback" class="alert alert-danger {{ $cartError ? '' : 'd-none' }}" role="alert"
                    tabindex="-1">{{ $cartError }}</div>
                @if ($hasWholesale)
                    <div class="alert alert-info">
                        Wholesale orders are priced in NGN. Quantity changes automatically update volume prices.
                        Retail quantities do not count toward wholesale minimums or price tiers.
                    </div>
                @endif
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
                                        $isWholesale = $item->attributes->get('purchase_type') === 'wholesale';
                                        $minimum = max(1, (int) $item->attributes->get('wholesale_min_quantity', 1));
                                        $mixed =
                                            $isWholesale &&
                                            (bool) $item->attributes->get('wholesale_allow_mixed_variants');
                                        $groupQuantity = $cartItems
                                            ->filter(
                                                fn($line) => $line->attributes->get('purchase_type') === 'wholesale' &&
                                                    (int) $line->attributes->get('product_id') ===
                                                        (int) $item->attributes->get('product_id'),
                                            )
                                            ->sum('quantity');
                                        $inputMinimum = $isWholesale
                                            ? ($mixed
                                                ? max(1, $minimum - ($groupQuantity - (int) $item->quantity))
                                                : $minimum)
                                            : 1;
                                        $lineCurrency = strtoupper(
                                            (string) $item->attributes->get('currency', $currency),
                                        );
                                        $lineSymbol = $lineCurrency === 'USD' ? '$' : '₦';
                                        $productUrl = $slug
                                            ? ($isWholesale
                                                ? url('/wholesale/' . $slug)
                                                : route('products.show', $slug))
                                            : '#';
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
                                                    @if ($isWholesale)
                                                        <span class="badge bg-primary mb-2">Wholesale</span>
                                                        <p class="fs-13px text-muted mb-1">
                                                            Minimum {{ $minimum }} units
                                                            {{ $mixed ? 'across eligible options of this product' : ($item->attributes->get('variant_id') ? 'per option' : 'of this product') }}.
                                                        </p>
                                                        @if ($mixed)
                                                            <p class="fs-13px text-muted mb-1">Combined wholesale quantity:
                                                                {{ $groupQuantity }}.</p>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary mb-2">Retail</span>
                                                    @endif
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
                                                            {{ $lineSymbol }}{{ number_format($item->price, 2) }} <small
                                                                class="text-muted">{{ $lineCurrency }} / unit</small>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </th>
                                        {{-- Quantity --}}
                                        <td class="align-middle">
                                            <div class="input-group position-relative shop-quantity"
                                                data-cart-id="{{ $item->id }}">
                                                <a href="#" class="shop-down position-absolute z-index-2"
                                                    aria-label="Decrease quantity">
                                                    <i class="far fa-minus"></i>
                                                </a>
                                                <input name="quantity[]" type="number"
                                                    class="form-control form-control-sm px-10 py-4 fs-6 text-center border-0 cart-page-quantity"
                                                    value="{{ $item->quantity }}" min="{{ $inputMinimum }}" step="1"
                                                    max="4294967295" data-saved-quantity="{{ $item->quantity }}"
                                                    aria-label="Quantity for {{ $item->name }} {{ $variantName }}"
                                                    data-cart-id="{{ $item->id }}" required>
                                                <a href="#" class="shop-up position-absolute z-index-2"
                                                    aria-label="Increase quantity">
                                                    <i class="far fa-plus"></i>
                                                </a>
                                            </div>
                                        </td>
                                        {{-- Line Total --}}
                                        <td class="align-middle">
                                            <p class="mb-0 text-body-emphasis fw-bold cart-line-total">
                                                {{ $lineSymbol }}{{ number_format($lineTotal, 2) }}
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
                                @if ($hasWholesale)
                                    Promotional codes are unavailable for carts containing wholesale products.
                                    Removing an option must leave the product's wholesale minimum fulfilled.
                                    Use Clear Shopping Cart to remove the entire order.
                                @else
                                    Discounts and promotional codes can be applied at checkout when available.
                                @endif
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
                                        title="Check Out"
                                        @if ($cartError) aria-disabled="true" tabindex="-1" style="pointer-events: none; opacity: .5;" @endif>
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
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const page = document.getElementById('cart-page');
            if (!page) return;
            const feedback = document.getElementById('cart-page-feedback');
            let busy = false;
            let notyfInstance = null;
            const routes = {
                update: @json(route('cart.update')),
                remove: @json(route('cart.remove')),
                clear: @json(route('cart.clear'))
            };

            function notify(type, message) {
                if (type === 'error') {
                    feedback.textContent = message;
                    feedback.classList.remove('d-none');
                    feedback.focus();
                }
                if (window.notyf && typeof window.notyf[type] === 'function') {
                    window.notyf[type](message);
                } else if (typeof Notyf !== 'undefined') {
                    notyfInstance ??= new Notyf({
                        duration: 3500,
                        dismissible: true
                    });
                    notyfInstance[type](message);
                }
            }

            function setBusy(value) {
                busy = value;
                page.setAttribute('aria-busy', String(value));
                page.querySelectorAll('button, .cart-page-quantity').forEach(el => el.disabled = value);
            }

            function quantity(input) {
                const number = Number(input.value);
                if (!input.value.trim() || !Number.isInteger(number) || !input.checkValidity()) {
                    input.reportValidity();
                    return null;
                }
                return number;
            }

            async function request(url, body) {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
                    },
                    body: JSON.stringify(body)
                });
                const data = await response.json();
                if (!response.ok || !data.status) {
                    throw new Error(data.message || Object.values(data.errors ?? {}).flat()[0] ||
                        'Unable to update your cart.');
                }
                return data;
            }

            async function save(input) {
                if (busy) return;
                const value = quantity(input);
                if (value === null || value === Number(input.dataset.savedQuantity)) return;
                setBusy(true);
                try {
                    await request(routes.update, {
                        id: input.dataset.cartId,
                        quantity: value
                    });
                    // Mixed tiers can reprice several rows. Reload all confirmed prices together.
                    window.location.reload();
                } catch (error) {
                    input.value = input.dataset.savedQuantity;
                    setBusy(false);
                    notify('error', error.message);
                }
            }

            document.addEventListener('click', function(event) {
                const button = event.target.closest('#cart-page .shop-up, #cart-page .shop-down');
                if (!button) return;
                event.preventDefault();
                event.stopImmediatePropagation();
                if (busy) return;
                const input = button.closest('.shop-quantity').querySelector('.cart-page-quantity');
                const current = quantity(input);
                if (current === null) return;
                const next = current + (button.classList.contains('shop-up') ? 1 : -1);
                if (next < Number(input.min) || next > Number(input.max)) {
                    notify('error',
                        `Quantity must be between ${input.min} and ${input.max}. The server also checks available stock.`
                        );
                    return;
                }
                input.value = next;
                save(input);
            }, true);

            page.addEventListener('change', event => {
                const input = event.target.closest('.cart-page-quantity');
                if (input) save(input);
            });

            page.addEventListener('click', async event => {
                const button = event.target.closest('.cart-page-remove');
                if (!button) return;
                event.preventDefault();
                if (busy) return;
                setBusy(true);
                try {
                    await request(routes.remove, {
                        id: button.dataset.cartId
                    });
                    window.location.reload();
                } catch (error) {
                    setBusy(false);
                    notify('error', error.message);
                }
            });

            const updateButton = document.getElementById('update-cart-button');
            // Keep a focused number input from triggering a competing blur request.
            updateButton?.addEventListener('mousedown', event => event.preventDefault());
            updateButton?.addEventListener('click', async () => {
                if (busy) return;
                const inputs = [...page.querySelectorAll('.cart-page-quantity')];
                const pending = [];
                for (const input of inputs) {
                    const value = quantity(input);
                    if (value === null) return;
                    if (value !== Number(input.dataset.savedQuantity)) pending.push({
                        input,
                        value
                    });
                }
                if (!pending.length) {
                    notify('success', 'Your cart is up to date.');
                    return;
                }
                setBusy(true);
                let saved = 0;
                try {
                    for (const {
                            input,
                            value
                        }
                        of pending) {
                        await request(routes.update, {
                            id: input.dataset.cartId,
                            quantity: value
                        });
                        input.dataset.savedQuantity = value;
                        saved++;
                    }
                    window.location.reload();
                } catch (error) {
                    for (const {
                            input
                        }
                        of pending) input.value = input.dataset.savedQuantity;
                    setBusy(false);
                    notify('error', error.message);
                    // Existing endpoint saves one row per request. Refresh any confirmed partial changes.
                    if (saved > 0) setTimeout(() => window.location.reload(), 2500);
                }
            });

            document.getElementById('clear-cart-button')?.addEventListener('click', async () => {
                if (busy) return;
                setBusy(true);
                try {
                    await request(routes.clear, {});
                    window.location.reload();
                } catch (error) {
                    setBusy(false);
                    notify('error', error.message);
                }
            });
        });
    </script>
@endpush
