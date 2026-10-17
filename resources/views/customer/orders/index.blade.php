@extends('layouts.customer')

@section('CustomerContent')
    <div class="dashboard-page-content">

        {{-- Page Header --}}
        <div class="row mb-9 align-items-center justify-content-between">
            <div class="col-lg-6 mb-6 mb-lg-0">
                <h2 class="fs-4 mb-2">My Orders</h2>

                <p class="mb-0 text-muted">
                    View your retail and wholesale orders, payments and fulfilment progress.
                </p>
            </div>

            <div class="col-lg-6">
                <div class="d-flex justify-content-lg-end flex-wrap gap-3">
                    <a href="{{ route('wholesale.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-boxes me-2"></i>
                        Bulk Purchase
                    </a>

                    <a href="{{ route('shop') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>

        {{-- Order History --}}
        <div class="card mb-7 rounded-4 p-7">
            <div class="card-header bg-transparent px-0 pt-0 pb-7 border-0">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-8 mb-5 mb-md-0">
                        <h4 class="card-title fs-18px mb-1">
                            Order History
                        </h4>

                        <p class="text-muted fs-14px mb-0">
                            Each order is labelled by its saved purchase type.
                        </p>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex justify-content-md-end">
                            <span class="badge bg-body-tertiary text-body-emphasis px-4 py-3 fs-13px">
                                {{ number_format($orders->total()) }}
                                {{ $orders->total() === 1 ? 'Order' : 'Orders' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0 table-borderless">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Order No.</th>
                                <th scope="col">Order Type</th>
                                <th scope="col">Date</th>
                                <th scope="col">Items</th>
                                <th scope="col">Fulfilment</th>
                                <th scope="col">Payment</th>
                                <th scope="col">Payment Status</th>
                                <th scope="col">Order Status</th>
                                <th scope="col" class="text-end">Total</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($orders as $order)
                                @php
                                    $paymentStatus = strtolower($order->payment_status ?? 'unpaid');

                                    $paymentStatusClass = match ($paymentStatus) {
                                        'paid' => 'alert-success',
                                        'failed' => 'alert-danger',
                                        'refunded' => 'alert-info',
                                        'pending' => 'alert-warning',
                                        default => 'alert-secondary',
                                    };

                                    $orderStatus = strtolower($order->status ?? 'pending');

                                    $orderStatusClass = match ($orderStatus) {
                                        'delivered', 'picked_up', 'completed' => 'alert-success',
                                        'processing' => 'alert-info',
                                        'shipped' => 'alert-primary',
                                        'ready_for_pickup' => 'alert-warning',
                                        'cancelled', 'failed' => 'alert-danger',
                                        default => 'alert-warning',
                                    };

                                    $orderStatusLabel = ucwords(str_replace('_', ' ', $orderStatus));

                                    $isPickup = $order->delivery_method === 'pickup';

                                    $itemCount = isset($order->items_count)
                                        ? (int) $order->items_count
                                        : $order->items->count();

                                    $orderNumber = $order->order_no ?? 'ORD-' . $order->id;
                                @endphp

                                <tr>
                                    {{-- Order Number --}}
                                    <td>
                                        <a href="{{ route('customer.orders.show', $order) }}"
                                            class="fw-semibold text-primary text-decoration-none">
                                            {{ $orderNumber }}
                                        </a>
                                    </td>

                                    {{-- Saved Purchase Type --}}
                                    <td>
                                        <span
                                            class="badge rounded-pill {{ $order->purchase_type_badge_class }} py-3 px-4 fs-12">
                                            {{ $order->purchase_type_label }}
                                        </span>
                                    </td>

                                    {{-- Date --}}
                                    <td>
                                        {{ $order->created_at?->format('d M Y') ?? 'N/A' }}
                                    </td>

                                    {{-- Number of Order Lines --}}
                                    <td>
                                        {{ $itemCount }}
                                        {{ $itemCount === 1 ? 'Item' : 'Items' }}
                                    </td>

                                    {{-- Fulfilment --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $isPickup ? 'fa-solid fa-location-dot' : 'fa-solid fa-truck-fast' }} me-3 text-muted"
                                                aria-hidden="true"></i>

                                            <span>
                                                {{ $isPickup ? 'Pickup' : 'Shipping' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Payment Method --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="far fa-credit-card me-3 text-muted" aria-hidden="true"></i>

                                            <span>
                                                {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Payment Status --}}
                                    <td>
                                        <span
                                            class="badge rounded-pill alert {{ $paymentStatusClass }} py-3 px-4 mb-0 border-0 fs-12">
                                            {{ ucwords(str_replace('_', ' ', $paymentStatus)) }}
                                        </span>
                                    </td>

                                    {{-- Order Status --}}
                                    <td>
                                        <span
                                            class="badge rounded-pill alert {{ $orderStatusClass }} py-3 px-4 mb-0 border-0 fs-12">
                                            {{ $orderStatusLabel }}
                                        </span>
                                    </td>

                                    {{-- Saved Currency and Total --}}
                                    <td class="text-end fw-semibold text-body-emphasis">
                                        {{ $order->formatted_total }}
                                    </td>

                                    {{-- Action --}}
                                    <td class="text-center">
                                        <a href="{{ route('customer.orders.show', $order) }}"
                                            class="btn btn-primary btn-xs py-4 px-5 fs-13px"
                                            aria-label="View order {{ $orderNumber }}">
                                            <i class="far fa-eye me-2" aria-hidden="true"></i>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-10">
                                        <div class="mb-5">
                                            <span
                                                class="square d-inline-flex align-items-center justify-content-center rounded-circle bg-body-tertiary text-muted"
                                                style="--square-size: 64px">
                                                <i class="fas fa-shopping-bag fs-4" aria-hidden="true"></i>
                                            </span>
                                        </div>

                                        <h5 class="fs-6 mb-2">No orders yet</h5>

                                        <p class="text-muted fs-14px mb-5">
                                            Your retail and wholesale orders will appear here.
                                        </p>

                                        <div class="d-flex justify-content-center flex-wrap gap-3">
                                            <a href="{{ route('shop') }}" class="btn btn-primary btn-sm">
                                                Start Shopping
                                            </a>

                                            <a href="{{ route('wholesale.index') }}"
                                                class="btn btn-outline-primary btn-sm">
                                                Shop Bulk Products
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div class="mt-6 mb-4">
                {{ $orders->withQueryString()->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
@endsection
