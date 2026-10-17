<section id="shop_categories" class="container pt-15 pt-lg-17 pb-12">

    {{-- Section Heading --}}
    <div class="mb-12 text-center" data-animate="fadeInUp">

        <span class="text-uppercase fs-13px fw-semibold ls-2 text-primary d-block mb-3">
            Explore De Lamoure
        </span>

        <h2 class="fs-3 mb-6">
            Shop Our Collections
        </h2>

        <p class="fs-18px mb-0">
            Discover signature scents for your home, bathroom and car.
        </p>

    </div>

    @if (isset($categories) && $categories->isNotEmpty())

        <div class="row gy-30px">

            @foreach ($categories as $category)
                @php
                    $placeholder = asset('assets/images/products/product-placeholder.jpg');

                    $categoryImage = $placeholder;

                    if (!empty($category->image)) {
                        $imagePath = ltrim($category->image, '/');

                        if (is_file(public_path($imagePath))) {
                            $categoryImage = asset($imagePath);
                        }
                    }

                    $categoryUrl = route('shop.category', $category->slug);
                @endphp

                <div class="col-md-4 col-sm-6 col-12" data-animate="fadeInUp">

                    <article class="card border-0 rounded-0 hover-zoom-in hover-shine dela-collection-card">

                        <img src="{{ $categoryImage }}" alt="{{ $category->name }} collection" width="370"
                            height="490" loading="lazy" decoding="async"
                            class="card-img rounded-0 dela-collection-image"
                            onerror="this.onerror=null;this.src='{{ $placeholder }}';">

                        <div class="card-img-overlay d-flex flex-column p-6 p-lg-9 justify-content-end text-center">

                            <div class="position-relative dela-collection-content">

                                @if (isset($category->products_count))
                                    <p class="dela-collection-count mb-3">
                                        {{ number_format($category->products_count) }}
                                        {{ \Illuminate\Support\Str::plural('Product', $category->products_count) }}
                                    </p>
                                @endif

                                <h3 class="mb-0">
                                    <a href="{{ $categoryUrl }}"
                                        class="btn btn-white dela-collection-button stretched-link"
                                        aria-label="Shop {{ $category->name }}">
                                        {{ $category->name }}

                                        <span class="ms-2" aria-hidden="true">
                                            &rarr;
                                        </span>
                                    </a>
                                </h3>

                            </div>

                        </div>

                    </article>

                </div>
            @endforeach

        </div>

    @endif

</section>

@push('styles')
    <style>
        #shop_categories .dela-collection-card {
            isolation: isolate;
            overflow: hidden;
            background-color: #302016;
        }

        #shop_categories .dela-collection-image {
            display: block;
            width: 100%;
            height: auto;
            aspect-ratio: 370 / 490;
            object-fit: cover;
            object-position: center;
        }

        #shop_categories .card-img-overlay {
            z-index: 1;
            background: linear-gradient(180deg,
                    transparent 45%,
                    rgba(24, 15, 9, 0.12) 65%,
                    rgba(24, 15, 9, 0.65) 100%);
        }

        /*
                 * Keep the content static so Bootstrap's stretched link
                 * covers the full card overlay.
                 */
        #shop_categories .dela-collection-content {
            position: static !important;
        }

        #shop_categories .dela-collection-count {
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.35);
        }

        #shop_categories .dela-collection-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            max-width: 100%;
            min-height: 50px;
            padding: 14px 24px;
            border: 1px solid #fff;
            border-radius: 0;
            background-color: #fff;
            color: #292018;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.4;
            white-space: normal;
            transition:
                background-color 0.25s ease,
                border-color 0.25s ease,
                color 0.25s ease;
        }

        #shop_categories .dela-collection-card:hover .dela-collection-button,
        #shop_categories .dela-collection-card:focus-within .dela-collection-button {
            background-color: #292018;
            border-color: #292018;
            color: #fff;
        }

        #shop_categories .dela-collection-button:focus-visible {
            outline: 3px solid #d7b578;
            outline-offset: 4px;
        }

        @media (max-width: 575.98px) {
            #shop_categories .dela-collection-image {
                aspect-ratio: 1 / 1;
            }

            #shop_categories .dela-collection-button {
                padding: 12px 20px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            #shop_categories .dela-collection-image,
            #shop_categories .dela-collection-button {
                transition: none !important;
            }

            #shop_categories .dela-collection-card:hover .dela-collection-image {
                transform: none !important;
            }
        }
    </style>
@endpush
