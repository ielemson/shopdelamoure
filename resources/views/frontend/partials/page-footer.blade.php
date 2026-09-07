<footer class="ec-footer section-space-mt">

    <div class="footer-container">
<div class="footer-offer">
    <div class="container">
        <div class="row">
            <div class="text-center footer-off-msg">
                <span>
                    Quality Products • Trusted Service • Reliable Nationwide Delivery
                </span>
                <a href="{{ route('shop') }}">
                    Shop Now
                </a>
            </div>
        </div>
    </div>
</div>
        <div class="footer-top section-space-footer-p">
            <div class="container">
                <div class="row">

                    <!-- Company Info -->
                    <div class="col-sm-12 col-lg-3 ec-footer-contact">
                        <div class="ec-footer-widget">

                            <div class="ec-footer-logo mb-3">
                                <a href="{{ route('index') }}">
                                    @if($setting?->logo)
                                        <img src="{{ asset('storage/'.$setting->logo) }}"
                                            alt="{{ $setting->website_name }}">
                                    @endif
                                </a>
                            </div>

                            <h4 class="ec-footer-heading">Contact Us</h4>

                            <div class="ec-footer-links">
                                <ul class="align-items-center">

                                    @if($setting?->address)
                                    <li class="ec-footer-link">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ $setting->address }}
                                    </li>
                                    @endif

                                    @if($setting?->phone)
                                    <li class="ec-footer-link">
                                        <i class="fas fa-phone-alt me-2"></i>
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $setting->phone) }}">
                                            {{ $setting->phone }}
                                        </a>
                                    </li>
                                    @endif

                                    @if($setting?->email)
                                    <li class="ec-footer-link">
                                        <i class="fas fa-envelope me-2"></i>
                                        <a href="mailto:{{ $setting->email }}">
                                            {{ $setting->email }}
                                        </a>
                                    </li>
                                    @endif

                                </ul>
                            </div>

                        </div>
                    </div>

                    <!-- Information -->
                    <div class="col-sm-12 col-lg-3 ec-footer-info">
                        <div class="ec-footer-widget">

                            <h4 class="ec-footer-heading">Information</h4>

                            <div class="ec-footer-links">
                                <ul class="align-items-center">

                                    <li class="ec-footer-link">
                                        <a href="{{ route('about') }}">About Us</a>
                                    </li>

                                    <li class="ec-footer-link">
                                        <a href="{{ route('contact') }}">Contact Us</a>
                                    </li>

                                    <li class="ec-footer-link">
                                        <a href="{{ route('shop') }}">Shop</a>
                                    </li>

                                    <li class="ec-footer-link">
                                        <a href="{{ route('cart.index') }}">Shopping Cart</a>
                                    </li>

                                </ul>
                            </div>

                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="col-sm-12 col-lg-3 ec-footer-service">
                        <div class="ec-footer-widget">

                            <h4 class="ec-footer-heading">Categories</h4>

                            <div class="ec-footer-links">
                                <ul class="align-items-center">

                                    @forelse($footerCategories as $category)
                                        <li class="ec-footer-link">
                                            <a href="{{ route('category.products', $category->slug) }}">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @empty
                                        <li class="ec-footer-link">
                                            <a href="{{ route('shop') }}">
                                                Browse Products
                                            </a>
                                        </li>
                                    @endforelse

                                </ul>
                            </div>

                        </div>
                    </div>

                    <!-- Account -->
                    <div class="col-sm-12 col-lg-2 ec-footer-account">
                        <div class="ec-footer-widget">

                            <h4 class="ec-footer-heading">Account</h4>

                            <div class="ec-footer-links">
                                <ul class="align-items-center">

                                    @guest

                                        <li class="ec-footer-link">
                                            <a href="{{ route('login') }}">
                                                Login
                                            </a>
                                        </li>

                                        <li class="ec-footer-link">
                                            <a href="{{ route('register') }}">
                                                Register
                                            </a>
                                        </li>

                                    @else

                                        <li class="ec-footer-link">
                                            <a href="{{ route('customer.dashboard') }}">
                                                Dashboard
                                            </a>
                                        </li>

                                    @endguest

                                    <li class="ec-footer-link">
                                        <a href="{{ route('checkout.index') }}">
                                            Checkout
                                        </a>
                                    </li>

                                    <li class="ec-footer-link">
                                        <a href="{{ route('cart.index') }}">
                                            Cart
                                        </a>
                                    </li>

                                </ul>
                            </div>

                        </div>
                    </div>

                 

                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">

            <div class="container">

                <div class="row align-items-center">

                    <!-- Social -->
                    <div class="col-lg-4 col-md-12 footer-bottom-left">

                        <div class="footer-bottom-social">

                            <span class="social-text">
                                Follow Us:
                            </span>

                            <ul class="mb-0">

                                @if($setting?->facebook)
                                <li class="list-inline-item">
                                    <a href="{{ $setting->facebook }}" target="_blank">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </li>
                                @endif

                                @if($setting?->instagram)
                                <li class="list-inline-item">
                                    <a href="{{ $setting->instagram }}" target="_blank">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                </li>
                                @endif

                                @if($setting?->twitter)
                                <li class="list-inline-item">
                                    <a href="{{ $setting->twitter }}" target="_blank">
                                        <i class="fab fa-x-twitter"></i>
                                    </a>
                                </li>
                                @endif

                                @if($setting?->linkedin)
                                <li class="list-inline-item">
                                    <a href="{{ $setting->linkedin }}" target="_blank">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                </li>
                                @endif

                                @if($setting?->youtube)
                                <li class="list-inline-item">
                                    <a href="{{ $setting->youtube }}" target="_blank">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                </li>
                                @endif

                            </ul>

                        </div>

                    </div>

                    <!-- Copyright -->
                    <div class="col-lg-4 col-md-12 footer-copy text-center">

                        <div class="footer-bottom-copy">

                            <div class="ec-copy">

                                © {{ date('Y') }}

                                <a class="site-name"
                                    href="{{ route('index') }}">

                                    {{ $setting?->website_name ?? 'Springcrest Trading' }}

                                </a>

                                . All Rights Reserved.

                            </div>

                        </div>

                    </div>

                    <!-- Payment -->
                    <div class="col-lg-4 col-md-12 footer-bottom-right">

                        <div class="footer-bottom-payment d-flex justify-content-lg-end justify-content-center">

                            <div class="payment-link">
                                <img src="{{ asset('assets/images/icons/payment.png') }}"
                                    alt="Payment Methods">
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</footer>