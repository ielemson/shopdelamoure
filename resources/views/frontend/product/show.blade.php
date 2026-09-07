@extends('layouts.app')

@section('meta_title', $product->name . ' | Dela Moure Luxury Fragrances')

@section('meta_description',
    Str::limit(
    strip_tags(
    $product->short_description ??
    ($product->description ??
    'Shop
    premium fragrances and scenting essentials from Dela Moure.'),
    ),
    155,
    ))

    {{-- @section('meta_image', $product->image ? asset('storage/' . $product->image) : asset('assets/images/others/social-share.jpg')) --}}

@section('PageContent')

    <section class="pb-lg-20 pb-16">
        @include('frontend.partials.breadcrumb', [
            'title' => 'Shop',
            'item' => 'Collection',
        ])
    </section>

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
        | Cart Variant Quantities
        |--------------------------------------------------------------------------
        */
        $variantCartQuantities = $variantCartQuantities ?? collect();

        /*
        |--------------------------------------------------------------------------
        | Product Gallery
        |--------------------------------------------------------------------------
        */
        $galleryImages = collect();

        if ($product->main_image) {
            $galleryImages->push([
                'image' => $product->main_image,
                'alt' => $product->name,
            ]);
        }

        foreach ($product->images as $image) {
            if ($image->image && !$galleryImages->contains(fn($item) => $item['image'] === $image->image)) {
                $galleryImages->push([
                    'image' => $image->image,
                    'alt' => $image->alt_text ?: $product->name,
                ]);
            }
        }

        foreach ($product->variants as $variant) {
            if ($variant->image && !$galleryImages->contains(fn($item) => $item['image'] === $variant->image)) {
                $galleryImages->push([
                    'image' => $variant->image,
                    'alt' => $variant->name ?: $product->name,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Variant Selection
        |--------------------------------------------------------------------------
        */
        $selectedVariant = null;

        if ($product->has_variants && $product->variants->isNotEmpty()) {
            $defaultVariant = $product->variants->firstWhere('is_default', 1);

            if ($defaultVariant) {
                $defaultCartQuantity = (int) $variantCartQuantities->get((string) $defaultVariant->id, 0);

                $defaultAvailableQuantity = $defaultVariant->track_stock
                    ? max((int) $defaultVariant->stock_quantity - $defaultCartQuantity, 0)
                    : null;

                $defaultUnavailable =
                    $defaultVariant->stock_status === 'out_of_stock' ||
                    ($defaultVariant->track_stock && $defaultAvailableQuantity <= 0);

                if (!$defaultUnavailable) {
                    $selectedVariant = $defaultVariant;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Selected Variant Cart/Stock
        |--------------------------------------------------------------------------
        */
        $selectedVariantCartQuantity = $selectedVariant
            ? (int) $variantCartQuantities->get((string) $selectedVariant->id, 0)
            : 0;

        $selectedVariantAvailableQuantity =
            $selectedVariant && $selectedVariant->track_stock
                ? max((int) $selectedVariant->stock_quantity - $selectedVariantCartQuantity, 0)
                : null;

        /*
        |--------------------------------------------------------------------------
        | Starting Price
        |--------------------------------------------------------------------------
        */
        if ($selectedVariant) {
            $regularPrice = $selectedVariant->{$priceField};

            $salePrice = $selectedVariant->{$salePriceField};
        } elseif ($product->has_variants && $product->variants->isNotEmpty()) {
            /*
            |--------------------------------------------------------------------------
            | Lowest Available Variant
            |--------------------------------------------------------------------------
            */
            $lowestVariant = $product->variants
                ->filter(function ($variant) use ($priceField, $variantCartQuantities) {
                    $price = $variant->{$priceField};

                    if (is_null($price) || $price <= 0) {
                        return false;
                    }

                    if ($variant->stock_status === 'out_of_stock') {
                        return false;
                    }

                    if ($variant->track_stock) {
                        $cartQuantity = (int) $variantCartQuantities->get((string) $variant->id, 0);

                        $available = max((int) $variant->stock_quantity - $cartQuantity, 0);

                        if ($available <= 0) {
                            return false;
                        }
                    }

                    return true;
                })
                ->sortBy(function ($variant) use ($priceField, $salePriceField) {
                    $price = $variant->{$priceField};

                    $sale = $variant->{$salePriceField};

                    return $sale && $sale > 0 && $sale < $price ? $sale : $price;
                })
                ->first();

            $regularPrice = $lowestVariant?->{$priceField};

            $salePrice = $lowestVariant?->{$salePriceField};
        } else {
            $regularPrice = $product->{$priceField};

            $salePrice = $product->{$salePriceField};
        }

        /*
        |--------------------------------------------------------------------------
        | Sale
        |--------------------------------------------------------------------------
        */
        $hasSale = $regularPrice && $salePrice && $salePrice > 0 && $salePrice < $regularPrice;

        $discountPercentage = $hasSale ? round((($regularPrice - $salePrice) / $regularPrice) * 100) : null;

        /*
        |--------------------------------------------------------------------------
        | Product Stock
        |--------------------------------------------------------------------------
        */
        $productOutOfStock =
            $product->stock_status === 'out_of_stock' || ($product->track_stock && $product->quantity <= 0);

        /*
        |--------------------------------------------------------------------------
        | Selected Variant Stock State
        |--------------------------------------------------------------------------
        */
        $selectedVariantOutOfStock =
            $selectedVariant &&
            ($selectedVariant->stock_status === 'out_of_stock' ||
                ($selectedVariant->track_stock && $selectedVariantAvailableQuantity <= 0));

        /*
        |--------------------------------------------------------------------------
        | Add Button State
        |--------------------------------------------------------------------------
        */
        $disableAddButton = $product->has_variants
            ? !$selectedVariant || $selectedVariantOutOfStock
            : $productOutOfStock;

        /*
        |--------------------------------------------------------------------------
        | Current SKU
        |--------------------------------------------------------------------------
        */
        $currentSku = $selectedVariant?->sku ?: $product->sku;
    @endphp


    {{-- ================================================================
    PRODUCT DETAILS
    ================================================================= --}}

    <section class="container pt-6 pb-14 pb-lg-20">

        <div class="row">

            {{-- ========================================================
            PRODUCT GALLERY
            ========================================================= --}}

            <div class="col-md-6 pe-lg-13">

                <div class="position-relative">

                    {{-- Wishlist --}}
                    <div class="position-absolute z-index-2 w-100 d-flex justify-content-end">

                        <div class="p-6">

                            <a href="#"
                                class="d-flex align-items-center justify-content-center product-gallery-action rounded-circle"
                                data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Add to wishlist">

                                <svg class="icon fs-4">
                                    <use xlink:href="#icon-star-light"></use>
                                </svg>

                            </a>

                        </div>

                    </div>


                    {{-- Main Slider --}}
                    <div id="slider"
                        class="slick-slider slick-slider-arrow-inside slick-slider-dots-inside slick-slider-dots-light g-0"
                        data-slick-options='{
                            "arrows": false,
                            "asNavFor": "#slider-thumb",
                            "dots": false,
                            "slidesToShow": 1
                        }'>

                        @forelse($galleryImages as $galleryImage)
                            <a href="{{ asset($galleryImage['image']) }}" data-gallery="product-gallery"
                                data-product-image="{{ $galleryImage['image'] }}"
                                data-thumb-src="{{ asset($galleryImage['image']) }}">

                                <img src="#" data-src="{{ asset($galleryImage['image']) }}"
                                    class="h-auto lazy-image w-100" width="540" height="720"
                                    alt="{{ $galleryImage['alt'] }}"
                                    style="
                                        aspect-ratio: 3 / 4;
                                        object-fit: cover;
                                    ">

                            </a>

                        @empty

                            <div>

                                <img src="{{ asset('assets/images/products/product-placeholder.jpg') }}"
                                    class="h-auto w-100" width="540" height="720" alt="{{ $product->name }}"
                                    style="
                                        aspect-ratio: 3 / 4;
                                        object-fit: cover;
                                    ">

                            </div>
                        @endforelse

                    </div>

                </div>


                {{-- Thumbnail Slider --}}
                @if ($galleryImages->count() > 1)
                    <div class="mt-6">

                        <div id="slider-thumb" class="slick-slider slick-slider-thumb ps-1 ms-n3 me-n4"
                            data-slick-options='{
                                "arrows": false,
                                "asNavFor": "#slider",
                                "dots": false,
                                "focusOnSelect": true,
                                "slidesToShow": 5,
                                "vertical": false
                            }'>

                            @foreach ($galleryImages as $galleryImage)
                                <img src="#" data-src="{{ asset($galleryImage['image']) }}"
                                    class="mx-3 px-0 h-auto cursor-pointer lazy-image" width="75" height="100"
                                    alt="{{ $galleryImage['alt'] }}">
                            @endforeach

                        </div>

                    </div>
                @endif

            </div>


            {{-- ========================================================
            PRODUCT INFORMATION
            ========================================================= --}}

            <div class="col-md-6 pt-md-0 pt-10">


                {{-- Category --}}
                @if ($product->category)
                    <div class="mb-4">

                        <span class="fs-13px text-uppercase fw-semibold text-muted">

                            {{ $product->category->name }}

                            @if ($product->subcategory)
                                <span class="mx-2">/</span>

                                {{ $product->subcategory->name }}
                            @endif

                        </span>

                    </div>
                @endif


                {{-- Product Name --}}
                <h1 class="mb-5 pb-2 fs-4">
                    {{ $product->name }}
                </h1>


                {{-- ====================================================
                PRICE
                ===================================================== --}}

                <div class="d-flex align-items-center flex-wrap mb-6" id="product-price">

                    @if ($product->has_variants && !$selectedVariant)
                        <span id="price-prefix" class="text-muted me-3">
                            From
                        </span>
                    @else
                        <span id="price-prefix" class="text-muted me-3 d-none">
                            From
                        </span>
                    @endif


                    <span id="regular-price"
                        class="{{ $hasSale ? 'text-decoration-line-through' : 'fs-18px text-body-emphasis fw-bold' }}">

                        @if ($regularPrice)
                            {{ $currencySymbol }}{{ number_format($regularPrice, 2) }}
                        @else
                            Price unavailable
                        @endif

                    </span>


                    <span id="sale-price" class="fs-18px text-body-emphasis ps-6 fw-bold {{ $hasSale ? '' : 'd-none' }}">

                        @if ($hasSale)
                            {{ $currencySymbol }}{{ number_format($salePrice, 2) }}
                        @endif

                    </span>


                    <span id="discount-badge"
                        class="badge text-bg-primary fs-6 fw-semibold ms-7 px-6 py-3 {{ $hasSale ? '' : 'd-none' }}">

                        @if ($hasSale)
                            -{{ $discountPercentage }}%
                        @endif

                    </span>

                </div>


                {{-- ====================================================
                SHORT DESCRIPTION
                ===================================================== --}}

                @if ($product->short_description)
                    <p class="fs-15px mb-7">
                        {{ $product->short_description }}
                    </p>
                @endif


                {{-- ====================================================
                VARIANTS
                ===================================================== --}}

                @if ($product->has_variants && $product->variants->isNotEmpty())
                    <div class="mb-7">

                        <label class="text-body-emphasis fw-semibold fs-15px mb-4 d-block">
                            Select Option
                        </label>


                        {{-- Variant Buttons --}}
                        <div class="d-flex flex-wrap gap-3" id="variant-options">

                            @foreach ($product->variants as $variant)
                                @php
                                    $variantPrice = $variant->{$priceField};

                                    $variantSalePrice = $variant->{$salePriceField};

                                    $variantHasSale =
                                        $variantPrice &&
                                        $variantSalePrice &&
                                        $variantSalePrice > 0 &&
                                        $variantSalePrice < $variantPrice;

                                    $variantDisplayPrice = $variantHasSale ? $variantSalePrice : $variantPrice;

                                    $variantCartQuantity = (int) $variantCartQuantities->get((string) $variant->id, 0);

                                    $variantAvailableQuantity = $variant->track_stock
                                        ? max((int) $variant->stock_quantity - $variantCartQuantity, 0)
                                        : null;

                                    $variantOutOfStock =
                                        $variant->stock_status === 'out_of_stock' ||
                                        ($variant->track_stock && $variantAvailableQuantity <= 0);

                                    $variantSelected = $selectedVariant && $selectedVariant->id === $variant->id;
                                @endphp


                                <button type="button" data-variant-id="{{ $variant->id }}"
                                    class="btn variant-option-btn px-5 py-3 {{ $variantSelected ? 'btn-dark' : 'btn-outline-dark' }}"
                                    @disabled($variantOutOfStock)>

                                    <span class="fw-semibold">
                                        {{ $variant->name }}
                                    </span>

                                    @if ($variantDisplayPrice)
                                        <span class="d-block fs-13px mt-1">

                                            {{ $currencySymbol }}{{ number_format($variantDisplayPrice, 2) }}

                                        </span>
                                    @endif


                                    @if ($variantOutOfStock)
                                        <span class="d-block fs-12px mt-1 opacity-75">
                                            Unavailable
                                        </span>
                                    @endif

                                </button>
                            @endforeach

                        </div>


                        {{-- Hidden Variant Data --}}
                        <select id="product-variant" class="d-none">

                            <option value="">
                                Choose an option
                            </option>


                            @foreach ($product->variants as $variant)
                                @php
                                    $variantPrice = $variant->{$priceField};

                                    $variantSalePrice = $variant->{$salePriceField};

                                    $variantCartQuantity = (int) $variantCartQuantities->get((string) $variant->id, 0);

                                    $variantAvailableQuantity = $variant->track_stock
                                        ? max((int) $variant->stock_quantity - $variantCartQuantity, 0)
                                        : null;

                                    $variantOutOfStock =
                                        $variant->stock_status === 'out_of_stock' ||
                                        ($variant->track_stock && $variantAvailableQuantity <= 0);
                                @endphp


                                <option value="{{ $variant->id }}" data-price="{{ $variantPrice }}"
                                    data-sale-price="{{ $variantSalePrice }}" data-sku="{{ $variant->sku }}"
                                    data-stock="{{ $variantAvailableQuantity ?? '' }}"
                                    data-total-stock="{{ $variant->stock_quantity ?? '' }}"
                                    data-cart-quantity="{{ $variantCartQuantity }}"
                                    data-track-stock="{{ $variant->track_stock ? 1 : 0 }}"
                                    data-stock-status="{{ $variant->stock_status }}"
                                    data-low-stock="{{ $variant->low_stock_threshold ?? 0 }}"
                                    data-image="{{ $variant->image ?? '' }}" @selected($selectedVariant && $selectedVariant->id === $variant->id)
                                    @disabled($variantOutOfStock)>

                                    {{ $variant->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>
                @endif


                {{-- ====================================================
                STOCK
                ===================================================== --}}

                <div class="mb-7">

                    <p class="mb-0 text-body-emphasis" id="stock-message">

                        @if ($selectedVariant)
                            @if ($selectedVariant->stock_status === 'out_of_stock')
                                <span class="text-danger fw-semibold">
                                    Out of Stock
                                </span>
                            @elseif ($selectedVariant->track_stock && $selectedVariantAvailableQuantity <= 0)
                                <span class="text-danger fw-semibold">
                                    Maximum available quantity is already in your bag
                                </span>
                            @elseif ($selectedVariant->track_stock && $selectedVariantAvailableQuantity <= $selectedVariant->low_stock_threshold)
                                Only
                                {{ $selectedVariantAvailableQuantity }}
                                more available
                            @else
                                In Stock
                            @endif
                        @elseif ($product->has_variants)
                            Select an option to check availability.
                        @elseif ($productOutOfStock)
                            <span class="text-danger fw-semibold">
                                Out of Stock
                            </span>
                        @elseif ($product->track_stock && $product->quantity <= $product->low_stock_alert)
                            Only {{ $product->quantity }} left in stock
                        @else
                            In Stock
                        @endif

                    </p>

                </div>


                {{-- ====================================================
                ADD TO BAG
                ===================================================== --}}

                <form class="mb-9 pb-2" method="POST" action="{{ route('cart.add') }}" id="add-to-cart-form"
                    data-cart-url="{{ route('cart.add') }}">

                    @csrf

                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <input type="hidden" name="variant_id" id="selected-variant-id"
                        value="{{ $selectedVariant?->id }}">


                    <div class="row align-items-end">

                        {{-- Quantity --}}
                        <div class="form-group col-sm-4">

                            <label class="text-body-emphasis fw-semibold fs-15px pb-6" for="quantity">
                                Quantity:
                            </label>


                            <div class="input-group position-relative w-100 input-group-lg">

                                <a href="#"
                                    class="shop-down position-absolute translate-middle-y top-50 start-0 ps-7 product-info-2-minus">
                                    <i class="far fa-minus"></i>
                                </a>


                                <input name="quantity" type="number" id="quantity"
                                    class="product-info-2-quantity form-control w-100 px-6 text-center" value="1"
                                    min="1"
                                    @if ($selectedVariant && $selectedVariant->track_stock && $selectedVariantAvailableQuantity > 0) max="{{ $selectedVariantAvailableQuantity }}" @endif
                                    required>


                                <a href="#"
                                    class="shop-up position-absolute translate-middle-y top-50 end-0 pe-7 product-info-2-plus">
                                    <i class="far fa-plus"></i>
                                </a>

                            </div>

                        </div>


                        {{-- Add Button --}}
                        <div class="col-sm-8 pt-9 mt-2 mt-sm-0 pt-sm-0">

                            <button type="submit" id="add-to-cart-button"
                                class="btn-hover-bg-primary btn-hover-border-primary btn btn-lg btn-dark w-100"
                                @disabled($disableAddButton)>

                                <span class="add-to-cart-text">

                                    @if (!$product->has_variants && $productOutOfStock)
                                        Out of Stock
                                    @elseif ($product->has_variants && !$selectedVariant)
                                        Select Option
                                    @elseif ($selectedVariantOutOfStock)
                                        Out of Stock
                                    @else
                                        Add To Bag
                                    @endif

                                </span>


                                <span class="add-to-cart-loading d-none">

                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>

                                    Adding...

                                </span>

                            </button>

                        </div>

                    </div>

                </form>


                {{-- ====================================================
                PRODUCT META
                ===================================================== --}}

                <ul class="single-product-meta list-unstyled border-top pt-7 mt-7">


                    {{-- SKU --}}
                    <li class="d-flex mb-4 pb-2 align-items-center">

                        <span class="text-body-emphasis fw-semibold fs-14px">
                            SKU:
                        </span>

                        <span class="ps-4" id="product-sku">
                            {{ $currentSku ?: 'N/A' }}
                        </span>

                    </li>


                    {{-- Category --}}
                    @if ($product->category)
                        <li class="d-flex mb-4 pb-2 align-items-center">

                            <span class="text-body-emphasis fw-semibold fs-14px">
                                Collection:
                            </span>

                            <span class="ps-4">

                                {{ $product->category->name }}

                                @if ($product->subcategory)
                                    / {{ $product->subcategory->name }}
                                @endif

                            </span>

                        </li>
                    @endif


                    {{-- Material --}}
                    @if ($product->material)
                        <li class="d-flex mb-4 pb-2 align-items-center">

                            <span class="text-body-emphasis fw-semibold fs-14px">
                                Material:
                            </span>

                            <span class="ps-4">
                                {{ $product->material }}
                            </span>

                        </li>
                    @endif


                    {{-- Model --}}
                    @if ($product->model)
                        <li class="d-flex mb-4 pb-2 align-items-center">

                            <span class="text-body-emphasis fw-semibold fs-14px">
                                Model:
                            </span>

                            <span class="ps-4">
                                {{ $product->model }}
                            </span>

                        </li>
                    @endif


                    {{-- Weight --}}
                    @if ($product->weight)
                        <li class="d-flex mb-4 pb-2 align-items-center">

                            <span class="text-body-emphasis fw-semibold fs-14px">
                                Weight:
                            </span>

                            <span class="ps-4">
                                {{ $product->weight }}
                            </span>

                        </li>
                    @endif

                </ul>

            </div>

        </div>

    </section>


    {{-- ================================================================
    PRODUCT DESCRIPTION
    ================================================================= --}}

    @if ($product->description)
        <div class="border-top w-100 h-1px"></div>

        <section class="container pt-15 pb-12 pt-lg-17 pb-lg-20">

            <div class="row justify-content-center">

                <div class="col-lg-9">

                    <div class="text-center mb-10">

                        <h2 class="mb-0">
                            Product Details
                        </h2>

                    </div>

                    <div class="fs-16px lh-lg">
                        {!! nl2br(e($product->description)) !!}
                    </div>

                </div>

            </div>

        </section>
    @endif


    {{-- ================================================================
    RELATED PRODUCTS
    ================================================================= --}}

    @if ($relatedProducts->isNotEmpty())
        <div class="border-top w-100 h-1px"></div>

        <section class="container pt-15 pb-15 pt-lg-17 pb-lg-20">

            <div class="text-center">

                <h2 class="mb-12">
                    You May Also Like
                </h2>

            </div>


            <div class="slick-slider"
                data-slick-options='{
                    "arrows": true,
                    "dots": true,
                    "infinite": true,
                    "responsive": [
                        {
                            "breakpoint": 1200,
                            "settings": {
                                "arrows": false,
                                "dots": false,
                                "slidesToShow": 3
                            }
                        },
                        {
                            "breakpoint": 992,
                            "settings": {
                                "arrows": false,
                                "dots": false,
                                "slidesToShow": 2
                            }
                        },
                        {
                            "breakpoint": 576,
                            "settings": {
                                "arrows": false,
                                "dots": false,
                                "slidesToShow": 1
                            }
                        }
                    ],
                    "slidesToShow": 4
                }'>

                @foreach ($relatedProducts as $relatedProduct)
                    <div class="mb-6">

                        @include('frontend.partials.product-card', [
                            'product' => $relatedProduct,
                            'gridStyle' => 'grid-2',
                            'actionLayout' => 'vertical',
                            'compact' => false,
                            'showCategory' => false,
                            'showVariantCount' => false,
                            'showCompare' => false,
                        ])

                    </div>
                @endforeach

            </div>

        </section>
    @endif

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form =
                document.getElementById('add-to-cart-form');

            if (!form) return;


            const quantityInput =
                form.querySelector('#quantity');

            const minusButton =
                form.querySelector('.product-info-2-minus');

            const plusButton =
                form.querySelector('.product-info-2-plus');

            const addButton =
                form.querySelector('#add-to-cart-button');

            const buttonText =
                addButton.querySelector('.add-to-cart-text');

            const buttonLoading =
                addButton.querySelector('.add-to-cart-loading');

            const variantSelect =
                document.getElementById('product-variant');

            const variantButtons =
                document.querySelectorAll('.variant-option-btn');

            const selectedVariantInput =
                document.getElementById('selected-variant-id');

            const stockMessage =
                document.getElementById('stock-message');

            const regularPriceEl =
                document.getElementById('regular-price');

            const salePriceEl =
                document.getElementById('sale-price');

            const discountBadge =
                document.getElementById('discount-badge');

            const pricePrefix =
                document.getElementById('price-prefix');

            const productSku =
                document.getElementById('product-sku');


            /*
            |--------------------------------------------------------------------------
            | Initial Values
            |--------------------------------------------------------------------------
            */

            const currencySymbol =
                @json($currencySymbol);

            const initialRegularPrice =
                @json($regularPrice ? $currencySymbol . number_format($regularPrice, 2) : 'Price unavailable');

            const initialSalePrice =
                @json($hasSale ? $currencySymbol . number_format($salePrice, 2) : '');

            const initialDiscount =
                @json($hasSale ? '-' . $discountPercentage . '%' : '');

            const initialSku =
                @json($currentSku ?: 'N/A');

            const initialHasSale =
                {{ $hasSale ? 'true' : 'false' }};

            const initialShowFrom =
                {{ $product->has_variants && !$selectedVariant ? 'true' : 'false' }};


            /*
            |--------------------------------------------------------------------------
            | Format Price
            |--------------------------------------------------------------------------
            */

            function formatPrice(price) {

                return currencySymbol +
                    Number(price).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
            }


            /*
            |--------------------------------------------------------------------------
            | Reset Price
            |--------------------------------------------------------------------------
            */

            function resetPrice() {

                regularPriceEl.textContent =
                    initialRegularPrice;

                if (initialHasSale) {

                    regularPriceEl.className =
                        'text-decoration-line-through';

                    salePriceEl.textContent =
                        initialSalePrice;

                    salePriceEl.classList.remove('d-none');

                    discountBadge.textContent =
                        initialDiscount;

                    discountBadge.classList.remove('d-none');

                } else {

                    regularPriceEl.className =
                        'fs-18px text-body-emphasis fw-bold';

                    salePriceEl.textContent = '';

                    salePriceEl.classList.add('d-none');

                    discountBadge.textContent = '';

                    discountBadge.classList.add('d-none');
                }


                if (initialShowFrom) {
                    pricePrefix?.classList.remove('d-none');
                } else {
                    pricePrefix?.classList.add('d-none');
                }


                if (productSku) {
                    productSku.textContent = initialSku;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Active Variant Button
            |--------------------------------------------------------------------------
            */

            function setActiveVariantButton(variantId) {

                variantButtons.forEach(function(button) {

                    const active =
                        button.dataset.variantId ===
                        String(variantId);

                    button.classList.toggle(
                        'btn-dark',
                        active
                    );

                    button.classList.toggle(
                        'btn-outline-dark',
                        !active
                    );
                });
            }


            /*
            |--------------------------------------------------------------------------
            | Apply Variant State
            |--------------------------------------------------------------------------
            */

            function applyVariantState() {

                if (!variantSelect) return;

                const option =
                    variantSelect.options[
                        variantSelect.selectedIndex
                    ];

                const variantId =
                    variantSelect.value;

                selectedVariantInput.value =
                    variantId;

                setActiveVariantButton(
                    variantId
                );


                /*
                |--------------------------------------------------------------------------
                | No Variant Selected
                |--------------------------------------------------------------------------
                */

                if (!variantId) {

                    addButton.disabled = true;

                    buttonText.textContent =
                        'Select Option';

                    resetPrice();

                    quantityInput.value = 1;

                    quantityInput.removeAttribute(
                        'max'
                    );

                    if (stockMessage) {

                        stockMessage.textContent =
                            'Select an option to check availability.';
                    }

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Price
                |--------------------------------------------------------------------------
                */

                const price =
                    parseFloat(
                        option.dataset.price || 0
                    );

                const salePrice =
                    parseFloat(
                        option.dataset.salePrice || 0
                    );

                const hasSale =
                    price > 0 &&
                    salePrice > 0 &&
                    salePrice < price;

                pricePrefix?.classList.add(
                    'd-none'
                );


                if (hasSale) {

                    regularPriceEl.textContent =
                        formatPrice(price);

                    regularPriceEl.className =
                        'text-decoration-line-through';

                    salePriceEl.textContent =
                        formatPrice(salePrice);

                    salePriceEl.classList.remove(
                        'd-none'
                    );

                    const discount =
                        Math.round(
                            (
                                (price - salePrice) /
                                price
                            ) * 100
                        );

                    discountBadge.textContent =
                        `-${discount}%`;

                    discountBadge.classList.remove(
                        'd-none'
                    );

                } else {

                    regularPriceEl.textContent =
                        price > 0 ?
                        formatPrice(price) :
                        'Price unavailable';

                    regularPriceEl.className =
                        'fs-18px text-body-emphasis fw-bold';

                    salePriceEl.textContent = '';

                    salePriceEl.classList.add(
                        'd-none'
                    );

                    discountBadge.textContent = '';

                    discountBadge.classList.add(
                        'd-none'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | SKU
                |--------------------------------------------------------------------------
                */

                if (productSku) {

                    productSku.textContent =
                        option.dataset.sku ||
                        @json($product->sku ?: 'N/A');
                }


                /*
                |--------------------------------------------------------------------------
                | Cart-aware Stock
                |--------------------------------------------------------------------------
                */

                const stockStatus =
                    option.dataset.stockStatus;

                const trackStock =
                    parseInt(
                        option.dataset.trackStock || 0
                    );

                const available =
                    parseInt(
                        option.dataset.stock || 0
                    );

                const cartQuantity =
                    parseInt(
                        option.dataset.cartQuantity || 0
                    );

                const lowStock =
                    parseInt(
                        option.dataset.lowStock || 0
                    );


                /*
                |--------------------------------------------------------------------------
                | Actual Out Of Stock
                |--------------------------------------------------------------------------
                */

                if (
                    stockStatus ===
                    'out_of_stock'
                ) {

                    addButton.disabled = true;

                    buttonText.textContent =
                        'Out of Stock';

                    quantityInput.value = 1;

                    quantityInput.removeAttribute(
                        'max'
                    );

                    if (stockMessage) {

                        stockMessage.innerHTML =
                            '<span class="text-danger fw-semibold">Out of Stock</span>';
                    }

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Maximum Already In Cart
                |--------------------------------------------------------------------------
                */

                if (
                    trackStock === 1 &&
                    available <= 0
                ) {

                    addButton.disabled = true;

                    buttonText.textContent =
                        'No More Available';

                    quantityInput.value = 1;

                    quantityInput.removeAttribute(
                        'max'
                    );

                    if (stockMessage) {

                        stockMessage.innerHTML =
                            cartQuantity > 0 ?
                            '<span class="text-danger fw-semibold">Maximum available quantity is already in your bag</span>' :
                            '<span class="text-danger fw-semibold">Out of Stock</span>';
                    }

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Available
                |--------------------------------------------------------------------------
                */

                addButton.disabled = false;

                buttonText.textContent =
                    'Add To Bag';


                if (stockMessage) {

                    if (
                        trackStock === 1 &&
                        available <= lowStock
                    ) {

                        stockMessage.textContent =
                            `Only ${available} more available`;

                    } else {

                        stockMessage.textContent =
                            'In Stock';
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Quantity Max
                |--------------------------------------------------------------------------
                */

                if (
                    trackStock === 1 &&
                    available > 0
                ) {

                    quantityInput.max =
                        available;

                    if (
                        parseInt(
                            quantityInput.value || 1
                        ) > available
                    ) {

                        quantityInput.value =
                            available;
                    }

                } else {

                    quantityInput.removeAttribute(
                        'max'
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Variant Buttons
            |--------------------------------------------------------------------------
            */

            variantButtons.forEach(
                function(button) {

                    button.addEventListener(
                        'click',
                        function() {

                            if (
                                this.disabled ||
                                !variantSelect
                            ) {
                                return;
                            }

                            variantSelect.value =
                                this.dataset.variantId;

                            applyVariantState();
                        }
                    );
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Hidden Select Change
            |--------------------------------------------------------------------------
            */

            variantSelect?.addEventListener(
                'change',
                applyVariantState
            );


            /*
            |--------------------------------------------------------------------------
            | Quantity -
            |--------------------------------------------------------------------------
            */

            minusButton?.addEventListener(
                'click',
                function(e) {

                    e.preventDefault();

                    let quantity =
                        parseInt(
                            quantityInput.value || 1
                        );

                    if (quantity > 1) {

                        quantityInput.value =
                            quantity - 1;
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Quantity +
            |--------------------------------------------------------------------------
            */

            plusButton?.addEventListener(
                'click',
                function(e) {

                    e.preventDefault();

                    let quantity =
                        parseInt(
                            quantityInput.value || 1
                        );

                    const max =
                        parseInt(
                            quantityInput.max || 0
                        );

                    if (
                        max > 0 &&
                        quantity >= max
                    ) {

                        quantityInput.value =
                            max;

                        return;
                    }

                    quantityInput.value =
                        quantity + 1;
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Manual Quantity
            |--------------------------------------------------------------------------
            */

            quantityInput?.addEventListener(
                'change',
                function() {

                    let quantity =
                        parseInt(
                            this.value || 1
                        );

                    const max =
                        parseInt(
                            this.max || 0
                        );

                    if (
                        !quantity ||
                        quantity < 1
                    ) {
                        quantity = 1;
                    }

                    if (
                        max > 0 &&
                        quantity > max
                    ) {

                        quantity = max;

                        notyf.error(
                            `Only ${max} more item(s) can be added.`
                        );
                    }

                    this.value =
                        quantity;
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Update Variant Locally After Successful Add
            |--------------------------------------------------------------------------
            */

            function updateVariantAfterAdd(
                quantity
            ) {

                if (
                    !variantSelect ||
                    !variantSelect.value
                ) {
                    return;
                }

                const option =
                    variantSelect.options[
                        variantSelect.selectedIndex
                    ];

                const trackStock =
                    parseInt(
                        option.dataset.trackStock || 0
                    );

                quantityInput.value = 1;


                if (trackStock !== 1) {

                    applyVariantState();

                    return;
                }


                const currentAvailable =
                    parseInt(
                        option.dataset.stock || 0
                    );

                const currentCartQuantity =
                    parseInt(
                        option.dataset.cartQuantity || 0
                    );

                const newAvailable =
                    Math.max(
                        currentAvailable - quantity,
                        0
                    );

                const newCartQuantity =
                    currentCartQuantity + quantity;


                option.dataset.stock =
                    newAvailable;

                option.dataset.cartQuantity =
                    newCartQuantity;


                const button =
                    document.querySelector(
                        `.variant-option-btn[data-variant-id="${variantSelect.value}"]`
                    );


                if (
                    button &&
                    newAvailable <= 0
                ) {

                    button.disabled = true;
                }


                applyVariantState();
            }


            /*
            |--------------------------------------------------------------------------
            | Add To Cart AJAX
            |--------------------------------------------------------------------------
            */

            form.addEventListener(
                'submit',
                async function(e) {

                    e.preventDefault();


                    if (addButton.disabled) {
                        return;
                    }


                    const productId =
                        form.querySelector(
                            '[name="product_id"]'
                        ).value;


                    const variantId =
                        selectedVariantInput.value ||
                        null;


                    const quantity =
                        parseInt(
                            quantityInput.value || 1
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Variant Required
                    |--------------------------------------------------------------------------
                    */

                    if (
                        variantSelect &&
                        !variantId
                    ) {

                        notyf.error(
                            'Please select an option.'
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Frontend Stock Validation
                    |--------------------------------------------------------------------------
                    */

                    if (
                        variantSelect &&
                        variantId
                    ) {

                        const option =
                            variantSelect.options[
                                variantSelect.selectedIndex
                            ];

                        const trackStock =
                            parseInt(
                                option.dataset.trackStock || 0
                            );

                        const available =
                            parseInt(
                                option.dataset.stock || 0
                            );


                        if (
                            trackStock === 1 &&
                            quantity > available
                        ) {

                            quantityInput.value =
                                Math.max(
                                    available,
                                    1
                                );

                            notyf.error(
                                `Only ${available} more item(s) can be added.`
                            );

                            return;
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Loading
                    |--------------------------------------------------------------------------
                    */

                    addButton.disabled = true;

                    buttonText.classList.add(
                        'd-none'
                    );

                    buttonLoading.classList.remove(
                        'd-none'
                    );


                    try {

                        const csrfToken =
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            )?.content;


                        const response =
                            await fetch(
                                form.dataset.cartUrl, {
                                    method: 'POST',

                                    headers: {
                                        'Content-Type': 'application/json',

                                        'Accept': 'application/json',

                                        'X-CSRF-TOKEN': csrfToken
                                    },

                                    body: JSON.stringify({
                                        product_id: productId,

                                        variant_id: variantId,

                                        quantity: quantity
                                    })
                                }
                            );


                        const data =
                            await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | Server Stock Correction
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !response.ok ||
                            !data.status
                        ) {

                            if (
                                variantSelect &&
                                variantId &&
                                data.available_quantity !==
                                undefined
                            ) {

                                const option =
                                    variantSelect.options[
                                        variantSelect.selectedIndex
                                    ];

                                option.dataset.stock =
                                    Math.max(
                                        parseInt(
                                            data.available_quantity ||
                                            0
                                        ),
                                        0
                                    );


                                if (
                                    data.cart_quantity !==
                                    undefined
                                ) {

                                    option.dataset.cartQuantity =
                                        parseInt(
                                            data.cart_quantity ||
                                            0
                                        );
                                }


                                const button =
                                    document.querySelector(
                                        `.variant-option-btn[data-variant-id="${variantId}"]`
                                    );


                                if (
                                    button &&
                                    parseInt(
                                        option.dataset.stock ||
                                        0
                                    ) <= 0
                                ) {

                                    button.disabled =
                                        true;
                                }


                                applyVariantState();
                            }


                            throw new Error(
                                data.message ||
                                'Unable to add product to cart.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Update Header + Sidebar
                        |--------------------------------------------------------------------------
                        */

                        updateCartUI(data);


                        /*
                        |--------------------------------------------------------------------------
                        | Update Remaining Stock On Page
                        |--------------------------------------------------------------------------
                        */

                        updateVariantAfterAdd(
                            quantity
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | Notification
                        |--------------------------------------------------------------------------
                        */

                        notyf.success(
                            data.message ||
                            'Product added to cart.'
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | Open Shopping Bag
                        |--------------------------------------------------------------------------
                        */

                        const shoppingCart =
                            document.getElementById(
                                'shoppingCart'
                            );


                        if (
                            shoppingCart &&
                            typeof bootstrap !==
                            'undefined'
                        ) {

                            bootstrap.Offcanvas
                                .getOrCreateInstance(
                                    shoppingCart
                                )
                                .show();
                        }

                    } catch (error) {

                        notyf.error(
                            error.message ||
                            'Unable to add product to cart.'
                        );

                    } finally {

                        buttonText.classList.remove(
                            'd-none'
                        );

                        buttonLoading.classList.add(
                            'd-none'
                        );


                        if (variantSelect) {

                            applyVariantState();

                        } else {

                            addButton.disabled =
                                {{ $productOutOfStock ? 'true' : 'false' }};
                        }
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Initialise Default Variant
            |--------------------------------------------------------------------------
            */

            if (
                variantSelect &&
                variantSelect.value
            ) {

                applyVariantState();
            }

        });
    </script>
@endpush
