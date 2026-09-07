<!DOCTYPE html>
<html lang="en">

<head>
   
    <meta charset="UTF-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

<title>
    Dashboard | {{ $setting->website_name ?? 'Springcrest Trading' }}
</title>

<meta name="keywords"
      content="Springcrest Trading, login, customer account, ecommerce login, online shopping, wholesale products, retail store, customer portal">

<meta name="description"
      content="Sign in to your Springcrest Trading account to manage orders, track deliveries, save products, and enjoy a seamless shopping experience.">

<meta name="author"
      content="{{ $setting->website_name ?? 'Springcrest Trading' }}">

<link rel="icon"
      href="{{ $setting->favicon ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}"
      sizes="32x32">

<link rel="apple-touch-icon"
      href="{{ $setting->favicon ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}">

<meta name="msapplication-TileImage"
      content="{{ $setting->favicon ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}">
      
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
    <link rel="stylesheet" href="{{ asset("assets/css/custom.css") }}">
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
 @include('frontend.partials.cart.side-cart')
    @include('frontend.partials.breadcrumb', ['title' => 'Dashboard'])
   
    @yield("content")
   @include("frontend.partials.page-footer")
    <!-- Vendor JS -->
    <script src="{{ asset('assets/js/vendor/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery-migrate-3.3.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/modernizr-3.11.2.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/countdownTimer.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/scrollup.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.zoom.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/slick.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/infiniteslidev2.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.sticky-sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/index.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
     <meta name="csrf-token" content="{{ csrf_token() }}">
    @include("frontend.partials.cart_scripts")
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartCanvas = document.getElementById('purchaseChart');

        if (chartCanvas && typeof Chart !== 'undefined') {
            new Chart(chartCanvas, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Amount Spent',
                        data: [45000, 85000, 120000, 95000, 180000, 250000],
                        borderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
</script>
</body>

</html>
