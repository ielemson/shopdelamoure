@php

    /*
    |--------------------------------------------------------------------------
    | Website Settings
    |--------------------------------------------------------------------------
    */

    $setting = \App\Models\WebsiteSetting::first();

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    $activeCurrency = strtoupper(session('currency', 'NGN'));

    $currencySymbol = $activeCurrency === 'USD' ? '$' : '₦';

    /*
    |--------------------------------------------------------------------------
    | Header Categories
    |--------------------------------------------------------------------------
    |
    | We retrieve the actual category record so the current database slug
    | is always used in the URL.
    |
    */

    $accessoriesCategory = \App\Models\Category::query()->where('status', 1)->where('name', 'Accessories')->first();

    $giftSetsCategory = \App\Models\Category::query()
        ->where('status', 1)
        ->whereIn('name', ['Gift Set', 'Gift Sets'])
        ->first();

    $accessoriesUrl = $accessoriesCategory ? route('shop.category', $accessoriesCategory->slug) : route('shop');

    $giftSetsUrl = $giftSetsCategory ? route('shop.category', $giftSetsCategory->slug) : route('shop');

    /*
    |--------------------------------------------------------------------------
    | Navigation Active States
    |--------------------------------------------------------------------------
    */

    $accessoriesActive =
        $accessoriesCategory &&
        request()->routeIs('shop.category') &&
        request()->route('slug') === $accessoriesCategory->slug;

    $giftSetsActive =
        $giftSetsCategory &&
        request()->routeIs('shop.category') &&
        request()->route('slug') === $giftSetsCategory->slug;

    /*
    |--------------------------------------------------------------------------
    | Wishlist Count
    |--------------------------------------------------------------------------
    */

    if (auth()->check()) {
        $wishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
    } else {
        $wishlistCount = collect(session()->get('wishlist', []))
            ->unique()
            ->count();
    }

@endphp


