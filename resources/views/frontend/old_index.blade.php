@extends('layouts.app')
@push('styles')
    @include('frontend.partials.css')
@endpush
@section('content')
    @include('frontend.partials.header.header')
    @include('frontend.partials.cart.side-cart')
    @include('frontend.partials.main_slider')
    @include('frontend.partials.category-slider')
    <section class="section ec-new-test-product section-space-p">
        <div class="container">
            <div class="row">

                @include('frontend.partials.products.product-section', [
                    'title' => 'Featured Products',
                    'products' => $featuredProducts,
                ])

                {{-- New Arrivals --}}
                @include('frontend.partials.products.product-section', [
                    'title' => 'New Arrivals',
                    'products' => $newArrivals,
                ])

                {{-- Best Sellers --}}
                @include('frontend.partials.products.product-section', [
                    'title' => 'Best Sellers',
                    'products' => $bestSellers,
                ])

                {{-- Trending Products --}}
                @include('frontend.partials.products.product-section', [
                    'title' => 'Trending Products',
                    'products' => $trendingProducts,
                ])

            </div>
        </div>
    </section>
    @include('frontend.partials.services')
@endsection
@push('scripts')
    @include('frontend.partials.js')
@endpush
