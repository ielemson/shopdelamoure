<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

<title>About Us | {{ $setting?->website_name }}</title>

<meta name="keywords"
      content="about {{ strtolower($setting?->website_name) }}, company profile, online shopping, ecommerce store, trusted online retailer, customer satisfaction, quality products">

<meta name="description"
      content="Learn more about {{ $setting?->website_name }}, our mission, values, commitment to quality products, excellent customer service, and seamless online shopping experience.">

<meta name="author"
      content="{{ $setting?->website_name }}">

<link rel="icon"
      href="{{ !empty($setting?->favicon) ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}"
      sizes="32x32">

<link rel="apple-touch-icon"
      href="{{ !empty($setting?->favicon) ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}">

<meta name="msapplication-TileImage"
      content="{{ !empty($setting?->favicon) ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}">

<meta property="og:type" content="website">
<meta property="og:title" content="About Us | {{ $setting?->website_name }}">
<meta property="og:description"
      content="Discover the story, vision, and commitment behind {{ $setting?->website_name }}.">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ $setting?->website_name }}">

@if(!empty($setting?->logo))
<meta property="og:image" content="{{ asset('storage/'.$setting->logo) }}">
<meta name="twitter:image" content="{{ asset('storage/'.$setting->logo) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="About Us | {{ $setting?->website_name }}">
<meta name="twitter:description"
      content="Learn more about {{ $setting?->website_name }} and our commitment to delivering quality products and exceptional service.">
<meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="{{ asset('pages/assets/css/vendor/ecicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/countdowntimer.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/slick.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/bootstrap.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('pages/assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/responsive.css') }}" />
    <link rel="stylesheet" id="bg-switcher-css" href="{{ asset('pages/assets/css/backgrounds/bg-4.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />
    
    <style>
    </style>

<body>
    <div id="ec-overlay">
        <div class="ec-ellipsis">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    @include('frontend.partials.header.pages-header')
    @include('frontend.partials.breadcrumb', ['title' => 'About Us'])
    @include('frontend.partials.cart.side-cart')
    <section class="ec-page-content section-space-p">
      <div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <div class="section-title">
                <h2 class="ec-bg-title">About Us</h2>
                <h2 class="ec-title">About Springcrest Trading</h2>
                <p class="sub-title mb-3">
                    Your Trusted Partner for Quality Products and Reliable Distribution
                </p>
            </div>
        </div>

        <div class="ec-common-wrapper">
            <div class="row">
                <div class="col-md-6 ec-cms-block ec-abcms-block text-center">
                    <div class="ec-cms-block-inner">
                        <img class="a-img" src="assets/images/about/about-us.jpg" alt="Springcrest Trading">
                    </div>
                </div>

                <div class="col-md-6 ec-cms-block ec-abcms-block">
                    <div class="ec-cms-block-inner">
                        <h3 class="ec-cms-block-title">
                            More Than Just a Trading Company
                        </h3>

                        <p>
                            Springcrest Trading was established with a clear vision—to provide customers
                            with access to high-quality products at competitive prices while delivering
                            exceptional service and dependable distribution solutions.
                        </p>

                        <p>
                            We specialize in sourcing and supplying a diverse range of products including
                            household essentials, electronics, fashion items, business supplies, and
                            everyday consumer goods. Through strategic partnerships with trusted suppliers,
                            we ensure that every product meets our standards for quality, value, and reliability.
                        </p>

                        <p>
                            Our commitment to integrity, innovation, and customer satisfaction has earned
                            us the trust of individuals, retailers, and businesses alike. Whether you are
                            shopping for personal needs or sourcing products for your business, Springcrest
                            Trading is dedicated to providing a seamless experience from selection to delivery.
                        </p>

                        <p>
                            At Springcrest Trading, we believe that quality products, trusted service,
                            and reliable delivery are the foundation of long-lasting customer relationships.
                            We continuously strive to exceed expectations and remain your preferred trading
                            and distribution partner.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </section>
    @include('frontend.partials.services')
    @include('frontend.partials.page-footer')
    <script src="{{ asset('assets/js/vendor/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery-migrate-3.3.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/modernizr-3.11.2.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/countdowntimer.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/scrollup.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.zoom.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/slick.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/infiniteslidev2.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.sticky-sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/index.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
     <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        @include("frontend.partials.cart_scripts")

</body>

</html>
