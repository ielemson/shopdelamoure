@extends('layouts.customer')

@section('CustomerContent')
    <div class="dashboard-page-content">

        {{-- Page Header --}}
        <div class="row mb-9 align-items-center justify-content-between">

            <div class="col-sm-7 mb-6 mb-sm-0">
                <h2 class="fs-4 mb-2">
                    My Orders
                </h2>

                <p class="mb-0 text-muted">
                    View your order history, payment status and delivery progress.
                </p>
            </div>

            <div class="col-sm-5 d-flex justify-content-sm-end">

                <a href="{{ route('shop') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>
                    Continue Shopping
                </a>

            </div>

        </div>


        {{-- Orders Card --}}
        <div class="card mb-7 rounded-4 p-7">

            {{-- Card Header --}}
            <div class="card-header bg-transparent px-0 pt-0 pb-7 border-0">

                <div class="row align-items-center justify-content-between">

                    <div class="col-md-6 col-12 mb-5 mb-md-0">
                        <h4 class="card-title fs-18px mb-1">
                            Order History
                        </h4>

                        <p class="text-muted fs-14px mb-0">
                            All orders placed from your Delamoure account.
                        </p>
                    </div>

                    <div class="col-md-6 col-12">

                        <div class="d-flex justify-content-md-end">

                            <span class="badge bg-body-tertiary text-body-emphasis px-4 py-3 fs-13px">

                                {{ $orders->total() }}
                                {{ $orders->total() === 1 ? 'Order' : 'Orders' }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Orders Table --}}
            <div class="card-body px-0 pt-0 pb-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle table-nowrap mb-0 table-borderless">

                        <thead class="table-light">

                            <tr>

                                <th class="align-middle">
                                    Order No.
                                </th>

                                <th class="align-middle">
                                    Date
                                </th>

                                <th class="align-middle">
                                    Items
                                </th>

                                <th class="align-middle">
                                    Payment Method
                                </th>

                                <th class="align-middle">
                                    Payment Status
                                </th>

                                <th class="align-middle">
                                    Order Status
                                </th>

                                <th class="align-middle text-end">
                                    Total
                                </th>

                                <th class="align-middle text-center">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($orders as $order)
                                @php

                                    $paymentStatusClass = match (strtolower($order->payment_status ?? 'pending')) {
                                        'paid' => 'alert-success',
                                        'failed' => 'alert-danger',
                                        'refunded' => 'alert-warning',
                                        default => 'alert-warning',
                                    };

                                    $orderStatusClass = match (strtolower($order->status ?? 'pending')) {
                                        'completed', 'delivered' => 'alert-success',
                                        'processing' => 'alert-info',
                                        'shipped' => 'alert-primary',
                                        'cancelled', 'failed' => 'alert-danger',
                                        default => 'alert-warning',
                                    };

                                @endphp


                                <tr>

                                    {{-- Order Number --}}
                                    <td>

                                        <a href="{{ route('customer.orders.show', $order) }}"
                                            class="fw-semibold text-primary text-decoration-none">

                                            {{ $order->order_no }}

                                        </a>

                                    </td>


                                    {{-- Date --}}
                                    <td>
                                        {{ $order->created_at->format('d M Y') }}
                                    </td>


                                    {{-- Items --}}
                                    <td>

                                        {{ $order->items->count() }}

                                        {{ $order->items->count() === 1 ? 'Item' : 'Items' }}

                                    </td>


                                    {{-- Payment Method --}}
                                    <td>

                                        <div class="d-flex align-items-center">

                                            <i class="far fa-credit-card me-3 text-muted"></i>

                                            <span>

                                                {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}

                                            </span>

                                        </div>

                                    </td>


                                    {{-- Payment Status --}}
                                    <td>

                                        <span
                                            class="badge rounded-pill alert {{ $paymentStatusClass }}
                                               py-3 px-4 mb-0 border-0 text-capitalize fs-12">

                                            {{ ucfirst($order->payment_status ?? 'Pending') }}

                                        </span>

                                    </td>


                                    {{-- Order Status --}}
                                    <td>

                                        <span
                                            class="badge rounded-pill alert {{ $orderStatusClass }}
                                               py-3 px-4 mb-0 border-0 text-capitalize fs-12">

                                            {{ ucfirst($order->status ?? 'Pending') }}

                                        </span>

                                    </td>


                                    {{-- Total --}}
                                    <td class="text-end fw-semibold text-body-emphasis">

                                        ₦{{ number_format($order->total ?? 0, 2) }}

                                    </td>


                                    {{-- Action --}}
                                    <td class="text-center">

                                        <a href="{{ route('customer.orders.show', $order) }}"
                                            class="btn btn-primary btn-xs py-4 px-5 fs-13px">

                                            <i class="far fa-eye me-2"></i>
                                            View

                                        </a>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td colspan="8" class="text-center py-10">

                                        <div class="mb-5">

                                            <span
                                                class="square d-inline-flex align-items-center justify-content-center
                                                   rounded-circle bg-body-tertiary text-muted"
                                                style="--square-size: 64px">

                                                <i class="fas fa-shopping-bag fs-4"></i>

                                            </span>

                                        </div>

                                        <h5 class="fs-6 mb-2">
                                            No orders yet
                                        </h5>

                                        <p class="text-muted fs-14px mb-5">
                                            You have not placed any orders yet.
                                        </p>

                                        <a href="{{ route('shop') }}" class="btn btn-primary btn-sm">

                                            Start Shopping

                                        </a>

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

                {{ $orders->onEachSide(1)->links() }}

            </div>
        @endif

    </div>
@endsection
