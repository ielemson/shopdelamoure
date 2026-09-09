@php

    /*
    |--------------------------------------------------------------------------
    | Display Options
    |--------------------------------------------------------------------------
    */

    $gridStyle = $gridStyle ?? 'grid-1';
    $actionLayout = $actionLayout ?? 'horizontal';
    $compact = $compact ?? false;
    $showCategory = $showCategory ?? false;
    $showVariantCount = $showVariantCount ?? false;
    $showCompare = $showCompare ?? true;
    $showFromPrice = $showFromPrice ?? true;

    $actionSizeClass = $compact ? 'sm' : '';

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    $currency = session('currency', 'NGN');

    $priceField = $currency === 'USD' ? 'price_usd' : 'price_ngn';

    $salePriceField = $currency === 'USD' ? 'sale_price_usd' : 'sale_price_ngn';

    $currencySymbol = $currency === 'USD' ? '$' : '₦';

    /*
    |--------------------------------------------------------------------------
    | Product Image
    |--------------------------------------------------------------------------
    */

    $primaryImage = $product->images->firstWhere('is_primary', 1);

    $firstImage = $product->images->first();

    $productImage = $product->main_image ?: optional($primaryImage)->image ?: optional($firstImage)->image;

    /*
    |--------------------------------------------------------------------------
    | Product Price
    |--------------------------------------------------------------------------
    */

    $priceSource = $product;
    $isVariantPrice = false;

    if ($product->has_variants && $product->variants->isNotEmpty()) {
        $lowestVariant = $product->variants
            ->filter(function ($variant) use ($priceField) {
                return !is_null($variant->{$priceField}) && $variant->{$priceField} > 0;
            })
            ->sortBy(function ($variant) use ($priceField, $salePriceField) {
                $regular = $variant->{$priceField};

                $sale = $variant->{$salePriceField};

                return $sale && $sale > 0 && $sale < $regular ? $sale : $regular;
            })
            ->first();

        if ($lowestVariant) {
            $priceSource = $lowestVariant;

            $isVariantPrice = true;
        }
    }

    $regularPrice = $priceSource->{$priceField} ?? null;

    $salePrice = $priceSource->{$salePriceField} ?? null;

    /*
    |--------------------------------------------------------------------------
    | Sale
    |--------------------------------------------------------------------------
    */

    $hasSale = $regularPrice && $salePrice && $salePrice > 0 && $salePrice < $regularPrice;

    $discountPercentage = $hasSale ? round((($regularPrice - $salePrice) / $regularPrice) * 100) : null;

    /*
    |--------------------------------------------------------------------------
    | Product URL
    |--------------------------------------------------------------------------
    |
    | Replace this when your final product details route is created.
    |
    */

    $productUrl = route('products.show', $product->slug);
@endphp


