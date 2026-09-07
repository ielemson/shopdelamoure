<!DOCTYPE html>
<html lang="en">

<head>
   
    <meta charset="UTF-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

<title>
    Login | {{ $setting->website_name ?? 'Springcrest Trading' }}
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
    @include('frontend.partials.breadcrumb', ['title' => 'Login'])
    <section class="ec-page-content section-space-p">
        <div class="container">

            <div class="ec-login-simple mx-auto">

                <div class="ec-login-header text-center">
                    <h2>Welcome Back</h2>
                    <p>Login to access your account dashboard.</p>
                </div>

                <form id="customerLoginForm" method="POST" action="{{ route('login') }}" data-parsley-validate>

                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>

                        <input type="email" name="email" class="form-control" placeholder="you@example.com"
                            value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>

                        <input type="password" name="password" class="form-control" placeholder="Enter password"
                            required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">



                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="ec-login-link">
                                Forgot Password?
                            </a>
                        @endif

                    </div>

                    <button type="submit" class="btn btn-primary w-100 ec-login-btn">
                        Login
                    </button>

                    <p class="ec-login-register">
                        Don’t have an account?
                        <a href="{{ route('register') }}">Create Account</a>
                    </p>

                </form>

            </div>

        </div>
    </section>

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
   
    @include("frontend.partials.cart_scripts")
    <script>
        const notyf = new Notyf({
            duration: 3500,
            position: {
                x: 'right',
                y: 'top'
            },
            dismissible: true,
            ripple: true
        });

        $(document).on('submit', '#customerLoginForm', function(e) {
            e.preventDefault();

            let form = $(this);

            if (form.parsley && !form.parsley().validate()) {
                return false;
            }

            let submitBtn = form.find('button[type="submit"]');
            let originalText = submitBtn.text();

            submitBtn.prop('disabled', true).text('Logging in...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    notyf.success(response.message || 'Login successful');

                    window.location.href = response.redirect || '/home';
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(key, value) {
                            notyf.error(value[0]);
                        });
                    } else {
                        notyf.error('Login failed. Please try again.');
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });

            return false;
        });
    </script>
</body>

</html>
