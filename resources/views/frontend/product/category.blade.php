
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

<title>
    Shop | {{ $setting?->website_name ?? 'Springcrest Trading' }}
</title>

<meta name="keywords"
      content="online shopping, ecommerce store, products, electronics, fashion, home essentials, groceries, accessories, {{ strtolower($setting?->website_name ?? 'springcrest trading') }}">
<meta name="description" content="Browse quality products at {{ $setting?->website_name ?? 'Springcrest Trading' }}. Shop electronics, fashion, home essentials, groceries, accessories, and more at competitive prices.">
<meta name="author" content="{{ $setting?->website_name ?? 'Springcrest Trading' }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ !empty($setting?->favicon) ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}" sizes="32x32">
<link rel="apple-touch-icon" href="{{ !empty($setting?->favicon) ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}">
<meta name="msapplication-TileImage" content="{{ !empty($setting?->favicon) ? asset('storage/'.$setting->favicon) : asset('assets/images/favicon/favicon.png') }}">

<meta property="og:type" content="website">
<meta property="og:title" content="Shop | {{ $setting?->website_name ?? 'Springcrest Trading' }}">

<meta property="og:description" content="Discover quality products, great deals, and trusted online shopping at {{ $setting?->website_name ?? 'Springcrest Trading' }}.">

<meta property="og:url" content="{{ url()->current() }}">

<meta property="og:site_name" content="{{ $setting?->website_name ?? 'Springcrest Trading' }}">

@if(!empty($setting?->logo))
<meta property="og:image" content="{{ asset('storage/'.$setting->logo) }}">

<meta name="twitter:image"
      content="{{ asset('storage/'.$setting->logo) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Shop | {{ $setting?->website_name ?? 'Springcrest Trading' }}">
