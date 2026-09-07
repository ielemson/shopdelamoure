<section class="section ec-category-section ec-category-wrapper-1 section-space-p">
    <div class="container">

        <div class="row">
            <div class="col-md-12 text-center">
                <div class="section-title">
                    {{-- <h2 class="ec-bg-title">Categories</h2> --}}
                    <h2 class="ec-title">Product Categories</h2>
                    <p class="sub-title">Browse The Collection of Top Categories</p>
                </div>
            </div>
        </div>

        <div class="row margin-minus-tb-15">

            <div class="ec_cat_slider">

                @forelse($categories as $category)

                    <div class="ec_cat_content">

                        <div class="ec_cat_inner">

                            <div class="ec-cat-image">
                                <img src="{{ asset($category->image ?? 'assets/images/category-image/default.jpg') }}"
                                     alt="{{ $category->name }}">
                            </div>

                            <div class="ec-cat-desc">
                                <span class="ec-section-btn">
                                    <a href="{{ url('category/' . $category->slug) }}" class="btn-primary">
                                        {{ $category->name }}
                                    </a>
                                </span>
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="ec_cat_content">

                        <div class="ec_cat_inner">

                            <div class="ec-cat-image">
                                <img src="{{ asset('assets/images/category-image/default.jpg') }}"
                                     alt="No Category">
                            </div>

                            <div class="ec-cat-desc">
                                <span class="ec-section-btn">
                                    <a href="#" class="btn-primary">
                                        No Categories
                                    </a>
                                </span>
                            </div>

                        </div>

                    </div>

                @endforelse

            </div>

        </div>

    </div>
</section>