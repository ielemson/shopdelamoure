@php
    $setting = \App\Models\WebsiteSetting::first();
@endphp

<footer class="pt-16 py-lg-13 pb-16 footer">

    {{-- Top Footer --}}
    <div class="border-bottom pb-7 pb-lg-8">
        <div class="container">

            <div class="row align-items-center">

                {{-- Left Links --}}
                <div class="col-lg-4 col-12 mb-lg-0 mb-11 px-0">

                    <ul class="list-inline fs-15px text-center text-lg-start mb-0">

                        <li class="list-inline-item me-9 pe-2">
                            <a href="{{ route('about') }}" class="text-body-emphasis">
                                About Us
                            </a>
                        </li>

                        <li class="list-inline-item me-9 pe-2">
                            <a href="" class="text-body-emphasis">
                                FAQs
                            </a>
                        </li>

                        <li class="list-inline-item">
                            <a href="" class="text-body-emphasis">
                                Shipping &amp; Return Policy
                            </a>
                        </li>

                    </ul>

                </div>


                {{-- Logo --}}
                <div class="col-lg-4 col-12 mb-lg-0 mb-11">

                    <div class="mx-auto text-center">

                        <a href="{{ url('/') }}" class="d-inline-block">

                            <img class="light-mode-img img-fluid"
                                src="{{ asset('assets/images/others/logo-white.png') }}" width="192" height="54"
                                alt="{{ $setting->website_name ?? 'Delamoure' }}">

                            <img class="dark-mode-img img-fluid"
                                src="{{ asset('assets/images/others/logo-white.png') }}" width="192" height="54"
                                alt="{{ $setting->website_name ?? 'Delamoure' }}">

                        </a>

                    </div>

                </div>


                {{-- Right Links --}}
                <div class="col-lg-4 col-12 px-0">

                    <ul class="list-inline fs-15px text-center text-lg-end mb-0">

                        <li class="list-inline-item me-9 pe-2">
                            <a href="" class="text-body-emphasis">
                                Privacy Policy
                            </a>
                        </li>

                        <li class="list-inline-item me-9 pe-2">
                            <a href="{{ route('contact') }}" class="text-body-emphasis">
                                Contact Us
                            </a>
                        </li>

                        <li class="list-inline-item">
                            <a href="{{ route('shop') }}" class="text-body-emphasis">
                                Shop
                            </a>
                        </li>

                    </ul>

                </div>

            </div>

        </div>
    </div>


    {{-- Bottom Footer --}}
    <div class="pt-9 pt-lg-8">

        <div class="container">

            <div class="row align-items-center">

                {{-- Copyright + Social Media --}}
                <div
                    class="col-12 col-md-6
                           d-flex flex-column flex-sm-row
                           align-items-center
                           justify-content-center justify-content-md-start
                           px-0">

                    <p class="mb-3 mb-sm-0">
                        © {{ date('Y') }}
                        {{ $setting->website_name ?? 'Delamoure' }}.
                        All Rights Reserved.
                    </p>


                    <ul class="list-inline fs-18px ms-sm-6 mb-0">

                        @if (!empty($setting->twitter))
                            <li class="list-inline-item me-8">

                                <a href="{{ $setting->twitter }}" target="_blank" rel="noopener" aria-label="Twitter">

                                    <i class="fab fa-twitter"></i>

                                </a>

                            </li>
                        @endif


                        @if (!empty($setting->facebook))
                            <li class="list-inline-item me-8">

                                <a href="{{ $setting->facebook }}" target="_blank" rel="noopener"
                                    aria-label="Facebook">

                                    <i class="fab fa-facebook-f"></i>

                                </a>

                            </li>
                        @endif


                        @if (!empty($setting->instagram))
                            <li class="list-inline-item me-8">

                                <a href="{{ $setting->instagram }}" target="_blank" rel="noopener"
                                    aria-label="Instagram">

                                    <i class="fab fa-instagram"></i>

                                </a>

                            </li>
                        @endif


                        @if (!empty($setting->youtube))
                            <li class="list-inline-item me-8">

                                <a href="{{ $setting->youtube }}" target="_blank" rel="noopener" aria-label="YouTube">

                                    <i class="fab fa-youtube"></i>

                                </a>

                            </li>
                        @endif


                        @if (!empty($setting->tiktok))
                            <li class="list-inline-item">

                                <a href="{{ $setting->tiktok }}" target="_blank" rel="noopener" aria-label="TikTok">

                                    <i class="fab fa-tiktok"></i>

                                </a>

                            </li>
                        @endif

                    </ul>

                </div>


                {{-- Payment Methods --}}
                <div
                    class="col-12 col-md-6
                           text-center text-md-end
                           mt-8 mt-md-0
                           px-0">

                    <img src="{{ asset('assets/images/footer/payment-methods.png') }}" width="313" height="28"
                        alt="Secure payment methods" class="img-fluid">

                </div>

            </div>

        </div>

    </div>

</footer>
