</section>
<section id="shop_categories" class="shop-categories-section">

    <div class="container container-xxl py-8 py-lg-10">

        {{-- =========================================================
            SECTION HEADING
        ========================================================== --}}
        <div class="mb-7 d-xl-flex">

            <div class="flex-grow-1 text-start" data-animate="fadeInUp">

                <span class="text-uppercase fs-13px fw-semibold ls-2 text-primary d-block mb-3">
                    Explore Dela Moure
                </span>

                <h2 class="mb-4">
                    Shop Our Collections
                </h2>

                <p class="fs-18px mb-0 mw-xl-40 mw-lg-50 mw-md-75">
                    Explore our collections and discover your perfect fragrance.
                </p>

            </div>

        </div>

        @if (isset($categories) && $categories->isNotEmpty())

            <div class="container-fluid mb-4">

                <div class="slick-slider dela-category-slider"
                    data-slick-options='{
                        "arrows": true,
                        "centerMode": true,
                        "centerPadding": "calc((100% - 1440px) / 2)",
                        "dots": true,
                        "infinite": true,
                        "speed": 600,
                        "slidesToShow": 4,
                        "slidesToScroll": 1,
                        "autoplay": true,
                        "autoplaySpeed": 4500,
                        "pauseOnHover": true,

                        "responsive": [

                            {
                                "breakpoint": 1200,
                                "settings": {
                                    "arrows": false,
                                    "dots": false,
                                    "centerMode": false,
                                    "slidesToShow": 3
                                }
                            },

                            {
                                "breakpoint": 992,
                                "settings": {
                                    "arrows": false,
                                    "dots": false,
                                    "centerMode": false,
                                    "slidesToShow": 2
                                }
                            },

                            {
                                "breakpoint": 576,
                                "settings": {
                                    "arrows": false,
                                    "dots": false,
                                    "centerMode": false,
                                    "slidesToShow": 1
                                }
                            }

                        ]
                    }'>


                    @foreach ($categories as $category)
                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | Placeholder
                            |--------------------------------------------------------------------------
                            */

                            $placeholder = asset('assets/images/products/product-placeholder.jpg');

                            /*
                            |--------------------------------------------------------------------------
                            | Category Image
                            |--------------------------------------------------------------------------
                            |
                            | Keep the same logic that worked previously.
                            |
                            */

                            $categoryImage = null;

                            if (!empty($category->image)) {
                                /*
                                |--------------------------------------------------------------------------
                                | Remove leading slash
                                |--------------------------------------------------------------------------
                                */

                                $imagePath = ltrim($category->image, '/');

                                /*
                                |--------------------------------------------------------------------------
                                | Check public directory directly
                                |--------------------------------------------------------------------------
                                */

                                if (file_exists(public_path($imagePath))) {
                                    $categoryImage = asset($imagePath);
                                }
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Fallback
                            |--------------------------------------------------------------------------
                            */

                            $categoryImage = $categoryImage ?: $placeholder;

                        @endphp


                        <div class="px-2 px-lg-3" data-animate="fadeInUp">

                            <article class="dela-category-card position-relative overflow-hidden">

                                <a href="{{ route('shop.category', $category->slug) }}"
                                    class="d-block position-relative text-decoration-none"
                                    title="Shop {{ $category->name }}">

                                    {{-- =====================================================
                                        CATEGORY IMAGE
                                    ====================================================== --}}
                                    <figure class="dela-category-image mb-0">

                                        <img src="{{ $categoryImage }}" alt="{{ $category->name }}" width="420"
                                            height="540" loading="lazy" class="w-100"
                                            onerror="
                                                this.onerror=null;
                                                this.src='{{ $placeholder }}';
                                            ">

                                    </figure>


                                    {{-- =====================================================
                                        OVERLAY
                                    ====================================================== --}}
                                    <div class="dela-category-overlay"></div>


                                    {{-- =====================================================
                                        CATEGORY CONTENT
                                    ====================================================== --}}
                                    <div class="dela-category-content">

                                        @if (isset($category->products_count))
                                            <span class="dela-category-count d-block mb-2">

                                                {{ $category->products_count }}

                                                {{ \Illuminate\Support\Str::plural('Product', $category->products_count) }}

                                            </span>
                                        @endif


                                        <h3 class="dela-category-title mb-4">
                                            {{ $category->name }}
                                        </h3>


                                        <span class="dela-category-link">

                                            Shop Collection

                                            <span class="ms-2 dela-category-arrow">
                                                &rarr;
                                            </span>

                                        </span>

                                    </div>

                                </a>

                            </article>

                        </div>
                    @endforeach

                </div>

            </div>

        @endif

    </div>

</section>
