@extends('layouts.app')

@section('meta_title', 'Page Not Found | De Lamoure')

@section('meta_description', 'The page you are looking for could not be found. Return to De Lamoure to explore our
    signature fragrances and home scents.')

@section('meta_image', asset('assets/images/others/social-share.jpg'))
@section('meta_image_alt', 'De Lamoure — Signature Fragrances and Home Scents')
@section('meta_type', 'website')

@section('PageContent')
    <section class="pb-18">

        {{-- Breadcrumb --}}
        <div class="bg-body-secondary py-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-center">
                    <li class="breadcrumb-item">
                        <a class="text-decoration-none text-body" href="{{ url('/') }}">
                            <i class="fas fa-home-lg me-4 text-body-emphasis"></i>
                            Home
                        </a>
                    </li>

                    <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">
                        Page Not Found
                    </li>
                </ol>
            </nav>
        </div>

        {{-- Page Heading --}}
        <div class="container text-center">
            <h2 class="py-14 m-0">Page Not Found</h2>
        </div>

        {{-- Error Content --}}
        <div class="container text-center">
            <div class="row">
                <div id="main-content" class="col">
                    <div class="content-404-wrapper">
                        <h1 class="fs-160px">404</h1>

                        <h4 class="fs-3 mb-5">
                            Oops! That page can’t be found.
                        </h4>

                        <p class="mb-11">
                            The page you are looking for may have moved or is no longer available.
                            Please check the address or return to our homepage.
                        </p>

                        <a href="{{ url('/') }}" class="btn btn-dark btn-hover-border-primary btn-hover-bg-primary">
                            Go to home page
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
