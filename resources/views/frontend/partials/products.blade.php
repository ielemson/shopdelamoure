<section class="dela-product-section">

    <div class="container container-xxl py-8 py-lg-10">

        <div class="row align-items-end mb-7">

            <div class="col-sm-8" data-animate="fadeInUp">

                <h2 class="h3 mb-0">
                    {{ $title }}
                </h2>

                @if (!empty($subtitle))
                    <p class="fs-16px text-body mb-0 mt-3 mw-lg-75">
                        {{ $subtitle }}
                    </p>
                @endif

            </div>


            <div class="col-md-4 col-sm-4 text-sm-end mt-4 mt-sm-0" data-animate="fadeInUp">

                <a href="{{ $viewAllUrl ?? (Route::has('shop') ? route('shop') : url('/shop')) }}"
                    class="btn btn-link p-0 text-body-emphasis text-decoration-none fw-semibold text-primary-hover dela-shop-all">

                    Shop All Products

                    <svg class="icon ms-1">
                        <use xlink:href="#icon-arrow-right"></use>
                    </svg>

                </a>

            </div>

        </div>


        {{-- =========================================================
            PRODUCT SLIDER
        ========================================================== --}}
        <div class="slick-slider dela-product-slider" data-animate="fadeInUp"
            data-slick-options='{
                "arrows": true,
                "dots": false,
                "infinite": true,
                "speed": 500,
                "slidesToShow": 5,
                "slidesToScroll": 1,
                "responsive": [

                    {
                        "breakpoint": 1560,
                        "settings": {
                            "arrows": false,
                            "dots": true,
                            "slidesToShow": 5
                        }
                    },

                    {
                        "breakpoint": 1200,
                        "settings": {
                            "arrows": false,
                            "dots": true,
                            "slidesToShow": 3
                        }
                    },

                    {
                        "breakpoint": 992,
                        "settings": {
                            "arrows": false,
                            "dots": true,
                            "slidesToShow": 2
                        }
                    },

                    {
                        "breakpoint": 576,
                        "settings": {
                            "arrows": false,
                            "dots": true,
                            "slidesToShow": 1
                        }
                    }

                ]
            }'>


            @foreach ($products as $product)
                <div>

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

    </div>

</section>
