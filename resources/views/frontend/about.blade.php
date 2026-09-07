@extends('layouts.app')

@section('PageContent')
    <section class="pb-lg-20 pb-16">

        @include('frontend.partials.breadcrumb', [
            'title' => 'About Us',
            'item' => 'About Us',
        ])


        <section class="pb-14 py-lg-18">
            <div class="container container-xxl">

                <div class="row align-items-center">

                    {{-- Brand Image --}}
                    <div class="col-lg-6">
                        <div class="card border-0 hover-zoom-in">
                            <div class="image-box-4">

                                <img class="lazy-image img-fluid" src="{{ asset('assets/images/others/placeholder.jpg') }}"
                                    data-src="{{ asset('assets/images/background/about-delamoure.jpg') }}" width="960"
                                    height="640" alt="About Delamoure">

                            </div>
                        </div>
                    </div>


                    {{-- Brand Story --}}
                    <div class="col-lg-6 px-xxl-18 mt-12 mt-lg-0">

                        <p class="text-uppercase fw-semibold fs-15px mb-4">
                            Our Story
                        </p>

                        <h2 class="mb-8">
                            Beauty, Fragrance & Everyday Indulgence
                        </h2>

                        <p>
                            Delamoure was created around a simple idea: the things you use,
                            wear and experience every day should make you feel good.
                            We curate beauty, fragrance and personal-care essentials that
                            bring together quality, elegance and effortless everyday luxury.
                        </p>

                        <p class="mb-xl-14">
                            From signature scents to thoughtful beauty essentials, every
                            Delamoure selection is chosen with care. Our focus is on products
                            that feel refined, personal and easy to enjoy — whether you're
                            discovering something new, choosing a thoughtful gift, or
                            finding a favourite you'll return to again and again.
                        </p>


                        <div class="row">

                            {{-- Customer Care --}}
                            <div class="col-md-6">

                                <div class="d-flex align-items-start">
                                    <div>
                                        <h3 class="fs-5 mb-6">
                                            Customer Care
                                        </h3>

                                        <div class="fs-6">
                                            <p class="mb-6 fs-15px">
                                                Have a question about a product or your order?
                                                Our team is always happy to assist.
                                            </p>

                                            <a href="{{ url('/contact') }}"
                                                class="text-decoration-none fw-bold text-primary">
                                                Contact Us
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            {{-- Shopping Experience --}}
                            <div class="col-md-6 pt-9 pt-md-0">

                                <div class="d-flex align-items-start">
                                    <div>
                                        <h3 class="fs-5 mb-6">
                                            Made for You
                                        </h3>

                                        <div class="fs-6">
                                            <p class="mb-0 fs-15px">
                                                Thoughtfully selected products, beautiful
                                                experiences and dependable service —
                                                wherever you shop Delamoure.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </section>


        {{-- Brand Values --}}
        <section class="pb-15 pb-lg-18">

            <div class="container container-xxl">

                <div class="text-center pb-11 pb-lg-14">

                    <p class="text-uppercase fw-semibold fs-15px mb-4">
                        The Delamoure Standard
                    </p>

                    <h2 class="fs-3 w-lg-50 w-auto mx-auto pb-7">
                        Thoughtfully chosen for the moments that make you feel your best
                    </h2>

                    <p class="mw-lg-60 mx-auto mb-0">
                        We believe beauty is personal. That is why Delamoure focuses on
                        quality, thoughtful selection and an enjoyable shopping experience
                        designed around your everyday rituals.
                    </p>

                </div>


                <div class="row gy-30px">

                    {{-- Quality --}}
                    <div class="col-md-4">

                        <div>
                            <div class="d-flex justify-content-center">

                                <img class="lazy-image img-fluid light-mode-img" src="#"
                                    data-src="{{ asset('assets/images/image-box/image-box-11.png') }}" width="102"
                                    height="118" alt="Quality You Can Trust">

                                <img class="lazy-image dark-mode-img img-fluid" src="#"
                                    data-src="{{ asset('assets/images/image-box/image-box-white-11.png') }}" width="102"
                                    height="118" alt="Quality You Can Trust">

                            </div>

                            <div class="card-body text-center pt-7 mt-3">

                                <h3 class="fs-4 mb-6">
                                    Quality You Can Trust
                                </h3>

                                <p class="mb-0 px-lg-6">
                                    We carefully select products that meet our standards for
                                    quality, authenticity and an exceptional experience.
                                </p>

                            </div>
                        </div>

                    </div>


                    {{-- Curated Selection --}}
                    <div class="col-md-4">

                        <div>
                            <div class="d-flex justify-content-center">

                                <img class="lazy-image img-fluid light-mode-img" src="#"
                                    data-src="{{ asset('assets/images/image-box/image-box-02.png') }}" width="102"
                                    height="118" alt="Thoughtfully Curated">

                                <img class="lazy-image dark-mode-img img-fluid" src="#"
                                    data-src="{{ asset('assets/images/image-box/image-box-white-02.png') }}" width="102"
                                    height="118" alt="Thoughtfully Curated">

                            </div>

                            <div class="card-body text-center pt-7 mt-3">

                                <h3 class="fs-4 mb-6">
                                    Thoughtfully Curated
                                </h3>

                                <p class="mb-0 px-lg-6">
                                    Our collection brings together beauty and fragrance
                                    essentials chosen to complement different tastes,
                                    occasions and routines.
                                </p>

                            </div>
                        </div>

                    </div>


                    {{-- Customer Experience --}}
                    <div class="col-md-4">

                        <div>
                            <div class="d-flex justify-content-center">

                                <img class="lazy-image img-fluid light-mode-img" src="#"
                                    data-src="{{ asset('assets/images/image-box/image-box-03.png') }}" width="102"
                                    height="118" alt="Customer First">

                                <img class="lazy-image dark-mode-img img-fluid" src="#"
                                    data-src="{{ asset('assets/images/image-box/image-box-white-03.png') }}" width="102"
                                    height="118" alt="Customer First">

                            </div>

                            <div class="card-body text-center pt-7 mt-3">

                                <h3 class="fs-4 mb-6">
                                    Customer First
                                </h3>

                                <p class="mb-0 px-lg-6">
                                    From discovery to delivery, we aim to make every
                                    Delamoure interaction simple, reliable and enjoyable.
                                </p>

                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </section>
    @endsection