<div class="card card-product {{ $gridStyle }} bg-transparent border-0" data-animate="fadeInUp">

    {{-- =========================================================
        PRODUCT IMAGE
    ========================================================== --}}

    <figure class="card-img-top position-relative mb-7 overflow-hidden">


        <a href="{{ $productUrl }}" class="hover-zoom-in d-block" title="{{ $product->name }}">

            @if ($productImage)
                <img src="javascript:;" data-src="{{ asset($productImage) }}" class="img-fluid lazy-image w-100"
                    alt="{{ $product->name }}" width="330" height="440"
                    style="
                        aspect-ratio: 3 / 4;
                        object-fit: cover;
                    ">
            @else
                <img src="{{ asset('assets/images/products/product-placeholder.jpg') }}" class="img-fluid w-100"
                    alt="{{ $product->name }}" width="330" height="440"
                    style="
                        aspect-ratio: 3 / 4;
                        object-fit: cover;
                    ">
            @endif

        </a>


        {{-- =====================================================
            BADGES
        ====================================================== --}}

        @if ($hasSale)
            <div class="position-absolute product-flash z-index-2">

                <span class="badge badge-product-flash on-sale bg-primary">

                    -{{ $discountPercentage }}%

                </span>

            </div>
        @elseif($product->is_new_arrival)
            <div class="position-absolute product-flash z-index-2">

                <span class="badge badge-product-flash on-new">

                    New

                </span>

            </div>
        @endif


        {{-- =====================================================
            PRODUCT ACTIONS
        ====================================================== --}}

        <div class="position-absolute d-flex z-index-2 product-actions {{ $actionLayout }}">

            @php
                $simpleOutOfStock =
                    !$product->has_variants && ($product->stock_status !== 'in_stock' || (int) $product->quantity < 1);
            @endphp

            {{-- Add To Cart / Choose Options --}}
            <a class="text-body-emphasis
          bg-body
          bg-dark-hover
          text-light-hover
          rounded-circle
          square
          product-action
          shadow-sm
          add_to_cart
          {{ $actionSizeClass }}
          {{ $simpleOutOfStock ? 'opacity-50' : '' }}"
                href="{{ $product->has_variants ? $productUrl : 'javascript:void(0)' }}"
                @if (!$product->has_variants && !$simpleOutOfStock) data-product-id="{{ $product->id }}"
        data-cart-url="{{ route('cart.add') }}" @endif
                data-bs-toggle="tooltip" data-bs-placement="{{ $actionLayout === 'vertical' ? 'left' : 'top' }}"
                data-bs-title="
        @if ($product->has_variants) Choose Options
        @elseif ($simpleOutOfStock)
            Out of Stock
        @else
            Add To Cart @endif
    ">

                <span class="cart-icon">
                    <svg class="icon icon-shopping-bag-open-light">
                        <use xlink:href="#icon-shopping-bag-open-light"></use>
                    </svg>
                </span>

                <span class="cart-loading d-none">
                    <span class="spinner-border spinner-border-sm"></span>
                </span>
            </a>


            {{-- Quick View --}}

            <a href="javascript:;"
                class="text-body-emphasis
           bg-body
           bg-dark-hover
           text-light-hover
           rounded-circle
           square
           product-action
           shadow-sm
           quick-view-btn
           {{ $actionSizeClass }}"
                data-url="{{ route('products.quick-view', $product->slug) }}" title="Quick View">

                <span class="d-flex align-items-center justify-content-center">

                    <svg class="icon icon-eye-light">
                        <use xlink:href="#icon-eye-light"></use>
                    </svg>

                </span>

            </a>


            {{-- Wishlist --}}
            @php

                if (auth()->check()) {
                    $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                        ->where('product_id', $product->id)
                        ->exists();
                } else {
                    $isWishlisted = collect(session()->get('wishlist', []))
                        ->map(fn($id) => (int) $id)
                        ->contains((int) $product->id);
                }

            @endphp


            <a class="text-body-emphasis
          bg-body
          bg-dark-hover
          text-light-hover
          rounded-circle
          square
          product-action
          shadow-sm
          wishlist
          wishlist-toggle
          {{ $actionSizeClass }}
          {{ $isWishlisted ? 'wishlist-active' : '' }}"
                href="javascript:;" data-product-id="{{ $product->id }}"
                data-url="{{ route('wishlist.toggle', $product->id) }}" data-csrf="{{ csrf_token() }}"
                data-bs-toggle="tooltip" data-bs-placement="{{ $actionLayout === 'vertical' ? 'left' : 'top' }}"
                data-bs-title="{{ $isWishlisted ? 'Remove From Wishlist' : 'Add To Wishlist' }}"
                aria-label="{{ $isWishlisted ? 'Remove From Wishlist' : 'Add To Wishlist' }}">


                <svg class="icon icon-star-light">

                    <use xlink:href="#icon-star-light"></use>

                </svg>

            </a>

            {{-- @if ($showCompare)
            
                @php
                    $compareIds = array_map('intval', session('compare', []));

                    $isCompared = in_array((int) $product->id, $compareIds, true);
                @endphp


                <a class="text-body-emphasis
              bg-body
              bg-dark-hover
              text-light-hover
              rounded-circle
              square
              product-action
              shadow-sm
              compare-toggle
              {{ $isCompared ? 'compare-active' : '' }}
              {{ $actionSizeClass }}"
                    href="javascript:void(0);" data-product-id="{{ $product->id }}"
                    data-url="{{ route('compare.toggle', $product->id) }}" data-csrf="{{ csrf_token() }}"
                    data-bs-toggle="tooltip" data-bs-placement="{{ $actionLayout === 'vertical' ? 'left' : 'top' }}"
                    data-bs-title="{{ $isCompared ? 'Remove From Compare' : 'Add To Compare' }}"
                    aria-label="{{ $isCompared ? 'Remove From Compare' : 'Add To Compare' }}">

                    <svg class="icon icon-arrows-left-right-light">

                        <use xlink:href="#icon-arrows-left-right-light"></use>

                    </svg>

                </a>
            @endif --}}
        </div>

    </figure>


    {{-- =========================================================
        PRODUCT INFORMATION
    ========================================================== --}}

    <div class="card-body text-center p-0">


        {{-- Category --}}
        @if ($showCategory && $product->category)
            <div class="text-muted fs-13px mb-2">

                {{ $product->category->name }}

            </div>
        @endif


        {{-- Price --}}
        <span
            class="d-flex align-items-center
                   price
                   text-body-emphasis
                   fw-bold
                   justify-content-center
                   mb-3
                   fs-6">

            @if ($isVariantPrice && $showFromPrice)
                <small class="text-muted fw-normal me-2">
                    From
                </small>
            @endif


            @if ($hasSale)
                <del class="text-body fw-500 me-4 fs-13px">

                    {{ $currencySymbol }}{{ number_format($regularPrice, 2) }}

                </del>


                <ins class="text-decoration-none">

                    {{ $currencySymbol }}{{ number_format($salePrice, 2) }}

                </ins>
            @elseif($regularPrice)
                {{ $currencySymbol }}{{ number_format($regularPrice, 2) }}
            @else
                <span class="text-muted fw-normal">
                    Price unavailable
                </span>
            @endif

        </span>


        {{-- Product Name --}}
        <h4
            class="product-title
                   card-title
                   text-primary-hover
                   text-body-emphasis
                   fs-15px
                   fw-500
                   mb-3">

            <a class="text-decoration-none text-reset" href="{{ $productUrl }}">

                {{ $product->name }}

            </a>

        </h4>


        {{-- Variant Count --}}
        @if ($showVariantCount && $product->has_variants && $product->variants->isNotEmpty())
            <div class="fs-13px text-muted">

                {{ $product->variants->count() }}

                {{ Str::plural('option', $product->variants->count()) }}

                available

            </div>
        @endif

    </div>

