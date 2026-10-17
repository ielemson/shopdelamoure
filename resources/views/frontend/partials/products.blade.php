@if (isset($products) && $products->isNotEmpty())
    <section class="dela-product-section">

        <div class="container py-8 py-lg-12">

            {{-- Section Label and Description --}}
            <div class="dela-product-heading text-center mb-10 mb-lg-12" data-animate="fadeInUp">

                <h2 class="h3 mb-0">
                    {{ $title }}
                </h2>

                @if (!empty($subtitle))
                    <p class="fs-16px text-body mt-4 mb-0">
                        {{ $subtitle }}
                    </p>
                @endif

            </div>

            {{-- Product Grid --}}
            <div class="row dela-product-grid">

                @foreach ($products as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 dela-product-column" data-animate="fadeInUp">

                        @include('frontend.partials.product-card', [
                            'product' => $product,
                            'gridStyle' => 'grid-1',
                            'actionLayout' => 'horizontal',
                            'compact' => false,
                            'showCategory' => false,
                            'showVariantCount' => false,
                            'showCompare' => true,
                        ])

                    </div>
                @endforeach

            </div>

            {{-- Shop All Products --}}
            <div class="text-center mt-10 mt-lg-12" data-animate="fadeInUp">

                <a href="{{ $viewAllUrl ?? (Route::has('shop') ? route('shop') : url('/shop')) }}"
                    class="btn btn-outline-dark dela-shop-all">

                    <span>Shop All Products</span>

                    <svg class="icon ms-3" aria-hidden="true" focusable="false">
                        <use xlink:href="#icon-arrow-right"></use>
                    </svg>

                </a>

            </div>

        </div>

    </section>
@endif

@once
    @push('styles')
        <style>
            .dela-product-section .dela-product-heading {
                max-width: 680px;
                margin-right: auto;
                margin-left: auto;
            }

            .dela-product-section .dela-product-heading h2 {
                line-height: 1.3;
            }

            .dela-product-section .dela-product-heading p {
                line-height: 1.75;
            }

            .dela-product-section .dela-product-grid {
                row-gap: 50px;
            }

            .dela-product-section .dela-product-column {
                min-width: 0;
            }

            .dela-product-section .dela-product-column>.card {
                height: 100%;
            }

            .dela-product-section .card-product {
                background: transparent;
                border: 0;
                border-radius: 0;
            }

            .dela-product-section .card-product .card-body {
                padding: 0;
                text-align: center;
            }

            .dela-product-section .card-product .price {
                justify-content: center;
                flex-wrap: wrap;
                row-gap: 4px;
            }

            .dela-product-section .card-product .product-title {
                font-size: 15px;
                line-height: 1.6;
            }

            .dela-product-section .dela-shop-all {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 50px;
                padding: 14px 30px;
                border-radius: 0;
                font-size: 14px;
                font-weight: 600;
            }

            .dela-product-section .dela-shop-all .icon {
                width: 18px;
                height: 18px;
                flex-shrink: 0;
            }

            .dela-product-section .dela-shop-all:focus-visible {
                outline: 3px solid #b89156;
                outline-offset: 4px;
            }

            @media (max-width: 575.98px) {
                .dela-product-section .dela-product-grid {
                    row-gap: 36px;
                }

                .dela-product-section .dela-shop-all {
                    width: 100%;
                    max-width: 300px;
                }
            }
        </style>
    @endpush
@endonce
