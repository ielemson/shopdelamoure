<header class="ec-header">
    <div class="header-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col text-left header-top-left d-none d-lg-block">
                    <div class="header-top-social">
                        <ul class="mb-0">
                            <li class="list-inline-item"><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li class="list-inline-item"><a href="#"><i class="fab fa-twitter"></i></a></li>
                            <li class="list-inline-item"><a href="#"><i class="fab fa-youtube"></i></a></li>
                            <li class="list-inline-item"><a href="#"><i class="fab fa-vimeo-v"></i></a></li>
                            <li class="list-inline-item"><a href="#"><i class="fab fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col text-center header-top-center">
                    <div class="header-top-message">
                        World's Fastest Online Shopping Destination
                    </div>
                </div>
                <div class="col header-top-right d-none d-lg-block">
                    <div class="header-top-right-inner d-flex justify-content-end">
                        <div class="header-top-lan-curr header-top-lan dropdown">
                            <button class="dropdown-toggle" data-bs-toggle="dropdown">
                                English
                                <i class="fas fa-angle-down" aria-hidden="true"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li class="active">
                                    <a class="dropdown-item" href="#">English</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">Italiano</a>
                                </li>
                            </ul>
                        </div>
                        <div class="header-top-lan-curr header-top-curr dropdown">
                            <button class="dropdown-toggle" data-bs-toggle="dropdown">
                                Dollar
                                <i class="fas fa-angle-down" aria-hidden="true"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li class="active">
                                    <a class="dropdown-item" href="#">USD $</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">EUR €</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col header-top-res d-lg-none">
                    <div class="ec-header-bottons">
                        @auth
                            <a href="{{ route('home') }}" class="ec-header-btn ec-header-user">
                                <div class="header-icon">
                                    <i class="fi-rr-user"></i>
                                </div>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="ec-header-btn ec-header-user">
                                <div class="header-icon">
                                    <i class="fi-rr-user"></i>
                                </div>
                            </a>
                        @endauth
                        <a href="#" class="ec-header-btn ec-header-wishlist">
                            <div class="header-icon"><i class="fi-rr-heart"></i></div>
                            <span class="ec-header-count ec-wishlist-count">0</span>
                        </a>
                        @php
                            use Darryldecode\Cart\Facades\CartFacade as Cart;
                        @endphp

                        <a href="#ec-side-cart" class="ec-header-btn ec-side-toggle" data-cart-url="{{ route('cart.sidebar') }}">
                            <div class="header-icon">
                                <i class="fi-rr-shopping-basket"></i>
                            </div>

                            <span class="ec-header-count ec-cart-count">
                                {{ Cart::getTotalQuantity() }}
                            </span>
                        </a>
                        <a href="#ec-mobile-menu" class="ec-header-btn ec-side-toggle d-lg-none">
                            <i class="fas fa-bars"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ec-header-bottom d-none d-lg-block">
        <div class="container position-relative">
            <div class="row">
                <div class="ec-flex">
                    <div class="align-self-center ec-header-logo">
                        <div class="header-logo">
                            <a href="{{ route('index') }}"><img src="assets/images/logo/logo-4.png"
                                    alt="Site Logo" /><img class="dark-logo" src="assets/images/logo/dark-logo-4.png"
                                    alt="Site Logo" style="display: none;" /></a>
                        </div>
                    </div>
                    <div class="align-self-center ec-header-search">
                        <div class="header-search">
                            <form class="ec-search-group-form" action="#">
                                <input class="form-control" placeholder="Search Your Products..." type="text">
                                <button class="search_submit" type="submit"><i class="fi-rr-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="align-self-center">
                        <div class="ec-header-bottons">
                            @auth
                                <a href="{{ route('home') }}" class="ec-header-btn ec-header-user">
                                    <div class="header-icon">
                                        <i class="fi-rr-user"></i>
                                    </div>
                                    <div class="ec-btn-desc">
                                        <span class="ec-btn-title">
                                            {{ auth()->user()->name }}
                                        </span>
                                        <span class="ec-btn-stitle">
                                            My Account
                                        </span>
                                    </div>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="ec-header-btn ec-header-user">
                                    <div class="header-icon">
                                        <i class="fi-rr-user"></i>
                                    </div>
                                    <div class="ec-btn-desc">
                                        <span class="ec-btn-title">Account</span>
                                        <span class="ec-btn-stitle">Login</span>
                                    </div>
                                </a>
                            @endauth
                            <a href="#" class="ec-header-btn ec-header-wishlist">
                                <div class="header-icon"><i class="fi-rr-heart"></i></div>
                                <div class="ec-btn-desc">
                                    <span class="ec-btn-title">Wishlist</span>
                                    <span class="ec-btn-stitle"><b class="ec-wishlist-count">0</b>-items</span>
                                </div>
                            </a>
                            @php
                                $cartCount = \Darryldecode\Cart\Facades\CartFacade::getTotalQuantity();
                            @endphp
                            <a href="#ec-side-cart" class="ec-header-btn ec-side-toggle">

                                <div class="header-icon">
                                    <i class="fi-rr-shopping-basket"></i>
                                </div>

                                <div class="ec-btn-desc">
                                    <span class="ec-btn-title">Cart</span>

                                    <span class="ec-btn-stitle">
                                        <b class="ec-cart-count">{{ $cartCount }}</b>
                                        {{ $cartCount == 1 ? 'item' : 'items' }}
                                    </span>
                                </div>

                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('frontend.partials.header.mobile-menu')
    <div class="ec-header-cat d-none d-lg-block">
        <div class="container position-relative">
            <div class="row">
                <div class="col ec-category-block">
                    <div class="ec-category-menu">
                        <div class="ec-category-toggle">
                            <i class="fas fa-bars"></i>
                            <span class="ec-category-title d-1199">
                                Shop By Category
                            </span>
                            <i class="fas fa-angle-down d-1199" aria-hidden="true"></i>
                        </div>
                        <div class="ec-category-content">
                            <div class="ec-category-dropdown">
                                <ul class="ec-category-wrapper">
                                    @forelse($categories as $category)
                                        <li class="menu-item">
                                            <a class="ec-cat-menu-link {{ request()->route('slug') == $category->slug ? 'active' : '' }}"
                                                href="{{ route('category.products', $category->slug) }}">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @empty
                                        <li class="menu-item">
                                            <span class="ec-cat-menu-link text-muted">
                                                No Categories Found
                                            </span>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="ec-main-menu-desk" class="d-none d-lg-block sticky-nav">
                    <div class="position-relative nav-desk">
                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="ec-main-menu">
                                    <ul>
                                        <li class="dropdown">
                                            <a href="/">Home</a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="{{ route('shop') }}">Shop</a>
                                        </li>

                                        <li class="dropdown">
                                            <a href="javascript:void(0)">
                                                My Account
                                                <i class="fas fa-angle-down"></i>
                                            </a>
                                            <ul class="sub-menu">
                                                @guest
                                                    <li>
                                                        <a href="{{ route('login') }}">
                                                            Login
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('register') }}">
                                                            Register
                                                        </a>
                                                    </li>
                                                @endguest
                                                @auth
                                                   
                                                    <li>
                                                        <a href="{{ route("customer.orders.index") }}">
                                                            My Orders
                                                        </a>
                                                    </li>
                                                    @if (auth()->user()->role === 'Admin')
                                                        <li>
                                                            <a href="{{ route('home') }}">
                                                                Admin Dashboard
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a href="{{ route('logout') }}"
                                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                            Logout
                                                        </a>
                                                        <form id="logout-form" action="{{ route('logout') }}"
                                                            method="POST" class="d-none">
                                                            @csrf
                                                        </form>
                                                    </li>
                                                @endauth
                                            </ul>
                                        </li>
                                        <li class="dropdown">
                                            <a href="/about-us">
                                                About Us
                                            </a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="/contact-us">
                                                Contact Us
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
