@extends('layouts.admin')

@section('content')

    @php
        $statusClass = match ($order->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'ready_for_pickup' => 'warning',
            'picked_up' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };

        $paymentClass = match ($order->payment_status) {
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
            'pending' => 'warning',
            'unpaid' => 'secondary',
            default => 'secondary',
        };

        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        if (!$customerName) {
            $customerName = $order->user?->name ?? 'Guest Customer';
        }

        $isPickup = $order->delivery_method === 'pickup';

        $orderStatusLabel = ucwords(str_replace('_', ' ', $order->status ?? 'pending'));

        $refundClass = match ($order->refund_status) {
            'processed' => 'success',
            'initiating', 'pending', 'processing' => 'warning',
            'needs-attention' => 'info',
            'failed', 'request_failed' => 'danger',
            default => 'secondary',
        };

        $refundLabel = $order->refund_status ? ucwords(str_replace(['-', '_'], ' ', $order->refund_status)) : null;

        $refundLocked = in_array(
            $order->refund_status,
            ['initiating', 'pending', 'processing', 'needs-attention', 'processed'],
            true,
        );

        $refundEligibleByOrderState =
            $order->status === 'cancelled' ||
            (in_array($order->status, ['delivered', 'picked_up'], true) &&
                in_array($order->return_status, ['received', 'no_return_required'], true));

        $canInitiateRefund =
            $refundEligibleByOrderState &&
            $order->payment_method === 'paystack' &&
            $order->payment_status === 'paid' &&
            !$refundLocked;

        $canCancelOrder = !in_array($order->status, ['cancelled', 'delivered', 'picked_up'], true);

        $orderIsFinal = in_array($order->status, ['cancelled', 'delivered', 'picked_up'], true);

        $isCompletedOrder = in_array($order->status, ['delivered', 'picked_up'], true);

        $returnStatusClass = match ($order->return_status) {
            'requested' => 'warning',
            'approved' => 'info',
            'received' => 'success',
            'rejected' => 'danger',
            'no_return_required' => 'primary',
            default => 'secondary',
        };

        $returnStatusLabel = $order->return_status ? ucwords(str_replace('_', ' ', $order->return_status)) : null;

        $hasReturnRoute = \Illuminate\Support\Facades\Route::has('admin.orders.return.update');
    @endphp

    <div class="body d-flex py-3">
        <div class="container-xxl">

            <div class="row align-items-center mb-4">
                <div class="col">
                    <h3 class="fw-bold mb-0">Order Details</h3>
                    <small class="text-muted">
                        {{ $order->order_no ?? 'ORD-' . $order->id }}
                    </small>
                </div>

                <div class="col-auto">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Back
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif


            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-3 mb-3 row-cols-1 row-cols-sm-2 row-cols-xl-4">

                <div class="col">
                    <div class="alert alert-primary mb-0 h-100">
                        <div class="d-flex align-items-center">
                            <div class="avatar rounded no-thumbnail bg-primary text-light">
                                <i class="fa-solid fa-bag-shopping fa-lg"></i>
                            </div>
                            <div class="flex-fill ms-3">
                                <div class="h6 mb-0">Order ID</div>
                                <span class="small">
                                    {{ $order->order_no ?? 'ORD-' . $order->id }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="alert alert-info mb-0 h-100">
                        <div class="d-flex align-items-center">
                            <div class="avatar rounded no-thumbnail bg-info text-light">
                                <i class="fa-solid fa-naira-sign fa-lg"></i>
                            </div>
                            <div class="flex-fill ms-3">
                                <div class="h6 mb-0">Total Amount</div>
                                <span class="small">
                                    ₦{{ number_format((float) ($order->total ?? 0), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="alert alert-{{ $paymentClass }} mb-0 h-100">
                        <div class="d-flex align-items-center">
                            <div class="avatar rounded no-thumbnail bg-{{ $paymentClass }} text-light">
                                <i class="fa-solid fa-credit-card fa-lg"></i>
                            </div>
                            <div class="flex-fill ms-3">
                                <div class="h6 mb-0">Payment</div>
                                <span class="small">
                                    {{ ucfirst($order->payment_status ?? 'unpaid') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="alert alert-{{ $statusClass }} mb-0 h-100">
                        <div class="d-flex align-items-center">
                            <div class="avatar rounded no-thumbnail bg-{{ $statusClass }} text-light">
                                <i class="fa-solid {{ $isPickup ? 'fa-box' : 'fa-truck-fast' }} fa-lg"></i>
                            </div>
                            <div class="flex-fill ms-3">
                                <div class="h6 mb-0">Order Status</div>
                                <span class="small">{{ $orderStatusLabel }}</span>
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
                                        @forelse ($order->items as $item)
                                            <tr>
                                                <td>
                                                    <strong>
                                                        {{ $item->name ?: $item->product?->name ?: 'Product Removed' }}
                                                    </strong>

                                                    @if ($item->variant_name)
                                                        <div class="small text-muted mt-1">
                                                            {{ $item->variant_name }}
                                                        </div>
                                                    @endif

                                                    @if ($item->sku)
                                                        <div class="small text-muted">
                                                            SKU: {{ $item->sku }}
                                                        </div>
                                                    @endif
                                                </td>

                                                <td>
                                                    ₦{{ number_format((float) ($item->price ?? 0), 2) }}
                                                </td>

                                                <td>{{ $item->quantity }}</td>

                                                <td class="text-end">
                                                    ₦{{ number_format((float) ($item->total ?? ($item->price ?? 0) * ($item->quantity ?? 1)), 2) }}
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
                                            <th colspan="3" class="text-end">Subtotal</th>
                                            <th class="text-end">
                                                ₦{{ number_format((float) ($order->subtotal ?? 0), 2) }}
                                            </th>
                                        </tr>

                                        <tr>
                                            <th colspan="3" class="text-end">
                                                {{ $isPickup ? 'Pickup' : 'Shipping' }}
                                            </th>
                                            <th class="text-end">
                                                @if ($isPickup)
                                                    <span class="text-success fw-semibold">FREE</span>
                                                @else
                                                    ₦{{ number_format((float) ($order->shipping ?? 0), 2) }}
                                                @endif
                                            </th>
                                        </tr>

                                        @if ((float) ($order->discount ?? 0) > 0)
                                            <tr>
                                                <th colspan="3" class="text-end">Discount</th>
                                                <th class="text-end">
                                                    -₦{{ number_format((float) $order->discount, 2) }}
                                                </th>
                                            </tr>
                                        @endif

                                        <tr>
                                            <th colspan="3" class="text-end">Total</th>
                                            <th class="text-end">
                                                ₦{{ number_format((float) ($order->total ?? 0), 2) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header py-3 bg-transparent">
                            <h6 class="m-0 fw-bold">Payment Information</h6>
                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <small class="text-muted d-block">Payment Method</small>
                                    <strong>
                                        {{ ucwords(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}
                                    </strong>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-muted d-block">Payment Status</small>
                                    <span class="badge bg-{{ $paymentClass }}">
                                        {{ ucwords(str_replace('_', ' ', $order->payment_status ?? 'unpaid')) }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-muted d-block">Payment Reference</small>
                                    <strong class="text-break">
                                        {{ $order->payment_reference ?: 'N/A' }}
                                    </strong>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-muted d-block">Paid At</small>
                                    <strong>
                                        {{ $order->paid_at ? \Carbon\Carbon::parse($order->paid_at)->format('d M, Y h:i A') : 'N/A' }}
                                    </strong>
                                </div>

                                @if ($order->refund_status)
                                    <div class="col-12">
                                        <hr class="my-1">
                                    </div>

                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Refund Status</small>
                                        <span
                                            class="badge bg-{{ $refundClass }} {{ in_array($order->refund_status, ['initiating', 'pending', 'processing'], true) ? 'text-dark' : '' }}">
                                            {{ $refundLabel }}
                                        </span>
                                    </div>

                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Refund Amount</small>
                                        <strong>
                                            {{ $order->refund_amount !== null ? '₦' . number_format((float) $order->refund_amount, 2) : 'N/A' }}
                                        </strong>
                                    </div>

                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Refund Reference</small>
                                        <strong class="text-break">
                                            {{ $order->refund_reference ?: 'N/A' }}
                                        </strong>
                                    </div>

                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Refund Requested At</small>
                                        <strong>
                                            {{ $order->refund_requested_at
                                                ? \Carbon\Carbon::parse($order->refund_requested_at)->format('d M, Y h:i A')
                                                : 'N/A' }}
                                        </strong>
                                    </div>

                                    @if ($order->refunded_at)
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Refunded At</small>
                                            <strong>
                                                {{ \Carbon\Carbon::parse($order->refunded_at)->format('d M, Y h:i A') }}
                                            </strong>
                                        </div>
                                    @endif
                                @endif

                            </div>

                            @if ($canInitiateRefund)
                                <hr class="my-4">

                                <div class="alert alert-warning small">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                    This order is cancelled but the Paystack payment is still marked as paid.
                                    You can initiate a full refund of
                                    <strong>₦{{ number_format((float) $order->total, 2) }}</strong>.
                                </div>

                                <form action="{{ route('admin.orders.refund', $order) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to initiate a full refund of ₦{{ number_format((float) $order->total, 2) }} for order {{ $order->order_no }}?');">
                                    @csrf

                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fa-solid fa-rotate-left me-1"></i>
                                        Initiate Full Refund
                                    </button>
                                </form>
                            @elseif ($order->payment_method === 'paystack' && $order->status === 'cancelled' && $order->refund_status)
                                <hr class="my-4">

                                @if ($order->refund_status === 'processed')
                                    <div class="alert alert-success mb-0">
                                        <i class="fa-solid fa-circle-check me-1"></i>
                                        This refund has been processed successfully.
                                    </div>
                                @elseif (in_array($order->refund_status, ['initiating', 'pending', 'processing'], true))
                                    <div class="alert alert-warning mb-0">
                                        <i class="fa-solid fa-clock me-1"></i>
                                        Refund processing is in progress. Final payment status will update after Paystack
                                        confirms the refund.
                                    </div>
                                @elseif ($order->refund_status === 'needs-attention')
                                    <div class="alert alert-info mb-0">
                                        <i class="fa-solid fa-circle-info me-1"></i>
                                        This refund requires attention. Review the Paystack dashboard before taking further
                                        action.
                                    </div>
                                @elseif (in_array($order->refund_status, ['failed', 'request_failed'], true))
                                    <div class="alert alert-danger mb-0">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>
                                        The previous refund attempt failed. Review the error/logs before retrying.
                                    </div>
                                @endif
                            @endif

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
                                {{ $customerName }}
                            </p>

                            <p class="mb-2">
                                <strong>Email:</strong>
                                {{ $order->email ?: $order->user?->email ?: 'N/A' }}
                            </p>

                            <p class="mb-2">
                                <strong>Phone:</strong>
                                {{ $order->phone ?: 'N/A' }}
                            </p>

                            <p class="mb-0">
                                <strong>Date:</strong>
                                {{ $order->created_at ? $order->created_at->format('d M, Y h:i A') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header py-3 bg-transparent">
                            <h6 class="m-0 fw-bold">
                                {{ $isPickup ? 'Pickup Information' : 'Shipping Address' }}
                            </h6>
                        </div>

                        <div class="card-body">

                            @if ($isPickup)
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-info">Pickup</span>
                                    <span class="badge bg-success ms-2">FREE</span>
                                </div>

                                <p class="mb-2">
                                    <strong>Pickup Location:</strong>
                                    {{ $order->pickupLocation?->name ?: 'Pickup Location' }}
                                </p>

                                <p class="mb-2">
                                    <i class="fa-solid fa-location-dot me-2 text-muted"></i>
                                    {{ $order->pickupLocation?->address ?: $order->address ?: 'N/A' }}
                                </p>

                                @if ($order->pickupLocation?->city || $order->city)
                                    <p class="mb-2">
                                        <strong>City:</strong>
                                        {{ $order->pickupLocation?->city ?: $order->city }}
                                    </p>
                                @endif

                                <p class="mb-2">
                                    <strong>State:</strong>
                                    {{ $order->pickupLocation?->state?->name ?: $order->state?->name ?: 'N/A' }}
                                </p>

                                @if ($order->pickupLocation?->opening_hours)
                                    <p class="mb-2">
                                        <strong>Opening Hours:</strong>
                                        {{ $order->pickupLocation->opening_hours }}
                                    </p>
                                @endif

                                @if ($order->pickupLocation?->pickup_time)
                                    <p class="mb-2">
                                        <strong>Pickup Time:</strong>
                                        {{ $order->pickupLocation->pickup_time }}
                                    </p>
                                @endif

                                @if ($order->pickupLocation?->phone)
                                    <p class="mb-2">
                                        <strong>Pickup Contact:</strong>
                                        {{ $order->pickupLocation->phone }}
                                    </p>
                                @endif

                                <p class="mb-0">
                                    <strong>Customer Phone:</strong>
                                    {{ $order->phone ?: 'N/A' }}
                                </p>

                                @if ($order->order_note)
                                    <hr>

                                    <div class="alert alert-light border mb-0">
                                        <small class="text-muted d-block mb-1">Pickup Note</small>
                                        {{ $order->order_note }}
                                    </div>
                                @endif
                            @else
                                <p class="mb-2 fw-semibold">{{ $customerName }}</p>

                                <p class="mb-2">
                                    <i class="fa-solid fa-location-dot me-2 text-muted"></i>
                                    {{ $order->address ?: 'N/A' }}
                                </p>

                                @if ($order->city)
                                    <p class="mb-2">
                                        <strong>Delivery Zone:</strong>
                                        {{ $order->city }}
                                    </p>
                                @endif

                                <p class="mb-2">
                                    <strong>State:</strong>
                                    {{ $order->state?->name ?: 'N/A' }}
                                </p>

                                <p class="mb-2">
                                    <strong>Country:</strong>
                                    {{ $order->country?->name ?: 'N/A' }}
                                </p>

                                <p class="mb-2">
                                    <strong>Delivery Method:</strong>
                                    Shipping
                                </p>

                                <p class="mb-0">
                                    <strong>Phone:</strong>
                                    {{ $order->phone ?: 'N/A' }}
                                </p>

                                @if ($order->order_note)
                                    <hr>

                                    <div class="alert alert-light border mb-0">
                                        <small class="text-muted d-block mb-1">Delivery Note</small>
                                        {{ $order->order_note }}
                                    </div>
                                @endif
                            @endif

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header py-3 bg-transparent">
                            <h6 class="m-0 fw-bold">Attend To Order</h6>
                        </div>

                        <div class="card-body">

                            {{-- ========================================================= --}}
                            {{-- ORDER STATUS --}}
                            {{-- ========================================================= --}}

                            @if (!$orderIsFinal)
                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST"
                                    class="mb-3">

                                    @csrf
                                    @method('PATCH')

                                    <label class="form-label">
                                        Order Status
                                    </label>

                                    <select name="status" class="form-select mb-2" required>

                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>

                                        <option value="processing"
                                            {{ $order->status === 'processing' ? 'selected' : '' }}>
                                            Processing
                                        </option>

                                        @if ($isPickup)
                                            <option value="ready_for_pickup"
                                                {{ $order->status === 'ready_for_pickup' ? 'selected' : '' }}>
                                                Ready for Pickup
                                            </option>

                                            <option value="picked_up"
                                                {{ $order->status === 'picked_up' ? 'selected' : '' }}>
                                                Picked Up
                                            </option>
                                        @else
                                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>
                                                Shipped
                                            </option>

                                            <option value="delivered"
                                                {{ $order->status === 'delivered' ? 'selected' : '' }}>
                                                Delivered
                                            </option>
                                        @endif

                                    </select>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fa-solid {{ $isPickup ? 'fa-box' : 'fa-truck-fast' }} me-1"></i>
                                        Update Order Status
                                    </button>

                                </form>
                            @else
                                @if ($order->status === 'cancelled')
                                    <div class="alert alert-danger mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fa-solid fa-ban me-2 mt-1"></i>

                                            <div>
                                                <strong>Order Cancelled</strong>

                                                <div class="small mt-1">
                                                    This order has been cancelled and can no longer progress through
                                                    fulfilment.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($order->status === 'delivered')
                                    <div class="alert alert-success mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fa-solid fa-circle-check me-2 mt-1"></i>

                                            <div>
                                                <strong>Order Delivered</strong>

                                                <div class="small mt-1">
                                                    This shipping order has been completed.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($order->status === 'picked_up')
                                    <div class="alert alert-success mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fa-solid fa-circle-check me-2 mt-1"></i>

                                            <div>
                                                <strong>Order Picked Up</strong>

                                                <div class="small mt-1">
                                                    This pickup order has been collected and completed.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif


                            {{-- ========================================================= --}}
                            {{-- CANCEL ORDER --}}
                            {{-- ========================================================= --}}

                            @if ($canCancelOrder)
                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST"
                                    class="mb-4"
                                    onsubmit="return confirm('Are you sure you want to cancel order {{ $order->order_no }}? Stock will be restored and the customer will be notified by email.{{ $order->payment_method === 'paystack' && $order->payment_status === 'paid' ? ' The payment will remain marked as paid until you initiate the refund separately.' : '' }}');">

                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="status" value="cancelled">

                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fa-solid fa-ban me-1"></i>
                                        Cancel Order
                                    </button>

                                </form>
                            @endif


                            {{-- ========================================================= --}}
                            {{-- PAYMENT STATUS --}}
                            {{-- ========================================================= --}}

                            <form action="{{ route('admin.orders.updatePaymentStatus', $order->id) }}" method="POST">

                                @csrf
                                @method('PATCH')

                                <label class="form-label">
                                    Payment Status
                                </label>

                                <select name="payment_status" class="form-select mb-2">

                                    <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>
                                        Unpaid
                                    </option>

                                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>

                                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>
                                        Paid
                                    </option>

                                    <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>
                                        Failed
                                    </option>

                                    @if ($order->payment_status === 'refunded')
                                        <option value="refunded" selected>
                                            Refunded
                                        </option>
                                    @endif

                                </select>

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fa-solid fa-credit-card me-1"></i>
                                    Update Payment Status
                                </button>

                            </form>

                        </div>
                    </div>


                    {{-- ========================================================= --}}
                    {{-- RETURN / REFUND WORKFLOW --}}
                    {{-- ========================================================= --}}

                    @if ($isCompletedOrder || $order->return_status)

                        <div class="card mt-3">

                            <div class="card-header py-3 bg-transparent">
                                <div class="d-flex align-items-center justify-content-between">

                                    <h6 class="m-0 fw-bold">
                                        Return / Refund
                                    </h6>

                                    @if ($order->return_status)
                                        <span class="badge bg-{{ $returnStatusClass }}">
                                            {{ $returnStatusLabel }}
                                        </span>
                                    @endif

                                </div>
                            </div>


                            <div class="card-body">

                                {{-- No return case has been opened yet --}}
                                @if (!$order->return_status)

                                    <div class="alert alert-light border">
                                        <div class="d-flex align-items-start">
                                            <i class="fa-solid fa-rotate-left me-2 mt-1 text-muted"></i>

                                            <div>
                                                <strong>Completed Order</strong>

                                                <div class="small text-muted mt-1">
                                                    Use the return workflow if this delivered or collected order
                                                    needs to be returned or refunded.
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    @if ($hasReturnRoute)
                                        <form action="{{ route('admin.orders.return.update', $order->id) }}"
                                            method="POST">

                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden" name="action" value="start">

                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Return / Refund Reason
                                                </label>

                                                <textarea name="return_reason" class="form-control" rows="3" required
                                                    placeholder="Enter the reason for the return or refund..."></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Resolution
                                                </label>

                                                <select name="return_required" class="form-select" required>
                                                    <option value="1">
                                                        Product must be returned
                                                    </option>

                                                    <option value="0">
                                                        Refund without physical return
                                                    </option>
                                                </select>
                                            </div>

                                            <button type="submit" class="btn btn-outline-primary w-100"
                                                onclick="return confirm('Start the return/refund process for order {{ $order->order_no }}?');">
                                                <i class="fa-solid fa-rotate-left me-1"></i>
                                                Start Return / Refund
                                            </button>

                                        </form>
                                    @else
                                        <div class="alert alert-warning mb-0 small">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                            Return controls are ready in the UI. The return-processing route still
                                            needs to be connected before these actions can be used.
                                        </div>
                                    @endif


                                    {{-- Return requested --}}
                                @elseif ($order->return_status === 'requested')
                                    <div class="mb-3">
                                        <small class="text-muted d-block">
                                            Reason
                                        </small>

                                        <strong>
                                            {{ $order->return_reason ?: 'No reason provided.' }}
                                        </strong>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">
                                            Return Required
                                        </small>

                                        <strong>
                                            {{ $order->return_required ? 'Yes' : 'No' }}
                                        </strong>
                                    </div>

                                    @if ($order->return_requested_at)
                                        <div class="mb-3">
                                            <small class="text-muted d-block">
                                                Requested At
                                            </small>

                                            <strong>
                                                {{ \Carbon\Carbon::parse($order->return_requested_at)->format('d M, Y h:i A') }}
                                            </strong>
                                        </div>
                                    @endif


                                    @if ($hasReturnRoute)
                                        <div class="d-grid gap-2">

                                            @if ($order->return_required)
                                                <form action="{{ route('admin.orders.return.update', $order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden" name="action" value="approve">

                                                    <button type="submit" class="btn btn-primary w-100"
                                                        onclick="return confirm('Approve this return request?');">
                                                        <i class="fa-solid fa-check me-1"></i>
                                                        Approve Return
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.orders.return.update', $order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden" name="action" value="no_return_required">

                                                    <button type="submit" class="btn btn-primary w-100"
                                                        onclick="return confirm('Approve this refund without requiring the product to be returned?');">
                                                        <i class="fa-solid fa-check me-1"></i>
                                                        Approve Refund Without Return
                                                    </button>
                                                </form>
                                            @endif


                                            <form action="{{ route('admin.orders.return.update', $order->id) }}"
                                                method="POST" class="border rounded p-3 mt-2">
                                                @csrf
                                                @method('PATCH')

                                                <input type="hidden" name="action" value="reject">

                                                <label class="form-label text-danger fw-semibold">
                                                    Rejection Reason
                                                </label>

                                                <textarea name="return_note" class="form-control mb-2" rows="3" maxlength="2000" required
                                                    placeholder="Explain why this return/refund request is being rejected...">{{ old('return_note') }}</textarea>

                                                <small class="text-muted d-block mb-3">
                                                    This reason will be included in the customer notification email.
                                                </small>

                                                <button type="submit" class="btn btn-outline-danger w-100"
                                                    onclick="return confirm('Reject this return/refund request and notify the customer?');">
                                                    <i class="fa-solid fa-xmark me-1"></i>
                                                    Reject Request
                                                </button>
                                            </form>

                                        </div>
                                    @endif


                                    {{-- Return approved --}}
                                @elseif ($order->return_status === 'approved')
                                    <div class="alert alert-info">
                                        <i class="fa-solid fa-box-open me-1"></i>
                                        The return has been approved. Mark it as received only after the merchandise
                                        is physically returned and accepted.
                                    </div>

                                    @if ($order->return_reason)
                                        <div class="mb-3">
                                            <small class="text-muted d-block">
                                                Reason
                                            </small>

                                            <strong>
                                                {{ $order->return_reason }}
                                            </strong>
                                        </div>
                                    @endif

                                    @if ($hasReturnRoute)
                                        <form action="{{ route('admin.orders.return.update', $order->id) }}"
                                            method="POST" class="mb-3">
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden" name="action" value="received">

                                            <button type="submit" class="btn btn-success w-100"
                                                onclick="return confirm('Confirm that the returned merchandise has been received and accepted? Stock must only be restored at this point.');">
                                                <i class="fa-solid fa-box-open me-1"></i>
                                                Mark Return Received
                                            </button>
                                        </form>


                                        <form action="{{ route('admin.orders.return.update', $order->id) }}"
                                            method="POST" class="border rounded p-3">
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden" name="action" value="reject">

                                            <label class="form-label text-danger fw-semibold">
                                                Reject Approved Return
                                            </label>

                                            <textarea name="return_note" class="form-control mb-2" rows="3" maxlength="2000" required
                                                placeholder="State why this approved return can no longer be accepted...">{{ old('return_note') }}</textarea>

                                            <small class="text-muted d-block mb-3">
                                                Use this only before the merchandise has been marked as received.
                                            </small>

                                            <button type="submit" class="btn btn-outline-danger w-100"
                                                onclick="return confirm('Reject this approved return and notify the customer?');">
                                                <i class="fa-solid fa-xmark me-1"></i>
                                                Reject Return
                                            </button>
                                        </form>
                                    @endif


                                    {{-- Return received --}}
                                @elseif ($order->return_status === 'received')
                                    <div class="alert alert-success">
                                        <i class="fa-solid fa-circle-check me-1"></i>
                                        Returned merchandise has been received.
                                    </div>

                                    @if ($order->returned_at)
                                        <div class="mb-3">
                                            <small class="text-muted d-block">
                                                Returned At
                                            </small>

                                            <strong>
                                                {{ \Carbon\Carbon::parse($order->returned_at)->format('d M, Y h:i A') }}
                                            </strong>
                                        </div>
                                    @endif

                                    @if ($canInitiateRefund)
                                        <div class="alert alert-warning small">
                                            <i class="fa-solid fa-credit-card me-1"></i>
                                            The returned merchandise has been received and stock has been restored.
                                            You can now initiate the full Paystack refund.
                                        </div>

                                        <form action="{{ route('admin.orders.refund', $order) }}" method="POST"
                                            onsubmit="return confirm('Initiate a full refund of ₦{{ number_format((float) $order->total, 2) }} for order {{ $order->order_no }}?');">
                                            @csrf

                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fa-solid fa-rotate-left me-1"></i>
                                                Initiate Full Refund
                                            </button>
                                        </form>
                                    @elseif ($order->payment_method === 'paystack' && $order->payment_status === 'paid' && $order->refund_status)
                                        <div class="alert alert-info mb-0 small">
                                            <i class="fa-solid fa-circle-info me-1"></i>
                                            Refund status:
                                            <strong>{{ $refundLabel }}</strong>
                                        </div>
                                    @endif


                                    {{-- Refund without return approved --}}
                                @elseif ($order->return_status === 'no_return_required')
                                    <div class="alert alert-primary">
                                        <i class="fa-solid fa-circle-info me-1"></i>
                                        Refund without physical return has been approved. Inventory must not be restored.
                                    </div>

                                    @if ($canInitiateRefund)
                                        <div class="alert alert-warning small">
                                            <i class="fa-solid fa-credit-card me-1"></i>
                                            Refund without physical return has been approved.
                                            Inventory will not be restored. You can now initiate the full Paystack refund.
                                        </div>

                                        <form action="{{ route('admin.orders.refund', $order) }}" method="POST"
                                            onsubmit="return confirm('Initiate a full refund of ₦{{ number_format((float) $order->total, 2) }} for order {{ $order->order_no }} without restoring inventory?');">
                                            @csrf

                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fa-solid fa-rotate-left me-1"></i>
                                                Initiate Full Refund
                                            </button>
                                        </form>
                                    @elseif ($order->payment_method === 'paystack' && $order->payment_status === 'paid' && $order->refund_status)
                                        <div class="alert alert-info mb-0 small">
                                            <i class="fa-solid fa-circle-info me-1"></i>
                                            Refund status:
                                            <strong>{{ $refundLabel }}</strong>
                                        </div>
                                    @endif


                                    {{-- Rejected --}}
                                @elseif ($order->return_status === 'rejected')
                                    <div class="alert alert-danger mb-0">
                                        <i class="fa-solid fa-circle-xmark me-1"></i>
                                        This return/refund request was rejected.
                                    </div>

                                    @if ($order->return_reason)
                                        <div class="mt-3">
                                            <small class="text-muted d-block">
                                                Original Reason
                                            </small>

                                            <strong>
                                                {{ $order->return_reason }}
                                            </strong>
                                        </div>
                                    @endif

                                @endif

                            </div>
                        </div>

                    @endif

                </div>

            </div>

        </div>
    </div>

@endsection
