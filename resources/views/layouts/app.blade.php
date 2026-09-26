<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    @php
        /*
    |--------------------------------------------------------------------------
    | SEO / Social Sharing Defaults
    |--------------------------------------------------------------------------
    */

        $defaultTitle = 'Delamoure | Fashion, Fragrance & Lifestyle';

        $defaultDescription =
            'Discover Delamoure by DE LAMOURE BRAND LTD—fashion, accessories, fragrances, home scents and thoughtful gifts curated for distinctive living.';

        $defaultKeywords =
            'Delamoure, Dela Moure, DE LAMOURE BRAND LTD, fashion Nigeria, fashion accessories, perfumes, fragrances, home scents, diffusers, essential oils, candles, gift sets, lifestyle products, online shopping Nigeria';

        /*
    |--------------------------------------------------------------------------
    | Social Sharing Image
    |--------------------------------------------------------------------------
    |
    | Recommended path:
    | public/assets/images/others/social-share.jpg
    |
    | Recommended dimensions: 1200 x 630px
    |
    */

        $defaultImage = asset('assets/images/others/social-share.jpg');

        /*
    |--------------------------------------------------------------------------
    | Brand Assets
    |--------------------------------------------------------------------------
    */

        $logo = asset('assets/images/others/logo.png');

        $logoWhite = asset('assets/images/others/logo-white.png');

        $favicon = asset('assets/images/others/favicon.ico');

        /*
    |--------------------------------------------------------------------------
    | Page Metadata
    |--------------------------------------------------------------------------
    */

        $pageTitle = trim($__env->yieldContent('meta_title')) ?: $defaultTitle;

        $pageDescription = trim($__env->yieldContent('meta_description')) ?: $defaultDescription;

        $pageKeywords = trim($__env->yieldContent('meta_keywords')) ?: $defaultKeywords;

        $pageImage = trim($__env->yieldContent('meta_image')) ?: $defaultImage;

        $canonicalUrl = url()->current();
    @endphp


    {{-- =========================================================
        PRIMARY SEO
    ========================================================== --}}

    <title>{{ $pageTitle }}</title>

    <meta name="description" content="{{ $pageDescription }}">

    <meta name="keywords" content="{{ $pageKeywords }}">

    <meta name="author" content="Dela Moure">

    <meta name="robots" content="index, follow, max-image-preview:large">

    <meta name="googlebot" content="index, follow, max-image-preview:large">

    <link rel="canonical" href="{{ $canonicalUrl }}">


    {{-- =========================================================
        BRAND / BROWSER
    ========================================================== --}}

    <meta name="application-name" content="Dela Moure">

    <meta name="apple-mobile-web-app-title" content="Dela Moure">

    <meta name="theme-color" content="#ffffff">

    <meta name="msapplication-TileColor" content="#ffffff">


    {{-- =========================================================
        FAVICON
    ========================================================== --}}

    <link rel="shortcut icon" href="{{ asset('assets/images/others/favicon.ico') }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/others/favicon.ico') }}">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/others/favicon-32x32.png') }}">

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/others/favicon-16x16.png') }}">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/others/apple-touch-icon.png') }}">


    {{-- =========================================================
        OPEN GRAPH
        WhatsApp / Facebook / LinkedIn / Messenger
    ========================================================== --}}

    <meta property="og:type" content="website">

    <meta property="og:site_name" content="Dela Moure">

    <meta property="og:title" content="{{ $pageTitle }}">

    <meta property="og:description" content="{{ $pageDescription }}">

    <meta property="og:url" content="{{ $canonicalUrl }}">

    <meta property="og:image" content="{{ $pageImage }}">

    <meta property="og:image:secure_url" content="{{ $pageImage }}">

    <meta property="og:image:type" content="image/jpeg">

    <meta property="og:image:width" content="1200">

    <meta property="og:image:height" content="630">

    <meta property="og:image:alt" content="Dela Moure Luxury Fragrances, Perfumes and Home Scents">

    <meta property="og:locale" content="en_NG">


    {{-- =========================================================
        X / TWITTER
    ========================================================== --}}

    <meta name="twitter:card" content="summary_large_image">

    <meta name="twitter:title" content="{{ $pageTitle }}">

    <meta name="twitter:description" content="{{ $pageDescription }}">

    <meta name="twitter:image" content="{{ $pageImage }}">

    <meta name="twitter:image:alt" content="Dela Moure Luxury Fragrances, Perfumes and Home Scents">


    {{-- =========================================================
        SECURITY
    ========================================================== --}}

    <meta name="csrf-token" content="{{ csrf_token() }}">


    {{-- =========================================================
        STYLES
    ========================================================== --}}

    <link rel="stylesheet" href="{{ asset('assets/vendors/lightgallery/css/lightgallery-bundle.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/animate/animate.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/slick/slick.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/mapbox-gl/mapbox-gl.min.css') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/theme-black.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    @stack('styles')

</head>

<body>

    @include('frontend.partials.main_header')
    <main id="content" class="wrapper layout-page">
        @yield('PageContent')
        @include('frontend.partials.infosection')
    </main>
    @include('frontend.partials.footer.footer-main')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendors/clipboard/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/vanilla-lazyload/lazyload.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/waypoints/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/lightgallery/lightgallery.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/lightgallery/plugins/zoom/lg-zoom.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/lightgallery/plugins/thumbnail/lg-thumbnail.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/lightgallery/plugins/video/lg-video.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/lightgallery/plugins/vimeoThumbnail/lg-vimeo-thumbnail.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/isotope/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/slick/slick.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/gsap/gsap.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/gsap/ScrollToPlugin.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/gsap/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/mapbox-gl/mapbox-gl.js') }}"></script>
    <script src="{{ asset('assets/js/theme.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    @include('frontend.partials.modals')
    @include('frontend.partials.products.quick-view-modal')
    @include('frontend.partials.header.mobile-header')
    @stack('scripts')
    @include('frontend.partials.scriptsbuilder')
</body>

</html>
