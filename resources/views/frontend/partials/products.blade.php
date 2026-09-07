<section>

    <div class="container container-xxl py-lg-17 pt-14 pb-16">

        <div class="mb-13 pb-3 text-center" data-animate="fadeInUp">

            <h2 class="mb-5">
                {{ $title }}
            </h2>

            @if (!empty($subtitle))
                <p class="fs-18px mb-0">
                    {{ $subtitle }}
                </p>
            @endif

        </div>


        <div class="slick-slider"
            data-slick-options='{
                "arrows": true,
                "dots": false,
                "responsive": [
                    {
                        "breakpoint": 1560,
                        "settings": {
                            "arrows": false,
                            "dots": true
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
                ],
                "slidesToShow": 4
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
