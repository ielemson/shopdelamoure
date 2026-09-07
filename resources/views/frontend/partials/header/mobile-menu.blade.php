<div id="ec-mobile-menu" class="ec-side-cart ec-mobile-menu">

    <div class="ec-menu-title">
        <span class="menu_title">My Menu</span>

        <button class="ec-close">
            <i class="fas fa-times"></i>
        </button>
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
                                <a href="{{ route("customer.orders.index") }}">Profile</a>
                            </li>

                            <li>
                                <a href="">My Orders</a>
                            </li>

                          
                           

                            @if(auth()->user()->role === 'Admin')
                                <li>
                                    <a href="{{ route('home') }}">Admin Dashboard</a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">
                                    Logout
                                </a>

                                <form id="mobile-logout-form"
                                      action="{{ route('logout') }}"
                                      method="POST"
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
{{-- 
    <div class="header-res-social">
        <div class="header-top-social">
            <ul class="mb-0">
                <li class="list-inline-item">
                    <a class="hdr-facebook" href="#"><i class="fab fa-facebook-f"></i></a>
                </li>

                <li class="list-inline-item">
                    <a class="hdr-twitter" href="#"><i class="fab fa-twitter"></i></a>
                </li>

                <li class="list-inline-item">
                    <a class="hdr-instagram" href="#"><i class="fab fa-instagram"></i></a>
                </li>

                <li class="list-inline-item">
                    <a class="hdr-linkedin" href="#"><i class="fab fa-linkedin-in"></i></a>
                </li>
            </ul>
        </div>
    </div> --}}

</div>

<div class="ec-header-bottom d-lg-none">

    <div class="container position-relative">

        <div class="row">

            <div class="ec-flex">

                <div class="col ec-header-logo">
                    <div class="header-logo">
                        <a href="{{ route('index') }}">
                            <img src="{{ asset('assets/images/logo/logo-4.png') }}" alt="Site Logo">

                            <img class="dark-logo"
                                 src="{{ asset('assets/images/logo/dark-logo-4.png') }}"
                                 alt="Site Logo"
                                 style="display: none;">
                        </a>
                    </div>
                </div>

                <div class="col ec-category-block">

                    <div class="ec-category-menu">

                        <div class="ec-category-toggle">
                            <i class="fas fa-bars"></i>
                            <span class="ec-category-title d-479">Category</span>
                        </div>

                        <div class="ec-category-content">

                            <div class="ec-category-dropdown">

                                <ul class="ec-category-wrapper">

                                    @forelse($categories as $category)

                                        <li>
                                            <a class="ec-cat-menu-link"
                                               href="{{ url('category/' . $category->slug) }}">
                                                {{ $category->name }}
                                            </a>
                                        </li>

                                    @empty

                                        <li>
                                            <a class="ec-cat-menu-link" href="#">
                                                No Categories Found
                                            </a>
                                        </li>

                                    @endforelse

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>