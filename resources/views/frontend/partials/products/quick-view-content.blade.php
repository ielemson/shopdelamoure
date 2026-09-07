<div class="row">

    {{-- Product Gallery --}}
    @php
        $primaryImage = $product->images->firstWhere('is_primary', 1);
        $firstImage = $product->images->first();

        $galleryImages = $product->images->pluck('image')->filter()->unique()->values();

        if ($product->main_image && !$galleryImages->contains($product->main_image)) {
            $galleryImages->prepend($product->main_image);
        }

        if ($galleryImages->isEmpty()) {
            $galleryImages->push(
                optional($primaryImage)->image ?:
                optional($firstImage)->image ?:
                'assets/images/products/product-placeholder.jpg',
            );
        }
    @endphp


    <div class="col-md-6 pe-md-13">

        <div class="position-relative">

            <div id="quickViewSlider"
                class="slick-slider slick-slider-arrow-inside slick-slider-dots-inside slick-slider-dots-light g-0">

                @foreach ($galleryImages as $image)
                    <div>
                        <img src="{{ asset($image) }}" class="img-fluid w-100 h-auto" width="540" height="720"
                            alt="{{ $product->name }}">
                    </div>
                @endforeach

            </div>

        </div>


        @if ($galleryImages->count() > 1)

            <div class="mt-6">

                <div id="quickViewSliderThumb" class="slick-slider slick-slider-thumb ps-1 ms-n3 me-n4">

                    @foreach ($galleryImages as $image)
                        <div class="px-2">

                            <img src="{{ asset($image) }}" class="mx-3 px-0 h-auto cursor-pointer img-fluid"
                                width="75" height="100" alt="{{ $product->name }}">

                        </div>
                    @endforeach

                </div>

            </div>

        @endif

    </div>



    {{-- Product Information --}}
    <div class="col-md-6 pt-md-0 pt-10">


        {{-- Price --}}
        <p class="d-flex align-items-center mb-6">

            <span id="quickViewRegularPrice"
                class="{{ $salePrice ? 'text-decoration-line-through' : 'fs-18px text-body-emphasis fw-bold' }}">

                {{ $symbol }}{{ number_format($price, 2) }}

            </span>


            <span id="quickViewSalePrice" class="fs-18px text-body-emphasis ps-6 fw-bold"
                @if (!$salePrice) style="display:none;" @endif>

                @if ($salePrice)
                    {{ $symbol }}{{ number_format($salePrice, 2) }}
                @endif

            </span>

        </p>



        {{-- Product Name --}}
        <h1 class="mb-4 pb-2 fs-4">

            {{ $product->name }}

        </h1>



        {{-- Description --}}
        @if ($product->short_description)
            <p class="fs-15px">

                {{ $product->short_description }}

            </p>
        @elseif ($product->description)
            <p class="fs-15px">

                {{ \Illuminate\Support\Str::limit(strip_tags($product->description), 250) }}

            </p>
        @endif



        {{-- Stock --}}
        <p class="mb-4 pb-2 text-body-emphasis" id="quickViewStock">

            <svg class="icon fs-5 me-4 pe-2 align-text-bottom">
                <use xlink:href="#icon-Timer"></use>
            </svg>

            @if ($product->stock_status === 'in_stock')
                In Stock
            @else
                Out of Stock
            @endif

        </p>



        {{-- Add To Cart Form --}}
        <form class="mb-9 pb-2" id="quickViewCartForm" action="{{ route('cart.add') }}" method="POST">

            @csrf


            <input type="hidden" name="product_id" value="{{ $product->id }}">

            {{-- Variant --}}
            @if ($product->variants->count())

                <div class="mb-7">

                    <label for="quickViewVariant" class="text-body-emphasis fw-semibold fs-15px pb-3">

                        Select Option:

                    </label>


                    <select id="quickViewVariant" name="variant_id" class="form-select"
                        data-symbol="{{ $symbol }}" required>

                        @foreach ($product->variants as $variant)
                            @php
                                $variantPrice = $currency === 'USD' ? $variant->price_usd : $variant->price_ngn;

                                $variantSalePrice =
                                    $currency === 'USD' ? $variant->sale_price_usd : $variant->sale_price_ngn;
                            @endphp


                            <option value="{{ $variant->id }}" data-price="{{ $variantPrice }}"
                                data-sale-price="{{ $variantSalePrice }}" data-stock="{{ $variant->stock_quantity }}"
                                data-stock-status="{{ $variant->stock_status }}"
                                {{ $variant->is_default ? 'selected' : '' }}>

                                {{ $variant->name }}

                            </option>
                        @endforeach

                    </select>

                </div>

            @endif



            <div class="row align-items-end">


                {{-- Quantity --}}
                <div class="form-group col-sm-4">

                    <label class="text-body-emphasis fw-semibold fs-15px pb-6" for="QuickViewNumber">

                        Quantity:

                    </label>


                    <div class="input-group position-relative w-100 input-group-lg">

                        {{-- Minus --}}
                        <button type="button"
                            class="btn position-absolute top-50 start-0 translate-middle-y ps-5 pe-3 quick-view-minus"
                            style="z-index:5; border:0; background:transparent;">

                            <i class="far fa-minus"></i>

                        </button>


                        {{-- Quantity Input --}}
                        <input name="quantity" type="number" id="QuickViewNumber"
                            class="form-control w-100 px-10 text-center" value="1" min="1" required>


                        {{-- Plus --}}
                        <button type="button"
                            class="btn position-absolute top-50 end-0 translate-middle-y pe-5 ps-3 quick-view-plus"
                            style="z-index:5; border:0; background:transparent;">

                            <i class="far fa-plus"></i>

                        </button>

                    </div>

                </div>



                {{-- Add To Bag --}}
                <div class="col-sm-8 pt-9 mt-2 mt-sm-0 pt-sm-0">

                    <button type="submit"
                        class="btn-hover-bg-primary
                               btn-hover-border-primary
                               btn
                               btn-lg
                               btn-dark
                               w-100"
                        id="quickViewAddToCart">

                        Add To Bag

                    </button>

                </div>

            </div>

        </form>



        {{-- Meta --}}
        <ul class="single-product-meta list-unstyled border-top pt-7 mt-7">


            @if ($product->sku)
                <li class="d-flex mb-4 pb-2 align-items-center">

                    <span class="text-body-emphasis fw-semibold fs-14px">
                        SKU:
                    </span>

                    <span class="ps-4">
                        {{ $product->sku }}
                    </span>

                </li>
            @endif



            @if ($product->category)
                <li class="d-flex mb-4 pb-2 align-items-center">

                    <span class="text-body-emphasis fw-semibold fs-14px">
                        Category:
                    </span>

                    <span class="ps-4">
                        {{ $product->category->name }}
                    </span>

                </li>
            @endif

        </ul>

    </div>

</div>