</div>

@once

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                /*
                |--------------------------------------------------------------------------
                | Wishlist Notification
                |--------------------------------------------------------------------------
                */

                function wishlistNotify(message, type = 'success') {

                    // Toastr
                    if (typeof toastr !== 'undefined') {

                        if (type === 'success') {
                            toastr.success(message);
                        } else if (type === 'error') {
                            toastr.error(message);
                        } else {
                            toastr.info(message);
                        }

                        return;
                    }


                    // Notify.js
                    if (typeof $ !== 'undefined' && typeof $.notify === 'function') {

                        $.notify(message, {
                            className: type,
                            globalPosition: 'top right'
                        });

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Bootstrap Fallback Alert
                    |--------------------------------------------------------------------------
                    */

                    const oldAlert = document.querySelector('.wishlist-notification-alert');

                    if (oldAlert) {
                        oldAlert.remove();
                    }


                    const alertType = type === 'error' ? 'danger' : type;

                    const alert = document.createElement('div');

                    alert.className =
                        `alert alert-${alertType} alert-dismissible fade show wishlist-notification-alert position-fixed`;

                    alert.style.cssText = `
                top: 90px;
                right: 20px;
                z-index: 99999;
                min-width: 280px;
                max-width: 400px;
                box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            `;

                    alert.innerHTML = `
                <div>${message}</div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
                </button>
            `;

                    document.body.appendChild(alert);


                    setTimeout(function() {

                        if (alert && alert.parentNode) {

                            alert.classList.remove('show');

                            setTimeout(function() {
                                alert.remove();
                            }, 300);

                        }

                    }, 3000);
                }


                /*
                |--------------------------------------------------------------------------
                | Wishlist Toggle
                |--------------------------------------------------------------------------
                */

                document.addEventListener('click', async function(event) {

                    const wishlistButton = event.target.closest('.wishlist-toggle');

                    if (!wishlistButton) {
                        return;
                    }

                    event.preventDefault();


                    if (wishlistButton.classList.contains('wishlist-loading')) {
                        return;
                    }


                    const productId = wishlistButton.dataset.productId;
                    const url = wishlistButton.dataset.url;
                    const csrfToken = wishlistButton.dataset.csrf;


                    wishlistButton.classList.add('wishlist-loading');


                    try {

                        const response = await fetch(url, {

                            method: 'POST',

                            headers: {

                                'X-CSRF-TOKEN': csrfToken,

                                'X-Requested-With': 'XMLHttpRequest',

                                'Accept': 'application/json'

                            },

                            credentials: 'same-origin'

                        });


                        /*
                        |--------------------------------------------------------------------------
                        | Parse Response
                        |--------------------------------------------------------------------------
                        */

                        const data = await response.json();


                        if (!response.ok) {

                            throw new Error(
                                data.message || 'Unable to update your wishlist.'
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Update Every Copy Of Product
                        |--------------------------------------------------------------------------
                        */

                        document
                            .querySelectorAll(
                                `.wishlist-toggle[data-product-id="${productId}"]`
                            )
                            .forEach(function(button) {

                                if (data.wishlisted) {

                                    button.classList.add('wishlist-active');

                                    button.setAttribute(
                                        'data-bs-title',
                                        'Remove From Wishlist'
                                    );

                                    button.setAttribute(
                                        'aria-label',
                                        'Remove From Wishlist'
                                    );

                                } else {

                                    button.classList.remove('wishlist-active');

                                    button.setAttribute(
                                        'data-bs-title',
                                        'Add To Wishlist'
                                    );

                                    button.setAttribute(
                                        'aria-label',
                                        'Add To Wishlist'
                                    );

                                }

                            });


                        /*
                        |--------------------------------------------------------------------------
                        | Header Wishlist Count
                        |--------------------------------------------------------------------------
                        */

                        document
                            .querySelectorAll('.wishlist-count')
                            .forEach(function(counter) {

                                counter.textContent = data.count;

                                if (data.count > 0) {

                                    counter.classList.remove('d-none');

                                } else {

                                    counter.classList.add('d-none');

                                }

                            });


                        /*
                        |--------------------------------------------------------------------------
                        | Notify Customer
                        |--------------------------------------------------------------------------
                        */

                        if (data.wishlisted) {

                            wishlistNotify(
                                data.message || 'Product added to your wishlist.',
                                'success'
                            );

                        } else {

                            wishlistNotify(
                                data.message || 'Product removed from your wishlist.',
                                'success'
                            );

                        }


                    } catch (error) {

                        console.error('Wishlist Error:', error);


                        wishlistNotify(
                            error.message || 'Something went wrong. Please try again.',
                            'error'
                        );


                    } finally {

                        wishlistButton.classList.remove('wishlist-loading');

                    }

                });

            });
        </script>
    @endpush

@endonce
