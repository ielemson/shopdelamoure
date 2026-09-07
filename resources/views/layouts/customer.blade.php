<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width,user-scalable=no,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dela Moure | Luxury Fragrances, Perfumes & Home Scents</title>
    <meta name="description"
        content="Discover Dela Moure's collection of luxury perfumes, home fragrances, diffusers, essential oils, room sprays, candles and premium scenting essentials.">
    <meta name="keywords"
        content="Dela Moure,shop Dela Moure,luxury perfumes,fragrances,home fragrances,diffusers,essential oils,room sprays,reed diffusers,candles,car diffusers,perfume Nigeria,luxury scents Nigeria">
    <meta name="author" content="Dela Moure">
    <meta name="robots" content="index,follow">
    <meta name="googlebot" content="index,follow">
    <meta name="theme-color" content="#ffffff">
    <meta name="application-name" content="Dela Moure">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/others/favicon.ico') }}">
    {{-- <link rel="apple-touch-icon" href="{{asset('assets/images/others/apple-touch-icon.png')}}"> --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Dela Moure">
    <meta property="og:title" content="Dela Moure | Luxury Fragrances, Perfumes & Home Scents">
    <meta property="og:description"
        content="Elevate your everyday experience with luxury perfumes, home fragrances, diffusers, essential oils, room sprays and premium scenting essentials from Dela Moure.">
    <meta property="og:url" content="{{ url()->current() }}">
    {{-- <meta property="og:image" content="{{asset('assets/images/others/social-share.jpg')}}"> --}}
    <meta property="og:image:alt" content="Dela Moure Luxury Fragrances">
    <meta property="og:locale" content="en_NG">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Dela Moure | Luxury Fragrances, Perfumes & Home Scents">
    <meta name="twitter:description"
        content="Discover luxury perfumes, fragrances, diffusers, essential oils, room sprays, candles and premium scenting essentials from Dela Moure.">
    {{-- <meta name="twitter:image" content="{{asset('assets/images/others/social-share.jpg')}}"> --}}

    {{-- Core Vendor Styles --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/animate/animate.min.css') }}">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

    {{-- Google Font --}}
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">

    {{-- Customer Theme --}}
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">

    {{-- Page-specific styles --}}
    @stack('styles')
</head>

<body>


    <div class="wrapper dashboard-wrapper">
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
