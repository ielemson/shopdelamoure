<section id="shop_categories">

    <div class="pt-14 pb-14 pt-lg-19">

        <div class="container container-xxl mb-13 d-xl-flex">

            <div class="flex-grow-1 text-left" data-animate="fadeInUp">

                <h2 class="mb-5">Shop our Collections</h2>

                <p class="fs-18px mb-0 mw-xl-40 mw-lg-50 mw-md-75">
                    Explore our collections and discover your perfect fragrance.
                </p>

            </div>

        </div>

        @if ($categories->isNotEmpty())

            <div class="container-fluid mb-4">

                <div class="slick-slider our-best-seller-4"
                    data-slick-options='{
                        "arrows": true,
                        "centerMode": true,
                        "centerPadding": "calc((100% - 1440px) / 2)",
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

                    @foreach ($categories as $category)
                        <div data-animate="fadeInUp">

                            <div class="card card-product grid-1 bg-transparent border-0">

                                <figure class="card-img-top position-relative mb-5 overflow-hidden">

                                    <a href="#" class="hover-zoom-in d-block" title="{{ $category->name }}">

                                        <img src="{{ $category->image ? asset($category->image) : asset('assets/images/products/product-01-330x440.jpg') }}"
                                            class="img-fluid w-100" alt="{{ $category->name }}" width="330"
                                            height="440" style="aspect-ratio: 3 / 4; object-fit: cover;">

                                    </a>

                                </figure>

                                <div class="card-body text-center p-0">

                                    <h4 class="card-title text-primary-hover text-body-emphasis fs-18px fw-500 mb-0">

                                        <a class="text-decoration-none text-reset" href="#">

                                            {{ $category->name }}

                                        </a>

                                    </h4>

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>

        @endif

    </div>

</section>