<header id="header" class="header header-sticky header-sticky-smart disable-transition-all z-index-5">


    {{-- ============================================================
    | TOP BAR
    ============================================================ --}}
    <div class="bg-primary bg-opacity-15">

        <div class="container container-xxl d-flex align-items-center py-4">

            {{-- Social Media --}}
            <div class="w-50 d-none d-lg-block">

                <ul class="social-icons list-inline mb-0 fs-14">

                    {{-- Instagram --}}
                    @if (!empty($setting?->instagram))
                        <li class="list-inline-item">
                            <a href="{{ $setting->instagram }}" target="_blank" rel="noopener noreferrer"
                                title="Instagram">
                                <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Facebook --}}
                    @if (!empty($setting?->facebook))
                        <li class="list-inline-item ms-6">
                            <a href="{{ $setting->facebook }}" target="_blank" rel="noopener noreferrer"
                                title="Facebook">
                                <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Snapchat --}}
                    @if (!empty($setting?->snapchat))
                        <li class="list-inline-item ms-6">
                            <a href="{{ $setting->snapchat }}" target="_blank" rel="noopener noreferrer"
                                title="Snapchat">
                                <i class="fa-brands fa-snapchat" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endif

                    {{-- TikTok --}}
                    @if (!empty($setting?->tiktok))
                        <li class="list-inline-item ms-6">
                            <a href="{{ $setting->tiktok }}" target="_blank" rel="noopener noreferrer" title="TikTok">
                                <i class="fa-brands fa-tiktok" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endif

                </ul>

            </div>



            {{-- ========================================================
            | DESKTOP CURRENCY
            ======================================================== --}}
            <div class="w-50 d-none d-lg-block">

                <div class="d-flex align-items-center justify-content-end">

                    <div class="dropdown currency-switcher">

                        <a href="#" class="dropdown-toggle currency-switcher-trigger d-flex align-items-center"
                            data-bs-toggle="dropdown" aria-expanded="false">

                            <span class="currency-main-code">

                                {{ $activeCurrency }}
                                ({{ $currencySymbol }})

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
                                    <span class="currency-check">
                                        ✓
                                    </span>
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
                                    <span class="currency-check">
                                        ✓
                                    </span>
                                @endif

                            </button>

                        </div>

                    </div>

                </div>

            </div>



            {{-- ========================================================
            | MOBILE CURRENCY
            ======================================================== --}}
            <div class="d-lg-none ms-auto">

                <div class="dropdown currency-switcher">

                    <a href="#" class="dropdown-toggle currency-switcher-trigger d-flex align-items-center"
                        data-bs-toggle="dropdown" aria-expanded="false">

                        <span class="currency-main-code">

                            {{ $activeCurrency }}
                            ({{ $currencySymbol }})

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
                                <span class="currency-check">
                                    ✓
                                </span>
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
                                <span class="currency-check">
                                    ✓
                                </span>
                            @endif

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
    | MAIN HEADER
    ============================================================ --}}
    <div class="sticky-area">

        <div class="main-header nav navbar bg-body navbar-light navbar-expand-xl py-6 py-xl-0">

            <div class="container container-xxl">



                {{-- ====================================================
                | MOBILE HEADER
                ==================================================== --}}
                <div class="d-flex d-xl-none align-items-center w-100">


                    {{-- Hamburger --}}
                    <div class="w-72px d-flex">

                        <button class="navbar-toggler align-self-center border-0 shadow-none px-0 canvas-toggle p-4"
                            type="button" data-bs-toggle="offcanvas" data-bs-target="#offCanvasNavBar"
                            aria-controls="offCanvasNavBar" aria-expanded="false" aria-label="Toggle Navigation">

                            <span class="fs-24 toggle-icon"></span>

                        </button>

                    </div>



                    {{-- Mobile Logo --}}
                    <div class="d-flex mx-auto">

                        <a href="{{ url('/') }}" class="navbar-brand px-2 py-3 mx-auto">

                            <img class="light-mode-img" src="{{ asset('assets/images/others/logo.png') }}"
                                width="160" height="45" alt="De Lamoure">

                            <img class="dark-mode-img" src="{{ asset('assets/images/others/logo-white.png') }}"
                                width="160" height="45" alt="De Lamoure">

                        </a>

                    </div>



                    {{-- Mobile Actions --}}
                    <div class="icons-actions d-flex justify-content-end fs-28px text-body-emphasis">


                        {{-- Search --}}
                        <div class="px-2">

                            <a class="lh-1 color-inherit text-decoration-none" href="#" data-bs-toggle="offcanvas"
                                data-bs-target="#searchModal" aria-controls="searchModal" aria-label="Search">

                                <svg class="icon icon-magnifying-glass-light">

                                    <use xlink:href="#icon-magnifying-glass-light"></use>

                                </svg>

                            </a>

                        </div>



                        {{-- Cart --}}
                        <div class="ps-4">

                            <a class="position-relative lh-1 color-inherit text-decoration-none" href="#"
                                data-bs-toggle="offcanvas" data-bs-target="#shoppingCart"
                                aria-controls="shoppingCart" aria-label="Shopping Cart">

                                <svg class="icon">

                                    <use xlink:href="#icon-shopping-bag-open-light"></use>

                                </svg>


                                <span
                                    class="badge bg-dark text-white position-absolute top-0 start-100 translate-middle mt-4 rounded-circle fs-13px p-0 square
                                    {{ Cart::getTotalQuantity() < 1 ? 'd-none' : '' }}"
                                    style="--square-size: 18px" data-cart-count>

                                    {{ Cart::getTotalQuantity() }}

                                </span>

                            </a>

                        </div>

                    </div>

                </div>



                {{-- ====================================================
                | DESKTOP HEADER
                ==================================================== --}}
                <div class="d-none d-xl-flex flex-row align-items-center w-100">


                    {{-- =================================================
                    | LEFT SIDE
                    ================================================= --}}
                    <div class="w-xl-50 d-flex align-items-center">


                        {{-- Search --}}
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



                        {{-- Left Navigation --}}
                        <ul class="navbar-nav w-auto">


                            {{-- Home --}}
                            <li class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-4 px-xl-3">

                                <a class="nav-link position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-14px
                                    {{ request()->is('/') ? 'active' : '' }}"
                                    href="{{ url('/') }}">

                                    Home

                                </a>

                            </li>



                            {{-- Shop --}}
                            <li
                                class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-4 px-xl-3 dropdown dropdown-hover dropdown-fullwidth position-static">
                                <a class="nav-link d-flex justify-content-between position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-15px fs-xl-14px dropdown-toggle {{ request()->routeIs('shop', 'shop.category') ? 'active' : '' }}"
                                    href="{{ route('shop') }}" data-bs-toggle="dropdown" id="menu-item-shop"
                                    aria-haspopup="true" aria-expanded="false">Shop</a>
                                <div class="dropdown-menu mega-menu start-0 py-6 w-100"
                                    aria-labelledby="menu-item-shop">
                                    <div class="megamenu-shop container-wide py-8 px-12">
                                        @php
                                            $megaMenuCategories = \App\Models\Category::query()
                                                ->where('status', 1)
                                                ->orderBy('name')
                                                ->get();
                                            $megaMenuColumns = $megaMenuCategories->chunk(
                                                max(1, (int) ceil($megaMenuCategories->count() / 3)),
                                            );
                                        @endphp
                                        <div class="row">
                                            <div class="col">
                                                <h6 class="fs-18px">Discover De Lamoure</h6>
                                                <ul class="list-unstyled mb-0">
                                                    <li>
                                                        <a href="{{ route('shop') }}"
                                                            class="border-hover text-decoration-none py-3 d-block">
                                                            <span class="border-hover-target">Shop All Products</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('shop/wholesale') }}"
                                                            class="border-hover text-decoration-none py-3 d-block">
                                                            <span class="border-hover-target">Wholesale
                                                                Collection</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            @foreach ($megaMenuColumns as $megaMenuColumn)
                                                <div class="col">
                                                    <h6 class="fs-18px">
                                                        {{ $loop->first ? 'Our Collections' : 'Explore More' }}</h6>
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach ($megaMenuColumn as $megaMenuCategory)
                                                            <li>
                                                                <a href="{{ route('shop.category', $megaMenuCategory->slug) }}"
                                                                    class="border-hover text-decoration-none py-3 d-block">
                                                                    <span
                                                                        class="border-hover-target">{{ $megaMenuCategory->name }}</span>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </li>



                            {{-- Wholesale --}}
                            <li class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-4 px-xl-3">
                                <a class="nav-link position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-14px
                                    {{ request()->routeIs('wholesale.*') || request()->is('wholesale', 'wholesale/*') ? 'active' : '' }}"
                                    href="{{ url('shop/wholesale') }}"
                                    @if (request()->routeIs('wholesale.*') || request()->is('wholesale', 'wholesale/*')) aria-current="page" @endif>
                                    Wholesale
                                </a>
                            </li>

                            {{-- Accessories --}}
                            {{-- <li class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-4 px-xl-3">

                                <a class="nav-link position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-14px
                                    {{ $accessoriesActive ? 'active' : '' }}"
                                    href="{{ $accessoriesUrl }}">

                                    Accessories

                                </a>

                            </li> --}}

                        </ul>

                    </div>



                    {{-- =================================================
                    | CENTER LOGO
                    ================================================= --}}
                    <div class="px-8 d-flex align-items-center">

                        <a href="{{ url('/') }}" class="navbar-brand px-6 py-4 mx-auto">

                            <img class="light-mode-img" src="{{ asset('assets/images/others/logo.png') }}"
                                width="230" height="65" alt="De Lamoure">

                            <img class="dark-mode-img" src="{{ asset('assets/images/others/logo-white.png') }}"
                                width="230" height="65" alt="De Lamoure">

                        </a>

                    </div>



                    {{-- =================================================
                    | RIGHT SIDE
                    ================================================= --}}
                    <div class="w-xl-50 d-flex align-items-center">


                        {{-- Right Navigation --}}
                        <ul class="navbar-nav w-auto">


                            {{-- Gift Sets --}}
                            {{-- <li class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-7 px-xl-5">

                                <a class="nav-link position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-14px
                                    {{ $giftSetsActive ? 'active' : '' }}"
                                    href="{{ $giftSetsUrl }}">

                                    Gift Sets

                                </a>

                            </li> --}}



                            {{-- About --}}
                            <li class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-7 px-xl-5">

                                <a class="nav-link position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-14px
                                    {{ request()->routeIs('about') ? 'active' : '' }}"
                                    href="{{ route('about') }}">

                                    About Us

                                </a>

                            </li>



                            {{-- Contact --}}
                            <li class="nav-item transition-all-xl-1 py-xl-11 py-0 px-xxl-7 px-xl-5">

                                <a class="nav-link position-relative py-xl-0 px-xl-0 text-uppercase fw-semibold ls-1 fs-14px
                                    {{ request()->routeIs('contact') ? 'active' : '' }}"
                                    href="{{ route('contact') }}">

                                    Contact

                                </a>

                            </li>

                        </ul>



                        {{-- =================================================
                        | ACCOUNT / WISHLIST / COMPARE / CART
                        ================================================= --}}
                        <div class="icons-actions d-flex justify-content-end ms-auto fs-28px text-body-emphasis">


                            {{-- Account --}}
                            <div class="px-4">

                                @guest

                                    <a class="lh-1 color-inherit text-decoration-none" href="{{ route('login') }}"
                                        title="Login / My Account" aria-label="Login / My Account">

                                        <svg class="icon icon-user-light">

                                            <use xlink:href="#icon-user-light"></use>

                                        </svg>

                                    </a>
                                @else
                                    <a class="lh-1 color-inherit text-decoration-none" href="{{ route('home') }}"
                                        title="{{ auth()->user()->hasRole('Admin') ? 'Admin Dashboard' : 'My Account' }}"
                                        aria-label="{{ auth()->user()->hasRole('Admin') ? 'Admin Dashboard' : 'My Account' }}">

                                        <svg class="icon icon-user-light">

                                            <use xlink:href="#icon-user-light"></use>

                                        </svg>

                                    </a>

                                @endguest

                            </div>



                            {{-- Wishlist --}}
                            <div class="px-4">

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



                            {{-- Cart --}}
                            <div class="ps-4">

                                <a class="position-relative lh-1 color-inherit text-decoration-none" href="#"
                                    data-bs-toggle="offcanvas" data-bs-target="#shoppingCart"
                                    aria-controls="shoppingCart" title="Shopping Cart">

                                    <svg class="icon">

                                        <use xlink:href="#icon-shopping-bag-open-light"></use>

                                    </svg>


                                    <span
                                        class="badge bg-dark text-white position-absolute top-0 start-100 translate-middle mt-4 rounded-circle fs-13px p-0 square
                                        {{ Cart::getTotalQuantity() < 1 ? 'd-none' : '' }}"
                                        style="--square-size: 18px" data-cart-count id="cartCountBadge">

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


{{-- @include('frontend.partials.mobile-menu') --}}
