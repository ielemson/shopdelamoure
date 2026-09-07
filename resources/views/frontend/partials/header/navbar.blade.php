<header class="ec-header">

    <div class="ec-header-bottom d-none d-lg-block">
        <div class="container">

            <div class="ec-flex">

                {{-- Logo --}}
                <div class="ec-header-logo">
                    <a href="">
                        <img
                            src="{{ asset('assets/images/logo/logo.png') }}"
                            alt="{{ config('app.name') }}">
                    </a>
                </div>

                {{-- Search --}}
                @include('partials.header.search')

                {{-- Actions --}}
                <div class="ec-header-actions">

                    <a href="" class="ec-header-btn">
                        <i class="fi-rr-user"></i>
                        <span>Account</span>
                    </a>

                    <a href="" class="ec-header-btn">
                        <i class="fi-rr-heart"></i>
                        <span>Wishlist</span>
                    </a>

                    <a href="#ec-side-cart" class="ec-header-btn ec-side-toggle">
                        <i class="fi-rr-shopping-basket"></i>

                        <span class="ec-cart-count">
                           0
                        </span>
                    </a>

                </div>

            </div>

        </div>
    </div>

</header>