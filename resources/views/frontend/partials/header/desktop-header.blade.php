<div class="ec-header-bottom d-none d-lg-block">
    <div class="container position-relative">
        <div class="row">
            <div class="ec-flex">

                <div class="align-self-center ec-header-logo">
                    <div class="header-logo">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('assets/images/logo/logo-4.png') }}" alt="Site Logo">
                            <img class="dark-logo" src="{{ asset('assets/images/logo/dark-logo-4.png') }}" alt="Site Logo" style="display: none;">
                        </a>
                    </div>
                </div>

                <div class="align-self-center ec-header-search">
                    @include('partials.header.search')
                </div>

                <div class="align-self-center">
                    <div class="ec-header-bottons">

                        <a href="{{ url('/login') }}" class="ec-header-btn ec-header-user">
                            <div class="header-icon"><i class="fi-rr-user"></i></div>
                            <div class="ec-btn-desc">
                                <span class="ec-btn-title">Account</span>
                                <span class="ec-btn-stitle">Login</span>
                            </div>
                        </a>

                        <a href="{{ url('/wishlist') }}" class="ec-header-btn ec-header-wishlist">
                            <div class="header-icon"><i class="fi-rr-heart"></i></div>
                            <