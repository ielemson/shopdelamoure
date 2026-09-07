@extends('layouts.app')

@section('PageContent')
    <section class="pb-lg-20 pb-16">

        @include('frontend.partials.breadcrumb', [
            'title' => 'Shop',
            'item' => 'Collection',
        ])


    </section>

    <section class="container container-xxl">

        {{-- =========================================================
        SHOP TOOLBAR
    ========================================================== --}}
        <div class="tool-bar mb-11 align-items-center justify-content-between d-lg-flex">

            {{-- Product Count --}}
            <div class="tool-bar-left mb-6 mb-lg-0 fs-18px">

                We found

                <span class="text-body-emphasis fw-semibold">
                    {{ $products->total() }}
                </span>

                {{ Str::plural('product', $products->total()) }}
                available for you

            </div>

            <div class="tool-bar-right align-items-center d-lg-flex">

                {{-- View / Mobile Filter --}}
                <ul class="list-unstyled d-flex align-items-center list-inline me-lg-7 me-0 mb-6 mb-lg-0">

                    {{-- Grid View --}}
                    <li class="list-inline-item me-0">
                        <span class="fs-32px text-body-emphasis" title="Grid View">

                            <svg class="icon icon-squares-four">
                                <use xlink:href="#icon-squares-four"></use>
                            </svg>

                        </span>
                    </li>


                    {{-- Mobile Filter --}}
                    <li class="list-inline-item d-lg-none ms-auto">

                        <a data-bs-toggle="offcanvas" href="#offcanvasExample" role="button"
                            class="btn btn-hover-border-primary
                               btn-hover-bg-primary
                               btn-hover-text-light
                               btn-dark">

                            <svg class="icon icon-SlidersHorizontal fs-4 me-4">
                                <use xlink:href="#icon-SlidersHorizontal"></use>
                            </svg>

                            Filter

                        </a>

                    </li>

                </ul>


                {{-- Sort --}}
                <ul class="list-unstyled d-flex align-items-center list-inline mb-0">

                    <li class="list-inline-item me-0 w-100 w-lg-auto">

                        <form method="GET" action="{{ url()->current() }}">

                            {{-- Preserve Category Filter --}}
                            @if (request()->filled('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif


                            {{-- Preserve Min Price --}}
                            @if (request()->filled('min_price'))
                                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                            @endif


                            {{-- Preserve Max Price --}}
                            @if (request()->filled('max_price'))
                                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            @endif


                            <select class="form-select w-100 w-lg-auto" name="sort" onchange="this.form.submit()">

                                <option value="" @selected(!request('sort'))>
                                    Default sorting
                                </option>

                                <option value="latest" @selected(request('sort') === 'latest')>
                                    Sort by latest
                                </option>

                                <option value="name_asc" @selected(request('sort') === 'name_asc')>
                                    Name: A to Z
                                </option>

                                <option value="name_desc" @selected(request('sort') === 'name_desc')>
                                    Name: Z to A
                                </option>

                                <option value="price_low" @selected(request('sort') === 'price_low')>
                                    Price: Low to High
                                </option>

                                <option value="price_high" @selected(request('sort') === 'price_high')>
                                    Price: High to Low
                                </option>

                            </select>

                        </form>

                    </li>


                    {{-- Desktop Filter --}}
                    <li class="list-inline-item d-none d-lg-block ms-7">

                        <a data-bs-toggle="offcanvas" href="#offcanvasExample" role="button"
                            class="btn btn-hover-border-primary
                               btn-hover-bg-primary
                               btn-hover-text-light
                               btn-dark">

                            <svg class="icon icon-SlidersHorizontal fs-4 me-4">
                                <use xlink:href="#icon-SlidersHorizontal"></use>
                            </svg>

                            Filter

                        </a>

                    </li>

                </ul>

            </div>

        </div>


        {{-- =========================================================
        PRODUCT GRID
    ========================================================== --}}

        <div class="row gy-11">

            @forelse($products as $product)
                @php
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

                    $productImage =
                        $product->main_image ?: optional($primaryImage)->image ?: optional($firstImage)->image;

                    /*
            |--------------------------------------------------------------------------
            | Product / Variant Price
            |--------------------------------------------------------------------------
            */

                    $priceSource = $product;

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
            */

                    $productUrl = route('products.show', $product->slug);

                    /*
            |--------------------------------------------------------------------------
            | Stock
            |--------------------------------------------------------------------------
            */

                    $simpleOutOfStock =
                        !$product->has_variants &&
                        ($product->stock_status !== 'in_stock' || (int) $product->quantity < 1);
                @endphp


                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-12">

                    <div class="card card-product grid-1 bg-transparent border-0" data-animate="fadeInUp">


                        {{-- =====================================================
                    PRODUCT IMAGE
                ====================================================== --}}

                        <figure class="card-img-top position-relative mb-7 overflow-hidden">


                            <a href="{{ $productUrl }}" class="hover-zoom-in d-block" title="{{ $product->name }}">

                                @if ($productImage)
                                    <img src="#" data-src="{{ asset($productImage) }}"
                                        class="img-fluid lazy-image w-100" alt="{{ $product->name }}" width="330"
                                        height="440"
                                        style="
                                    aspect-ratio: 3 / 4;
                                    object-fit: cover;
                                ">
                                @else
                                    <img src="{{ asset('assets/images/products/product-placeholder.jpg') }}"
                                        class="img-fluid w-100" alt="{{ $product->name }}" width="330" height="440"
                                        style="
                                    aspect-ratio: 3 / 4;
                                    object-fit: cover;
                                ">
                                @endif

                            </a>


                            {{-- =================================================
                        PRODUCT BADGE
                    ================================================== --}}

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


                            {{-- =================================================
                        PRODUCT ACTIONS
                    ================================================== --}}

                            <div class="position-absolute d-flex z-index-2 product-actions horizontal">


                                {{-- Add To Cart / Choose Options --}}
                                <a class="
                                text-body-emphasis
                                bg-body
                                bg-dark-hover
                                text-light-hover
                                rounded-circle
                                square
                                product-action
                                shadow-sm
                                add_to_cart
                                {{ $simpleOutOfStock ? 'opacity-50' : '' }}
                            "
                                    href="{{ $product->has_variants ? $productUrl : 'javascript:void(0)' }}"
                                    @if (!$product->has_variants && !$simpleOutOfStock) data-product-id="{{ $product->id }}"

                                data-cart-url="{{ route('cart.add') }}" @endif
                                    data-bs-toggle="tooltip" data-bs-placement="top"
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
                                <a class="
                                text-body-emphasis
                                bg-body
                                bg-dark-hover
                                text-light-hover
                                rounded-circle
                                square
                                product-action
                                shadow-sm
                                quick-view
                            "
                                    href="#" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="Quick View">

                                    <span data-bs-toggle="modal" data-bs-target="#quickViewModal"
                                        class="d-flex align-items-center justify-content-center">

                                        <svg class="icon icon-eye-light">

                                            <use xlink:href="#icon-eye-light"></use>

                                        </svg>

                                    </span>

                                </a>


                                {{-- Wishlist --}}
                                <a class="
                                text-body-emphasis
                                bg-body
                                bg-dark-hover
                                text-light-hover
                                rounded-circle
                                square
                                product-action
                                shadow-sm
                                wishlist
                            "
                                    href="#" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="Add To Wishlist">

                                    <svg class="icon icon-star-light">

                                        <use xlink:href="#icon-star-light"></use>

                                    </svg>

                                </a>

                            </div>

                        </figure>


                        {{-- =====================================================
                    PRODUCT INFORMATION
                ====================================================== --}}

                        <div class="card-body text-center p-0">


                            {{-- Category --}}
                            @if ($product->category)
                                <div class="text-muted fs-13px mb-2">

                                    {{ $product->category->name }}

                                </div>
                            @endif


                            {{-- Price --}}
                            <span
                                class="
                            d-flex
                            align-items-center
                            price
                            text-body-emphasis
                            fw-bold
                            justify-content-center
                            mb-3
                            fs-6
                        ">

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
                                    <span class="text-muted">

                                        Price unavailable

                                    </span>
                                @endif

                            </span>


                            {{-- Product Name --}}
                            <h4
                                class="
                            product-title
                            card-title
                            text-primary-hover
                            text-body-emphasis
                            fs-15px
                            fw-500
                            mb-3
                        ">

                                <a class="text-decoration-none text-reset" href="{{ $productUrl }}">

                                    {{ $product->name }}

                                </a>

                            </h4>


                            {{-- Variant Indicator --}}
                            @if ($product->has_variants && $product->variants->isNotEmpty())
                                <p class="fs-13px text-muted mb-0">

                                    {{ $product->variants->count() }}

                                    {{ Str::plural('option', $product->variants->count()) }}

                                    available

                                </p>
                            @endif

                        </div>

                    </div>

                </div>


            @empty

                {{-- =========================================================
            NO PRODUCTS
        ========================================================== --}}

                <div class="col-12">

                    <div class="text-center py-15">

                        <h3 class="fs-4 mb-4">
                            No products found
                        </h3>

                        <p class="text-muted mb-6">
                            We couldn't find products matching your current selection.
                        </p>

                        <a href="{{ url()->current() }}" class="btn btn-dark">

                            View All Products

                        </a>

                    </div>

                </div>
            @endforelse

        </div>


        {{-- =========================================================
        PAGINATION
    ========================================================== --}}

        @if ($products->hasPages())
            <div class="mt-13 mb-15 d-flex justify-content-center">

                {{ $products->links() }}

            </div>
        @endif

    </section>
@endsection
