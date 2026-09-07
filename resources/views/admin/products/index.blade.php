@extends('layouts.admin')

@section('content')
<!-- Body: Product Management -->
<div class="body d-flex py-3">
    <div class="container-xxl">

        <div class="row align-items-center">
            <div class="border-0 mb-4">
                <div class="card-header py-3 no-bg bg-transparent d-flex align-items-center px-0 justify-content-between border-bottom flex-wrap">
                    <h3 class="fw-bold mb-0">Products</h3>

                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary py-2 px-4">
                        <i class="icofont-plus-circle me-2"></i>Add Product
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                        <h6 class="mb-0 fw-bold">Product List</h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Flags</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($products as $key => $product)
                                        <tr>
                                            <td>{{ $products->firstItem() + $key }}</td>

                                            <td>
                                                @if($product->main_image)
                                                    <img src="{{ asset($product->main_image) }}"
                                                         alt="{{ $product->name }}"
                                                         class="rounded"
                                                         width="55"
                                                         height="55"
                                                         style="object-fit: cover;">
                                                @else
                                                    <div class="avatar lg rounded bg-light d-flex align-items-center justify-content-center">
                                                        <i class="icofont-image text-muted fs-4"></i>
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    SKU: {{ $product->sku ?? 'N/A' }}
                                                </small>
                                            </td>

                                            <td>
                                                {{ $product->category?->name ?? 'N/A' }}
                                                @if($product->subcategory)
                                                    <br>
                                                    <small class="text-muted">{{ $product->subcategory->name }}</small>
                                                @endif
                                            </td>

                                            <td>
                                                <strong>₦{{ number_format($product->regular_price, 2) }}</strong>
                                                @if($product->sale_price)
                                                    <br>
                                                    <small class="text-success">
                                                        Sale: ₦{{ number_format($product->sale_price, 2) }}
                                                    </small>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="badge {{ $product->stock_status == 'in_stock' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucwords(str_replace('_', ' ', $product->stock_status)) }}
                                                </span>
                                                <br>
                                                <small class="text-muted">Qty: {{ $product->quantity }}</small>
                                            </td>

                                            <td>
                                                @if($product->is_featured)
                                                    <span class="badge bg-warning mb-1">Featured</span>
                                                @endif

                                                @if($product->is_new_arrival)
                                                    <span class="badge bg-info mb-1">New</span>
                                                @endif

                                                @if($product->is_best_seller)
                                                    <span class="badge bg-primary mb-1">Best</span>
                                                @endif

                                                @if($product->is_trending)
                                                    <span class="badge bg-secondary mb-1">Trending</span>
                                                @endif

                                                @if(!$product->is_featured && !$product->is_new_arrival && !$product->is_best_seller && !$product->is_trending)
                                                    <span class="text-muted">Normal</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($product->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>

                                            <td class="text-end">
                                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="icofont-edit text-success"></i>
                                                </a>

                                                <form action="{{ route('admin.products.destroy', $product->id) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                                                        <i class="icofont-ui-delete text-danger"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <h6 class="text-muted mb-2">No product found</h6>
                                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                                                    Add First Product
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $products->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection