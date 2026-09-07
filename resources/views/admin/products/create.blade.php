@extends('layouts.admin')

@section('content')
    <!-- Body: Add Product -->
    <div class="body d-flex py-3">
        <div class="container-xxl">
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
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row align-items-center">
                    <div class="border-0 mb-4">
                        <div
                            class="card-header py-3 no-bg bg-transparent d-flex align-items-center px-0 justify-content-between border-bottom flex-wrap">
                            <h3 class="fw-bold mb-0">Add Product</h3>

                            <div>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">
                                    Back
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="icofont-save me-2"></i>Save Product
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">

                    <!-- LEFT -->
                    <div class="col-xl-8 col-lg-8">

                        <!-- BASIC INFO -->
                        <div class="card mb-3">
                            <div class="card-header py-3 bg-transparent border-bottom-0">
                                <h6 class="mb-0 fw-bold">Basic Information</h6>
                            </div>

                            <div class="card-body">

                                <div class="row g-3">

                                    <div class="col-md-12">
                                        <label class="form-label">Product Name</label>

                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" placeholder="Enter product name">

                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Category</label>

                                        <select name="category_id"
                                            class="form-select @error('category_id') is-invalid @enderror">

                                            <option value="">Select Category</option>

                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Subcategory</label>

                                        <select name="subcategory_id"
                                            class="form-select @error('subcategory_id') is-invalid @enderror">

                                            <option value="">Select Subcategory</option>

                                            @foreach ($subcategories as $subcategory)
                                                <option value="{{ $subcategory->id }}">
                                                    {{ $subcategory->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('subcategory_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Brand</label>

                                        <input type="text" name="brand" class="form-control"
                                            value="{{ old('brand') }}" placeholder="Enter brand">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">SKU</label>

                                        <input type="text" name="sku" class="form-control"
                                            value="{{ old('sku') }}" placeholder="Enter SKU">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Short Description</label>

                                        <textarea name="short_description" rows="3" class="form-control" placeholder="Short description">{{ old('short_description') }}</textarea>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Description</label>

                                        <textarea name="description" rows="6" class="form-control" placeholder="Full product description">{{ old('description') }}</textarea>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <!-- PRICING -->
                        {{-- <div class="card mb-3">
                            <div class="card-header py-3 bg-transparent border-bottom-0">
                                <h6 class="mb-0 fw-bold">Pricing & Inventory</h6>
                            </div>

                            <div class="card-body">

                                <div class="row g-3">

                                    <div class="col-md-4">
                                        <label class="form-label">Regular Price</label>

                                        <input type="number" step="0.01" name="regular_price" class="form-control"
                                            value="{{ old('regular_price') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Sale Price</label>

                                        <input type="number" step="0.01" name="sale_price" class="form-control"
                                            value="{{ old('sale_price') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Cost Price</label>

                                        <input type="number" step="0.01" name="cost_price" class="form-control"
                                            value="{{ old('cost_price') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Quantity</label>

                                        <input type="number" name="quantity" class="form-control"
                                            value="{{ old('quantity', 0) }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Low Stock Alert</label>

                                        <input type="number" name="low_stock_alert" class="form-control"
                                            value="{{ old('low_stock_alert', 5) }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Stock Status</label>

                                        <select name="stock_status" class="form-select">
                                            <option value="in_stock">In Stock</option>
                                            <option value="out_of_stock">Out of Stock</option>
                                        </select>
                                    </div>

                                </div>

                            </div>
                        </div> --}}

                        <!-- PRICING & INVENTORY -->
                        <div class="card mb-3">
                            <div class="card-header py-3 bg-transparent border-bottom-0">
                                <h6 class="mb-0 fw-bold">Pricing & Inventory</h6>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">

                                    <!-- NGN PRICE -->
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Regular Price (NGN)
                                            <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <span class="input-group-text">₦</span>

                                            <input type="number" step="0.01" min="0" name="price_ngn"
                                                class="form-control @error('price_ngn') is-invalid @enderror"
                                                value="{{ old('price_ngn') }}" placeholder="0.00">
                                        </div>

                                        @error('price_ngn')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- NGN SALE PRICE -->
                                    <div class="col-md-6">
                                        <label class="form-label">Sale Price (NGN)</label>

                                        <div class="input-group">
                                            <span class="input-group-text">₦</span>

                                            <input type="number" step="0.01" min="0" name="sale_price_ngn"
                                                class="form-control @error('sale_price_ngn') is-invalid @enderror"
                                                value="{{ old('sale_price_ngn') }}" placeholder="0.00">
                                        </div>

                                        @error('sale_price_ngn')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- USD PRICE -->
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Regular Price (USD)
                                            <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <span class="input-group-text">$</span>

                                            <input type="number" step="0.01" min="0" name="price_usd"
                                                class="form-control @error('price_usd') is-invalid @enderror"
                                                value="{{ old('price_usd') }}" placeholder="0.00">
                                        </div>

                                        @error('price_usd')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- USD SALE PRICE -->
                                    <div class="col-md-6">
                                        <label class="form-label">Sale Price (USD)</label>

                                        <div class="input-group">
                                            <span class="input-group-text">$</span>

                                            <input type="number" step="0.01" min="0" name="sale_price_usd"
                                                class="form-control @error('sale_price_usd') is-invalid @enderror"
                                                value="{{ old('sale_price_usd') }}" placeholder="0.00">
                                        </div>

                                        @error('sale_price_usd')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- COST PRICE -->
                                    <div class="col-md-4">
                                        <label class="form-label">Cost Price</label>

                                        <input type="number" step="0.01" min="0" name="cost_price"
                                            class="form-control @error('cost_price') is-invalid @enderror"
                                            value="{{ old('cost_price', 0) }}">
                                    </div>

                                    <!-- QUANTITY -->
                                    <div class="col-md-4">
                                        <label class="form-label">Quantity</label>

                                        <input type="number" min="0" name="quantity"
                                            class="form-control @error('quantity') is-invalid @enderror"
                                            value="{{ old('quantity', 0) }}">
                                    </div>

                                    <!-- LOW STOCK -->
                                    <div class="col-md-4">
                                        <label class="form-label">Low Stock Alert</label>

                                        <input type="number" min="0" name="low_stock_alert"
                                            class="form-control @error('low_stock_alert') is-invalid @enderror"
                                            value="{{ old('low_stock_alert', 5) }}">
                                    </div>

                                    <!-- STOCK STATUS -->
                                    <div class="col-md-6">
                                        <label class="form-label">Stock Status</label>

                                        <select name="stock_status"
                                            class="form-select @error('stock_status') is-invalid @enderror">

                                            <option value="in_stock"
                                                {{ old('stock_status', 'in_stock') == 'in_stock' ? 'selected' : '' }}>
                                                In Stock
                                            </option>

                                            <option value="out_of_stock"
                                                {{ old('stock_status') == 'out_of_stock' ? 'selected' : '' }}>
                                                Out of Stock
                                            </option>

                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div class="col-xl-4 col-lg-4">

                        <!-- IMAGE -->
                        <!-- PRODUCT IMAGES -->

                        <div class="card mb-3">
                            <div class="card-header py-3 bg-transparent border-bottom-0">
                                <h6 class="mb-0 fw-bold">Product Images</h6>
                            </div>


                            <div class="card-body">

                                <!-- Main Image -->
                                <div class="mb-3">
                                    <label class="form-label">Main Image</label>

                                    <input type="file" name="main_image"
                                        class="form-control @error('main_image') is-invalid @enderror" accept="image/*">

                                    @error('main_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <small class="text-muted d-block mt-2">
                                        This image will appear as the main product image.
                                    </small>
                                </div>

                                <!-- Multiple Gallery Images -->
                                <div class="mb-3">
                                    <label class="form-label">Gallery Images</label>

                                    <input type="file" name="product_images[]"
                                        class="form-control @error('product_images.*') is-invalid @enderror"
                                        accept="image/*" multiple>

                                    @error('product_images.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                    <small class="text-muted d-block mt-2">
                                        You can select multiple images. Recommended size: 800x800px.
                                    </small>
                                </div>

                            </div>

                        </div>


                        <!-- ATTRIBUTES -->
                        <div class="card mb-3">
                            <div class="card-header py-3 bg-transparent border-bottom-0">
                                <h6 class="mb-0 fw-bold">Attributes</h6>
                            </div>

                            <div class="card-body">

                                <div class="mb-3">
                                    <label class="form-label">Weight</label>
                                    <input type="text" name="weight" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Size</label>
                                    <input type="text" name="size" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Color</label>
                                    <input type="text" name="color" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Material</label>
                                    <input type="text" name="material" class="form-control">
                                </div>

                            </div>
                        </div>

                        <!-- SETTINGS -->
                        <div class="card mb-3">
                            <div class="card-header py-3 bg-transparent border-bottom-0">
                                <h6 class="mb-0 fw-bold">Settings</h6>
                            </div>

                            <div class="card-body">

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1">
                                    <label class="form-check-label">Featured Product</label>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_new_arrival"
                                        value="1">
                                    <label class="form-check-label">New Arrival</label>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_best_seller"
                                        value="1">
                                    <label class="form-check-label">Best Seller</label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_trending" value="1">
                                    <label class="form-check-label">Trending Product</label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                        checked>

                                    <label class="form-check-label">
                                        Active Product
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>

                                    <input type="number" name="sort_order" class="form-control" value="0">
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </div>
@endsection
