@extends('layouts.admin')

@section('content')

@php
    $statusClass = match($order->status) {
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        default => 'secondary',
    };

    $paymentClass = match($order->payment_status) {
        'paid' => 'success',
        'failed' => 'danger',
        'refunded' => 'info',
        default => 'warning',
    };
@endphp

<div class="body d-flex py-3">
    <div class="container-xxl">

        <div class="row align-items-center mb-4">
            <div class="col">
                <h3 class="fw-bold mb-0">
                    Order Details
                </h3>
                <small class="text-muted">
                    {{ $order->order_number ?? 'ORD-'.$order->id }}
                </small>
            </div>

            <div class="col-auto">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-3 mb-3 row-cols-1 row-cols-sm-2 row-cols-xl-4">

            <div class="col">
                <div class="alert alert-primary mb-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar rounded no-thumbnail bg-primary text-light">
                            <i class="fa-solid fa-bag-shopping fa-lg"></i>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="h6 mb-0">Order ID</div>
                            <span class="small">{{ $order->order_number ?? 'ORD-'.$order->id }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="alert alert-info mb-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar rounded no-thumbnail bg-info text-light">
                            <i class="fa-solid fa-naira-sign fa-lg"></i>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="h6 mb-0">Total Amount</div>
                            <span class="small">₦{{ number_format($order->total ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="alert alert-{{ $paymentClass }} mb-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar rounded no-thumbnail bg-{{ $paymentClass }} text-light">
                            <i class="fa-solid fa-credit-card fa-lg"></i>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="h6 mb-0">Payment</div>
                            <span class="small">{{ ucfirst($order->payment_status ?? 'pending') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="alert alert-{{ $statusClass }} mb-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar rounded no-thumbnail bg-{{ $statusClass }} text-light">
                            <i class="fa-solid fa-truck-fast fa-lg"></i>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="h6 mb-0">Order Status</div>
                            <span class="small">{{ ucfirst($order->status ?? 'pending') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-3">

            <div class="col-xl-8 col-lg-8">

                <div class="card mb-3">
                    <div class="card-header py-3 bg-transparent">
                        <h6 class="m-0 fw-bold">Ordered Items</h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($order->items as $item)
                                        <tr>
                                            <td>
                                                <strong>
                                                    {{ $item->product->name ?? $item->product_name ?? 'Product Removed' }}
                                                </strong>
                                            </td>

                                            <td>₦{{ number_format($item->price ?? 0, 2) }}</td>

                                            <td>{{ $item->quantity }}</td>

                                            <td class="text-end">
                                                ₦{{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                No order items found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total</th>
                                        <th class="text-end">
                                            ₦{{ number_format($order->total ?? 0, 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-xl-4 col-lg-4">

                <div class="card mb-3">
                    <div class="card-header py-3 bg-transparent">
                        <h6 class="m-0 fw-bold">Customer Information</h6>
                    </div>

                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Name:</strong>
                            {{ $order->user->name ?? trim(($order->first_name ?? '').' '.($order->last_name ?? '')) ?: 'Guest Customer' }}
                        </p>

                        <p class="mb-2">
                            <strong>Email:</strong>
                            {{ $order->email ?? $order->user->email ?? 'N/A' }}
                        </p>

                        <p class="mb-2">
                            <strong>Phone:</strong>
                            {{ $order->phone ?? 'N/A' }}
                        </p>

                        <p class="mb-0">
                            <strong>Date:</strong>
                            {{ $order->created_at->format('d M, Y h:i A') }}
                        </p>
                    </div>
                </div>

               <div class="card mb-3">
    <div class="card-header py-3 bg-transparent">
        <h6 class="m-0 fw-bold">Shipping Address</h6>
    </div>

    <div class="card-body">
        @if($order->address)
            <p class="mb-2">{{ $order->address->street_address ?? $order->address->address ?? 'N/A' }}</p>
            <p class="mb-2">{{ $order->address->city->name ?? $order->address->city ?? 'N/A' }}</p>
            <p class="mb-2">{{ $order->address->state->name ?? 'N/A' }}</p>
            <p class="mb-0">{{ $order->address->country->name ?? 'N/A' }}</p>
        @else
            <p class="mb-0 text-muted">No shipping address found for this order.</p>
        @endif
    </div>
</div>

                <div class="card">
                    <div class="card-header py-3 bg-transparent">
                        <h6 class="m-0 fw-bold">Attend To Order</h6>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PATCH')

                            <label class="form-label">Order Status</label>
                            <select name="status" class="form-select mb-2">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>

                            <button class="btn btn-primary w-100">
                                <i class="fa-solid fa-truck-fast me-1"></i>
                                Update Order Status
                            </button>
                        </form>

                        <form action="{{ route('admin.orders.updatePaymentStatus', $order->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <label class="form-label">Payment Status</label>
                            <select name="payment_status" class="form-select mb-2">
                                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>

                            <button class="btn btn-success w-100">
                                <i class="fa-solid fa-credit-card me-1"></i>
                                Update Payment Status
                            </button>
                        </form>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

@endsection