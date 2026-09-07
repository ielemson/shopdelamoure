<div class="navbar">

    <div id="offCanvasNavBar" class="offcanvas offcanvas-start border-0" tabindex="-1"
        aria-labelledby="offCanvasNavBarLabel" style="--bs-offcanvas-width: 320px;">

        {{-- Header --}}
        <div class="offcanvas-header px-6 py-5 border-bottom">

            <a href="{{ url('/') }}" class="text-decoration-none text-body-emphasis" id="offCanvasNavBarLabel">

                <span class="fs-4 fw-semibold text-uppercase ls-2">
                    Delamoure
                </span>

            </a>

            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close">
            </button>

        </div>


        {{-- Navigation --}}
        <div class="offcanvas-body px-6 py-5 d-flex flex-column">

            <nav class="flex-grow-1">

                <ul class="navbar-nav mobile-main-menu">

                    {{-- Home --}}
                    <li class="nav-item">
                        <a href="{{ url('/') }}"
                            class="nav-link mobile-menu-link
                            {{ request()->is('/') ? 'active' : '' }}">

                            <span>Home</span>

                        </a>
                    </li>


                    {{-- Shop --}}
                    <li class="nav-item">
                        <a href="{{ url('/shop') }}"
                            class="nav-link mobile-menu-link
                            {{ request()->is('shop*') ? 'active' : '' }}">

                            <span>Shop</span>

                        </a>
                    </li>


                    {{-- About --}}
                    <li class="nav-item">
                        <a href="{{ url('/about') }}"
                            class="nav-link mobile-menu-link
                            {{ request()->is('about') ? 'active' : '' }}">

                            <span>About Us</span>

                        </a>
                    </li>


                    {{-- Contact --}}
                    <li class="nav-item">
                        <a href="{{ url('/contact') }}"
                            class="nav-link mobile-menu-link
                            {{ request()->is('contact') ? 'active' : '' }}">

                            <span>Contact Us</span>

                        </a>
                    </li>

                </ul>

            </nav>


            {{-- Account Area --}}
            <div class="mobile-account-section pt-5 mt-5 border-top">

                <p class="text-uppercase text-muted fw-semibold fs-13px ls-1 mb-4">
                    My Account
                </p>

                @guest

                    <a href="{{ route('login') }}"
                        class="d-flex align-items-center text-decoration-none text-body-emphasis py-2 mb-2">

                        <svg class="icon me-3" style="width:20px;height:20px;">
                            <use xlink:href="#icon-user-light"></use>
                        </svg>

                        <span class="fw-medium">Login</span>

                    </a>

                    <a href="{{ route('register') }}"
                        class="d-flex align-items-center text-decoration-none text-body-emphasis py-2">

                        <svg class="icon me-3" style="width:20px;height:20px;">
                            <use xlink:href="#icon-user-light"></use>
                        </svg>

                        <span class="fw-medium">Create Account</span>

                    </a>
                @else
                    <a href="{{ route('home') }}"
                        class="d-flex align-items-center text-decoration-none text-body-emphasis py-2 mb-2">

                        <svg class="icon me-3" style="width:20px;height:20px;">
                            <use xlink:href="#icon-user-light"></use>
                        </svg>

                        <span class="fw-medium">My Account</span>

                    </a>


                    <form method="POST" action="{{ route('logout') }}" class="m-0">

                        @csrf

                        <button type="submit"
                            class="btn p-0 border-0 shadow-none d-flex align-items-center text-body-emphasis py-2">

                            <i class="fa-solid fa-arrow-right-from-bracket me-3" style="width:20px;"></i>

                            <span class="fw-medium">
                                Logout
                            </span>

                        </button>

                    </form>

                @endguest

            </div>


            {{-- Footer --}}
            <div class="mobile-menu-footer pt-5 mt-5">

                <p class="small text-muted mb-1">
                    © {{ date('Y') }} Delamoure
                </p>

                <p class="small text-muted mb-0">
                    Beauty, fragrance & lifestyle.
                </p>

            </div>

        </div>

    </div>

</div>
