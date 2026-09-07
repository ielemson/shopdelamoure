@if($products->count() > 0)
    <div class="ec-new-product col-lg-12 col-md-12 col-sm-12 mb-2 margin-minus-b-15">
        <div class="col-md-12">
            <div class="section-title">
                <h2 class="ec-title">{{ $title }}</h2>
            </div>
        </div>

        <div class="ec-new-product-block">
            <div class="row">
                @foreach($products as $product)
                    @include('frontend.partials.products.product-card', [
                        'product' => $product
                    ])
                @endforeach
            </div>
        </div>
    </div>
@endif