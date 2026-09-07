<header class="ec-header">
    <div class="header-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col text-left header-top-left d-none d-lg-block">
                    <div class="header-top-social">
                        <span class="social-text text-upper">Follow us on:</span>
                        <ul class="mb-0">
                            <li class="list-inline-item">
                                <a class="hdr-facebook" href="#">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a class="hdr-twitter" href="#">
                                    <i class="fab fa-x-twitter"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a class="hdr-instagram" href="#">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a class="hdr-linkedin" href="#">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col text-center header-top-center">
                    <div class="header-top-message text-upper">
                        <span>Free Shipping</span>This Week Order Over - $75
                    </div>
                </div>
                <div class="col header-top-right d-none d-lg-block">
                    <div class="header-top-lan-curr d-flex justify-content-end">
                        <div class="header-top-curr dropdown">
                            <button class="dropdown-toggle text-upper" data-bs-toggle="dropdown">
                                Currency
                                <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="header-top-lan dropdown">
                            <button class="dropdown-toggle text-upper" data-bs-toggle="dropdown">
                                Language
                                <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>
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
                    </div>
                </div>
                <div class="col d-lg-none ">
                    <div class="ec-header-bottons">
                        <div class="ec-header-user dropdown">
                            <button class="dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fi-rr-user"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                @guest
                                    <li>
                                        <a class="dropdown-item" href="{{ route('register') }}">
                                            Register
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('login') }}">
                                            Login
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <span class="dropdown-item-text fw-bold">
                                            {{ auth()->user()->first_name ?? auth()->user()->name }}
                                        </span>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('home') }}">
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                Logout
                                            </button>
                                        </form>
                                    </li>
                                @endguest
                            </ul>
                        </div>
                        <a href="javascript:;" class="ec-header-btn ec-header-wishlist">
                            <div class="header-icon"><i class="fi-rr-heart"></i></div>
                            <span class="ec-header-count">0</span>
                        </a>
                        <a href="#ec-side-cart" class="ec-header-btn ec-side-toggle">
                            <div class="header-icon">
                                <i class="fi-rr-shopping-bag"></i>
                            </div>
                            <span class="ec-header-count cart-count-lable">
                                {{ \Darryldecode\Cart\Facades\CartFacade::getTotalQuantity() }}
                            </span>
                        </a>
                        <a href="#ec-mobile-menu" class="ec-header-btn ec-side-toggle d-lg-none">
                            <i class="fi fi-rr-menu-burger"></i>
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
                    <div class="align-self-center">
                        <div class="header-logo">
                            <a href="{{route("index")}}"><img src="assets/images/logo/logo.png" alt="Site Logo" /><img
                                    class="dark-logo" src="assets/images/logo/dark-logo.png" alt="Site Logo"
                                    style="display: none;" /></a>
                        </div>
                    </div>
                    <div class="align-self-center">
                        <div class="header-search">
                            <form class="ec-btn-group-form" action="#">
                                <input class="form-control ec-search-bar" placeholder="Search products..."
                                    type="text">
                                <button class="submit" type="submit"><i class="fi-rr-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="align-self-center">
                        <div class="ec-header-bottons">
                            <div class="ec-header-user dropdown">
                                <button class="dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fi-rr-user"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    @guest
                                        <li>
                                            <a class="dropdown-item" href="{{ route('register') }}">
                                                Register
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('login') }}">
                                                Login
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <span class="dropdown-item-text fw-bold">
                                                {{ auth()->user()->first_name ?? auth()->user()->name }}
                                            </span>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('home') }}">
                                                Dashboard
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">
                                                    Logout
                                                </button>
                                            </form>

                                        </li>

                                    @endguest

                                </ul>

                            </div>
                            <a href="javascript:;" class="ec-header-btn ec-header-wishlist">
                                <div class="header-icon"><i class="fi-rr-heart"></i></div>
                                <span class="ec-header-count">0</span>
                            </a>
                            <a href="#ec-side-cart" class="ec-header-btn ec-side-toggle">
                                <div class="header-icon">
                                    <i class="fi-rr-shopping-bag"></i>
                                </div>
                                <span class="ec-header-count cart-count-lable">
                                    {{ \Darryldecode\Cart\Facades\CartFacade::getTotalQuantity() }}
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ec-header-bottom d-lg-none">
        <div class="container position-relative">
            <div class="row ">
                <div class="col">
                    <div class="header-logo">
                        <a href="{{route("index")}}"><img src="assets/images/logo/logo.png" alt="Site Logo" /><img
                                class="dark-logo" src="assets/images/logo/dark-logo.png" alt="Site Logo"
                                style="display: none;" /></a>
                    </div>
                </div>
                <div class="col">
                    <div class="header-search">
                        <form class="ec-btn-group-form" action="#">
                            <input class="form-control ec-search-bar" placeholder="Search products..."
                                type="text">
                            <button class="submit" type="submit"><i class="fi-rr-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="ec-main-menu-desk" class="d-none d-lg-block sticky-nav">
        <div class="container position-relative">
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
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="d-none">
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
    <div id="ec-mobile-menu" class="ec-side-cart ec-mobile-menu">
        <div class="ec-menu-title">
            <span class="menu_title">My Menu</span>
            <button class="ec-close">×</button>
        </div>
        <div class="ec-menu-inner">
            <div class="ec-menu-content">
                <ul>
                    <li>
                        <a href="{{ route('index') }}">Home</a>
                    </li>
                    <li>
                        <a href="">Shop</a>
                    </li>
                    <li>
                        <a href="/offers">Offers</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">My Account</a>
                        <ul class="sub-menu">
                            @guest
                                <li>
                                    <a href="{{ route('login') }}">Login</a>
                                </li>
                                <li>
                                    <a href="{{ route('register') }}">Register</a>
                                </li>
                            @endguest
                            @auth
                                <li>
                                    <a href="">Profile</a>
                                </li>
                                <li>
                                    <a href="">My Orders</a>
                                </li>
                                <li>
                                    <a href="">Wishlist</a>
                                </li>
                                @if (auth()->user()->role === 'Admin')
                                    <li>
                                        <a href="{{ route('home') }}">Admin Dashboard</a>
                                    </li>
                                @endif
                                <li>
                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="mobile-logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            @endauth
                        </ul>
                    </li>
                    <li>
                        <a href="/about-us">About Us</a>
                    </li>
                    <li>
                        <a href="/contact-us">Contact Us</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
