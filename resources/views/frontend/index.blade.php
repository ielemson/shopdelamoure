@extends('layouts.app')

@section('PageContent')
    @include('frontend.partials.header_slider')

    @include('frontend.partials.infosection')

    {{-- Standard Product Sections --}}
    @foreach ($productSections as $section)
        @if ($section['products']->isNotEmpty())
            @include('frontend.partials.products', [
                'title' => $section['title'],
                'subtitle' => $section['subtitle'],
                'products' => $section['products'],
            ])
        @endif
    @endforeach

    {{-- Most Loved / Best Sellers --}}
    @if ($bestSellers->isNotEmpty())
        @include('frontend.partials.bestseller', [
            'products' => $bestSellers,
        ])
    @endif
@endsection
