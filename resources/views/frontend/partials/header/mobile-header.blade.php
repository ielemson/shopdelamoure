@php

    /*
    |--------------------------------------------------------------------------
    | Mobile Categories
    |--------------------------------------------------------------------------
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
            <nav class="flex-grow-1">

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



                    {{-- Shop --}}
                    <li class="nav-item">

                        <a href="{{ route('shop') }}"
                            class="nav-link mobile-menu-link
                                {{ request()->routeIs('shop') ? 'active' : '' }}">

                            <span>
                                Shop
                            </span>

                        </a>

                    </li>



                    {{-- Accessories --}}
                    <li class="nav-item">

                        <a href="{{ $accessoriesUrl }}"
                            class="nav-link mobile-menu-link
                                {{ $accessoriesCategory && $currentCategorySlug === $accessoriesCategory->slug ? 'active' : '' }}">

                            <span>
                                Accessories
                            </span>

                        </a>

                    </li>



                    {{-- Gift Sets --}}
                    <li class="nav-item">

                        <a href="{{ $giftSetsUrl }}"
                            class="nav-link mobile-menu-link
                                {{ $giftSetsCategory && $currentCategorySlug === $giftSetsCategory->slug ? 'active' : '' }}">

                            <span>
                                Gift Sets
                            </span>

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


                    @if ($wishlistCount > 0)
                        <span class="wishlist-count badge bg-dark text-white rounded-circle">
                            {{ $wishlistCount }}
                        </span>
                    @endif

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


                    @if ($compareCount > 0)
                        <span class="compare-count badge bg-dark text-white rounded-circle">
                            {{ $compareCount }}
                        </span>
                    @endif

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

                    Curated accessories & thoughtful gifts.

                </p>

            </div>

        </div>

    </div>

</div>
