<section>

    <div class="slick-slider hero hero-header-01"
        data-slick-options='{"arrows":false,"autoplay":true,"cssEase":"ease-in-out","dots":false,"fade":true,"infinite":true,"slidesToShow":1,"speed":600}'>

        <div class="vh-100 d-flex align-items-center position-relative">
            <div class="z-index-2 container container-xxl py-21 pt-xl-10 pb-xl-11">
                <div class="hero-content text-start text-white">
                    <div data-animate="fadeInDown">
                        <p class="mb-8 text-uppercase fw-semibold fs-15px">
                            De Lamoure — A Scented Life
                        </p>

                        <h1 class="mb-7 hero-title text-white">
                            Beautiful Scents.<br>Extraordinary Spaces.
                        </h1>

                        <p class="hero-desc fs-18px mb-11 text-white">
                            Set the mood with signature diffusers, candles and fragrances
                            for your home and every journey.
                        </p>
                    </div>

                    <a href="{{ url('/shop') }}" data-animate="fadeInUp"
                        class="btn btn-lg btn-light btn-hover-border-primary">
                        Shop the Collection
                    </a>
                </div>
            </div>

            <div class="lazy-bg bg-overlay position-absolute z-index-1 w-100 h-100"
                data-bg-src="{{ asset('assets/images/hero-slider/shopcollection-slider.jpg') }}">
            </div>
        </div>
    </div>
</section>
