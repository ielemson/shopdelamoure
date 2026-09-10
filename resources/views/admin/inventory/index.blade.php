@extends('layouts.admin')

@section('content')

    <div class="body d-flex py-3">

        <div class="container-xxl">

            {{-- =========================================================
                PAGE HEADER
            ========================================================== --}}

            <div class="row align-items-center mb-4">

                <div class="col">

                    <h3 class="fw-bold mb-0">
                        Inventory
                    </h3>

                    <small class="text-muted">
                        Monitor stock levels, product variants and inventory availability.
                    </small>

                </div>

                <div class="col-auto d-flex gap-2">

                    <a href="{{ route('admin.inventory.low-stock') }}" class="btn btn-outline-warning">

                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        Low Stock

                    </a>

                    <a href="{{ route('admin.inventory.movements') }}" class="btn btn-outline-primary">

                        <i class="fa-solid fa-clock-rotate-left me-1"></i>
                        Stock Movements

                    </a>

                </div>

            </div>


            {{-- =========================================================
                ALERTS
            ========================================================== --}}

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">

                    {{ session('success') }}

                    <button type="button" class="btn-close" data-bs-dismiss="alert">
                    </button>

                </div>
            @endif


            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">

                    {{ session('error') }}

                    <button type="button" class="btn-close" data-bs-dismiss="alert">
                    </button>

                </div>
            @endif


            @if ($errors->any())
                <div class="alert alert-danger">

                    <strong>
                        Please correct the following:
                    </strong>

                    <ul class="mb-0 mt-2">

                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach

                    </ul>

                </div>
            @endif


            {{-- =========================================================
                SUMMARY CARDS
            ========================================================== --}}

            <div class="row g-3 mb-4 row-cols-1 row-cols-sm-2 row-cols-xl-4">


                {{-- Products --}}
                <div class="col">

                    <div class="alert alert-primary mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-primary text-light">

                                <i class="fa-solid fa-boxes-stacked fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Products
                                </div>

                                <span class="small">
                                    {{ number_format($products->total()) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Total Units --}}
                <div class="col">

                    <div class="alert alert-success mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-success text-light">

                                <i class="fa-solid fa-cubes fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Total Units
                                </div>

                                <span class="small">
                                    {{ number_format($totalUnits ?? 0) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Out Of Stock --}}
                <div class="col">

                    <div class="alert alert-danger mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-danger text-light">

                                <i class="fa-solid fa-ban fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Out of Stock
                                </div>

                                <span class="small">
                                    {{ number_format($outOfStock ?? 0) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Current Page --}}
                <div class="col">

                    <div class="alert alert-info mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-info text-light">

                                <i class="fa-solid fa-layer-group fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Showing
                                </div>

                                <span class="small">

                                    {{ number_format($products->count()) }}
                                    items

                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =========================================================
                INVENTORY TABLE
            ========================================================== --}}

            <div class="card">

                <div
                    class="card-header py-3 bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-3">

                    <div>

                        <h6 class="m-0 fw-bold">
                            Stock List
                        </h6>

                        <small class="text-muted">
                            Simple products and product variants
                        </small>

                    </div>


                    {{-- Search --}}
                    <form method="GET" action="{{ route('admin.inventory.index') }}" class="d-flex gap-2">

                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control form-control-sm" placeholder="Search product or SKU...">

                        <button type="submit" class="btn btn-sm btn-primary">

                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                            Search

                        </button>


                        @if (request()->filled('search'))
                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline-secondary">

                                Clear

                            </a>
                        @endif

                    </form>

                </div>


                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>
                                        Product
                                    </th>

                                    <th>
                                        Variant
                                    </th>

                                    <th>
                                        SKU
                                    </th>

                                    <th>
                                        Stock
                                    </th>

                                    <th>
                                        Threshold
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th class="text-end">
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse ($products as $product)
                                    @php

                                        $hasVariants =
                                            (bool) $product->has_variants && $product->variants->isNotEmpty();
                                    @endphp


                                    {{-- =================================================
                                        PRODUCT WITH VARIANTS
                                    ================================================== --}}

                                    @if ($hasVariants)
                                        @foreach ($product->variants as $variant)
                                            @php

                                                $stock = (int) ($variant->stock_quantity ?? 0);

                                                $threshold = (int) ($variant->low_stock_threshold ?? 0);

                                                if (
                                                    ($variant->stock_status ?? null) === 'out_of_stock' ||
                                                    $stock <= 0
                                                ) {
                                                    $stockClass = 'danger';
                                                    $stockText = 'Out of Stock';
                                                } elseif ($stock <= $threshold) {
                                                    $stockClass = 'warning';
                                                    $stockText = 'Low Stock';
                                                } else {
                                                    $stockClass = 'success';
                                                    $stockText = 'In Stock';
                                                }
                                            @endphp


                                            <tr>

                                                {{-- Product --}}
                                                <td>

                                                    <strong>
                                                        {{ $product->name }}
                                                    </strong>

                                                </td>


                                                {{-- Variant --}}
                                                <td>

                                                    <span class="badge bg-light text-dark border">

                                                        {{ $variant->name ?? ($variant->size ?? 'Variant #' . $variant->id) }}

                                                    </span>

                                                </td>


                                                {{-- SKU --}}
                                                <td>

                                                    <code>

                                                        {{ $variant->sku ?: $product->sku ?: 'N/A' }}

                                                    </code>

                                                </td>


                                                {{-- Stock --}}
                                                <td>

                                                    <strong>
                                                        {{ number_format($stock) }}
                                                    </strong>

                                                </td>


                                                {{-- Threshold --}}
                                                <td>

                                                    {{ number_format($threshold) }}

                                                </td>


                                                {{-- Status --}}
                                                <td>

                                                    <span class="badge bg-{{ $stockClass }}">

                                                        {{ $stockText }}

                                                    </span>

                                                </td>


                                                {{-- Action --}}
                                                <td class="text-end">

                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal" data-bs-target="#adjustStockModal"
                                                        data-product-id="{{ $product->id }}"
                                                        data-variant-id="{{ $variant->id }}"
                                                        data-product-name="{{ $product->name }}"
                                                        data-variant-name="{{ $variant->name ?? ($variant->size ?? 'Variant #' . $variant->id) }}"
                                                        data-current-stock="{{ $stock }}">

                                                        <i class="fa-solid fa-pen-to-square me-1"></i>

                                                        Adjust

                                                    </button>

                                                </td>

                                            </tr>
                                        @endforeach


                                        {{-- =================================================
                                        SIMPLE PRODUCT
                                    ================================================== --}}
                                    @else
                                        @php

                                            $stock = (int) ($product->quantity ?? 0);

                                            $threshold = (int) ($product->low_stock_alert ?? 0);

                                            if (($product->stock_status ?? null) === 'out_of_stock' || $stock <= 0) {
                                                $stockClass = 'danger';
                                                $stockText = 'Out of Stock';
                                            } elseif ($stock <= $threshold) {
                                                $stockClass = 'warning';
                                                $stockText = 'Low Stock';
                                            } else {
                                                $stockClass = 'success';
                                                $stockText = 'In Stock';
                                            }
                                        @endphp


                                        <tr>

                                            {{-- Product --}}
                                            <td>

                                                <strong>
                                                    {{ $product->name }}
                                                </strong>

                                            </td>


                                            {{-- Variant --}}
                                            <td>

                                                <span class="text-muted">
                                                    —
                                                </span>

                                            </td>


                                            {{-- SKU --}}
                                            <td>

                                                <code>
                                                    {{ $product->sku ?: 'N/A' }}
                                                </code>

                                            </td>


                                            {{-- Stock --}}
                                            <td>

                                                <strong>
                                                    {{ number_format($stock) }}
                                                </strong>

                                            </td>


                                            {{-- Threshold --}}
                                            <td>

                                                {{ number_format($threshold) }}

                                            </td>


                                            {{-- Status --}}
                                            <td>

                                                <span class="badge bg-{{ $stockClass }}">

                                                    {{ $stockText }}

                                                </span>

                                            </td>


                                            {{-- Action --}}
                                            <td class="text-end">

                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#adjustStockModal"
                                                    data-product-id="{{ $product->id }}" data-variant-id=""
                                                    data-product-name="{{ $product->name }}" data-variant-name=""
                                                    data-current-stock="{{ $stock }}">

                                                    <i class="fa-solid fa-pen-to-square me-1"></i>

                                                    Adjust

                                                </button>

                                            </td>

                                        </tr>
                                    @endif

                                @empty

                                    <tr>

                                        <td colspan="7" class="text-center py-5">

                                            <i class="fa-solid fa-box-open fs-2 text-muted mb-2"></i>

                                            <p class="mb-0 text-muted">
                                                No inventory records found.
                                            </p>

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    {{-- Pagination --}}
                    @if ($products->hasPages())
                        <div class="mt-4">

                            {{ $products->links() }}

                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- =============================================================
        STOCK ADJUSTMENT MODAL
    ============================================================== --}}

    <div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form method="POST" action="{{ route('admin.inventory.adjust') }}">

                    @csrf


                    <div class="modal-header">

                        <h5 class="modal-title">
                            Adjust Stock
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body">


                        <input type="hidden" name="product_id" id="inventoryProductId">


                        <input type="hidden" name="variant_id" id="inventoryVariantId">


                        {{-- Product --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Product
                            </label>

                            <input type="text" id="inventoryProductName" class="form-control" readonly>

                        </div>


                        {{-- Variant --}}
                        <div class="mb-3" id="inventoryVariantWrapper">

                            <label class="form-label">
                                Variant
                            </label>

                            <input type="text" id="inventoryVariantName" class="form-control" readonly>

                        </div>


                        {{-- Current Stock --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Current Stock
                            </label>

                            <input type="text" id="inventoryCurrentStock" class="form-control" readonly>

                        </div>


                        {{-- Adjustment --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Stock Adjustment
                            </label>

                            <input type="number" name="adjustment" class="form-control" required
                                placeholder="Example: 10 or -5">

                            <small class="text-muted">

                                Use a positive number to add stock and a
                                negative number to reduce stock.

                            </small>

                        </div>


                        {{-- Note --}}
                        <div class="mb-0">

                            <label class="form-label">
                                Reason / Note
                            </label>

                            <textarea name="note" class="form-control" rows="3" maxlength="1000" required
                                placeholder="Example: New stock received"></textarea>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                            Cancel

                        </button>


                        <button type="submit" class="btn btn-primary">

                            <i class="fa-solid fa-floppy-disk me-1"></i>

                            Update Stock

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- =============================================================
        MODAL DATA
    ============================================================== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal =
                document.getElementById('adjustStockModal');

            if (!modal) {
                return;
            }


            modal.addEventListener(
                'show.bs.modal',
                function(event) {

                    const button =
                        event.relatedTarget;


                    const productId =
                        button.getAttribute(
                            'data-product-id'
                        );


                    const variantId =
                        button.getAttribute(
                            'data-variant-id'
                        );


                    const productName =
                        button.getAttribute(
                            'data-product-name'
                        );


                    const variantName =
                        button.getAttribute(
                            'data-variant-name'
                        );


                    const currentStock =
                        button.getAttribute(
                            'data-current-stock'
                        );


                    document.getElementById(
                        'inventoryProductId'
                    ).value = productId || '';


                    document.getElementById(
                        'inventoryVariantId'
                    ).value = variantId || '';


                    document.getElementById(
                        'inventoryProductName'
                    ).value = productName || '';


                    document.getElementById(
                        'inventoryVariantName'
                    ).value = variantName || '';


                    document.getElementById(
                        'inventoryCurrentStock'
                    ).value = currentStock || 0;


                    const variantWrapper =
                        document.getElementById(
                            'inventoryVariantWrapper'
                        );


                    if (variantId) {

                        variantWrapper.style.display =
                            'block';

                    } else {

                        variantWrapper.style.display =
                            'none';

                    }

                }
            );

        });
    </script>

@endsection
