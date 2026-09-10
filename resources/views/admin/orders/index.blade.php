@extends('layouts.admin')

@section('content')

    <div class="body d-flex py-3">

        <div class="container-xxl">

            {{-- Page Header --}}
            <div class="row align-items-center mb-4">

                <div class="col">

                    <h3 class="fw-bold mb-0">
                        Orders
                    </h3>

                    <small class="text-muted">
                        Manage customer orders, payment status and delivery progress.
                    </small>

                </div>

            </div>


            {{-- Alerts --}}
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


            {{-- Summary Cards --}}
            <div class="row g-3 mb-4 row-cols-1 row-cols-sm-2 row-cols-xl-4">


                {{-- Total Orders --}}
                <div class="col">

                    <div class="alert alert-primary mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-primary text-light">

                                <i class="fa-solid fa-bag-shopping fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Total Orders
                                </div>

                                <span class="small">
                                    {{ number_format($orders->total()) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Pending Orders --}}
                <div class="col">

                    <div class="alert alert-warning mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-warning text-light">

                                <i class="fa-solid fa-clock fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Pending Orders
                                </div>

                                <span class="small">
                                    {{ number_format($pendingOrders ?? 0) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Paid Orders --}}
                <div class="col">

                    <div class="alert alert-success mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-success text-light">

                                <i class="fa-solid fa-circle-check fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Paid Orders
                                </div>

                                <span class="small">
                                    {{ number_format($paidOrders ?? 0) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Total Sales --}}
                <div class="col">

                    <div class="alert alert-info mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-info text-light">

                                <i class="fa-solid fa-naira-sign fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Total Sales
                                </div>

                                <span class="small">

                                    ₦{{ number_format($totalSales ?? 0, 2) }}

                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Orders Table --}}
            <div class="card">

                <div
                    class="card-header py-3 bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <h6 class="m-0 fw-bold">
                        All Orders
                    </h6>


                    {{-- Filter --}}
                    <form method="GET" action="{{ route('admin.orders.index') }}" class="d-flex gap-2">

                        <select name="status" class="form-select form-select-sm">

                            <option value="">
                                All Status
                            </option>

                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>
                                Processing
                            </option>

                            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>
                                Shipped
                            </option>

                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>
                                Delivered
                            </option>

                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                        </select>


                        <button type="submit" class="btn btn-sm btn-primary">

                            <i class="fa-solid fa-filter me-1"></i>

                            Filter

                        </button>

                    </form>

                </div>


                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>
                                        Order
                                    </th>

                                    <th>
                                        Customer
                                    </th>

                                    <th>
                                        Amount
                                    </th>

                                    <th>
                                        Payment
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Date
                                    </th>

                                    <th class="text-end">
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse ($orders as $order)
                                    @php

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Order Status
                                        |--------------------------------------------------------------------------
                                        */

                                        $statusClass = match ($order->status) {
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary',
                                        };

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Payment Status
                                        |--------------------------------------------------------------------------
                                        */

                                        $paymentClass = match ($order->payment_status) {
                                            'paid' => 'success',
                                            'failed' => 'danger',
                                            'refunded' => 'info',
                                            'unpaid' => 'secondary',
                                            'pending' => 'warning',
                                            default => 'secondary',
                                        };

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Customer Name
                                        |--------------------------------------------------------------------------
                                        |
                                        | Prefer the order snapshot because this works
                                        | for both registered and guest customers.
                                        |
                                        */

                                        $customerName = trim(
                                            ($order->first_name ?? '') . ' ' . ($order->last_name ?? ''),
                                        );

                                        if (!$customerName) {
                                            $customerName = $order->user?->name ?? 'Guest Customer';
                                        }
                                    @endphp


                                    <tr>

                                        {{-- Order --}}
                                        <td>

                                            <strong>
                                                {{ $order->order_no ?? 'ORD-' . $order->id }}
                                            </strong>

                                            <br>

                                            <small class="text-muted">
                                                #{{ $order->id }}
                                            </small>

                                        </td>


                                        {{-- Customer --}}
                                        <td>

                                            <strong>
                                                {{ $customerName }}
                                            </strong>

                                            <br>

                                            <small class="text-muted">

                                                {{ $order->email ?? ($order->user?->email ?? 'No email') }}

                                            </small>

                                        </td>


                                        {{-- Amount --}}
                                        <td>

                                            <strong>

                                                ₦{{ number_format((float) ($order->total ?? 0), 2) }}

                                            </strong>

                                        </td>


                                        {{-- Payment --}}
                                        <td>

                                            <span class="badge bg-{{ $paymentClass }}">

                                                {{ ucfirst($order->payment_status ?? 'unpaid') }}

                                            </span>

                                        </td>


                                        {{-- Order Status --}}
                                        <td>

                                            <span class="badge bg-{{ $statusClass }}">

                                                {{ ucfirst($order->status ?? 'pending') }}

                                            </span>

                                        </td>


                                        {{-- Date --}}
                                        <td>

                                            {{ $order->created_at?->format('d M, Y') }}

                                            <br>

                                            <small class="text-muted">

                                                {{ $order->created_at?->format('h:i A') }}

                                            </small>

                                        </td>


                                        {{-- Action --}}
                                        <td class="text-end">

                                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                                class="btn btn-sm btn-outline-primary">

                                                <i class="fa-solid fa-eye me-1"></i>

                                                Attend

                                            </a>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="7" class="text-center py-5">

                                            <i class="fa-solid fa-box-open fs-2 text-muted mb-2">
                                            </i>

                                            <p class="mb-0 text-muted">
                                                No orders found.
                                            </p>

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    {{-- Pagination --}}
                    @if ($orders->hasPages())
                        <div class="mt-4">

                            {{ $orders->links() }}

                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

@endsection
