@php
    $cartItems = \Darryldecode\Cart\Facades\CartFacade::getContent();
    $cartCount = \Darryldecode\Cart\Facades\CartFacade::getTotalQuantity();
    $cartTotal = \Darryldecode\Cart\Facades\CartFacade::getTotal();

    $currency = strtoupper(session('currency', 'NGN'));
    $currencySymbol = $currency === 'USD' ? '$' : '₦';
@endphp


{{-- HEADER --}}
<div class="offcanvas-header fs-4 flex-shrink-0">

    <h4 class="offcanvas-title fw-semibold" id="shoppingCartLabel">
        Shopping Bag
    </h4>

    <button type="button" class="btn-close btn-close-bg-none" data-bs-dismiss="offcanvas" aria-label="Close">

        <i class="far fa-times"></i>

    </button>

</div>


{{-- SCROLLABLE BODY --}}
<div class="offcanvas-body me-xl-auto pt-0 mb-2 mb-xl-0">

    @if ($cartItems->isNotEmpty())

        <form class="table-responsive-md shopping-cart pb-8 pb-lg-10">

            <table class="table table-borderless">

                <thead>
                    <tr class="fw-500">

                        <td colspan="3" class="border-bottom pb-6">

                            <i
                                class="far fa-check fs-12px border me-4 px-2 py-1
                                text-body-emphasis border-dark rounded-circle"></i>

                            You have

                            <span class="text-body-emphasis fw-semibold">
                                {{ $cartCount }}
                                {{ Str::plural('item', $cartCount) }}
                            </span>

                            in your shopping bag.

                        </td>

                    </tr>
                </thead>


                <tbody>

                    @foreach ($cartItems as $item)
                        @php
                            $image = $item->attributes->get('image');
                            $slug = $item->attributes->get('slug');
                            $variantOptions = $item->attributes->get('variant_options');

                            $productUrl = $slug ? route('products.show', $slug) : '#';
                        @endphp


                        <tr class="position-relative" data-cart-row="{{ $item->id }}">


                            {{-- Remove --}}
                            <td class="align-middle text-center">

                                <a href="#" class="d-block clear-product" data-cart-id="{{ $item->id }}"
                                    data-remove-url="{{ route('cart.removeCartItem', $item->id) }}" title="Remove">

                                    <i class="far fa-times"></i>

                                </a>

                            </td>


                            {{-- Product --}}
                            <td class="shop-product">

                                <div class="d-flex align-items-center">

                                    <div class="me-6 flex-shrink-0">

                                        <a href="{{ $productUrl }}">

                                            <img src="{{ $image ? asset($image) : asset('assets/images/products/product-placeholder.jpg') }}"
                                                width="60" height="80" style="object-fit: cover;"
                                                alt="{{ $item->name }}">

                                        </a>

                                    </div>


                                    <div>

                                        <p class="card-text mb-1">

                                            <span class="fs-15px fw-bold text-body-emphasis">

                                                {{ $currencySymbol }}{{ number_format($item->price, 2) }}

                                            </span>

                                        </p>


                                        <p class="fw-500 text-body-emphasis mb-1">

                                            <a href="{{ $productUrl }}" class="text-decoration-none text-reset">

                                                {{ $item->name }}

                                            </a>

                                        </p>


                                        @if ($variantOptions)
                                            <div class="fs-13px text-muted">

                                                @if (is_array($variantOptions))
                                                    {{ collect($variantOptions)->implode(' / ') }}
                                                @else
                                                    {{ $variantOptions }}
                                                @endif

                                            </div>
                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- Quantity --}}
                            <td class="align-middle p-0">

                                <div class="input-group position-relative shop-quantity"
                                    data-cart-id="{{ $item->id }}">

                                    <a href="#" class="shop-down position-absolute z-index-2">

                                        <i class="far fa-minus"></i>

                                    </a>


                                    <input name="number[]" type="number"
                                        class="form-control form-control-sm px-6 py-4 fs-6 text-center border-0"
                                        value="{{ $item->quantity }}" min="1" required>


                                    <a href="#" class="shop-up position-absolute z-index-2">

                                        <i class="far fa-plus"></i>

                                    </a>

                                </div>

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </form>
    @else
        <div class="d-flex flex-column align-items-center
            justify-content-center text-center h-100 py-10">

            <div class="mb-5">

                <svg class="icon icon-shopping-bag-open-light" style="width:48px;height:48px;">

                    <use xlink:href="#icon-shopping-bag-open-light"></use>

                </svg>

            </div>


            <h5 class="fw-semibold mb-3">
                Your shopping bag is empty
            </h5>


            <p class="text-muted mb-6">
                Discover something you love.
            </p>


            <a href="{{ route('shop') }}" class="btn btn-dark px-8">

                Continue Shopping

            </a>

        </div>

    @endif

</div>


{{-- FOOTER --}}
@if ($cartItems->isNotEmpty())
    <div class="offcanvas-footer flex-wrap flex-shrink-0">

        <div class="d-flex align-items-center justify-content-between w-100 mb-5">

            <span class="text-body-emphasis">
                Total price:
            </span>


            <span class="cart-total fw-bold text-body-emphasis">

                {{ $currencySymbol }}{{ number_format($cartTotal, 2) }}

            </span>

        </div>

        <a href="{{ route('cart.index') }}" class="btn btn-outline-dark w-100" title="View shopping cart">

            View shopping cart

        </a>

    </div>
@endif
