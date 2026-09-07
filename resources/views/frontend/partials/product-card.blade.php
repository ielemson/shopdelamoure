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
                <img src="#" data-src="{{ asset($productImage) }}" class="img-fluid lazy-image w-100"
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

            <a href="#"
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
            <a class="text-body-emphasis
                       bg-body
                       bg-dark-hover
                       text-light-hover
                       rounded-circle
                       square
                       product-action
                       shadow-sm
                       wishlist
                       {{ $actionSizeClass }}"
                href="#" data-bs-toggle="tooltip"
                data-bs-placement="{{ $actionLayout === 'vertical' ? 'left' : 'top' }}"
                data-bs-title="Add To Wishlist">

                <svg class="icon icon-star-light">

                    <use xlink:href="#icon-star-light"></use>

                </svg>

            </a>


            {{-- Compare --}}
            @if ($showCompare)
                <a class="text-body-emphasis
                           bg-body
                           bg-dark-hover
                           text-light-hover
                           rounded-circle
                           square
                           product-action
                           shadow-sm
                           compare
                           {{ $actionSizeClass }}"
                    href="#" data-bs-toggle="tooltip"
                    data-bs-placement="{{ $actionLayout === 'vertical' ? 'left' : 'top' }}" data-bs-title="Compare">

                    <svg class="icon icon-arrows-left-right-light">

                        <use xlink:href="#icon-arrows-left-right-light">
                        </use>

                    </svg>

                </a>
            @endif

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
