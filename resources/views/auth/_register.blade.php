<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

<title>
    Register | {{ $setting->website_name ?? 'Springcrest Trading' }}
</title>

<meta name="keywords" content="Springcrest Trading, login, customer account, ecommerce login, online shopping, wholesale products, retail store, customer portal">

<meta name="description" content="Sign in to your Springcrest Trading account to manage orders, track deliveries, save products, and enjoy a seamless shopping experience.">

<meta name="author" content="{{ $setting->website_name ?? 'Springcrest Trading' }}">

<link rel="icon" href="{{ $setting->favicon ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}"
      sizes="32x32">

<link rel="apple-touch-icon" href="{{ $setting->favicon ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}">

<meta name="msapplication-TileImage" content="{{ $setting->favicon ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}">
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
    @include('frontend.partials.breadcrumb', ['title' => 'Register'])
    <section class="ec-page-content section-space-p">
        <div class="container">

            <div class="ec-register-simple mx-auto">

                <div class="ec-register-header text-center">
                    <h2>Create Account</h2>
                    <p>Register to start shopping and access your dashboard.</p>
                </div>

                <form id="customerRegisterForm" action="{{ route('register') }}" method="POST" data-parsley-validate>

                    @csrf

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" placeholder="John" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" placeholder="Doe" required>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+234 XXX XXX XXXX"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Minimum 8 characters" required data-parsley-minlength="8">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Confirm password" required data-parsley-equalto="#password">
                        </div>

                    </div>
                    <div class="mb-4">
                        <label class="form-label">Security Check</label>
                        <div class="ec-captcha-box">
                            <span>
                                {{ session('captcha_question') }}
                            </span>
                            <input type="number" name="captcha" class="form-control" placeholder="Answer" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 ec-register-btn">
                        Create Account
                    </button>
                    <p class="ec-register-login">
                        Already have an account?
                        <a href="{{ route('login') }}">Sign In</a>
                    </p>
                </form>
            </div>
        </div>
    </section>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        @include("frontend.partials.cart_scripts")
    <script>
        $(document).ready(function() {

            const notyf = new Notyf({
                duration: 3500,
                position: {
                    x: 'right',
                    y: 'top',
                },
                ripple: true,
                dismissible: true
            });

            $('#customerRegisterForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);

                if (!form.parsley().validate()) {
                    return false;
                }

                let submitBtn = form.find('button[type="submit"]');
                let originalText = submitBtn.text();

                submitBtn.prop('disabled', true).text('Creating Account...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),

                    success: function(response) {
                        notyf.success(response.message || 'Account created successfully');

                        form[0].reset();

                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1200);
                    },

                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            $.each(errors, function(key, value) {
                                notyf.error(value[0]);
                            });
                        } else {
                            notyf.error('Registration failed. Please try again.');
                        }
                    },

                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });

            });

        });
    </script>
</body>

</html>
