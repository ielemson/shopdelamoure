<section>

    <div class="slick-slider hero hero-header-01"
        data-slick-options='{"arrows":false,"autoplay":true,"cssEase":"ease-in-out","dots":false,"fade":true,"infinite":true,"slidesToShow":1,"speed":600}'>

        <div class="vh-100 d-flex align-items-center position-relative">
            <div class="z-index-2 container container-xxl py-21 pt-xl-10 pb-xl-11">
                <div class="hero-content text-start text-white">
                    <div data-animate="fadeInDown">
                        <p class="mb-8 text-uppercase fw-semibold fs-15px">Welcome to De Lamoure</p>
                        <h1 class="mb-7 hero-title text-white">Find Your<br>Everyday Luxury</h1>
                        <p class="hero-desc fs-18px mb-11 text-white">
                            Explore thoughtful finds for your space and the moments that matter.
                        </p>
                    </div>
                    <a href="{{ url('/shop') }}" data-animate="fadeInUp"
                        class="btn btn-lg btn-light  btn-hover-border-primary">
                        Explore the Collection
                    </a>
                </div>
            </div>
            <div class="lazy-bg bg-overlay position-absolute z-index-1 w-100 h-100"
                data-bg-src="{{ asset('assets/images/hero-slider/dela-moure-slider-collection.jpg') }}"></div>
        </div>

        <div class="vh-100 d-flex align-items-center position-relative">
            <div class="z-index-2 container container-xxl py-21 pt-xl-10 pb-xl-11">
                <div class="hero-content text-start text-white">
                    <div data-animate="fadeInDown">
                        <p class="mb-8 text-uppercase fw-semibold fs-15px">De Lamoure Candles</p>
                        <h1 class="mb-7 hero-title text-white">Set the Mood<br>Beautifully</h1>
                        <p class="hero-desc fs-18px mb-11 text-white">
                            Discover scented candles made for memorable moments.
                        </p>
                    </div>
                    <a href="{{ url('/shop') }}" data-animate="fadeInUp"
                        class="btn btn-lg btn-light  btn-hover-border-primary">
                        Shop Candles
                    </a>
                </div>
            </div>
            <div class="lazy-bg bg-overlay position-absolute z-index-1 w-100 h-100"
                data-bg-src="{{ asset('assets/images/hero-slider/dela-moure-slider-candles.jpg') }}"></div>
        </div>

        <div class="vh-100 d-flex align-items-center position-relative">
            <div class="z-index-2 container container-xxl py-21 pt-xl-10 pb-xl-11">
                <div class="hero-content text-start text-white">
                    <div data-animate="fadeInDown">
                        <p class="mb-8 text-uppercase fw-semibold fs-15px">De Lamoure Room Sprays</p>
                        <h1 class="mb-7 hero-title text-white">Refresh Your<br>Space</h1>
                        <p class="hero-desc fs-18px mb-11 text-white">
                            Explore room sprays that bring character to every space.
                        </p>
                    </div>
                    <a href="{{ url('/shop') }}" data-animate="fadeInUp"
                        class="btn btn-lg btn-light  btn-hover-border-primary">
                        Shop Room Sprays
                    </a>
                </div>
            </div>
            <div class="lazy-bg bg-overlay position-absolute z-index-1 w-100 h-100"
                data-bg-src="{{ asset('assets/images/hero-slider/dela-moure-slider-room-sprays.jpg') }}"></div>
        </div>
    </div>

</section>
