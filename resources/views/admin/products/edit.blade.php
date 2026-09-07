@extends('layouts.admin')

@section('content')

    <!-- Body: Edit Product -->
    <div class="body d-flex py-3">
        <div class="container-xxl">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Form error:</strong>

                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <!-- PAGE HEADER -->
                <div class="row align-items-center">
                    <div class="border-0 mb-4">

                        <div
                            class="card-header py-3 no-bg bg-transparent
                                d-flex align-items-center px-0
                                justify-content-between border-bottom flex-wrap">

                            <h3 class="fw-bold mb-0">
                                Edit Product
                            </h3>

                            <div>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">
                                    Back
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="icofont-save me-2"></i>
                                    Update Product
                                </button>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="row g-3 mb-3">

                    <!-- ====================================================== -->
                    <!-- LEFT COLUMN -->
                    <!-- ====================================================== -->

                    <div class="col-xl-8 col-lg-8">

                        <!-- BASIC INFORMATION -->
                        <div class="card mb-3">

                            <div class="card-header py-3 bg-transparent border-bottom-0">
                                <h6 class="mb-0 fw-bold">
                                    Basic Information
                                </h6>
                            </div>

                            <div class="card-body">

                                <div class="row g-3">

                                    <!-- Product Name -->
                                    <div class="col-md-12">

                                        <label class="form-label">
                                            Product Name
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $product->name) }}" placeholder="Enter product name">

                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- Category -->
                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Category
                                            <span class="text-danger">*</span>
                                        </label>

                                        <select name="category_id"
                                            class="form-select @error('category_id') is-invalid @enderror">

                                            <option value="">
                                                Select Category
                                            </option>

                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>

                                                    {{ $category->name }}

                                                </option>
                                            @endforeach

                                        </select>

                                        @error('category_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- Subcategory -->
                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Subcategory
                                        </label>

                                        <select name="subcategory_id"
                                            class="form-select @error('subcategory_id') is-invalid @enderror">

                                            <option value="">
                                                Select Subcategory
                                            </option>

                                            @foreach ($subcategories as $subcategory)
                                                <option value="{{ $subcategory->id }}" @selected(old('subcategory_id', $product->subcategory_id) == $subcategory->id)>

                                                    {{ $subcategory->name }}

                                                </option>
                                            @endforeach

                                        </select>

                                        @error('subcategory_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- Brand -->
                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Brand
                                        </label>

                                        <input type="text" name="brand"
                                            class="form-control @error('brand') is-invalid @enderror"
                                            value="{{ old('brand', $product->brand) }}" placeholder="Enter brand">

                                        @error('brand')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- SKU -->
                                    <div class="col-md-6">

                                        <label class="form-label">
                                            SKU
                                        </label>

                                        <input type="text" name="sku"
                                            class="form-control @error('sku') is-invalid @enderror"
                                            value="{{ old('sku', $product->sku) }}" placeholder="e.g. DLM-PERF-001">

                                        @error('sku')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- Barcode -->
                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Barcode
                                        </label>

                                        <input type="text" name="barcode"
                                            class="form-control @error('barcode') is-invalid @enderror"
                                            value="{{ old('barcode', $product->barcode) }}" placeholder="Enter barcode">

                                        @error('barcode')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- Short Description -->
                                    <div class="col-md-12">

                                        <label class="form-label">
                                            Short Description
                                        </label>

                                        <textarea name="short_description" rows="3" class="form-control @error('short_description') is-invalid @enderror"
                                            placeholder="Short product description">{{ old('short_description', $product->short_description) }}</textarea>

                                        @error('short_description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- Description -->
                                    <div class="col-md-12">

                                        <label class="form-label">
                                            Description
                                        </label>

                                        <textarea name="description" rows="6" class="form-control @error('description') is-invalid @enderror"
                                            placeholder="Full product description">{{ old('description', $product->description) }}</textarea>

                                        @error('description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                </div>
                            </div>
                        </div>


                        <!-- ================================================== -->
                        <!-- PRICING & INVENTORY -->
                        <!-- ================================================== -->

                        <div class="card mb-3">

                            <div class="card-header py-3 bg-transparent border-bottom-0">
                                <h6 class="mb-0 fw-bold">
                                    Pricing & Inventory
                                </h6>
                            </div>

                            <div class="card-body">

                                <div class="row g-3">

                                    <!-- NGN REGULAR PRICE -->
                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Regular Price (NGN)
                                            <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                ₦
                                            </span>

                                            <input type="number" step="0.01" min="0" name="price_ngn"
                                                class="form-control @error('price_ngn') is-invalid @enderror"
                                                value="{{ old('price_ngn', $product->price_ngn) }}" placeholder="0.00">

                                        </div>

                                        @error('price_ngn')
                                            <div class="text-danger small mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- NGN SALE PRICE -->
                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Sale Price (NGN)
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                ₦
                                            </span>

                                            <input type="number" step="0.01" min="0" name="sale_price_ngn"
                                                class="form-control @error('sale_price_ngn') is-invalid @enderror"
                                                value="{{ old('sale_price_ngn', $product->sale_price_ngn) }}"
                                                placeholder="0.00">

                                        </div>

                                        @error('sale_price_ngn')
                                            <div class="text-danger small mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- USD REGULAR PRICE -->
                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Regular Price (USD)
                                            <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                $
                                            </span>

                                            <input type="number" step="0.01" min="0" name="price_usd"
                                                class="form-control @error('price_usd') is-invalid @enderror"
                                                value="{{ old('price_usd', $product->price_usd) }}" placeholder="0.00">

                                        </div>

                                        @error('price_usd')
                                            <div class="text-danger small mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- USD SALE PRICE -->
                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Sale Price (USD)
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                $
                                            </span>

                                            <input type="number" step="0.01" min="0" name="sale_price_usd"
                                                class="form-control @error('sale_price_usd') is-invalid @enderror"
                                                value="{{ old('sale_price_usd', $product->sale_price_usd) }}"
                                                placeholder="0.00">

                                        </div>

                                        @error('sale_price_usd')
                                            <div class="text-danger small mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- Cost Price -->
                                    <div class="col-md-4">

                                        <label class="form-label">
                                            Cost Price
                                        </label>

                                        <input type="number" step="0.01" min="0" name="cost_price"
                                            class="form-control @error('cost_price') is-invalid @enderror"
                                            value="{{ old('cost_price', $product->cost_price) }}">

                                        @error('cost_price')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- Quantity -->
                                    <div class="col-md-4">

                                        <label class="form-label">
                                            Quantity
                                        </label>

                                        <input type="number" min="0" name="quantity"
                                            class="form-control @error('quantity') is-invalid @enderror"
                                            value="{{ old('quantity', $product->quantity) }}">

                                        @error('quantity')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- Low Stock Alert -->
                                    <div class="col-md-4">

                                        <label class="form-label">
                                            Low Stock Alert
                                        </label>

                                        <input type="number" min="0" name="low_stock_alert"
                                            class="form-control @error('low_stock_alert') is-invalid @enderror"
                                            value="{{ old('low_stock_alert', $product->low_stock_alert) }}">

                                        @error('low_stock_alert')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <!-- Stock Status -->
                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Stock Status
                                        </label>

                                        <select name="stock_status"
                                            class="form-select @error('stock_status') is-invalid @enderror">

                                            <option value="in_stock" @selected(old('stock_status', $product->stock_status) == 'in_stock')>

                                                In Stock

                                            </option>


                                            <option value="out_of_stock" @selected(old('stock_status', $product->stock_status) == 'out_of_stock')>

                                                Out of Stock

                                            </option>

                                        </select>

                                        @error('stock_status')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                </div>
                            </div>
                        </div>


                        <!-- ================================================== -->
                        <!-- SEO -->
                        <!-- ================================================== -->

                        <div class="card mb-3">

                            <div class="card-header py-3 bg-transparent border-bottom-0">
                                <h6 class="mb-0 fw-bold">
                                    SEO Information
                                </h6>
                            </div>

                            <div class="card-body">

                                <div class="row g-3">

                                    <div class="col-md-12">

                                        <label class="form-label">
                                            Meta Title
                                        </label>

                                        <input type="text" name="meta_title"
                                            class="form-control @error('meta_title') is-invalid @enderror"
                                            value="{{ old('meta_title', $product->meta_title) }}"
                                            placeholder="SEO title">

                                        @error('meta_title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <div class="col-md-12">

                                        <label class="form-label">
                                            Meta Description
                                        </label>

                                        <textarea name="meta_description" rows="3"
                                            class="form-control @error('meta_description') is-invalid @enderror" placeholder="SEO description">{{ old('meta_description', $product->meta_description) }}</textarea>

                                        @error('meta_description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>


                                    <div class="col-md-12">

                                        <label class="form-label">
                                            Meta Keywords
                                        </label>

                                        <input type="text" name="meta_keywords"
                                            class="form-control @error('meta_keywords') is-invalid @enderror"
                                            value="{{ old('meta_keywords', $product->meta_keywords) }}"
                                            placeholder="perfume, fragrance, beauty">

                                        @error('meta_keywords')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>


                    <!-- ====================================================== -->
                    <!-- RIGHT COLUMN -->
                    <!-- ====================================================== -->

                    <div class="col-xl-4 col-lg-4">


                        <!-- PRODUCT IMAGES -->
                        <div class="card mb-3">

                            <div class="card-header py-3 bg-transparent border-bottom-0">
                                <h6 class="mb-0 fw-bold">
                                    Product Images
                                </h6>
                            </div>

                            <div class="card-body">


                                <!-- Current Main Image -->
                                @if ($product->main_image)
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Current Main Image
                                        </label>

                                        <img src="{{ asset($product->main_image) }}" alt="{{ $product->name }}"
                                            class="rounded border d-block" width="100%"
                                            style="max-height: 240px; object-fit: cover;">

                                    </div>
                                @endif


                                <!-- Replace Main Image -->
                                <div class="mb-4">

                                    <label class="form-label">
                                        Replace Main Image
                                    </label>

                                    <input type="file" name="main_image"
                                        class="form-control @error('main_image') is-invalid @enderror" accept="image/*">

                                    @error('main_image')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <small class="text-muted d-block mt-2">
                                        Upload only if you want to replace the current main image.
                                    </small>

                                </div>


                                <!-- Existing Gallery Images -->
                                @if ($product->images && $product->images->count())
                                    <div class="mb-4">

                                        <label class="form-label">
                                            Current Gallery Images
                                        </label>

                                        <div class="row g-2">

                                            @foreach ($product->images as $image)
                                                <div class="col-4">

                                                    <div class="border rounded p-2">

                                                        <img src="{{ asset($image->image) }}" alt="{{ $product->name }}"
                                                            class="img-fluid rounded mb-2"
                                                            style="height: 90px; width: 100%; object-fit: cover;">

                                                        <div class="form-check">

                                                            <input class="form-check-input" type="checkbox"
                                                                name="remove_images[]" value="{{ $image->id }}"
                                                                id="removeImage{{ $image->id }}">

                                                            <label class="form-check-label small"
                                                                for="removeImage{{ $image->id }}">

                                                                Remove Image

                                                            </label>

                                                        </div>

                                                    </div>

                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                @endif


                                <!-- Add More Gallery Images -->
                                <div class="mb-3">

                                    <label class="form-label">
                                        Add More Gallery Images
                                    </label>

                                    <input type="file" name="product_images[]"
                                        class="form-control @error('product_images.*') is-invalid @enderror"
                                        accept="image/*" multiple>

                                    @error('product_images.*')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <small class="text-muted d-block mt-2">
                                        You can upload multiple new images.
                                        Recommended size: 800 × 800px.
                                    </small>

                                </div>

                            </div>
                        </div>


                        <!-- ================================================== -->
                        <!-- ATTRIBUTES -->
                        <!-- ================================================== -->

                        <div class="card mb-3">

                            <div class="card-header py-3 bg-transparent border-bottom-0">

                                <h6 class="mb-0 fw-bold">
                                    Attributes
                                </h6>

                            </div>

                            <div class="card-body">

                                <!-- Weight -->
                                <div class="mb-3">

                                    <label class="form-label">
                                        Weight
                                    </label>

                                    <input type="text" name="weight" class="form-control"
                                        value="{{ old('weight', $product->weight) }}" placeholder="e.g. 500g">

                                </div>


                                <!-- Size -->
                                <div class="mb-3">

                                    <label class="form-label">
                                        Size
                                    </label>

                                    <input type="text" name="size" class="form-control"
                                        value="{{ old('size', $product->size) }}" placeholder="e.g. 100ml">

                                    <small class="text-muted">
                                        Use variants where a product has multiple sizes.
                                    </small>

                                </div>


                                <!-- Color -->
                                <div class="mb-3">

                                    <label class="form-label">
                                        Color
                                    </label>

                                    <input type="text" name="color" class="form-control"
                                        value="{{ old('color', $product->color) }}" placeholder="e.g. Black">

                                    <small class="text-muted">
                                        Use variants where multiple colours have separate stock or pricing.
                                    </small>

                                </div>


                                <!-- Material -->
                                <div class="mb-3">

                                    <label class="form-label">
                                        Material
                                    </label>

                                    <input type="text" name="material" class="form-control"
                                        value="{{ old('material', $product->material) }}">

                                </div>


                                <!-- Model -->
                                <div class="mb-3">

                                    <label class="form-label">
                                        Model
                                    </label>

                                    <input type="text" name="model" class="form-control"
                                        value="{{ old('model', $product->model) }}">

                                </div>

                            </div>
                        </div>


                        <!-- ================================================== -->
                        <!-- SETTINGS -->
                        <!-- ================================================== -->

                        <div class="card mb-3">

                            <div class="card-header py-3 bg-transparent border-bottom-0">

                                <h6 class="mb-0 fw-bold">
                                    Settings
                                </h6>

                            </div>


                            <div class="card-body">

                                <!-- Featured -->
                                <input type="hidden" name="is_featured" value="0">

                                <div class="form-check form-switch mb-2">

                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                                        id="isFeatured" @checked(old('is_featured', $product->is_featured))>

                                    <label class="form-check-label" for="isFeatured">

                                        Featured Product

                                    </label>

                                </div>


                                <!-- New Arrival -->
                                <input type="hidden" name="is_new_arrival" value="0">

                                <div class="form-check form-switch mb-2">

                                    <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1"
                                        id="isNewArrival" @checked(old('is_new_arrival', $product->is_new_arrival))>

                                    <label class="form-check-label" for="isNewArrival">

                                        New Arrival

                                    </label>

                                </div>


                                <!-- Best Seller -->
                                <input type="hidden" name="is_best_seller" value="0">

                                <div class="form-check form-switch mb-3">

                                    <input class="form-check-input" type="checkbox" name="is_best_seller" value="1"
                                        id="isBestSeller" @checked(old('is_best_seller', $product->is_best_seller))>

                                    <label class="form-check-label" for="isBestSeller">

                                        Best Seller

                                    </label>

                                </div>


                                <!-- Active -->
                                <input type="hidden" name="status" value="0">

                                <div class="form-check form-switch mb-3">

                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                        id="productStatus" @checked(old('status', $product->status))>

                                    <label class="form-check-label" for="productStatus">

                                        Active Product

                                    </label>

                                </div>


                                <!-- Sort Order -->
                                <div class="mb-3">

                                    <label class="form-label">
                                        Sort Order
                                    </label>

                                    <input type="number" min="0" name="sort_order"
                                        class="form-control @error('sort_order') is-invalid @enderror"
                                        value="{{ old('sort_order', $product->sort_order ?? 0) }}">

                                    @error('sort_order')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>
                        </div>

                    </div>

                </div>

            </form>

        </div>
    </div>

@endsection
