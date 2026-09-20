@extends('layouts.app')

@section('meta_title', 'About Dela Moure | Luxury Fragrances & Home Scents')

@section('meta_description',
    'Discover the story behind Dela Moure and our passion for luxury perfumes, diffusers,
    essential oils, candles, home fragrances and beautifully curated gift sets.')

@section('meta_image', asset('assets/images/others/social-share.jpg'))

@section('PageContent')
    <section class="pb-lg-18 pb-14">

        @include('frontend.partials.breadcrumb', [
            'title' => 'About Us',
            'item' => 'About Us',
        ])

        {{-- ABOUT INTRODUCTION --}}
        <section class="py-lg-16 py-12">
            <div class="container">
                <div class="row align-items-center g-lg-12 g-8">

                    <div class="col-lg-6">
                        <div class="about-image-wrapper">
                            <img src="{{ asset('assets/images/banner/about-delamoure.jpg') }}"
                                class="img-fluid w-100 about-main-image"
                                alt="Delamoure fashion, fragrance and lifestyle collection">

                            <div class="about-image-accent d-none d-md-block"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="ps-lg-6">

                            <span class="about-eyebrow">OUR STORY</span>

                            <h2 class="about-title mt-3 mb-5">
                                Thoughtfully Curated. Distinctively You.
                            </h2>

                            <p class="about-text mb-4">
                                <strong>Delamoure</strong> is the retail and lifestyle
                                expression of <strong>DE LAMOURE BRAND LTD</strong>,
                                a Nigerian fashion and lifestyle company committed to
                                quality, individuality and refined living.
                            </p>

                            <p class="about-text mb-4">
                                Our growing collection brings together fashion,
                                accessories, fragrances, home scents and thoughtful
                                gifts selected to complement personal style and make
                                everyday moments feel special.
                            </p>

                            <p class="about-text mb-0">
                                We are building a trusted shopping destination where
                                elegant products, attentive service and a seamless
                                customer experience come together.
                            </p>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- BRAND VALUES --}}
        <section class="about-values py-lg-15 py-12">
            <div class="container">

                <div class="text-center mx-auto mb-lg-10 mb-8" style="max-width: 680px;">

                    <span class="about-eyebrow">
                        THE DELAMOURE EXPERIENCE
                    </span>

                    <h2 class="about-title mt-3 mb-4">
                        Style Made Personal
                    </h2>

                    <p class="about-text mb-0">
                        Everything we offer is chosen to inspire confidence,
                        express individuality and enrich everyday living.
                    </p>

                </div>

                <div class="row g-5">

                    <div class="col-lg-4 col-md-6">
                        <div class="about-value-card h-100 text-center">

                            <div class="about-icon mx-auto mb-4">
                                <i class="fa-solid fa-gem"></i>
                            </div>

                            <h4 class="about-card-title">
                                Thoughtful Quality
                            </h4>

                            <p class="about-card-text mb-0">
                                Products selected with attention to quality,
                                function and presentation.
                            </p>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="about-value-card h-100 text-center">

                            <div class="about-icon mx-auto mb-4">
                                <i class="fa-solid fa-sparkles"></i>
                            </div>

                            <h4 class="about-card-title">
                                Refined Style
                            </h4>

                            <p class="about-card-text mb-0">
                                Fashion and lifestyle pieces that feel contemporary,
                                elegant and distinctive.
                            </p>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mx-md-auto">
                        <div class="about-value-card h-100 text-center">

                            <div class="about-icon mx-auto mb-4">
                                <i class="fa-solid fa-heart"></i>
                            </div>

                            <h4 class="about-card-title">
                                Genuine Care
                            </h4>

                            <p class="about-card-text mb-0">
                                Customer service built on integrity, responsiveness
                                and lasting relationships.
                            </p>

                        </div>
                    </div>

                </div>

            </div>
        </section>

        {{-- MISSION AND VISION --}}
        <section class="about-mission-section py-lg-15 py-12">
            <div class="container">

                <div class="row g-5">

                    <div class="col-lg-6">
                        <div class="mission-card h-100">

                            <span class="mission-label">OUR MISSION</span>

                            <h3 class="mt-3 mb-4">
                                Making Refined Living More Accessible
                            </h3>

                            <p class="mb-0">
                                To provide thoughtfully curated fashion, fragrance,
                                accessories and lifestyle products that help customers
                                express themselves with confidence.
                            </p>

                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mission-card h-100">

                            <span class="mission-label">OUR VISION</span>

                            <h3 class="mt-3 mb-4">
                                A Trusted Nigerian Lifestyle Brand
                            </h3>

                            <p class="mb-0">
                                To build Delamoure into a distinctive brand recognised
                                for quality, creativity, authenticity and exceptional
                                customer experiences.
                            </p>

                        </div>
                    </div>

                </div>

            </div>
        </section>

        {{-- FINAL CTA --}}
        <section class="pt-lg-15 pt-12">
            <div class="container">

                <div class="about-cta text-center">

                    <span class="about-eyebrow">
                        DISCOVER DELAMOURE
                    </span>

                    <h2 class="about-title mt-3 mb-4">
                        Find Something That Feels Like You
                    </h2>

                    <p class="about-text mx-auto mb-6" style="max-width: 620px;">
                        Explore our growing collection of accessories,
                        fragrances, home scents and thoughtful gifts.
                    </p>

                    <a href="{{ url('/shop') }}" class="btn btn-dark btn-lg px-8">
                        Shop Delamoure
                    </a>

                </div>

            </div>
        </section>

    </section>
