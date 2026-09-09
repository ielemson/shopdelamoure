@extends('layouts.app')

@section('meta_title', 'Compare Products | Dela Moure')

@section('meta_description',
    'Compare your selected Dela Moure fragrances, diffusers, candles, essential oils and gift
    sets.')

@section('PageContent')

    @include('frontend.partials.breadcrumb', [
        'title' => 'Compare Products',
        'item' => 'Compare',
    ])


    <section class="py-14 py-lg-18">

        <div class="container container-xxl">

            {{-- Empty Compare --}}
            <div id="compare-empty-state" class="text-center py-15 {{ $products->isNotEmpty() ? 'd-none' : '' }}">

                <h2 class="fs-30px mb-4">
                    Your compare list is empty
                </h2>

                <p class="fs-18px text-muted mb-7">
                    Add products to compare their features, prices and details.
                </p>

                <a href="{{ route('shop') }}" class="btn btn-dark px-8">

                    Continue Shopping

                </a>

            </div>


            {{-- Compare Products --}}
            @if ($products->isNotEmpty())

                <div id="compare-products-wrapper" class="table-responsive">

                    <table class="table table-bordered align-middle text-center compare-table mb-0">

                        <tbody>

                            {{-- Product Image --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Product
                                </th>

                                @foreach ($products as $product)
                                    <td class="compare-product-column" data-compare-item="{{ $product->id }}">

                                        <div class="position-relative">

                                            {{-- Remove --}}
                                            <button type="button"
                                                class="compare-toggle btn btn-link text-body p-0 position-absolute top-0 end-0 z-index-2"
                                                data-product-id="{{ $product->id }}"
                                                data-url="{{ route('compare.toggle', $product->id) }}"
                                                data-csrf="{{ csrf_token() }}" aria-label="Remove From Compare"
                                                data-bs-title="Remove From Compare" data-bs-toggle="tooltip">

                                                <svg class="icon icon-close" width="18" height="18">

                                                    <use xlink:href="#icon-close"></use>

                                                </svg>

                                            </button>


                                            {{-- Product Image --}}
                                            <a href="{{ route('product.show', $product->slug) }}" class="d-block">

                                                @if (!empty($product->image))
                                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                                                        class="img-fluid"
                                                        style="
                                                            width: 220px;
                                                            height: 220px;
                                                            object-fit: contain;
                                                        ">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center mx-auto"
                                                        style="
                                                            width: 220px;
                                                            height: 220px;
                                                        ">

                                                        <span class="text-muted">
                                                            No Image
                                                        </span>

                                                    </div>
                                                @endif

                                            </a>

                                        </div>

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Product Name --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Product Name
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}">

                                        <a href="{{ route('product.show', $product->slug) }}"
                                            class="text-decoration-none text-body fw-semibold">

                                            {{ $product->name }}

                                        </a>

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Price --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Price
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}" class="fw-semibold">

                                        {{ $product->formatted_price ?? number_format($product->price, 2) }}

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Category --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Category
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}">

                                        {{ optional($product->category)->name ?? '—' }}

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Description --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Description
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}">

                                        {{ \Illuminate\Support\Str::limit(
                                            strip_tags($product->short_description ?? ($product->description ?? '')),
                                            120,
                                        ) ?:
                                            '—' }}

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Availability --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Availability
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}">

                                        @if (($product->stock ?? 0) > 0)
                                            <span class="text-success fw-semibold">
                                                In Stock
                                            </span>
                                        @else
                                            <span class="text-danger fw-semibold">
                                                Out of Stock
                                            </span>
                                        @endif

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Action --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Action
                                </th>

                                @foreach ($products as $product)
                                    @php
                                        $productUrl = route('product.show', $product->slug);
                                    @endphp

                                    <td data-compare-item="{{ $product->id }}">

                                        @if (($product->stock ?? 0) <= 0)
                                            <button type="button" class="btn btn-secondary px-6" disabled>

                                                Out of Stock

                                            </button>
                                        @elseif ($product->has_variants)
                                            <a href="{{ $productUrl }}" class="btn btn-dark px-6">

                                                Choose Options

                                            </a>
                                        @else
                                            <a href="javascript:void(0)" class="btn btn-dark px-6 add_to_cart"
                                                data-product-id="{{ $product->id }}"
                                                data-cart-url="{{ route('cart.add') }}">

                                                <span class="cart-icon">
                                                    Add To Cart
                                                </span>

                                                <span class="cart-loading d-none">
                                                    Adding...
                                                </span>

                                            </a>
                                        @endif

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Remove --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Remove
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}">

                                        <button type="button"
                                            class="compare-toggle btn btn-link text-danger text-decoration-none p-0"
                                            data-product-id="{{ $product->id }}"
                                            data-url="{{ route('compare.toggle', $product->id) }}"
                                            data-csrf="{{ csrf_token() }}" aria-label="Remove From Compare">

                                            Remove

                                        </button>

                                    </td>
                                @endforeach

                            </tr>

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </section>

