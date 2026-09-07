@extends('layouts.app')

@section('meta_title', 'About Dela Moure | Luxury Fragrances & Home Scents')

@section('meta_description', 'Discover the story behind Dela Moure and our passion for luxury perfumes, diffusers,
    essential oils, candles, home fragrances and beautifully curated gift sets.')

@section('meta_image', asset('assets/images/others/social-share.jpg'))

@section('PageContent')
    <section class="pb-lg-20 pb-16">

        @include('frontend.partials.breadcrumb', [
            'title' => 'About Us',
            'item' => 'About Us',
        ])


        {{-- ============================================================
            ABOUT INTRODUCTION
        ============================================================ --}}
        <section class="py-lg-18 py-14">
            <div class="container">
                <div class="row align-items-center g-lg-12 g-8">

                    <div class="col-lg-6">
                        <div class="about-image-wrapper position-relative">

                            <img src="{{ asset('assets/images/others/about-delamoure.jpg') }}"
                                class="img-fluid w-100 about-main-image" alt="Dela Moure Luxury Fragrances and Home Scents">

                            <div class="about-image-accent d-none d-md-block"></div>

                        </div>
                    </div>


                    <div class="col-lg-6">

                        <div class="ps-lg-6">

                            <span class="about-eyebrow">
                                OUR STORY
                            </span>

                            <h2 class="about-title mt-3 mb-5">
                                Fragrance Designed to Make Every Moment Memorable
                            </h2>

                            <p class="about-text mb-4">
                                At <strong>Dela Moure</strong>, we believe fragrance is more than a scent.
                                It is an expression of personality, atmosphere, emotion and unforgettable
                                experiences.
                            </p>

                            <p class="about-text mb-4">
                                Our collection brings together carefully selected perfumes, diffusers,
                                essential oils, candles, room fragrances and beautifully curated gift sets
                                created to elevate everyday living.
                            </p>

                            <p class="about-text mb-0">
                                From personal fragrance to home scenting, every Dela Moure experience is
                                designed around elegance, quality and the simple pleasure of surrounding
                                yourself with beautiful scents.
                            </p>

                        </div>

                    </div>

                </div>
            </div>
        </section>



        {{-- ============================================================
            BRAND PHILOSOPHY
        ============================================================ --}}
        <section class="about-philosophy py-lg-18 py-14">
            <div class="container">

                <div class="text-center mx-auto mb-lg-12 mb-9" style="max-width: 720px;">

                    <span class="about-eyebrow">
                        THE DELA MOURE EXPERIENCE
                    </span>

                    <h2 class="about-title mt-3 mb-4">
                        Luxury Made Personal
                    </h2>

                    <p class="about-text mb-0">
                        We create scent experiences that add character to the spaces you live in,
                        the moments you celebrate and the memories you carry with you.
                    </p>

                </div>


                <div class="row g-5">

                    {{-- Quality --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="about-value-card h-100 text-center">

                            <div class="about-icon mx-auto mb-5">
                                <i class="fa-solid fa-gem"></i>
                            </div>

                            <h4 class="about-card-title">
                                Premium Quality
                            </h4>

                            <p class="about-card-text mb-0">
                                We carefully curate fragrance products with attention to quality,
                                presentation and lasting scent experiences.
                            </p>

                        </div>
                    </div>


                    {{-- Elegance --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="about-value-card h-100 text-center">

                            <div class="about-icon mx-auto mb-5">
                                <i class="fa-solid fa-sparkles"></i>
                            </div>

                            <h4 class="about-card-title">
                                Timeless Elegance
                            </h4>

                            <p class="about-card-text mb-0">
                                From the fragrance to the packaging, every detail reflects a refined,
                                contemporary and luxurious aesthetic.
                            </p>

                        </div>
                    </div>


                    {{-- Experience --}}
                    <div class="col-lg-4 col-md-6 mx-md-auto">
                        <div class="about-value-card h-100 text-center">

                            <div class="about-icon mx-auto mb-5">
                                <i class="fa-solid fa-heart"></i>
                            </div>

                            <h4 class="about-card-title">
                                Memorable Experiences
                            </h4>

                            <p class="about-card-text mb-0">
                                Our scents are selected to help create beautiful moods, meaningful
                                moments and lasting impressions.
                            </p>

                        </div>
                    </div>

                </div>

            </div>
        </section>



        {{-- ============================================================
            PRODUCT CATEGORIES
        ============================================================ --}}
        <section class="py-lg-18 py-14">
            <div class="container">

                <div class="row align-items-center g-lg-12 g-9">

                    <div class="col-lg-5">

                        <span class="about-eyebrow">
                            WHAT WE OFFER
                        </span>

                        <h2 class="about-title mt-3 mb-5">
                            A Complete World of Fragrance
                        </h2>

                        <p class="about-text mb-5">
                            Whether you are choosing your signature scent, creating a welcoming
                            atmosphere at home or searching for the perfect gift, Dela Moure offers
                            fragrance experiences for every occasion.
                        </p>


                        <a href="{{ url('/shop') }}" class="btn btn-dark btn-lg px-7">
                            Explore Our Collection
                        </a>

                    </div>


                    <div class="col-lg-7">

                        <div class="row g-4">

                            <div class="col-md-6">
                                <div class="about-category-card">
                                    <span class="category-number">01</span>
                                    <h5>Perfumes</h5>
                                    <p>
                                        Distinctive fragrances created for confidence,
                                        individuality and lasting impressions.
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="about-category-card">
                                    <span class="category-number">02</span>
                                    <h5>Diffusers</h5>
                                    <p>
                                        Elegant scenting solutions designed to transform
                                        your living and working spaces.
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="about-category-card">
                                    <span class="category-number">03</span>
                                    <h5>Essential Oils</h5>
                                    <p>
                                        Carefully selected aromatic oils for refreshing
                                        and enriching everyday environments.
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="about-category-card">
                                    <span class="category-number">04</span>
                                    <h5>Candles & Gift Sets</h5>
                                    <p>
                                        Beautifully presented fragrance pieces made for
                                        gifting, celebrations and personal indulgence.
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </section>



        {{-- ============================================================
            MISSION / VISION
        ============================================================ --}}
        <section class="about-mission-section py-lg-18 py-14">
            <div class="container">

                <div class="row g-5">

                    <div class="col-lg-6">
                        <div class="mission-card h-100">

                            <span class="mission-label">
                                OUR MISSION
                            </span>

                            <h3 class="mt-3 mb-4">
                                Bringing Beautiful Fragrance Into Everyday Life
                            </h3>

                            <p class="mb-0">
                                Our mission is to make premium fragrance experiences accessible
                                through thoughtfully selected products that enhance personal style,
                                homes, celebrations and meaningful moments.
                            </p>

                        </div>
                    </div>


                    <div class="col-lg-6">
                        <div class="mission-card h-100">

                            <span class="mission-label">
                                OUR VISION
                            </span>

                            <h3 class="mt-3 mb-4">
                                Becoming a Trusted Name in Modern Luxury Scenting
                            </h3>

                            <p class="mb-0">
                                We aspire to build Dela Moure into a distinctive fragrance
                                destination known for quality, elegance, authenticity and exceptional
                                customer experiences.
                            </p>

                        </div>
                    </div>

                </div>

            </div>
        </section>



        {{-- ============================================================
            FINAL CTA
        ============================================================ --}}
        <section class="pt-lg-18 pt-14">
            <div class="container">

                <div class="about-cta text-center">

                    <span class="about-eyebrow">
                        DISCOVER YOUR SCENT
                    </span>

                    <h2 class="about-title mt-3 mb-4">
                        Find a Fragrance That Feels Like You
                    </h2>

                    <p class="about-text mx-auto mb-7" style="max-width: 650px;">
                        Discover perfumes, diffusers, essential oils, candles,
                        room scents and gift sets curated for unforgettable experiences.
                    </p>

                    <a href="{{ url('/shop') }}" class="btn btn-dark btn-lg px-8">
                        Shop Dela Moure
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
                | Dela Moure About Page
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
            font-size: 46px;
            font-weight: 500;
            line-height: 1.15;
            letter-spacing: -1px;
        }


        .about-text {
            color: #706761;
            font-size: 17px;
            line-height: 1.9;
        }


        /*
                |--------------------------------------------------------------------------
                | Main Image
                |--------------------------------------------------------------------------
                */

        .about-image-wrapper {
            position: relative;
            padding: 0 25px 25px 0;
        }


        .about-main-image {
            position: relative;
            z-index: 2;
            min-height: 520px;
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
                | Philosophy Section
                |--------------------------------------------------------------------------
                */

        .about-philosophy {
            background: #fbf9f6;
        }


        .about-value-card {
            background: #ffffff;
            border: 1px solid #eee8e1;
            padding: 48px 34px;
            transition: all .3s ease;
        }


        .about-value-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 45px rgba(57, 44, 38, .08);
        }


        .about-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #f5eee5;
            color: #a78552;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
        }


        .about-card-title {
            color: #392c26;
            font-weight: 600;
            margin-bottom: 15px;
        }


        .about-card-text {
            color: #756c66;
            font-size: 15px;
            line-height: 1.8;
        }


        /*
                |--------------------------------------------------------------------------
                | Categories
                |--------------------------------------------------------------------------
                */

        .about-category-card {
            position: relative;
            height: 100%;
            padding: 36px 32px;
            border: 1px solid #ece5dc;
            background: #ffffff;
            transition: all .3s ease;
        }


        .about-category-card:hover {
            border-color: #c7aa7d;
            transform: translateY(-4px);
        }


        .category-number {
            display: block;
            margin-bottom: 25px;
            color: #b4915c;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
        }


        .about-category-card h5 {
            color: #392c26;
            font-size: 21px;
            font-weight: 600;
            margin-bottom: 12px;
        }


        .about-category-card p {
            color: #766e68;
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 0;
        }


        /*
                |--------------------------------------------------------------------------
                | Mission / Vision
                |--------------------------------------------------------------------------
                */

        .about-mission-section {
            background: #3b2d27;
        }


        .mission-card {
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .12);
            padding: 52px 45px;
        }


        .mission-label {
            color: #d4b17a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 3px;
        }


        .mission-card h3 {
            color: #ffffff;
            font-size: 30px;
            line-height: 1.35;
            font-weight: 500;
        }


        .mission-card p {
            color: rgba(255, 255, 255, .72);
            font-size: 16px;
            line-height: 1.9;
        }


        /*
                |--------------------------------------------------------------------------
                | CTA
                |--------------------------------------------------------------------------
                */

        .about-cta {
            background: #faf7f2;
            padding: 80px 30px;
        }


        /*
                |--------------------------------------------------------------------------
                | Responsive
                |--------------------------------------------------------------------------
                */

        @media (max-width: 991.98px) {

            .about-title {
                font-size: 38px;
            }

            .about-main-image {
                min-height: 420px;
            }

            .about-image-wrapper {
                padding-right: 18px;
                padding-bottom: 18px;
            }

        }


        @media (max-width: 767.98px) {

            .about-title {
                font-size: 31px;
                line-height: 1.25;
                letter-spacing: -.5px;
            }

            .about-text {
                font-size: 16px;
                line-height: 1.8;
            }

            .about-main-image {
                min-height: 340px;
            }

            .about-image-wrapper {
                padding: 0;
            }

            .about-value-card {
                padding: 38px 25px;
            }

            .mission-card {
                padding: 38px 28px;
            }

            .mission-card h3 {
                font-size: 25px;
            }

            .about-cta {
                padding: 55px 22px;
            }

        }
    </style>
@endpush
