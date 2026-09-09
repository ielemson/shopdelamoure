@extends('layouts.app')

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