@endsection
@extends('layouts.app')

@section('meta_title', 'Compare Products | Dela Moure')

@section('meta_description',
    'Compare your selected Dela Moure fragrances, diffusers, candles, essential oils and gift
    sets.')

@section('PageContent')

    @include('frontend.partials.breadcrumb', [
        'title' => 'Compare Products',
        'item' => 'Compare',
    ])


    <section class="py-14 py-lg-18">

        <div class="container container-xxl">

            {{-- Empty Compare --}}
            <div id="compare-empty-state" class="text-center py-15 {{ $products->isNotEmpty() ? 'd-none' : '' }}">

                <h2 class="fs-30px mb-4">
                    Your compare list is empty
                </h2>

                <p class="fs-18px text-muted mb-7">
                    Add products to compare their features, prices and details.
                </p>

                <a href="{{ route('shop') }}" class="btn btn-dark px-8">

                    Continue Shopping

                </a>

            </div>


            {{-- Compare Products --}}
            @if ($products->isNotEmpty())

                <div id="compare-products-wrapper" class="table-responsive">

                    <table class="table table-bordered align-middle text-center compare-table mb-0">

                        <tbody>

                            {{-- Product Image --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Product
                                </th>

                                @foreach ($products as $product)
                                    <td class="compare-product-column" data-compare-item="{{ $product->id }}">

                                        <div class="position-relative">

                                            {{-- Remove --}}
                                            <button type="button"
                                                class="compare-toggle btn btn-link text-body p-0 position-absolute top-0 end-0 z-index-2"
                                                data-product-id="{{ $product->id }}"
                                                data-url="{{ route('compare.toggle', $product->id) }}"
                                                data-csrf="{{ csrf_token() }}" aria-label="Remove From Compare"
                                                data-bs-title="Remove From Compare" data-bs-toggle="tooltip">

                                                <svg class="icon icon-close" width="18" height="18">

                                                    <use xlink:href="#icon-close"></use>

                                                </svg>

                                            </button>


                                            {{-- Product Image --}}
                                            <a href="{{ route('product.show', $product->slug) }}" class="d-block">

                                                @if (!empty($product->image))
                                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                                                        class="img-fluid"
                                                        style="
                                                            width: 220px;
                                                            height: 220px;
                                                            object-fit: contain;
                                                        ">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center mx-auto"
                                                        style="
                                                            width: 220px;
                                                            height: 220px;
                                                        ">

                                                        <span class="text-muted">
                                                            No Image
                                                        </span>

                                                    </div>
                                                @endif

                                            </a>

                                        </div>

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Product Name --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Product Name
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}">

                                        <a href="{{ route('product.show', $product->slug) }}"
                                            class="text-decoration-none text-body fw-semibold">

                                            {{ $product->name }}

                                        </a>

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Price --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Price
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}" class="fw-semibold">

                                        {{ $product->formatted_price ?? number_format($product->price, 2) }}

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Category --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Category
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}">

                                        {{ optional($product->category)->name ?? '—' }}

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Description --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Description
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}">

                                        {{ \Illuminate\Support\Str::limit(
                                            strip_tags($product->short_description ?? ($product->description ?? '')),
                                            120,
                                        ) ?:
                                            '—' }}

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Availability --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Availability
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}">

                                        @if (($product->stock ?? 0) > 0)
                                            <span class="text-success fw-semibold">
                                                In Stock
                                            </span>
                                        @else
                                            <span class="text-danger fw-semibold">
                                                Out of Stock
                                            </span>
                                        @endif

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Action --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Action
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}">

                                        @if (($product->stock ?? 0) > 0)
                                            {{-- 
                                                Replace this with the same
                                                Add To Cart button/partial
                                                already used on your product cards.
                                            --}}

                                            <button type="button" class="btn btn-dark px-6 add-to-cart"
                                                data-product-id="{{ $product->id }}">

                                                Add To Cart

                                            </button>
                                        @else
                                            <button type="button" class="btn btn-secondary px-6" disabled>

                                                Out of Stock

                                            </button>
                                        @endif

                                    </td>
                                @endforeach

                            </tr>


                            {{-- Remove --}}
                            <tr>

                                <th class="text-start compare-label">
                                    Remove
                                </th>

                                @foreach ($products as $product)
                                    <td data-compare-item="{{ $product->id }}">

                                        <button type="button"
                                            class="compare-toggle btn btn-link text-danger text-decoration-none p-0"
                                            data-product-id="{{ $product->id }}"
                                            data-url="{{ route('compare.toggle', $product->id) }}"
                                            data-csrf="{{ csrf_token() }}" aria-label="Remove From Compare">

                                            Remove

                                        </button>

                                    </td>
                                @endforeach

                            </tr>

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </section>

@endsection
