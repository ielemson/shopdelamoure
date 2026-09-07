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

<meta name="author"
      content="{{ $setting?->website_name }}">

<link rel="icon"
      href="{{ !empty($setting?->favicon) ? asset('storage/'.$setting->favicon) : asset('pages/assets/images/favicon/favicon.png') }}"
      sizes="32x32">

<link rel="apple-touch-icon"
      href="{{ !empty($setting?->favicon) ? asset('storage/'.$setting->favicon) : asset('pages/assets/images/favicon/favicon.png') }}">

<meta name="msapplication-TileImage"
      content="{{ !empty($setting?->favicon) ? asset('storage/'.$setting->favicon) : asset('pages/assets/images/favicon/favicon.png') }}">

<meta property="og:type" content="website">
<meta property="og:title" content="Contact Us | {{ $setting?->website_name }}">
<meta property="og:description"
      content="Reach out to {{ $setting?->website_name }} for support, inquiries, and assistance.">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ $setting?->website_name }}">

@if(!empty($setting?->logo))
<meta property="og:image"
      content="{{ asset('storage/'.$setting->logo) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title"
      content="Contact Us | {{ $setting?->website_name }}">
<meta name="twitter:description"
      content="Contact {{ $setting?->website_name }} for product inquiries, customer support, and assistance.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
@if(!empty($setting?->logo))
<meta name="twitter:image" content="{{ asset('storage/'.$setting->logo) }}">
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
    @include('frontend.partials.breadcrumb', ['title' => 'Contact Us'])
    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="ec-common-wrapper">
                    <div class="ec-contact-leftside">
                        <div class="ec-contact-container">
                            <div class="ec-contact-form">
                                <form id="contactForm">
                                @csrf

                                <span class="ec-contact-wrap">
                                    <label>First Name*</label>
                                    <input type="text" name="first_name" placeholder="Enter your first name" required>
                                </span>

                                <span class="ec-contact-wrap">
                                    <label>Last Name*</label>
                                    <input type="text" name="last_name" placeholder="Enter your last name" required>
                                </span>

                                <span class="ec-contact-wrap">
                                    <label>Email Address*</label>
                                    <input type="email" name="email" placeholder="Enter your email address" required>
                                </span>

                                <span class="ec-contact-wrap">
                                    <label>Phone Number*</label>
                                    <input type="text" name="phone_number" placeholder="Enter your phone number" required>
                                </span>

                                <span class="ec-contact-wrap">
                                    <label>Message*</label>
                                    <textarea name="message" placeholder="Tell us how we can help you..." required></textarea>
                                </span>

                                <span class="ec-contact-wrap ec-contact-btn">
                                    <button class="btn btn-primary" type="submit" id="contactSubmitBtn">
                                        Send Message
                                    </button>
                                </span>
                            </form>
                            </div>
                        </div>
                    </div>
                    <div class="ec-contact-rightside">
                        <div class="ec_contact_map">
                            <div class="ec_map_canvas">
                                <iframe id="ec_map_canvas"
                                    src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d71263.65594328841!2d144.93151478652146!3d-37.8734290780509!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sus!4v1615963387757!5m2!1sen!2sus"></iframe>
                                <a href="https://sites.google.com/view/maps-api-v2/mapv2"></a>
                            </div>
                        </div>
                        <div class="ec_contact_info">
                            <h1 class="ec_contact_info_head">Contact us</h1>
                          <ul class="align-items-center">

    @if(!empty($setting?->address))
        <li class="ec-contact-item">
            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
            <span>Address :</span>
            {{ $setting->address }}
        </li>
    @endif

    @if(!empty($setting?->phone))
        <li class="ec-contact-item align-items-center">
            <i class="fas fa-phone-alt" aria-hidden="true"></i>
            <span>Call Us :</span>
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $setting->phone) }}">
                {{ $setting->phone }}
            </a>
        </li>
    @endif

    @if(!empty($setting?->email))
        <li class="ec-contact-item align-items-center">
            <i class="fas fa-envelope" aria-hidden="true"></i>
            <span>Email :</span>
            <a href="mailto:{{ $setting->email }}">
                {{ $setting->email }}
            </a>
        </li>
    @endif

</ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
    @include("frontend.partials.cart_scripts")
    <script>
    $('#contactForm').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let button = $('#contactSubmitBtn');

        button.prop('disabled', true).text('Sending...');

        $.ajax({
            url: "{{ route('contact.send') }}",
            type: "POST",
            data: form.serialize(),
            success: function (response) {
                Toastify({
                    text: response.message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745"
                }).showToast();

                form[0].reset();
            },
            error: function (xhr) {
                let message = 'Something went wrong. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                Toastify({
                    text: message,
                    duration: 4000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            },
            complete: function () {
                button.prop('disabled', false).text('Send Message');
            }
        });
    });
</script>
</body>

</html>
