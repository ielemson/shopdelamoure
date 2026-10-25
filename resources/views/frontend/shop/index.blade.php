@extends('layouts.app')


@section('meta_title', 'Shop Dela Moure | Perfumes, Diffusers, Candles & Gift Sets')

@section('meta_description',
    'Shop Dela Moure for luxury perfumes, diffusers, essential oils, candles, room fragrances,
    gift sets and premium scenting essentials designed for everyday elegance.')

    {{-- @section('meta_image', asset('assets/images/others/social-share.jpg')) --}}

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
                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-12">
                    @include('frontend.partials.product-card', [
                        'product' => $product,
                        'gridStyle' => 'grid-1',
                        'actionLayout' => 'horizontal',
                        'compact' => false,
                        'showCategory' => true,
                        'showVariantCount' => true,
                        'showCompare' => false,
                        'showFromPrice' => true,
                    ])
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
