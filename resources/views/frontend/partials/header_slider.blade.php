<section>

    <div class="slick-slider hero hero-header-01"
        data-slick-options='{
            "arrows": false,
            "autoplay": false,
            "cssEase": "ease-in-out",
            "dots": false,
            "fade": true,
            "infinite": false,
            "slidesToShow": 1,
            "speed": 600
        }'>

        <div class="vh-100 d-flex align-items-center">

            <div class="z-index-2 container container-xxl py-21 pt-xl-10 pb-xl-11">

                <div class="hero-content text-start">

                    <div data-animate="fadeInDown">

                        <p class="text-body-emphasis mb-8 text-uppercase fw-semibold fs-15px">
                            Luxury Fragrance & Home Scents
                        </p>

                        <h1 class="mb-7 hero-title">
                            Discover Scents<br>
                            Made For Every Moment
                        </h1>

                        <p class="hero-desc text-body-calculate fs-18px mb-11">
                            Explore our collection of perfumes, diffusers, candles,
                            essential oils and beautifully curated gift sets.
                        </p>

                    </div>

                    <a href="{{ route('shop') }}" data-animate="fadeInUp"
                        class="btn btn-lg btn-dark btn-hover-bg-primary btn-hover-border-primary">

                        Shop Collection

                    </a>

                </div>

            </div>


            {{-- Background Image --}}
            <div class="lazy-bg bg-overlay position-absolute z-index-1 w-100 h-100 light-mode-img"
                data-bg-src="{{ asset('assets/images/hero-slider/main-slider.png') }}">
            </div>

            <div class="lazy-bg bg-overlay dark-mode-img position-absolute z-index-1 w-100 h-100"
                data-bg-src="{{ asset('assets/images/hero-slider/main-slider.png') }}">
            </div>

        </div>

    </div>

</section>