@endsection

@push('styles')
    <style>
        /*
                |--------------------------------------------------------------------------
                | Delamoure About Page
                |--------------------------------------------------------------------------
                */

        .about-eyebrow {
            display: inline-block;
            color: #a78552;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .about-title {
            color: #392c26;
            font-size: 44px;
            font-weight: 500;
            line-height: 1.15;
            letter-spacing: -1px;
        }

        .about-text {
            color: #706761;
            font-size: 17px;
            line-height: 1.85;
        }

        /*
                |--------------------------------------------------------------------------
                | Introduction Image
                |--------------------------------------------------------------------------
                */

        .about-image-wrapper {
            position: relative;
            padding: 0 24px 24px 0;
        }

        .about-main-image {
            position: relative;
            z-index: 2;
            width: 100%;
            min-height: 480px;
            object-fit: cover;
        }

        .about-image-accent {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 70%;
            height: 70%;
            background: #e8ded0;
            z-index: 1;
        }

        /*
                |--------------------------------------------------------------------------
                | Brand Values
                |--------------------------------------------------------------------------
                */

        .about-values {
            background: #fbf9f6;
        }

        .about-value-card {
            background: #ffffff;
            border: 1px solid #eee8e1;
            padding: 40px 30px;
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .about-value-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(57, 44, 38, .08);
        }

        .about-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: #f5eee5;
            color: #a78552;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
        }

        .about-card-title {
            color: #392c26;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .about-card-text {
            color: #756c66;
            font-size: 15px;
            line-height: 1.75;
        }

        /*
                |--------------------------------------------------------------------------
                | Mission and Vision
                |--------------------------------------------------------------------------
                */

        .about-mission-section {
            background: #3b2d27;
        }

        .mission-card {
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .12);
            padding: 44px 40px;
        }

        .mission-label {
            color: #d4b17a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 3px;
        }

        .mission-card h3 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 500;
            line-height: 1.35;
        }

        .mission-card p {
            color: rgba(255, 255, 255, .72);
            font-size: 16px;
            line-height: 1.8;
        }

        /*
                |--------------------------------------------------------------------------
                | CTA
                |--------------------------------------------------------------------------
                */

        .about-cta {
            background: #faf7f2;
            padding: 65px 30px;
        }

        /*
                |--------------------------------------------------------------------------
                | Responsive
                |--------------------------------------------------------------------------
                */

        @media (max-width: 991.98px) {
            .about-title {
                font-size: 37px;
            }

            .about-main-image {
                min-height: 400px;
            }

            .about-image-wrapper {
                padding: 0 18px 18px 0;
            }
        }

        @media (max-width: 767.98px) {
            .about-title {
                font-size: 30px;
                line-height: 1.25;
                letter-spacing: -.5px;
            }

            .about-text {
                font-size: 16px;
                line-height: 1.75;
            }

            .about-main-image {
                min-height: 320px;
            }

            .about-image-wrapper {
                padding: 0;
            }

            .about-value-card {
                padding: 34px 24px;
            }

            .mission-card {
                padding: 34px 26px;
            }

            .mission-card h3 {
                font-size: 24px;
            }

            .about-cta {
                padding: 50px 22px;
            }
        }
    </style>
@endpush
