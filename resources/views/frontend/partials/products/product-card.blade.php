@php
    $mainImage = $product->main_image
        ? asset($product->main_image)
        : asset('assets/images/product-image/default.jpg');

    $hasSale = !empty($product->sale_price) && $product->sale_price < $product->regular_price;

    $price = $hasSale ? $product->sale_price : $product->regular_price;

    $discount = $hasSale
        ? round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100)
        : 0;
@endphp

<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">

    <div class="ec-product-ds">

        <div class="ec-product-image">

            <a href="{{ route('products.show', $product->slug) }}" class="image">
                <img class="pic-1"
                     src="{{ $mainImage }}"
                     alt="{{ $product->name }}">
            </a>

            @if($hasSale)
                <span class="ec-product-discount-label">
                    -{{ $discount }}%
                </span>
            @elseif($product->is_new_arrival)
                <span class="ec-product-discount-label new-label">
                    New
                </span>
            @endif

            <ul class="links">

                <li>
                    <a href="javascript:void(0)" data-tip="Add to Wishlist">
                        <i class="fi-rr-heart"></i>
                    </a>
                </li>

                <li>
                    <a href="javascript:void(0)" data-tip="Compare">
                        <i class="fi fi-rr-arrows-repeat"></i>
                    </a>
                </li>

                <li>
                    <a href="{{ route('products.show', $product->slug) }}">
                        <i class="fi-rr-eye"></i>
                    </a>
                </li>

            </ul>

        </div>

        <div class="ec-product-body">

            <a href="{{ route('category.products', $product->category->slug ?? '') }}">
                <small class="ec-product-category">
                    {{ $product->category->name ?? 'Products' }}
                </small>
            </a>

            <h3 class="ec-title">
                <a href="{{ route('products.show', $product->slug) }}">
                    {{ Str::limit($product->name, 40) }}
                </a>
            </h3>

            <div class="ec-price">
                @if($hasSale)
                    <span>₦{{ number_format($product->regular_price, 2) }}</span>
                @endif

                ₦{{ number_format($price, 2) }}
            </div>

            @if($product->stock_status === 'in_stock' && $product->quantity > 0)
                <a href="javascript:void(0)"
                class="ec-add-to-cart add-to-cart"
                data-url="{{ route('cart.add') }}"
                data-product-id="{{ $product->id }}">
                <i class="fas fa-shopping-cart me-1"></i>
                <span>Add to Cart</span>
                </a>

            @else

                <a href="javascript:void(0)"
                   class="ec-add-to-cart disabled"
                   style="pointer-events: none; opacity: .6;">
                    Out of stock
                </a>

            @endif

        </div>

    </div>

</div>