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

        $defaultTitle = 'Dela Moure | Luxury Fragrances, Perfumes & Home Scents';

        $defaultDescription =
            "Discover Dela Moure's collection of luxury perfumes, home fragrances, diffusers, essential oils, room sprays, candles, gift sets and premium scenting essentials.";

        $defaultKeywords =
            'Dela Moure, shop Dela Moure, luxury perfumes, fragrances, home fragrances, diffusers, essential oils, room sprays, reed diffusers, candles, gift sets, car diffusers, perfume Nigeria, luxury scents Nigeria';

        /*
        |--------------------------------------------------------------------------
        | Social Sharing Image
        |--------------------------------------------------------------------------
        |
        | Recommended image:
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

    <script>
        const notyf = new Notyf({
            duration: 3000,
            position: {
                x: 'right',
                y: 'top'
            },
            dismissible: true,
            ripple: true
        });

        // ADD TO CART
        document.addEventListener('click', async function(e) {

            const button = e.target.closest('.add_to_cart[data-product-id]');

            if (!button) return;

            e.preventDefault();

            if (button.classList.contains('cart-processing')) {
                return;
            }

            const productId = button.dataset.productId;
            const url = button.dataset.cartUrl;

            const icon = button.querySelector('.cart-icon');
            const loader = button.querySelector('.cart-loading');

            button.classList.add('cart-processing');

            if (icon) {
                icon.classList.add('d-none');
            }

            if (loader) {
                loader.classList.remove('d-none');
            }

            try {

                const response = await fetch(url, {

                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content
                    },

                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.status) {
                    throw new Error(
                        data.message || 'Unable to add product to cart.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Refresh Cart UI
                |--------------------------------------------------------------------------
                */
                updateCartUI(data);

                /*
                |--------------------------------------------------------------------------
                | Notification
                |--------------------------------------------------------------------------
                */
                notyf.success(
                    data.message || 'Product added to cart.'
                );

                /*
                |--------------------------------------------------------------------------
                | Open Shopping Cart
                |--------------------------------------------------------------------------
                */
                const shoppingCart =
                    document.getElementById('shoppingCart');

                if (
                    shoppingCart &&
                    typeof bootstrap !== 'undefined'
                ) {
                    bootstrap.Offcanvas
                        .getOrCreateInstance(shoppingCart)
                        .show();
                }

            } catch (error) {

                notyf.error(
                    error.message ||
                    'Something went wrong.'
                );

            } finally {

                button.classList.remove('cart-processing');

                if (icon) {
                    icon.classList.remove('d-none');
                }

                if (loader) {
                    loader.classList.add('d-none');
                }
            }
        });

        function updateCartUI(data) {
            document.querySelectorAll('[data-cart-count]').forEach(element => {
                element.textContent = data.cart_count ?? 0;
            });

            const sideCartContent =
                document.getElementById('side-cart-content');

            if (sideCartContent && data.side_cart_html) {
                sideCartContent.innerHTML = data.side_cart_html;
            }
        }

        // UPDATE CART QUANTITY
        async function updateCartQuantity(cartId, quantity) {
            try {
                const response = await fetch("{{ route('cart.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify({
                        id: cartId,
                        quantity: quantity
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.status) {
                    throw new Error(
                        data.message || 'Unable to update cart.'
                    );
                }

                updateCartUI(data);

            } catch (error) {
                notyf.error(
                    error.message || 'Unable to update cart.'
                );
            }
        }

        document.addEventListener('click', function(e) {
            const plus = e.target.closest('.shop-up');
            const minus = e.target.closest('.shop-down');

            if (!plus && !minus) return;

            e.preventDefault();

            const quantityWrapper = e.target.closest('.shop-quantity');

            if (!quantityWrapper) return;

            const cartId = quantityWrapper.dataset.cartId;

            const input = quantityWrapper.querySelector(
                'input[type="number"]'
            );

            if (!input) return;

            let quantity = parseInt(input.value || 1);

            if (plus) {
                quantity++;
            }

            if (minus) {
                if (quantity <= 1) {
                    return;
                }

                quantity--;
            }

            input.value = quantity;

            updateCartQuantity(cartId, quantity);
        });

        // Remove Cart Item side cart
        document.addEventListener('click', async function(e) {
            const removeButton = e.target.closest('.clear-product');

            if (!removeButton) return;

            e.preventDefault();

            const removeUrl = removeButton.dataset.removeUrl;

            if (!removeUrl) return;

            const row = removeButton.closest('[data-cart-row]');

            removeButton.style.pointerEvents = 'none';
            removeButton.style.opacity = '0.5';

            try {
                const response = await fetch(removeUrl, {
                    method: 'DELETE',

                    headers: {
                        'Accept': 'application/json',

                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            ?.content
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.status) {
                    throw new Error(
                        data.message ||
                        'Unable to remove item.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Remove Row
                |--------------------------------------------------------------------------
                */
                if (row) {
                    row.remove();
                }

                /*
                |--------------------------------------------------------------------------
                | Update Complete Cart UI
                |--------------------------------------------------------------------------
                */
                if (typeof updateCartUI === 'function') {
                    updateCartUI(data);
                }

                /*
                |--------------------------------------------------------------------------
                | Replace Side Cart
                |--------------------------------------------------------------------------
                */
                if (data.cart_html) {
                    const shoppingCart =
                        document.getElementById('shoppingCart');

                    if (shoppingCart) {
                        shoppingCart.innerHTML =
                            data.cart_html;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Notification
                |--------------------------------------------------------------------------
                */
                if (typeof notyf !== 'undefined') {
                    notyf.success(
                        data.message ||
                        'Item removed from your bag.'
                    );
                }

            } catch (error) {

                removeButton.style.pointerEvents = '';
                removeButton.style.opacity = '';

                if (typeof notyf !== 'undefined') {
                    notyf.error(
                        error.message ||
                        'Unable to remove item.'
                    );
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.currency-option').forEach(function(item) {

                item.addEventListener('click', function() {

                    const currency = this.dataset.currency;

                    fetch('{{ route('currency.switch') }}', {
                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },

                            body: JSON.stringify({
                                currency: currency
                            })
                        })
                        .then(response => {

                            if (!response.ok) {
                                throw new Error('Unable to switch currency.');
                            }

                            return response.json();
                        })
                        .then(data => {

                            if (data.status) {
                                window.location.reload();
                            }

                        })
                        .catch(error => {
                            console.error('Currency switch error:', error);
                        });

                });

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | Compare Notification
            |--------------------------------------------------------------------------
            */

            function compareNotify(message, type = 'success') {

                // Toastr
                if (typeof toastr !== 'undefined') {

                    if (type === 'success') {
                        toastr.success(message);
                    } else if (type === 'error') {
                        toastr.error(message);
                    } else {
                        toastr.info(message);
                    }

                    return;
                }


                // Notify.js
                if (typeof $ !== 'undefined' && typeof $.notify === 'function') {

                    $.notify(message, {
                        className: type,
                        globalPosition: 'top right'
                    });

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Bootstrap Fallback
                |--------------------------------------------------------------------------
                */

                const oldAlert = document.querySelector('.compare-notification-alert');

                if (oldAlert) {
                    oldAlert.remove();
                }


                const alertType = type === 'error' ? 'danger' : type;

                const alert = document.createElement('div');

                alert.className =
                    `alert alert-${alertType} alert-dismissible fade show compare-notification-alert position-fixed`;

                alert.style.cssText = `
                top: 90px;
                right: 20px;
                z-index: 99999;
                min-width: 280px;
                max-width: 400px;
                box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            `;

                alert.innerHTML = `
                <div>${message}</div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
                </button>
            `;

                document.body.appendChild(alert);


                setTimeout(function() {

                    if (alert.parentNode) {

                        alert.classList.remove('show');

                        setTimeout(function() {
                            alert.remove();
                        }, 300);

                    }

                }, 3000);
            }


            /*
            |--------------------------------------------------------------------------
            | Compare Toggle
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', async function(event) {

                const compareButton = event.target.closest('.compare-toggle');

                if (!compareButton) {
                    return;
                }

                event.preventDefault();


                /*
                |--------------------------------------------------------------------------
                | Prevent Multiple Requests
                |--------------------------------------------------------------------------
                */

                if (compareButton.classList.contains('compare-loading')) {
                    return;
                }


                const productId = compareButton.dataset.productId;
                const url = compareButton.dataset.url;
                const csrfToken = compareButton.dataset.csrf;


                if (!productId || !url) {

                    console.error('Compare configuration is missing.');

                    compareNotify(
                        'Unable to update compare. Please try again.',
                        'error'
                    );

                    return;
                }


                compareButton.classList.add('compare-loading');


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | Send Request
                    |--------------------------------------------------------------------------
                    */

                    const response = await fetch(url, {

                        method: 'POST',

                        headers: {

                            'X-CSRF-TOKEN': csrfToken,

                            'X-Requested-With': 'XMLHttpRequest',

                            'Accept': 'application/json'

                        },

                        credentials: 'same-origin'

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | Read JSON Response
                    |--------------------------------------------------------------------------
                    */

                    const data = await response.json();


                    /*
                    |--------------------------------------------------------------------------
                    | Handle Compare Limit / Errors
                    |--------------------------------------------------------------------------
                    */

                    if (!response.ok || data.success === false) {

                        compareNotify(
                            data.message ||
                            'Unable to update product comparison.',
                            data.limit ? 'info' : 'error'
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Update Every Copy Of This Product
                    |--------------------------------------------------------------------------
                    */

                    document
                        .querySelectorAll(
                            `.compare-toggle[data-product-id="${productId}"]`
                        )
                        .forEach(function(button) {

                            if (data.compared) {

                                /*
                                |--------------------------------------------------------------------------
                                | Product Is Now Compared
                                |--------------------------------------------------------------------------
                                */

                                button.classList.add('compare-active');

                                button.setAttribute(
                                    'data-bs-title',
                                    'Remove From Compare'
                                );

                                button.setAttribute(
                                    'aria-label',
                                    'Remove From Compare'
                                );

                            } else {

                                /*
                                |--------------------------------------------------------------------------
                                | Product Removed From Compare
                                |--------------------------------------------------------------------------
                                */

                                button.classList.remove('compare-active');

                                button.setAttribute(
                                    'data-bs-title',
                                    'Add To Compare'
                                );

                                button.setAttribute(
                                    'aria-label',
                                    'Add To Compare'
                                );

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Refresh Bootstrap Tooltip
                            |--------------------------------------------------------------------------
                            */

                            if (typeof bootstrap !== 'undefined') {

                                const tooltip =
                                    bootstrap.Tooltip.getInstance(button);

                                if (tooltip) {
                                    tooltip.dispose();
                                }

                                new bootstrap.Tooltip(button);
                            }

                        });


                    /*
                    |--------------------------------------------------------------------------
                    | Update Header Compare Count
                    |--------------------------------------------------------------------------
                    */

                    document
                        .querySelectorAll('.compare-count')
                        .forEach(function(counter) {

                            counter.textContent = data.count;

                            if (data.count > 0) {

                                counter.classList.remove('d-none');

                            } else {

                                counter.classList.add('d-none');

                            }

                        });


                    /*
                    |--------------------------------------------------------------------------
                    | Notification
                    |--------------------------------------------------------------------------
                    */

                    compareNotify(
                        data.message ||
                        (
                            data.compared ?
                            'Product added to compare.' :
                            'Product removed from compare.'
                        ),
                        'success'
                    );


                } catch (error) {

                    console.error('Compare Error:', error);

                    compareNotify(
                        'Something went wrong. Please try again.',
                        'error'
                    );


                } finally {

                    compareButton.classList.remove('compare-loading');

                }

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | Currency Switch
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', async function(event) {

                const currencyButton = event.target.closest('.currency-option');

                if (!currencyButton) {
                    return;
                }

                event.preventDefault();


                /*
                |--------------------------------------------------------------------------
                | Prevent Multiple Requests
                |--------------------------------------------------------------------------
                */

                if (currencyButton.classList.contains('currency-loading')) {
                    return;
                }


                const currency = currencyButton.dataset.currency;

                if (!currency) {
                    return;
                }


                const csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute('content');


                currencyButton.classList.add('currency-loading');


                try {

                    const response = await fetch(
                        "{{ route('currency.switch') }}", {
                            method: 'POST',

                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },

                            credentials: 'same-origin',

                            body: JSON.stringify({
                                currency: currency
                            })
                        }
                    );


                    const data = await response.json();


                    if (!response.ok || data.status !== true) {

                        throw new Error(
                            data.message || 'Unable to change currency.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Reload Page
                    |--------------------------------------------------------------------------
                    |
                    | Reloading is intentional because every product price,
                    | shipping amount, cart amount and total should be rendered
                    | using the newly selected currency.
                    |
                    */

                    window.location.reload();


                } catch (error) {

                    console.error(
                        'Currency Switch Error:',
                        error
                    );

                    alert(
                        error.message ||
                        'Unable to change currency. Please try again.'
                    );


                } finally {

                    currencyButton.classList.remove(
                        'currency-loading'
                    );

                }

            });

        });
    </script>


</body>

</html>
