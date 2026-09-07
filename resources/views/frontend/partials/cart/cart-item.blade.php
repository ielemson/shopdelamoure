<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

    <title>Contact Us | {{ $setting?->website_name }}</title>

    <meta name="keywords"
        content="contact us, customer support, help center, {{ strtolower($setting?->website_name ?? '') }}, phone number, email address, customer service">

    <meta name="description"
        content="Get in touch with {{ $setting?->website_name }}. Contact our customer support team for inquiries, orders, product information, and assistance.">

    <meta name="author" content="{{ $setting?->website_name }}">

    <link rel="icon"
        href="{{ !empty($setting?->favicon) ? asset('storage/' . $setting->favicon) : asset('pages/assets/images/favicon/favicon.png') }}"
        sizes="32x32">

    <link rel="apple-touch-icon"
        href="{{ !empty($setting?->favicon) ? asset('storage/' . $setting->favicon) : asset('pages/assets/images/favicon/favicon.png') }}">

    <meta name="msapplication-TileImage"
        content="{{ !empty($setting?->favicon) ? asset('storage/' . $setting->favicon) : asset('pages/assets/images/favicon/favicon.png') }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="Contact Us | {{ $setting?->website_name }}">
    <meta property="og:description"
        content="Reach out to {{ $setting?->website_name }} for support, inquiries, and assistance.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $setting?->website_name }}">

    @if (!empty($setting?->logo))
        <meta property="og:image" content="{{ asset('storage/' . $setting->logo) }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Contact Us | {{ $setting?->website_name }}">
    <meta name="twitter:description"
        content="Contact {{ $setting?->website_name }} for product inquiries, customer support, and assistance.">

    @if (!empty($setting?->logo))
        <meta name="twitter:image" content="{{ asset('storage/' . $setting->logo) }}">
    @endif

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
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="contact_us_page">
    <div id="ec-overlay">
        <div class="ec-ellipsis">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    @include('frontend.partials.header.pages-header')
    @include('frontend.partials.cart.side-cart')
    @include('frontend.partials.breadcrumb', ['title' => 'Carty'])


    @include('frontend.partials.page-footer')
    <script src="{{ asset('pages/assets/js/vendor/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/bootstrap.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/jquery-migrate-3.3.0.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/modernizr-3.11.2.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/countdowntimer.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/scrollup.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/jquery.zoom.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/slick.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/infiniteslidev2.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('pages/assets/js/plugins/jquery.sticky-sidebar.js') }}"></script>
    <script src="{{ asset('pages/assets/js/vendor/index.js') }}"></script>
    <script src="{{ asset('pages/assets/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    @include('frontend.partials.cart_scripts')
</body>

</html>
