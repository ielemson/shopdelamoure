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
                        Stock Movements
                    </h3>

                    <small class="text-muted">
                        Review inventory sales, restocks and manual stock adjustments.
                    </small>

                </div>

                <div class="col-auto d-flex gap-2">

                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">

                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Stock List

                    </a>

                    <a href="{{ route('admin.inventory.low-stock') }}" class="btn btn-outline-warning">

                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        Low Stock

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


            {{-- =========================================================
                FILTER
            ========================================================== --}}

            <div class="card mb-4">

                <div class="card-body">

                    <form method="GET" action="{{ route('admin.inventory.movements') }}" class="row g-3 align-items-end">

                        <div class="col-md-4">

                            <label class="form-label">
                                Movement Type
                            </label>

                            <select name="type" class="form-select">

                                <option value="">
                                    All Movements
                                </option>

                                <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>
                                    Sale
                                </option>

                                <option value="restock" {{ request('type') === 'restock' ? 'selected' : '' }}>
                                    Restock
                                </option>

                                <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>
                                    Adjustment
                                </option>

                                <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>
                                    Return
                                </option>

                                <option value="cancellation" {{ request('type') === 'cancellation' ? 'selected' : '' }}>
                                    Cancellation
                                </option>

                            </select>

                        </div>


                        <div class="col-md-auto">

                            <button type="submit" class="btn btn-primary">

                                <i class="fa-solid fa-filter me-1"></i>
                                Filter

                            </button>

                        </div>


                        @if (request()->filled('type'))
                            <div class="col-md-auto">

                                <a href="{{ route('admin.inventory.movements') }}" class="btn btn-outline-secondary">

                                    Clear

                                </a>

                            </div>
                        @endif

                    </form>

                </div>

            </div>


            {{-- =========================================================
                MOVEMENTS TABLE
            ========================================================== --}}

            <div class="card">

                <div class="card-header py-3 bg-transparent">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="m-0 fw-bold">
                                Inventory History
                            </h6>

                            <small class="text-muted">
                                Every stock-changing activity is recorded here.
                            </small>

                        </div>

                        <span class="badge bg-secondary">

                            {{ number_format($movements->total()) }}

                        </span>

                    </div>

                </div>


                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Product
                                    </th>

                                    <th>
                                        Variant
                                    </th>

                                    <th>
                                        Type
                                    </th>

                                    <th>
                                        Change
                                    </th>

                                    <th>
                                        Before
                                    </th>

                                    <th>
                                        After
                                    </th>

                                    <th>
                                        Order
                                    </th>

                                    <th>
                                        By
                                    </th>

                                    <th>
                                        Note
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse ($movements as $movement)
                                    @php

                                        $typeClass = match ($movement->type) {
                                            'sale' => 'danger',
                                            'restock' => 'success',
                                            'adjustment' => 'warning',
                                            'return' => 'info',
                                            'cancellation' => 'secondary',
                                            default => 'secondary',
                                        };

                                        $quantity = (int) ($movement->quantity ?? 0);
                                    @endphp


                                    <tr>

                                        {{-- Date --}}
                                        <td>

                                            {{ $movement->created_at?->format('d M, Y') }}

                                            <br>

                                            <small class="text-muted">

                                                {{ $movement->created_at?->format('h:i A') }}

                                            </small>

                                        </td>


                                        {{-- Product --}}
                                        <td>

                                            <strong>

                                                {{ $movement->product?->name ?? 'Product Removed' }}

                                            </strong>

                                        </td>


                                        {{-- Variant --}}
                                        <td>

                                            @if ($movement->variant)
                                                <span class="badge bg-light text-dark border">

                                                    {{ $movement->variant->name ?? ($movement->variant->size ?? 'Variant #' . $movement->variant->id) }}

                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    —
                                                </span>
                                            @endif

                                        </td>


                                        {{-- Type --}}
                                        <td>

                                            <span class="badge bg-{{ $typeClass }}">

                                                {{ ucfirst($movement->type ?? 'Unknown') }}

                                            </span>

                                        </td>


                                        {{-- Quantity Change --}}
                                        <td>

                                            @if ($quantity > 0)
                                                <strong class="text-success">
                                                    +{{ number_format($quantity) }}
                                                </strong>
                                            @elseif ($quantity < 0)
                                                <strong class="text-danger">
                                                    {{ number_format($quantity) }}
                                                </strong>
                                            @else
                                                <span class="text-muted">
                                                    0
                                                </span>
                                            @endif

                                        </td>


                                        {{-- Before --}}
                                        <td>

                                            {{ number_format((int) ($movement->quantity_before ?? 0)) }}

                                        </td>


                                        {{-- After --}}
                                        <td>

                                            <strong>

                                                {{ number_format((int) ($movement->quantity_after ?? 0)) }}

                                            </strong>

                                        </td>


                                        {{-- Order --}}
                                        <td>

                                            @if ($movement->order)
                                                <a href="{{ route('admin.orders.show', $movement->order->id) }}"
                                                    class="text-decoration-none">

                                                    {{ $movement->order->order_no ?? 'ORD-' . $movement->order->id }}

                                                </a>
                                            @else
                                                <span class="text-muted">
                                                    —
                                                </span>
                                            @endif

                                        </td>


                                        {{-- Created By --}}
                                        <td>

                                            @if ($movement->creator)
                                                {{ $movement->creator->name }}
                                            @elseif ($movement->type === 'sale')
                                                <span class="text-muted">
                                                    System
                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    —
                                                </span>
                                            @endif

                                        </td>


                                        {{-- Note --}}
                                        <td style="min-width:220px;">

                                            {{ $movement->note ?: '—' }}

                                            @if ($movement->reference)
                                                <br>

                                                <small class="text-muted">

                                                    Ref:
                                                    {{ $movement->reference }}

                                                </small>
                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="10" class="text-center py-5">

                                            <i class="fa-solid fa-clock-rotate-left fs-2 text-muted mb-3"></i>

                                            <p class="mb-1 fw-semibold">
                                                No stock movements yet
                                            </p>

                                            <p class="mb-0 text-muted">

                                                Sales, restocks and manual adjustments
                                                will appear here.

                                            </p>

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    {{-- =====================================================
                        PAGINATION
                    ====================================================== --}}

                    @if ($movements->hasPages())
                        <div class="mt-4">

                            {{ $movements->links() }}

                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

@endsection
