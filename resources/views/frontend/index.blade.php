@extends('layouts.app')

{{-- Homepage SEO / Social Sharing --}}
@section('meta_title', 'De Lamoure | Fragrances, Home Scents & Everyday Luxury')

@section('meta_description', 'Discover signature scents for your home, bathroom and car. Shop De Lamoure diffusers, air
    fresheners, scented candles, essential oils and scented crystal stones.')

@section('meta_keywords', 'De Lamoure, Delamoure, home fragrances Nigeria, diffusers, car diffusers, air fresheners,
    scented candles, essential oils, toilet care, scented crystal stones')

@section('meta_image', asset('assets/images/others/social-share.jpg'))
@section('meta_image_alt', 'De Lamoure — Signature Fragrances and Home Scents')
@section('meta_type', 'website')

@section('PageContent')
    @include('frontend.partials.header_slider')

    @include('frontend.partials.collection')

    @foreach ($productSections as $section)
        @if ($section['products']->isNotEmpty())
            @include('frontend.partials.products', [
                'title' => $section['title'],
                'subtitle' => $section['subtitle'] ?? null,
                'products' => $section['products'],
                'viewAllUrl' => $section['view_all_url'] ?? (Route::has('shop') ? route('shop') : url('/shop')),
            ])
        @endif
    @endforeach

    {{-- Most Loved / Best Sellers --}}
    @if ($bestSellers->isNotEmpty())
        @include('frontend.partials.bestseller', [
            'title' => 'Most Loved',
            'subtitle' => 'Discover the De Lamoure favourites our customers keep coming back to.',
            'products' => $bestSellers,
            'viewAllUrl' => Route::has('shop') ? route('shop') : url('/shop'),
        ])
    @endif
@endsection


@section('PageContent')
    @include('frontend.partials.header_slider')

    @include('frontend.partials.collection')

    @foreach ($productSections as $section)
        @if ($section['products']->isNotEmpty())
            @include('frontend.partials.products', [
                'title' => $section['title'],
                'subtitle' => $section['subtitle'] ?? null,
                'products' => $section['products'],
                'viewAllUrl' => $section['view_all_url'] ?? (Route::has('shop') ? route('shop') : url('/shop')),
            ])
        @endif
    @endforeach

    {{-- =========================================================
    MOST LOVED / BEST SELLERS
========================================================= --}}

    @if ($bestSellers->isNotEmpty())
        @include('frontend.partials.bestseller', [
            'title' => 'Most Loved',
        
            'subtitle' => 'Discover the Dela Moure favourites our customers keep coming back to.',
        
            'products' => $bestSellers,
        
            'viewAllUrl' => Route::has('shop') ? route('shop') : url('/shop'),
        ])
    @endif
@endsection
