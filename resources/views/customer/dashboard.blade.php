@extends('layouts.customer')

@section('CustomerContent')
    <div class="dashboard-page-content">

        {{-- Page Heading --}}
        <div class="row mb-9 align-items-center">

            <div class="col-lg-7 mb-6 mb-lg-0">
                <h2 class="fs-4 mb-2">
                    Welcome Back,
                    {{ optional($user->customerProfile)->first_name ?? $user->name }}
                </h2>

                <p class="mb-0 text-muted">
                    Manage your orders, saved addresses and shopping activity from your account.
                </p>
            </div>

            <div class="col-lg-5 d-flex flex-wrap justify-content-lg-end gap-3">

                <a href="{{ route('shop') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>
                    Continue Shopping
                </a>

                <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-box me-2"></i>
                    My Orders
                </a>

            </div>

        </div>


        {{-- Dashboard Statistics --}}
        <div class="row">

            {{-- Total Orders --}}
            <div class="col-sm-6 col-xxl-3 mb-7">
                <div class="card rounded-4 h-100">
                    <div class="card-body p-7">

                        <div class="d-flex align-items-center">

                            <div class="me-6">
                                <span
                                    class="square d-flex align-items-center justify-content-center
                                       fs-5 badge rounded-circle text-success bg-success-light"
                                    style="--square-size: 48px">

                                    <i class="fas fa-shopping-bag"></i>

                                </span>
                            </div>

                            <div>
                                <h6 class="mb-3 card-title">
                                    Total Orders
                                </h6>

                                <span class="fs-4 d-block fw-500 text-primary lh-12">
                                    {{ $totalOrders ?? 0 }}
                                </span>

                                <span class="fs-14px text-muted">
                                    Orders placed
                                </span>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            {{-- Pending Orders --}}
            <div class="col-sm-6 col-xxl-3 mb-7">
                <div class="card rounded-4 h-100">
                    <div class="card-body p-7">

                        <div class="d-flex align-items-center">

                            <div class="me-6">
                                <span
                                    class="square d-flex align-items-center justify-content-center
                                       fs-5 badge rounded-circle text-warning bg-warning-light"
                                    style="--square-size: 48px">

                                    <i class="fas fa-clock"></i>

                                </span>
                            </div>

                            <div>
                                <h6 class="mb-3 card-title">
                                    Pending Orders
                                </h6>

                                <span class="fs-4 d-block fw-500 text-primary lh-12">
                                    {{ $pendingOrders ?? 0 }}
                                </span>

                                <span class="fs-14px text-muted">
                                    Awaiting completion
                                </span>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            {{-- Wishlist --}}
            <div class="col-sm-6 col-xxl-3 mb-7">
                <div class="card rounded-4 h-100">
                    <div class="card-body p-7">

                        <div class="d-flex align-items-center">

                            <div class="me-6">
                                <span
                                    class="square d-flex align-items-center justify-content-center
                                       fs-5 badge rounded-circle text-danger bg-danger-light"
                                    style="--square-size: 48px">

                                    <i class="fas fa-heart"></i>

                                </span>
                            </div>

                            <div>
                                <h6 class="mb-3 card-title">
                                    Wishlist
                                </h6>

                                <span class="fs-4 d-block fw-500 text-primary lh-12">
                                    {{ $wishlistCount ?? 0 }}
                                </span>

                                <span class="fs-14px text-muted">
                                    Saved favourites
                                </span>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            {{-- Total Spent --}}
            <div class="col-sm-6 col-xxl-3 mb-7">
                <div class="card rounded-4 h-100">
                    <div class="card-body p-7">

                        <div class="d-flex align-items-center">

                            <div class="me-6">
                                <span
                                    class="square d-flex align-items-center justify-content-center
                                       fs-5 badge rounded-circle text-info bg-info-light"
                                    style="--square-size: 48px">

                                    <i class="fas fa-wallet"></i>

                                </span>
                            </div>

                            <div>
                                <h6 class="mb-3 card-title">
                                    Total Spent
                                </h6>

                                <span class="fs-4 d-block fw-500 text-primary lh-12">
                                    ₦{{ number_format($totalSpent ?? 0, 2) }}
                                </span>

                                <span class="fs-14px text-muted">
                                    Completed purchases
                                </span>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>


        {{-- Quick Actions --}}
        <div class="card rounded-4 p-7 mb-7">

            <div class="card-header bg-transparent px-0 pt-0 pb-6 border-0">

                <h4 class="card-title fs-18px mb-1">
                    Quick Actions
                </h4>

                <p class="text-muted fs-14px mb-0">
                    Quickly access the most important parts of your account.
                </p>

            </div>

            <div class="card-body px-0 py-0">

                <div class="d-flex flex-wrap gap-3">

                    <a href="{{ route('shop') }}" class="btn btn-primary">
                        <i class="fas fa-store me-2"></i>
                        Continue Shopping
                    </a>

                    <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-primary">

                        <i class="fas fa-shopping-bag me-2"></i>
                        View Orders
                    </a>

                    <a href="{{ route('customer.addresses.index') }}" class="btn btn-outline-primary">

                        <i class="fas fa-map-marker-alt me-2"></i>
                        Manage Addresses
                    </a>

                    <a href="" class="btn btn-outline-primary">

                        <i class="fas fa-user me-2"></i>
                        My Profile
                    </a>

                </div>

            </div>
        </div>


        {{-- Recent Orders --}}
        <div class="card mb-7 rounded-4 p-7">

            <div
                class="card-header bg-transparent px-0 pt-0 pb-7 border-0
                   d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div>
                    <h4 class="card-title fs-18px mb-1">
                        Recent Orders
                    </h4>

                    <p class="text-muted fs-14px mb-0">
                        Your most recent purchases.
                    </p>
                </div>

                <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-primary btn-sm">

                    View All Orders
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>

            </div>


            <div class="card-body px-0 pt-0 pb-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle table-nowrap mb-0 table-borderless">

                        <thead class="table-light">
                            <tr>
                                <th class="align-middle">Order No.</th>
                                <th class="align-middle">Date</th>
                                <th class="align-middle">Items</th>
                                <th class="align-middle">Payment</th>
                                <th class="align-middle">Status</th>
                                <th class="align-middle text-end">Amount</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($recentOrders as $order)
                                @php
                                    $statusClass = match (strtolower($order->status ?? 'pending')) {
                                        'completed', 'delivered' => 'badge-soft-success',
                                        'processing' => 'badge-soft-info',
                                        'shipped' => 'badge-soft-primary',
                                        'cancelled', 'failed' => 'badge-soft-danger',
                                        'refunded' => 'badge-soft-warning',
                                        default => 'badge-soft-warning',
                                    };
                                @endphp

                                <tr>

                                    <td>
                                        <span class="fw-semibold text-body-emphasis">
                                            {{ $order->order_no }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $order->created_at->format('d M Y') }}
                                    </td>

                                    <td>
                                        {{ $order->items->count() }}
                                        {{ $order->items->count() === 1 ? 'Item' : 'Items' }}
                                    </td>

                                    <td>
                                        {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}
                                    </td>

                                    <td>
                                        <span
                                            class="badge rounded-lg {{ $statusClass }}
                                               border-0 text-capitalize fs-12">

                                            {{ ucfirst($order->status ?? 'Pending') }}

                                        </span>
                                    </td>

                                    <td class="text-end fw-semibold text-body-emphasis">
                                        ₦{{ number_format($order->total ?? 0, 2) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center py-10">

                                        <div class="mb-4">
                                            <i class="fas fa-shopping-bag fs-2 text-muted"></i>
                                        </div>

                                        <h6 class="mb-2">
                                            No orders yet
                                        </h6>

                                        <p class="text-muted fs-14px mb-5">
                                            When you place an order, it will appear here.
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


        {{-- Saved Addresses --}}
        <div class="card mb-7 rounded-4 p-7">

            <div
                class="card-header bg-transparent px-0 pt-0 pb-7 border-0
                   d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div>
                    <h4 class="card-title fs-18px mb-1">
                        Saved Addresses
                    </h4>

                    <p class="text-muted fs-14px mb-0">
                        Addresses available for delivery and checkout.
                    </p>
                </div>

                <a href="{{ route('customer.addresses.index') }}" class="btn btn-outline-primary btn-sm">

                    Manage Addresses
                </a>

            </div>


            <div class="card-body px-0 pt-0 pb-0">

                @forelse($savedAddresses as $address)
                    <div
                        class="d-flex flex-column flex-md-row
                           justify-content-between align-items-md-center
                           py-5 border-bottom">

                        <div class="d-flex">

                            <div class="me-5">
                                <span
                                    class="square d-flex align-items-center justify-content-center
                                       rounded-circle bg-body-tertiary text-primary"
                                    style="--square-size: 44px">

                                    <i class="fas fa-map-marker-alt"></i>

                                </span>
                            </div>

                            <div>

                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">

                                    <h6 class="mb-0 fs-14px">
                                        Delivery Address
                                    </h6>

                                    @if ($address->is_default)
                                        <span class="badge badge-soft-success border-0 fs-11px">
                                            Default
                                        </span>
                                    @endif

                                </div>

                                <p class="text-muted fs-14px mb-0">

                                    {{ $address->street_address }}

                                    @if ($address->city)
                                        , {{ $address->city }}
                                    @endif

                                    @if (optional($address->state)->name)
                                        , {{ optional($address->state)->name }}
                                    @endif

                                    @if (optional($address->country)->name)
                                        , {{ optional($address->country)->name }}
                                    @endif

                                </p>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="text-center py-10">

                        <div class="mb-4">
                            <i class="fas fa-map-marker-alt fs-2 text-muted"></i>
                        </div>

                        <h6 class="mb-2">
                            No saved addresses
                        </h6>

                        <p class="text-muted fs-14px mb-5">
                            Add a delivery address to make checkout faster.
                        </p>

                        <a href="{{ route('customer.addresses.index') }}" class="btn btn-primary btn-sm">

                            Add Address
                        </a>

                    </div>
                @endforelse

            </div>

        </div>

    </div>
@endsection
