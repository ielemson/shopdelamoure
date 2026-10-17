<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    @php
        $defaultTitle = 'Dela Moure | Luxury Fragrances, Perfumes & Home Scents';

        $defaultDescription =
            'Discover Dela Moure’s collection of luxury perfumes, home fragrances, diffusers, essential oils, room sprays, candles and premium scenting essentials.';

        $defaultKeywords =
            'Dela Moure, luxury perfumes, fragrances, home fragrances, diffusers, essential oils, room sprays, reed diffusers, candles, car diffusers, perfume Nigeria, luxury scents Nigeria';

        $pageTitle = trim($__env->yieldContent('title')) ?: $defaultTitle;
        $pageDescription = trim($__env->yieldContent('meta_description')) ?: $defaultDescription;
        $pageKeywords = trim($__env->yieldContent('meta_keywords')) ?: $defaultKeywords;

        $canonicalUrl = trim($__env->yieldContent('canonical')) ?: url()->current();
        $pageRobots = trim($__env->yieldContent('meta_robots')) ?: 'index,follow';

        $socialTitle = trim($__env->yieldContent('og_title')) ?: $pageTitle;
        $socialDescription = trim($__env->yieldContent('og_description')) ?: $pageDescription;
        $socialType = trim($__env->yieldContent('og_type')) ?: 'website';

        $socialImage = trim($__env->yieldContent('og_image')) ?: asset('assets/images/others/social-share.jpg');

        $socialImageAlt =
            trim($__env->yieldContent('og_image_alt')) ?: 'Dela Moure Luxury Fragrances, Perfumes and Home Scents';
    @endphp

    {{-- Page identity --}}
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
    <meta name="author" content="Dela Moure">
    <meta name="robots" content="{{ $pageRobots }}">
    <meta name="theme-color" content="#ffffff">
    <meta name="application-name" content="Dela Moure">

    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/others/favicon.ico') }}">

    {{-- Social sharing --}}
    <meta property="og:type" content="{{ $socialType }}">
    <meta property="og:site_name" content="Dela Moure">
    <meta property="og:title" content="{{ $socialTitle }}">
    <meta property="og:description" content="{{ $socialDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $socialImage }}">
    <meta property="og:image:alt" content="{{ $socialImageAlt }}">
    <meta property="og:locale" content="en_NG">

    {{-- Twitter / X --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $socialTitle }}">
    <meta name="twitter:description" content="{{ $socialDescription }}">
    <meta name="twitter:image" content="{{ $socialImage }}">
    <meta name="twitter:image:alt" content="{{ $socialImageAlt }}">

    {{-- Font connections --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Urbanist font --}}
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap">

    {{-- Core vendor styles --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/animate/animate.min.css') }}">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

    {{-- Customer theme --}}
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">

    {{-- Page-specific styles --}}
    @stack('styles')
</head>

<body>

    <div class="wrapper dashboard-wrapper customer-dashboard">
        <div class="d-flex flex-wrap flex-xl-nowrap">
            <div class="db-sidebar bg-body">
                @include('customer.partials.sidebar')
            </div>
            <div class="page-content">
                @include('customer.partials.header')
                <main id="content" class="bg-body-tertiary-01 d-flex flex-column main-content">
                    @yield('CustomerContent')
                    @include('customer.partials.footer')
                </main>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script src="{{ asset('assets/vendors/chartjs/chart.min.js') }}"></script>

    {{-- Core --}}
    <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.bundle.js') }}"></script>

    {{-- Utilities --}}
    <script src="{{ asset('assets/vendors/clipboard/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/vanilla-lazyload/lazyload.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/waypoints/jquery.waypoints.min.js') }}"></script>

    {{-- UI Components --}}
    <script src="{{ asset('assets/vendors/slick/slick.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/isotope/isotope.pkgd.min.js') }}"></script>

    {{-- Animation --}}
    <script src="{{ asset('assets/vendors/gsap/gsap.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/gsap/ScrollToPlugin.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/gsap/ScrollTrigger.min.js') }}"></script>

    {{-- Customer Dashboard --}}
    <script src="{{ asset('assets/js/dashboard.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    {{-- Page-specific scripts --}}
    @stack('scripts')


</body>

</html>
