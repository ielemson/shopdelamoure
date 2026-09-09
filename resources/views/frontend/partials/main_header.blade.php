<header id="header" class="header header-sticky header-sticky-smart disable-transition-all z-index-5">

    <!-- =========================================================
         TOP BAR
    ========================================================== -->
    <div class="bg-primary bg-opacity-15">
        <div class="container-xxl container d-flex align-items-center py-4">

            @php
                $setting = \App\Models\WebsiteSetting::first();
            @endphp

            <!-- Social Media -->
            <div class="w-50 d-none d-lg-block">
                <ul class="social-icons list-inline mb-0 fs-14">

                    @if (!empty($setting?->instagram))
                        <li class="list-inline-item">
                            <a href="{{ $setting->instagram }}" target="_blank" rel="noopener noreferrer"
                                title="Instagram">
                                <svg class="icon">
                                    <use xlink:href="#instagram"></use>
                                </svg>
                            </a>
                        </li>
                    @endif

                    @if (!empty($setting?->facebook))
                        <li class="list-inline-item ms-6">
                            <a href="{{ $setting->facebook }}" target="_blank" rel="noopener noreferrer"
                                title="Facebook">
                                <svg class="icon">
                                    <use xlink:href="#facebook"></use>
                                </svg>
                            </a>
                        </li>
                    @endif


                    @if (!empty($setting?->phone))
                        <li class="list-inline-item ms-6">
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $setting->phone) }}" target="_blank"
                                rel="noopener noreferrer" title="WhatsApp">
                                <svg class="icon">
                                    <use xlink:href="#whatsapp"></use>
                                </svg>
                            </a>
                        </li>
                    @endif

                </ul>


            </div>


            <!-- Announcement -->
            <div class="w-100 text-center">

                <p class="mb-0 fs-14px fw-bold text-primary text-uppercase">
                    Discover Your Signature Scent
                </p>

            </div>

            <!-- Currency Switch -->
            @php
                $activeCurrency = strtoupper(session('currency', 'NGN'));

                $currencySymbol = $activeCurrency === 'USD' ? '$' : '₦';
            @endphp


            {{-- ================= DESKTOP CURRENCY SWITCH ================= --}}
            <div class="w-50 d-none d-lg-block">

                <div class="d-flex align-items-center justify-content-end">

                    <div class="dropdown currency-switcher">

                        <a href="#" class="dropdown-toggle currency-switcher-trigger d-flex align-items-center"
                            data-bs-toggle="dropdown" aria-expanded="false">

                            <span class="currency-main-code">
                                {{ $activeCurrency }} ({{ $currencySymbol }})
                            </span>

                        </a>


                        <div class="dropdown-menu dropdown-menu-end py-3 currency-menu" style="min-width: 135px;">

                            {{-- NGN --}}
                            <button type="button"
                                class="dropdown-item py-2 currency-option d-flex align-items-center justify-content-between
                        {{ $activeCurrency === 'NGN' ? 'currency-active' : '' }}"
                                data-currency="NGN">

                                <span>
                                    NGN (₦)
                                </span>

                                @if ($activeCurrency === 'NGN')
                                    <span class="currency-check">✓</span>
                                @endif

                            </button>


                            {{-- USD --}}
                            <button type="button"
                                class="dropdown-item py-2 currency-option d-flex align-items-center justify-content-between
                        {{ $activeCurrency === 'USD' ? 'currency-active' : '' }}"
                                data-currency="USD">

                                <span>
                                    USD ($)
                                </span>

                                @if ($activeCurrency === 'USD')
                                    <span class="currency-check">✓</span>
                                @endif

                            </button>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ================= MOBILE CURRENCY SWITCH ================= --}}
            <div class="d-lg-none w-100 mobile-currency-switch">

                <div class="d-flex align-items-center justify-content-between">

                    <div class="dropdown currency-switcher">

                        <a href="#" class="dropdown-toggle currency-switcher-trigger d-flex align-items-center"
                            data-bs-toggle="dropdown" aria-expanded="false">

                            <span class="currency-main-code">
                                {{ $activeCurrency }} ({{ $currencySymbol }})
                            </span>

                        </a>


                        <div class="dropdown-menu dropdown-menu-end py-3 currency-menu" style="min-width: 135px;">

                            {{-- NGN --}}
                            <button type="button"
                                class="dropdown-item py-2 currency-option d-flex align-items-center justify-content-between
                        {{ $activeCurrency === 'NGN' ? 'currency-active' : '' }}"
                                data-currency="NGN">

                                <span>
                                    NGN (₦)
                                </span>

                                @if ($activeCurrency === 'NGN')
                                    <span class="currency-check">✓</span>
                                @endif

                            </button>


                            {{-- USD --}}
                            <button type="button"
                                class="dropdown-item py-2 currency-option d-flex align-items-center justify-content-between
                        {{ $activeCurrency === 'USD' ? 'currency-active' : '' }}"
                                data-currency="USD">

                                <span>
                                    USD ($)
                                </span>

                                @if ($activeCurrency === 'USD')
                                    <span class="currency-check">✓</span>
                                @endif

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- =========================================================
         MAIN HEADER
    ========================================================== -->
    <div class="sticky-area">

        <div class="main-header nav navbar bg-body navbar-light navbar-expand-xl py-6 py-xl-0">

            <div class="container-xxl container">


                <!-- =====================================================
                     MOBILE HEADER
                ====================================================== -->
                <div class="d-flex d-xl-none align-items-center w-100">


                    <!-- Hamburger -->
                    <div class="w-72px d-flex">

                        <button class="navbar-toggler align-self-center border-0 shadow-none px-0 canvas-toggle p-4"
                            type="button" data-bs-toggle="offcanvas" data-bs-target="#offCanvasNavBar"
                            aria-controls="offCanvasNavBar" aria-expanded="false" aria-label="Toggle Navigation">

                            <span class="fs-24 toggle-icon"></span>

                        </button>

                    </div>

                    <!-- Logo -->
                    <div class="d-flex mx-auto">

                        <a href="#" class="navbar-brand px-4 py-4 mx-auto">

                            <img class="light-mode-img" src="{{ asset('assets/images/others/logo.png') }}"
                                width="230" height="65" alt="Delamoure">

                            <img class="dark-mode-img" src="{{ asset('assets/images/others/logo-white.png') }}"
                                width="230" height="65" alt="Delamoure">

                        </a>

                    </div>

                    <!-- Mobile Actions -->
                    <div class="icons-actions d-flex justify-content-end fs-28px text-body-emphasis">


                        <!-- Search -->
                        <div class="px-2">

                            <a class="lh-1 color-inherit text-decoration-none" href="#" data-bs-toggle="offcanvas"
                                data-bs-target="#searchModal" aria-controls="searchModal">

                                <svg class="icon icon-magnifying-glass-light">
                                    <use xlink:href="#icon-magnifying-glass-light"></use>
                                </svg>

                            </a>

                        </div>


                        <!-- Cart -->
                        <div class="ps-4">

                            <a class="position-relative lh-1 color-inherit text-decoration-none" href="#"
                                data-bs-toggle="offcanvas" data-bs-target="#shoppingCart"
                                aria-controls="shoppingCart">

                                <svg class="icon">
                                    <use xlink:href="#icon-shopping-bag-open-light"></use>
                                </svg>

                            </a>

                        </div>

                    </div>

                </div>



                <!-- =====================================================
                     DESKTOP HEADER
                ====================================================== -->
                <div class="d-none d-xl-flex flex-row align-items-center w-100">


                    <!-- =================================================
                         LEFT SIDE
                    ================================================== -->
                    <div class="w-xl-50 d-flex align-items-center">


                        <!-- Search -->
                        <div class="icons-actions d-flex justify-content-start me-auto fs-28px text-body-emphasis">

                            <div class="pe-6">

                                <a class="lh-1 color-inherit text-decoration-none" href="#"
                                    data-bs-toggle="offcanvas" data-bs-target="#searchModal"
                                    aria-controls="searchModal">

                                    <svg class="icon icon-magnifying-glass-light fs-5">
                                        <use xlink:href="#icon-magnifying-glass-light"></use>
                                    </svg>

                                    <span class="fs-15px">
                                        Search
                                    </span>

                                </a>

                            </div>

                        </div>



                        <!-- Left Navigation -->
                        <ul class="navbar-nav w-auto">


                            <!-- Home -->
                            <li class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-7 px-xl-5">

                                <a class="nav-link position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-14px"
                                    href="{{ url('/') }}">

                                    Home

                                </a>

                            </li>


                            <!-- Shop -->
                            <li class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-7 px-xl-5">

                                <a class="nav-link position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-14px"
                                    href="{{ route('shop') }}">

                                    Shop

                                </a>

                            </li>


                            <!-- New Arrivals -->
                            <li class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-7 px-xl-5">

                                <a class="nav-link position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-14px"
                                    href="#">

                                    New Arrivals

                                </a>

                            </li>

                        </ul>

                    </div>



                    <!-- =================================================
                         CENTER LOGO
                    ================================================== -->
                    <div class="px-8 d-flex align-items-center">

                        <a href="#" class="navbar-brand px-6 py-4 mx-auto">

                            <img class="light-mode-img" src="{{ asset('assets/images/others/logo.png') }}"
                                width="230" height="65" alt="Delamoure">

                            <img class="dark-mode-img" src="{{ asset('assets/images/others/logo-white.png') }}"
                                width="179" height="26" alt="Delamoure">

                        </a>

                    </div>

                    <!-- =================================================
                         RIGHT SIDE
                    ================================================== -->
                    <div class="w-xl-50 d-flex align-items-center">


                        <!-- Right Navigation -->
                        <ul class="navbar-nav w-auto">

                            <!-- About -->
                            <li class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-7 px-xl-5">

                                <a class="nav-link position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-14px"
                                    href="{{ route('about') }}">

                                    About Us

                                </a>

                            </li>

                            <!-- Contact -->
                            <li class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-7 px-xl-5">

                                <a class="nav-link position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-14px"
                                    href="{{ route('contact') }}">
                                    Contact

                                </a>

                            </li>

                        </ul>

                        <!-- Account / Cart -->
                        <div class="icons-actions d-flex justify-content-end ms-auto fs-28px text-body-emphasis">
                            <!-- Account -->
                            <div class="px-4">

                                @guest

                                    {{-- Guest --}}
                                    <a class="lh-1 color-inherit text-decoration-none" href="{{ route('login') }}"
                                        title="Login / My Account">

                                        <svg class="icon icon-user-light">
                                            <use xlink:href="#icon-user-light"></use>
                                        </svg>

                                    </a>
                                @else
                                    @php
                                        $user = auth()->user();
                                        $isAdmin = $user->hasRole('Admin');
                                    @endphp

                                    {{-- Authenticated User --}}
                                    <a class="lh-1 color-inherit text-decoration-none" href="{{ route('home') }}"
                                        title="{{ $isAdmin ? 'Admin Dashboard' : 'My Account' }}">

                                        <svg class="icon icon-user-light">
                                            <use xlink:href="#icon-user-light"></use>
                                        </svg>

                                    </a>

                                @endguest

                            </div>
                            @php

                                if (auth()->check()) {
                                    $wishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
                                } else {
                                    $wishlistCount = collect(session()->get('wishlist', []))
                                        ->unique()
                                        ->count();
                                }

                            @endphp


                            <div class="px-5 d-none d-xl-inline-block">

                                <a class="position-relative lh-1 color-inherit text-decoration-none"
                                    href="{{ route('wishlist.index') }}" title="Wishlist" aria-label="Wishlist">

                                    <svg class="icon icon-star-light">
                                        <use xlink:href="#icon-star-light"></use>
                                    </svg>


                                    <span
                                        class="wishlist-count badge bg-dark text-white position-absolute top-0 start-100 translate-middle mt-4 rounded-circle fs-13px p-0 square
                {{ $wishlistCount < 1 ? 'd-none' : '' }}"
                                        style="--square-size: 18px">

                                        {{ $wishlistCount }}

                                    </span>

                                </a>

                            </div>

                            <!-- Cart -->
                            <div class="ps-4">

                                <a class="position-relative lh-1 color-inherit text-decoration-none" href="#"
                                    data-bs-toggle="offcanvas" data-bs-target="#shoppingCart"
                                    aria-controls="shoppingCart">

                                    <svg class="icon">
                                        <use xlink:href="#icon-shopping-bag-open-light"></use>
                                    </svg>

                                    <span
                                        class="badge bg-dark text-white position-absolute top-0 start-100 translate-middle mt-4 rounded-circle fs-13px p-0 square"
                                        style="--square-size:18px" data-cart-count id="cartCountBadge">
                                        {{ Cart::getTotalQuantity() }}
                                    </span>

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</header>