<meta name="twitter:description" content="Browse products and enjoy a seamless online shopping experience at {{ $setting?->website_name ?? 'Springcrest Trading' }}.">

 <link rel="stylesheet" href="{{ asset('pages/assets/css/vendor/ecicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/countdowntimer.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/slick.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('pages/assets/css/plugins/bootstrap.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('pages/assets/css/style.css') }}" />
    {{-- <link rel="stylesheet" href="{{ asset('pages/assets/css/responsive.css') }}" /> --}}
    {{-- <link rel="stylesheet" id="bg-switcher-css" href="{{ asset('pages/assets/css/backgrounds/bg-4.css') }}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
   

<body class="shop_page">
    <div id="ec-overlay">
        <div class="ec-ellipsis">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <!-- Header start  -->
 @include('frontend.partials.header.pages-header')
@include('frontend.partials.cart.side-cart')
@include('frontend.partials.breadcrumb', ['title' => 'Shop'])

  <section class="ec-page-content-bnr section-space-pb">
    <div class="container">
        <div class="row">

            <div class="ec-shop-rightside col-lg-12 col-md-12">

                {{-- Shop Top --}}
                <div class="ec-pro-list-top d-flex">
                    <div class="col-md-6 ec-grid-list">
                        <div class="ec-gl-btn">
                            <button class="btn sidebar-toggle-icon">
                                <i class="fi-rr-filter"></i>
                            </button>

                            <button class="btn btn-grid-50 active">
                                <i class="fi-rr-apps"></i>
                            </button>

                            <button class="btn btn-list-50">
                                <i class="fi-rr-list"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 ec-sort-select">
                        <span class="sort-by">Sort by</span>

                        <div class="ec-select-inner">
                            <select id="productSort">
                                <option value="">Position</option>

                                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>
                                    Name, A to Z
                                </option>

                                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>
                                    Name, Z to A
                                </option>

                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>
                                    Price, low to high
                                </option>

                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>
                                    Price, high to low
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Shop Content --}}
                <div class="shop-pro-content">
                    <div class="shop-pro-inner">
                        <div class="row">

                            @forelse($products as $product)

                                @php
                                    $mainImage = $product->main_image
                                        ? asset($product->main_image)
                                        : asset('assets/images/product-image/default.jpg');

                                    $hoverImage = optional(
                                        $product->images
                                            ->where('is_primary', 0)
                                            ->sortBy('sort_order')
                                            ->first()
                                    )->image;

                                    $hoverImage = $hoverImage ? asset($hoverImage) : $mainImage;

                                    $hasSale = !empty($product->sale_price)
                                        && $product->sale_price < $product->regular_price;

                                    $price = $hasSale
                                        ? $product->sale_price
                                        : $product->regular_price;

                                    $discount = $hasSale
                                        ? round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100)
                                        : 0;
                                @endphp

                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 mb-6 pro-gl-content">

                                    <div class="ec-product-inner">

                                        <div class="ec-pro-image-outer">
                                            <div class="ec-pro-image">

                                                <a href="{{ route('products.show', $product->slug) }}" class="image">
                                                    <img class="main-image"
                                                         src="{{ $mainImage }}"
                                                         alt="{{ $product->name }}">

                                                    <img class="hover-image"
                                                         src="{{ $hoverImage }}"
                                                         alt="{{ $product->name }}">
                                                </a>

                                                @if($hasSale)
                                                    <span class="percentage">
                                                        -{{ $discount }}%
                                                    </span>

                                                    <span class="flags">
                                                        <span class="sale">Sale</span>
                                                    </span>
                                                @elseif($product->is_new_arrival)
                                                    <span class="flags">
                                                        <span class="new">New</span>
                                                    </span>
                                                @endif

                                                <a href="javascript:void(0)"
                                                   class="quickview"
                                                   title="Quick view"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#ec_quickview_modal"
                                                   data-id="{{ $product->id }}">
                                                    <i class="fi-rr-eye"></i>
                                                </a>

                                                <div class="ec-pro-actions">

                                                    <a href="javascript:void(0)"
                                                       class="ec-btn-group compare"
                                                       title="Compare">
                                                        <i class="fi fi-rr-arrows-repeat"></i>
                                                    </a>

                                                    @if($product->stock_status === 'in_stock' && $product->quantity > 0)

                                                        <button title="Add To Cart"
                                                                class="add-to-cart"
                                                                data-url="{{ route('cart.add', $product->id) }}">
                                                            <i class="fi-rr-shopping-basket"></i>
                                                            Add To Cart
                                                        </button>

                                                    @else

                                                        <button title="Out Of Stock"
                                                                class="add-to-cart"
                                                                disabled>
                                                            Out Of Stock
                                                        </button>

                                                    @endif

                                                    <a href="javascript:void(0)"
                                                       class="ec-btn-group wishlist"
                                                       title="Wishlist">
                                                        <i class="fi-rr-heart"></i>
                                                    </a>

                                                </div>

                                            </div>
                                        </div>

                                        <div class="ec-pro-content">

                                            <h5 class="ec-pro-title">
                                                <a href="{{ route('products.show', $product->slug) }}">
                                                    {{ Str::limit($product->name, 45) }}
                                                </a>
                                            </h5>

                                            @if($product->short_description)
                                                <div class="ec-pro-list-desc">
                                                    {{ Str::limit($product->short_description, 120) }}
                                                </div>
                                            @endif

                                            <span class="ec-price">

                                                @if($hasSale)
                                                    <span class="old-price">
                                                        ₦{{ number_format($product->regular_price, 2) }}
                                                    </span>
                                                @endif

                                                <span class="new-price">
                                                    ₦{{ number_format($price, 2) }}
                                                </span>

                                            </span>

                                            @if($product->category)
                                                <div class="ec-pro-list-desc mt-1">
                                                    Category: {{ $product->category->name }}
                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </div>

                            @empty

                                <div class="col-12">
                                    <div class="alert alert-warning text-center">
                                        No products found.
                                    </div>
                                </div>

                            @endforelse

                        </div>
                    </div>

                    {{-- Pagination --}}
                    <div class="ec-pro-pagination">
                        <span>
                            Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}
                            of {{ $products->total() }} item(s)
                        </span>

                        {{ $products->links() }}
                    </div>

                </div>
            </div>

            {{-- Filter Sidebar --}}
            <div class="filter-sidebar-overlay"></div>

            <div class="ec-shop-leftside filter-sidebar">
                <div class="ec-sidebar-heading">
                    <h1>Filter Products By</h1>
                    <a class="filter-cls-btn" href="javascript:void(0)">×</a>
                </div>

                <div class="ec-sidebar-wrap">

                    {{-- Category Filter --}}
                    <div class="ec-sidebar-block">
                        <div class="ec-sb-title">
                            <h3 class="ec-sidebar-title">Category</h3>
                        </div>

                        <div class="ec-sb-block-content">
                            <ul>

                                <li>
                                    <div class="ec-sidebar-block-item">
                                        <input type="checkbox" {{ !request('category') ? 'checked' : '' }}>

                                        <a href="{{ route('shop', request()->except('category', 'page')) }}">
                                            All Products
                                        </a>

                                        <span class="checked"></span>
                                    </div>
                                </li>

                                @foreach($categories as $category)
                                    <li>
                                        <div class="ec-sidebar-block-item">
                                            <input type="checkbox"
                                                   {{ request('category') === $category->slug ? 'checked' : '' }}>

                                            <a href="{{ route('shop', array_merge(request()->except('page'), [
                                                'category' => $category->slug
                                            ])) }}">
                                                {{ $category->name }}
                                                ({{ $category->products_count }})
                                            </a>

                                            <span class="checked"></span>
                                        </div>
                                    </li>
                                @endforeach

                            </ul>
                        </div>
                    </div>

                    {{-- Price Filter --}}
                    <div class="ec-sidebar-block">
                        <div class="ec-sb-title">
                            <h3 class="ec-sidebar-title">Price</h3>
                        </div>

                        <div class="ec-sb-block-content es-price-slider">

                            <form action="{{ route('shop') }}" method="GET">

                                @foreach(request()->except('min_price', 'max_price', 'page') as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach

                                <div class="ec-price-input">

                                    <label class="filter__label">
                                        <input type="number"
                                               name="min_price"
                                               class="filter__input"
                                               value="{{ request('min_price') }}"
                                               placeholder="Min">
                                    </label>

                                    <span class="ec-price-divider"></span>

                                    <label class="filter__label">
                                        <input type="number"
                                               name="max_price"
                                               class="filter__input"
                                               value="{{ request('max_price') }}"
                                               placeholder="Max">
                                    </label>

                                </div>

                                <button type="submit" class="btn btn-primary w-100 mt-3">
                                    Apply Filter
                                </button>

                            </form>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

    <!-- Footer Start -->
    @include("frontend.partials.page-footer")
    <!-- Footer Area End -->

    <!-- jQuery -->
<script src="{{ asset('assets/js/vendor/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/jquery-migrate-3.3.0.min.js') }}"></script>

<!-- Bootstrap -->
<script src="{{ asset('assets/js/vendor/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/bootstrap.min.js') }}"></script>

<!-- Modernizr -->
<script src="{{ asset('assets/js/vendor/modernizr-3.11.2.min.js') }}"></script>

<!-- Plugins -->
<script src="{{ asset('assets/js/plugins/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/countdownTimer.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/scrollup.js') }}"></script>
<script src="{{ asset('assets/js/plugins/jquery.zoom.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/infiniteslidev2.js') }}"></script>
<script src="{{ asset('assets/js/vendor/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/jquery.sticky-sidebar.js') }}"></script>
<script src="{{ asset('assets/js/plugins/nouislider.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('assets/js/vendor/index.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
 
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sortSelect = document.getElementById('productSort');

    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            const url = new URL(window.location.href);

            if (this.value) {
                url.searchParams.set('sort', this.value);
            } else {
                url.searchParams.delete('sort');
            }

            url.searchParams.delete('page');

            window.location.href = url.toString();
        });
    }
});
</script>
</body>

</html>