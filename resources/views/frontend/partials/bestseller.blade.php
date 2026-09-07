<section>

    <div class="container container-xxl mb-2">

        {{-- Section Heading --}}
        <div class="mb-11 mt-2 pt-1 pb-3 text-center" data-animate="fadeInUp">

            <h2 class="mb-5">
                Most Loved
            </h2>

            <p class="fs-18px mb-0 mw-xl-40 mw-lg-50 mw-md-75 ms-auto me-auto">

                Discover the signature scents and standout fragrances
                our customers keep coming back for.

            </p>

        </div>


        <div class="row">

            {{-- Editorial Banner --}}
            <div class="col-lg-5 mb-10 mb-lg-0" data-animate="fadeInUp">

                <div class="card border-0 rounded-0 hover-zoom-in hover-shine">

                    <img class="lazy-image w-100 img-fluid card-img object-fit-cover banner-02" src="#"
                        data-src="{{ asset('assets/images/banner/best-seller-banner.png') }}" width="570"
                        height="913" alt="Discover our most loved fragrances">


                    <div class="card-img-overlay p-12 m-2 d-inline-flex flex-column justify-content-end">

                        <h3 class="card-title mb-0 fs-2 text-white">
                            Find Your Signature
                        </h3>

                        <p class="card-text mb-0 fs-18px text-white mt-5">
                            Discover fragrances made to be remembered.
                        </p>

                        <div class="mt-10 pt-2">

                            <a href="#" class="btn btn-white">
                                Explore Collection
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Products --}}
            <div class="col-lg-7">

                <div class="row gy-11">

                    @foreach ($products as $product)
                        <div class="col-md-4 col-sm-6 col-12">

                            @include('frontend.partials.product-card', [
                                'product' => $product,
                                'gridStyle' => 'grid-2',
                                'actionLayout' => 'vertical',
                                'compact' => true,
                                'showCategory' => false,
                                'showVariantCount' => false,
                                'showCompare' => false,
                            ])

                        </div>
                    @endforeach

                </div>

            </div>

        </div>

    </div>

</section>
