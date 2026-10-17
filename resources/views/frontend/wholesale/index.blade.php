@extends('layouts.app')

@section('meta_title', 'Wholesale | Dela Moure Fragrances & Home Scents')
@section('meta_description', 'Explore Dela Moure wholesale products, minimum order quantities and volume pricing for
    fragrances and home scent essentials.')

@section('PageContent')
    <section class="pb-lg-20 pb-16">
        @include('frontend.partials.breadcrumb', [
            'title' => 'Wholesale',
            'item' => 'Bulk Purchasing',
        ])
    </section>

    <section class="container container-xxl pb-16">
        <div class="mb-9">
            <h1 class="fs-3 mb-4">Wholesale Purchasing</h1>
            <p class="text-muted mb-2">
                Browse products available for bulk purchase. Open a product to see its
                wholesale prices, quantity discounts and available options.
            </p>
            <p class="small text-muted mb-0">
                Wholesale prices and price filters on this page are in Nigerian naira (NGN).
                Each product has its own minimum order quantity.
            </p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="tool-bar mb-11 align-items-center justify-content-between d-lg-flex">
            <div class="tool-bar-left mb-6 mb-lg-0 fs-18px">
                We found <span class="text-body-emphasis fw-semibold">{{ $products->total() }}</span>
                {{ \Illuminate\Support\Str::plural('wholesale product', $products->total()) }}
            </div>
            <div class="tool-bar-right align-items-center d-flex flex-wrap gap-3">
                <form method="GET" action="{{ url('/wholesale') }}">
                    @foreach (['q', 'category', 'min_price', 'max_price'] as $filter)
                        @if (request()->filled($filter))
                            <input type="hidden" name="{{ $filter }}" value="{{ request($filter) }}">
                        @endif
                    @endforeach
                    <label class="visually-hidden" for="wholesale-sort">Sort wholesale products</label>
                    <select class="form-select w-100 w-lg-auto" name="sort" id="wholesale-sort"
                        onchange="this.form.submit()">
                        @foreach ([
            'latest' => 'Sort by latest',
            'name_asc' => 'Name: A to Z',
            'name_desc' => 'Name: Z to A',
            'price_low' => 'Base price: Low to High',
            'price_high' => 'Base price: High to Low',
        ] as $value => $label)
                            <option value="{{ $value }}" @selected(($sort ?? 'latest') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
                <button type="button" class="btn btn-dark" data-bs-toggle="offcanvas" data-bs-target="#wholesaleFilters"
                    aria-controls="wholesaleFilters">
                    <svg class="icon icon-SlidersHorizontal fs-4 me-3" aria-hidden="true">
                        <use xlink:href="#icon-SlidersHorizontal"></use>
                    </svg>
                    Filter
                </button>
            </div>
        </div>

        <div class="row gy-11">
            @forelse ($products as $product)
                @php
                    $minimum = (int) $product->wholesale_min_quantity;
                    $variants = $product->has_variants ? $product->activeVariants : collect();
                    $lowestVariant = $variants->sortBy('wholesale_price_ngn')->first();
                    $primaryImage = $product->images->firstWhere('is_primary', 1);
                    $firstImage = $product->images->first();
                    $productImage =
                        $product->main_image ?: $primaryImage?->image ?: $firstImage?->image ?: $lowestVariant?->image;
                    $basePrice = $product->wholesale_from_price_ngn;
                    $productUrl = url('shop/wholesale/' . $product->slug);

                    $canFulfil = function ($variant) use ($minimum) {
                        return $variant->track_stock
                            ? $variant->stock_status === 'in_stock' && (int) $variant->stock_quantity >= $minimum
                            : $variant->stock_status !== 'out_of_stock';
                    };
                    if (!$product->has_variants) {
                        $wholesaleInStock = $product->track_stock
                            ? $product->stock_status === 'in_stock' && (int) $product->quantity >= $minimum
                            : $product->stock_status !== 'out_of_stock';
                        $hasTiers = $product->wholesalePriceTiers->isNotEmpty();
                    } else {
                        if ($product->wholesale_allow_mixed_variants) {
                            $untrackedAvailable = $variants->contains(
                                fn($variant) => !$variant->track_stock && $variant->stock_status !== 'out_of_stock',
                            );
                            $availableUnits = $variants
                                ->filter(fn($variant) => $variant->track_stock && $variant->stock_status === 'in_stock')
                                ->sum('stock_quantity');
                            $wholesaleInStock = $untrackedAvailable || $availableUnits >= $minimum;
                        } else {
                            $wholesaleInStock = $variants->contains($canFulfil);
                        }
                        $hasTiers = $variants->contains(fn($variant) => $variant->wholesalePriceTiers->isNotEmpty());
                    }
                @endphp

                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-12">
                    <div class="card card-product grid-1 bg-transparent border-0 h-100" data-animate="fadeInUp">
                        <figure class="card-img-top position-relative mb-7 overflow-hidden">
                            <a href="{{ $productUrl }}" class="hover-zoom-in d-block" title="{{ $product->name }}">
                                <img src="{{ asset($productImage ?: 'assets/images/products/product-placeholder.jpg') }}"
                                    class="img-fluid w-100" alt="{{ $product->name }}" width="330" height="440"
                                    loading="lazy" style="aspect-ratio: 3 / 4; object-fit: cover;">
                            </a>
                            <div class="position-absolute product-flash z-index-2">
                                <span class="badge badge-product-flash bg-primary">Wholesale</span>
                            </div>
                        </figure>
                        <div class="card-body text-center p-0 d-flex flex-column">
                            @if ($product->category)
                                <div class="text-muted fs-13px mb-2">{{ $product->category->name }}</div>
                            @endif
                            <h2 class="product-title card-title text-primary-hover text-body-emphasis fs-15px fw-500 mb-3">
                                <a class="text-decoration-none text-reset"
                                    href="{{ $productUrl }}">{{ $product->name }}</a>
                            </h2>
                            <div class="price text-body-emphasis fw-bold mb-2 fs-6">
                                @if ($basePrice !== null)
                                    @if ($product->has_variants)
                                        From
                                    @endif
                                    ₦{{ number_format((float) $basePrice, 2) }}
                                    <span class="small fw-normal">per unit</span>
                                @else
                                    <span class="text-muted">Price unavailable</span>
                                @endif
                            </div>
                            <p class="small text-muted mb-2">Base price at the minimum quantity</p>
                            <p class="fs-13px mb-2">Minimum order: <strong>{{ number_format($minimum) }} units</strong></p>
                            @if ($product->has_variants)
                                <p class="fs-13px text-muted mb-2">
                                    {{ $variants->count() }}
                                    {{ \Illuminate\Support\Str::plural('option', $variants->count()) }}
                                    ·
                                    {{ $product->wholesale_allow_mixed_variants ? 'Mixed options allowed' : 'Minimum per option' }}
                                </p>
                            @endif
                            @if ($hasTiers)
                                <p class="small text-primary mb-2">Additional volume discounts available</p>
                            @endif
                            @if (!$wholesaleInStock)
                                <p class="small text-muted mb-2">Wholesale quantity currently unavailable</p>
                            @endif
                            <a href="{{ $productUrl }}" class="btn btn-outline-dark btn-sm mt-3">
                                View Wholesale Options
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-15">
                        <h2 class="fs-4 mb-4">No wholesale products found</h2>
                        <p class="text-muted mb-6">No wholesale products match your current selection.</p>
                        <a href="{{ url('/wholesale') }}" class="btn btn-dark">Clear Filters</a>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($products->hasPages())
            <div class="mt-13 mb-15 d-flex justify-content-center">{{ $products->links() }}</div>
        @endif
    </section>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="wholesaleFilters" aria-labelledby="wholesaleFiltersTitle">
        <div class="offcanvas-header">
            <h2 class="offcanvas-title fs-5" id="wholesaleFiltersTitle">Filter Wholesale Products</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form method="GET" action="{{ url('/wholesale') }}">
                <input type="hidden" name="sort" value="{{ $sort ?? 'latest' }}">
                <div class="mb-4">
                    <label class="form-label" for="wholesale-search">Search Products</label>
                    <input type="search" name="q" id="wholesale-search" class="form-control" maxlength="255"
                        value="{{ request('q') }}" placeholder="Product name or SKU">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="wholesale-category">Category</label>
                    <select name="category" id="wholesale-category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="wholesale-min-price">Minimum Base Unit Price (₦)</label>
                    <input type="number" name="min_price" id="wholesale-min-price" class="form-control" min="0"
                        step="0.01" value="{{ request('min_price') }}">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="wholesale-max-price">Maximum Base Unit Price (₦)</label>
                    <input type="number" name="max_price" id="wholesale-max-price" class="form-control" min="0"
                        step="0.01" value="{{ request('max_price') }}">
                </div>
                <p class="small text-muted">Price filters compare base wholesale unit prices before higher quantity
                    discounts.</p>
                <button type="submit" class="btn btn-dark w-100 mb-3">Apply Filters</button>
                <a href="{{ url('/wholesale') }}" class="btn btn-outline-dark w-100">Clear Filters</a>
            </form>
        </div>
    </div>
@endsection
