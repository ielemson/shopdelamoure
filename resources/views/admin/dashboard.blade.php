@extends('layouts.admin')

@section('content')
<!-- Body: Ecommerce Admin Overview -->
<div class="body d-flex py-3">
    <div class="container-xxl">

       <div class="body d-flex py-3">
    <div class="container-xxl">

        <!-- Top Summary Cards -->
        <div class="row g-3 mb-3 row-cols-1 row-cols-sm-2 row-cols-xl-4">

            <div class="col">
                <div class="alert-success alert mb-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar rounded no-thumbnail bg-success text-light">
                            <i class="fa-solid fa-naira-sign fa-lg"></i>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="h6 mb-0">Total Sales</div>
                            <span class="small">₦{{ number_format($totalSales, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="alert-danger alert mb-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar rounded no-thumbnail bg-danger text-light">
                            <i class="fa-solid fa-clock fa-lg"></i>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="h6 mb-0">Pending Orders</div>
                            <span class="small">{{ number_format($pendingOrders) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="alert-warning alert mb-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar rounded no-thumbnail bg-warning text-light">
                            <i class="fa-solid fa-box fa-lg"></i>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="h6 mb-0">Total Products</div>
                            <span class="small">{{ number_format($totalProducts) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="alert-info alert mb-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar rounded no-thumbnail bg-info text-light">
                            <i class="fa-solid fa-users fa-lg"></i>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="h6 mb-0">Customers</div>
                            <span class="small">{{ number_format($totalCustomers) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Period Summary -->
        <div class="row g-3">
            <div class="col-lg-12">
                <div class="row g-1 g-sm-3 mb-3 row-deck">

                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                        <div class="card">
                            <div class="card-body py-xl-4 py-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted">Total Orders</span>
                                    <div><span class="fs-6 fw-bold">{{ number_format($totalOrders) }}</span></div>
                                </div>
                                <i class="fa-solid fa-cart-shopping fs-3 text-primary"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                        <div class="card">
                            <div class="card-body py-xl-4 py-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted">Pending Orders</span>
                                    <div><span class="fs-6 fw-bold">{{ number_format($pendingOrders) }}</span></div>
                                </div>
                                <i class="fa-solid fa-hourglass-half fs-3 text-warning"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                        <div class="card">
                            <div class="card-body py-xl-4 py-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted">Paid Sales</span>
                                    <div><span class="fs-6 fw-bold">₦{{ number_format($totalSales, 2) }}</span></div>
                                </div>
                                <i class="fa-solid fa-money-bill-wave fs-3 text-success"></i>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                        <h6 class="m-0 fw-bold">Recent Orders</h6>

                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">
                            View All Orders
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Payment</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <strong>{{ $order->order_number ?? 'ORD-'.$order->id }}</strong>
                                            </td>

                                            <td>
                                                {{ $order->user->name ?? 'Guest Customer' }}
                                            </td>

                                            <td>
                                                <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($order->payment_status ?? 'pending') }}
                                                </span>
                                            </td>

                                            <td>
                                                ₦{{ number_format($order->total ?? 0, 2) }}
                                            </td>

                                            <td>
                                                @php
                                                    $statusClass = match($order->status) {
                                                        'pending' => 'warning',
                                                        'processing' => 'info',
                                                        'shipped' => 'primary',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger',
                                                        default => 'secondary',
                                                    };
                                                @endphp

                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ ucfirst($order->status ?? 'pending') }}
                                                </span>
                                            </td>

                                            <td>{{ $order->created_at->format('d M, Y') }}</td>

                                            <td class="text-end">
                                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                                    Attend
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                No recent orders found.
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

    </div>
</div>

    </div>
</div>
@endsection