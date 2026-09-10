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
                        Low Stock
                    </h3>

                    <small class="text-muted">
                        Products and variants that require stock attention.
                    </small>

                </div>

                <div class="col-auto d-flex gap-2">

                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">

                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Stock List

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
                SUMMARY
            ========================================================== --}}

            @php

                $simpleLowStockCount = isset($products) ? $products->count() : 0;

                $variantLowStockCount = isset($variants) ? $variants->count() : 0;

                $totalLowStock = $simpleLowStockCount + $variantLowStockCount;

                $outOfStockSimple = isset($products)
                    ? $products->filter(fn($product) => (int) ($product->quantity ?? 0) <= 0)->count()
                    : 0;

                $outOfStockVariants = isset($variants)
                    ? $variants->filter(fn($variant) => (int) ($variant->stock_quantity ?? 0) <= 0)->count()
                    : 0;

                $totalOutOfStock = $outOfStockSimple + $outOfStockVariants;
            @endphp


            <div class="row g-3 mb-4 row-cols-1 row-cols-sm-2 row-cols-xl-4">


                {{-- Total Low Stock --}}
                <div class="col">

                    <div class="alert alert-warning mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-warning text-light">

                                <i class="fa-solid fa-triangle-exclamation fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Low Stock Records
                                </div>

                                <span class="small">
                                    {{ number_format($totalLowStock) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Simple Products --}}
                <div class="col">

                    <div class="alert alert-primary mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-primary text-light">

                                <i class="fa-solid fa-box fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Products
                                </div>

                                <span class="small">
                                    {{ number_format($simpleLowStockCount) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Variants --}}
                <div class="col">

                    <div class="alert alert-info mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-info text-light">

                                <i class="fa-solid fa-layer-group fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Variants
                                </div>

                                <span class="small">
                                    {{ number_format($variantLowStockCount) }}
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
                                    {{ number_format($totalOutOfStock) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =========================================================
                SIMPLE PRODUCTS
            ========================================================== --}}

            <div class="card mb-4">

                <div class="card-header py-3 bg-transparent">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="m-0 fw-bold">
                                Low Stock Products
                            </h6>

                            <small class="text-muted">
                                Products without variants
                            </small>

                        </div>

                        <span class="badge bg-warning">

                            {{ number_format($simpleLowStockCount) }}

                        </span>

                    </div>

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
                                        SKU
                                    </th>

                                    <th>
                                        Current Stock
                                    </th>

                                    <th>
                                        Low Stock Level
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

                                        $stock = (int) ($product->quantity ?? 0);

                                        $threshold = (int) ($product->low_stock_alert ?? 0);

                                        if ($stock <= 0) {
                                            $statusClass = 'danger';
                                            $statusText = 'Out of Stock';
                                        } else {
                                            $statusClass = 'warning';
                                            $statusText = 'Low Stock';
                                        }
                                    @endphp


                                    <tr>

                                        <td>

                                            <strong>
                                                {{ $product->name }}
                                            </strong>

                                        </td>


                                        <td>

                                            <code>
                                                {{ $product->sku ?: 'N/A' }}
                                            </code>

                                        </td>


                                        <td>

                                            <strong class="text-{{ $statusClass }}">

                                                {{ number_format($stock) }}

                                            </strong>

                                        </td>


                                        <td>

                                            {{ number_format($threshold) }}

                                        </td>


                                        <td>

                                            <span class="badge bg-{{ $statusClass }}">

                                                {{ $statusText }}

                                            </span>

                                        </td>


                                        <td class="text-end">

                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal" data-bs-target="#adjustStockModal"
                                                data-product-id="{{ $product->id }}" data-variant-id=""
                                                data-product-name="{{ $product->name }}" data-variant-name=""
                                                data-current-stock="{{ $stock }}">

                                                <i class="fa-solid fa-plus-minus me-1"></i>

                                                Adjust Stock

                                            </button>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="6" class="text-center py-5">

                                            <i class="fa-solid fa-circle-check fs-2 text-success mb-3"></i>

                                            <p class="mb-0 text-muted">

                                                No simple products are currently low on stock.

                                            </p>

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- =========================================================
                VARIANT PRODUCTS
            ========================================================== --}}

            <div class="card">

                <div class="card-header py-3 bg-transparent">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="m-0 fw-bold">
                                Low Stock Variants
                            </h6>

                            <small class="text-muted">
                                Individual product variants requiring attention
                            </small>

                        </div>


                        <span class="badge bg-warning">

                            {{ number_format($variantLowStockCount) }}

                        </span>

                    </div>

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
                                        Current Stock
                                    </th>

                                    <th>
                                        Low Stock Level
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

                                @forelse ($variants as $variant)
                                    @php

                                        $stock = (int) ($variant->stock_quantity ?? 0);

                                        $threshold = (int) ($variant->low_stock_threshold ?? 0);

                                        if ($stock <= 0) {
                                            $statusClass = 'danger';
                                            $statusText = 'Out of Stock';
                                        } else {
                                            $statusClass = 'warning';
                                            $statusText = 'Low Stock';
                                        }

                                        $variantName = $variant->name ?? ($variant->size ?? 'Variant #' . $variant->id);
                                    @endphp


                                    <tr>

                                        {{-- Product --}}
                                        <td>

                                            <strong>

                                                {{ $variant->product?->name ?? 'Product Removed' }}

                                            </strong>

                                        </td>


                                        {{-- Variant --}}
                                        <td>

                                            <span class="badge bg-light text-dark border">

                                                {{ $variantName }}

                                            </span>

                                        </td>


                                        {{-- SKU --}}
                                        <td>

                                            <code>

                                                {{ $variant->sku ?: $variant->product?->sku ?: 'N/A' }}

                                            </code>

                                        </td>


                                        {{-- Stock --}}
                                        <td>

                                            <strong class="text-{{ $statusClass }}">

                                                {{ number_format($stock) }}

                                            </strong>

                                        </td>


                                        {{-- Threshold --}}
                                        <td>

                                            {{ number_format($threshold) }}

                                        </td>


                                        {{-- Status --}}
                                        <td>

                                            <span class="badge bg-{{ $statusClass }}">

                                                {{ $statusText }}

                                            </span>

                                        </td>


                                        {{-- Action --}}
                                        <td class="text-end">

                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal" data-bs-target="#adjustStockModal"
                                                data-product-id="{{ $variant->product_id }}"
                                                data-variant-id="{{ $variant->id }}"
                                                data-product-name="{{ $variant->product?->name ?? 'Product' }}"
                                                data-variant-name="{{ $variantName }}"
                                                data-current-stock="{{ $stock }}">

                                                <i class="fa-solid fa-plus-minus me-1"></i>

                                                Adjust Stock

                                            </button>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="7" class="text-center py-5">

                                            <i class="fa-solid fa-circle-check fs-2 text-success mb-3"></i>

                                            <p class="mb-0 text-muted">

                                                No product variants are currently low on stock.

                                            </p>

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

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
                                Adjustment
                            </label>

                            <input type="number" name="adjustment" class="form-control" placeholder="Example: 10 or -2"
                                required>

                            <small class="text-muted">

                                Positive values add stock.
                                Negative values reduce stock.

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
        STOCK MODAL SCRIPT
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

                    const button = event.relatedTarget;

                    if (!button) {
                        return;
                    }


                    const productId =
                        button.getAttribute('data-product-id');

                    const variantId =
                        button.getAttribute('data-variant-id');

                    const productName =
                        button.getAttribute('data-product-name');

                    const variantName =
                        button.getAttribute('data-variant-name');

                    const currentStock =
                        button.getAttribute('data-current-stock');


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


                    variantWrapper.style.display =
                        variantId ?
                        'block' :
                        'none';

                }
            );

        });
    </script>

@endsection
