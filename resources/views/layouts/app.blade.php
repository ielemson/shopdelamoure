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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <meta name="twitter:image" content="{{asset('assets/images/others/social-share.jpg')}}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/lightgallery/css/lightgallery-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/animate/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mapbox-gl/mapbox-gl.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/theme-black.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">


</head>

<body>

    @include('frontend.partials.main_header')
    <main id="content" class="wrapper layout-page">
        @yield('PageContent')
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
    </script>


</body>

</html>
