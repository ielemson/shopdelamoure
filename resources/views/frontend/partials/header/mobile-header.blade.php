@once('delamoure-mobile-navigation')
    @php

        /*
    |--------------------------------------------------------------------------
    | Mobile Categories
    |--------------------------------------------------------------------------
    */

        $mobileCategories = \App\Models\Category::query()->where('status', 1)->orderBy('name')->get();

        $mobileCategoryOrder = [
            'diffusers' => 1,
            'diffuser' => 1,
            'air fresheners' => 2,
            'air freshener' => 2,
            'scented candles' => 3,
            'scented candle' => 3,
            'essential oils' => 4,
            'essential oil' => 4,
            'toilet care' => 5,
            'scented crystal stones' => 6,
            'scented crystal stone' => 6,
            'scented stones' => 6,
        ];

        $mobileCategories = $mobileCategories
            ->sortBy(fn($category) => $mobileCategoryOrder[strtolower(trim($category->name))] ?? 99)
            ->values();

        $mobileWholesaleActive =
            request()->routeIs('wholesale.*') || request()->is('shop/wholesale', 'shop/wholesale/*');
        $mobileShopActive = !$mobileWholesaleActive && request()->routeIs('shop', 'shop.category');

        /*
    |--------------------------------------------------------------------------
    | Active Category
    |--------------------------------------------------------------------------
    */

        $currentCategorySlug = request()->route('slug');

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

        /*
    |--------------------------------------------------------------------------
    | Compare Count
    |--------------------------------------------------------------------------
    */

        $compareCount = collect(session()->get('compare', []))
            ->unique()
            ->count();

    @endphp

    <div class="navbar">

        <div id="offCanvasNavBar" class="offcanvas offcanvas-start border-0" tabindex="-1"
            aria-labelledby="offCanvasNavBarLabel" style="--bs-offcanvas-width: 320px;">

            {{-- ============================================================
        | HEADER
        ============================================================ --}}
            <div class="offcanvas-header px-6 py-5 border-bottom">

                <a href="{{ url('/') }}" class="text-decoration-none" id="offCanvasNavBarLabel">

                    <img src="{{ asset('assets/images/others/logo-white.png') }}" width="145" height="41"
                        alt="De Lamoure">
                </a>

                <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close">
                </button>

            </div>

            {{-- ============================================================
        | BODY
        ============================================================ --}}
            <div class="offcanvas-body px-6 py-5 d-flex flex-column">

                {{-- ========================================================
            | MAIN NAVIGATION
            ======================================================== --}}
                <nav class="flex-grow-1" aria-label="Mobile navigation">

                    <ul class="navbar-nav mobile-main-menu">

                        {{-- Home --}}
                        <li class="nav-item">

                            <a href="{{ url('/') }}"
                                class="nav-link mobile-menu-link
                                {{ request()->is('/') ? 'active' : '' }}">

                                <span>
                                    Home
                                </span>

                            </a>

                        </li>

                        {{-- Shop: collections stay collapsed until opened. --}}
                        <li class="nav-item">
                            <button type="button"
                                class="nav-link mobile-menu-link d-flex align-items-center justify-content-between w-100 border-0 bg-transparent text-start collapsed {{ $mobileShopActive ? 'active' : '' }}"
                                data-bs-toggle="collapse" data-bs-target="#mobileShopCollections"
                                aria-controls="mobileShopCollections" aria-expanded="false">
                                <span>Shop</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </button>

                            <div class="collapse" id="mobileShopCollections">
                                <ul class="list-unstyled ps-5 pb-3 mb-0">
                                    <li>
                                        <a href="{{ route('shop') }}"
                                            class="border-hover text-decoration-none text-body-emphasis py-3 d-block">
                                            <span class="border-hover-target">Shop All Products</span>
                                        </a>
                                    </li>
                                    @foreach ($mobileCategories as $mobileCategory)
                                        @php
                                            $mobileCategoryActive =
                                                request()->routeIs('shop.category') &&
                                                (string) $currentCategorySlug === (string) $mobileCategory->slug;
                                        @endphp
                                        <li>
                                            <a href="{{ route('shop.category', $mobileCategory->slug) }}"
                                                class="border-hover text-decoration-none py-3 d-block {{ $mobileCategoryActive ? 'text-primary' : 'text-body-emphasis' }}"
                                                @if ($mobileCategoryActive) aria-current="page" @endif>
                                                <span class="border-hover-target">{{ $mobileCategory->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>

                        {{-- Wholesale --}}
                        <li class="nav-item">
                            <a href="{{ url('shop/wholesale') }}"
                                class="nav-link mobile-menu-link {{ $mobileWholesaleActive ? 'active' : '' }}"
                                @if ($mobileWholesaleActive) aria-current="page" @endif>
                                <span>Wholesale</span>
                            </a>
                        </li>

                        {{-- About --}}
                        <li class="nav-item">

                            <a href="{{ route('about') }}"
                                class="nav-link mobile-menu-link
                                {{ request()->routeIs('about') ? 'active' : '' }}">

                                <span>
                                    About Us
                                </span>

                            </a>

                        </li>

                        {{-- Contact --}}
                        <li class="nav-item">

                            <a href="{{ route('contact') }}"
                                class="nav-link mobile-menu-link
                                {{ request()->routeIs('contact') ? 'active' : '' }}">

                                <span>
                                    Contact Us
                                </span>

                            </a>

                        </li>

                    </ul>

                </nav>

                {{-- ========================================================
            | SHOPPING LINKS
            ======================================================== --}}
                <div class="mobile-shopping-section pt-5 mt-5 border-top">

                    <p class="text-uppercase text-muted fw-semibold fs-13px ls-1 mb-4">
                        Shopping
                    </p>

                    {{-- Wishlist --}}
                    <a href="{{ route('wishlist.index') }}"
                        class="d-flex align-items-center justify-content-between text-decoration-none text-body-emphasis py-2 mb-2">

                        <span class="d-flex align-items-center">

                            <svg class="icon me-3" style="width:20px;height:20px;">

                                <use xlink:href="#icon-star-light"></use>

                            </svg>

                            <span class="fw-medium">
                                Wishlist
                            </span>

                        </span>

                        <span
                            class="wishlist-count badge bg-dark text-white rounded-circle {{ $wishlistCount < 1 ? 'd-none' : '' }}"
                            aria-live="polite">
                            {{ $wishlistCount }}
                        </span>

                    </a>

                    {{-- Compare --}}
                    <a href="{{ route('compare.index') }}"
                        class="d-flex align-items-center justify-content-between text-decoration-none text-body-emphasis py-2">

                        <span class="d-flex align-items-center">

                            <svg class="icon me-3" style="width:20px;height:20px;">

                                <use xlink:href="#icon-arrows-left-right-light"></use>

                            </svg>

                            <span class="fw-medium">
                                Compare
                            </span>

                        </span>

                        <span
                            class="compare-count badge bg-dark text-white rounded-circle {{ $compareCount < 1 ? 'd-none' : '' }}"
                            aria-live="polite">
                            {{ $compareCount }}
                        </span>

                    </a>

                </div>

                {{-- ========================================================
            | ACCOUNT
            ======================================================== --}}
                <div class="mobile-account-section pt-5 mt-5 border-top">

                    <p class="text-uppercase text-muted fw-semibold fs-13px ls-1 mb-4">
                        My Account
                    </p>

                    @guest

                        {{-- Login --}}
                        <a href="{{ route('login') }}"
                            class="d-flex align-items-center text-decoration-none text-body-emphasis py-2 mb-2">

                            <svg class="icon me-3" style="width:20px;height:20px;">

                                <use xlink:href="#icon-user-light"></use>

                            </svg>

                            <span class="fw-medium">
                                Login
                            </span>

                        </a>

                        {{-- Register --}}
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="d-flex align-items-center text-decoration-none text-body-emphasis py-2">

                                <svg class="icon me-3" style="width:20px;height:20px;">

                                    <use xlink:href="#icon-user-light"></use>

                                </svg>

                                <span class="fw-medium">
                                    Create Account
                                </span>

                            </a>
                        @endif
                    @else
                        @php
                            $isAdmin = auth()->user()->hasRole('Admin');
                        @endphp

                        {{-- Dashboard --}}
                        <a href="{{ route('home') }}"
                            class="d-flex align-items-center text-decoration-none text-body-emphasis py-2 mb-2">

                            <svg class="icon me-3" style="width:20px;height:20px;">

                                <use xlink:href="#icon-user-light"></use>

                            </svg>

                            <span class="fw-medium">

                                {{ $isAdmin ? 'Admin Dashboard' : 'My Account' }}

                            </span>

                        </a>

                        {{-- Logout --}}
                        <form method="POST" action="{{ route('logout') }}" class="m-0">

                            @csrf

                            <button type="submit"
                                class="btn p-0 border-0 shadow-none d-flex align-items-center text-body-emphasis py-2">

                                <i class="fa-solid fa-arrow-right-from-bracket me-3" style="width:20px;">
                                </i>

                                <span class="fw-medium">
                                    Logout
                                </span>

                            </button>

                        </form>

                    @endguest

                </div>

                {{-- ========================================================
            | FOOTER
            ======================================================== --}}
                <div class="mobile-menu-footer pt-5 mt-5">

                    <p class="small text-muted mb-1">

                        © {{ date('Y') }} De Lamoure Brand Ltd.

                    </p>

                    <p class="small text-muted mb-0">

                        Signature scents for your home, bathroom and car.

                    </p>

                </div>

            </div>

        </div>

    </div>

@endonce
