@extends('layouts.app')

@section('meta_title', 'My Wishlist | Dela Moure')

@section('meta_description',
    'View and manage your favourite Dela Moure perfumes, diffusers, candles, essential oils and
    gift collections.')

@section('PageContent')

    @include('frontend.partials.breadcrumb', [
        'title' => 'My Wishlist',
        'item' => 'Wishlist',
    ])


    <section id="wishlist_page">

        <div class="container container-xxl py-8 py-lg-12">

            @if ($products->isNotEmpty())

                {{-- =========================================================
                    HEADING
                ========================================================== --}}
                <div class="row align-items-center mb-8">

                    <div class="col-md-8">

                        <h2 class="h3 mb-2">
                            Saved Favourites
                        </h2>

                        <p class="mb-0 text-body">
                            Your favourite Dela Moure pieces, saved in one place.
                        </p>

                    </div>

                    <div class="col-md-4 text-md-end mt-4 mt-md-0">

                        <a href="{{ Route::has('shop') ? route('shop') : url('/shop') }}"
                            class="btn btn-link p-0 text-body-emphasis text-decoration-none fw-semibold text-primary-hover">

                            Continue Shopping

                            <svg class="icon ms-1">
                                <use xlink:href="#icon-arrow-right"></use>
                            </svg>

                        </a>

                    </div>

                </div>


                {{-- =========================================================
                    PRODUCTS
                ========================================================== --}}
                <div class="row gy-8 gx-4">

                    @foreach ($products as $product)
                        <div class="col-6 col-md-4 col-lg-3 wishlist-product-item" data-product-id="{{ $product->id }}">

                            @include('frontend.partials.product-card', [
                                'product' => $product,
                            
                                'gridStyle' => 'grid-1',
                            
                                'actionLayout' => 'horizontal',
                            
                                'compact' => false,
                            
                                'showCategory' => false,
                            
                                'showVariantCount' => false,
                            
                                'showCompare' => false,
                            ])

                            <div class="text-center mt-3">

                                <button type="button"
                                    class="btn btn-link p-0 text-body text-decoration-none wishlist-page-remove"
                                    data-product-id="{{ $product->id }}"
                                    data-url="{{ route('wishlist.toggle', $product->id) }}" data-csrf="{{ csrf_token() }}">

                                    Remove from Wishlist

                                </button>

                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                {{-- =========================================================
                    EMPTY WISHLIST
                ========================================================== --}}
                <div class="text-center py-10 py-lg-14">

                    <div class="mb-5">

                        <svg class="icon" style="width: 52px; height: 52px;">
                            <use xlink:href="#icon-star-light"></use>
                        </svg>

                    </div>

                    <h2 class="h3 mb-4">
                        Your Wishlist Is Empty
                    </h2>

                    <p class="fs-17px text-body mb-6">
                        Save the fragrances and home scents you love and come back to them anytime.
                    </p>

                    <a href="{{ Route::has('shop') ? route('shop') : url('/shop') }}" class="btn btn-dark px-8">

                        Explore Our Collection

                    </a>

                </div>

            @endif

        </div>

    </section>

@endsection
@push('scripts')
    <script>
        document.addEventListener('click', async function(event) {

            const removeButton = event.target.closest('.wishlist-page-remove');

            if (!removeButton) {
                return;
            }

            event.preventDefault();

            const productId = removeButton.dataset.productId;
            const url = removeButton.dataset.url;
            const csrfToken = removeButton.dataset.csrf;

            removeButton.disabled = true;

            try {

                const response = await fetch(url, {

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },

                    credentials: 'same-origin'

                });

                if (!response.ok) {
                    throw new Error('Unable to remove wishlist item.');
                }

                const data = await response.json();

                const item = document.querySelector(
                    `.wishlist-product-item[data-product-id="${productId}"]`
                );

                if (item && !data.wishlisted) {

                    item.classList.add('wishlist-removing');

                    setTimeout(function() {
                        item.remove();
                    }, 300);

                }


                /*
                |--------------------------------------------------------------------------
                | Update Header Count
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll('.wishlist-count')
                    .forEach(function(counter) {

                        counter.textContent = data.count;

                        counter.classList.toggle(
                            'd-none',
                            data.count < 1
                        );

                    });


                /*
                |--------------------------------------------------------------------------
                | Empty Wishlist
                |--------------------------------------------------------------------------
                */

                if (data.count < 1) {

                    setTimeout(function() {
                        window.location.reload();
                    }, 350);

                }

            } catch (error) {

                console.error('Wishlist Error:', error);

                removeButton.disabled = false;

            }

        });
    </script>
@endpush
