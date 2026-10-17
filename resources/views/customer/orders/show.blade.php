@extends('layouts.customer')

@section('CustomerContent')
    @php
        $isPickup = $order->delivery_method === 'pickup';

        $orderStatusLabel = ucwords(str_replace('_', ' ', $order->status ?? 'pending'));

        $orderStatusClass = match (strtolower($order->status ?? 'pending')) {
            'delivered', 'picked_up', 'completed' => 'success',
            'processing' => 'info',
            'shipped' => 'primary',
            'ready_for_pickup' => 'warning',
            'cancelled', 'failed' => 'danger',
            default => 'warning',
        };

        $paymentStatusClass = match (strtolower($order->payment_status ?? 'unpaid')) {
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
            'pending' => 'warning',
            default => 'secondary',
        };

        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        if (!$customerName) {
            $customerName = $order->user?->name ?? 'Customer';
        }

        $orderNumber = $order->order_no ?? 'ORD-' . $order->id;
        $itemCount = $order->items->count();
        $totalUnits = $order->items->sum('quantity');
    @endphp

    <div class="dashboard-page-content">

        {{-- Page Header --}}
        <div class="row mb-9 align-items-center justify-content-between">
            <div class="col-sm-7 mb-6 mb-sm-0">
                <h2 class="fs-4 mb-2">Order Details</h2>

                <p class="mb-3 text-muted">
                    {{ $orderNumber }}
                </p>

                <span class="badge rounded-pill {{ $order->purchase_type_badge_class }} px-4 py-3">
                    {{ $order->purchase_type_label }} Order
                </span>
            </div>

            <div class="col-sm-5 d-flex justify-content-sm-end">
                <a href="{{ route('customer.orders.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left me-2" aria-hidden="true"></i>
                    Back to Orders
                </a>
            </div>
        </div>

        {{-- Summary --}}
        <div class="row g-4 mb-7">
            <div class="col-md-4">
                <div class="card rounded-4 p-5 h-100">
                    <small class="text-muted d-block mb-2">
                        Order Status
                    </small>

                    <div>
                        <span class="badge bg-{{ $orderStatusClass }} px-4 py-3">
                            {{ $orderStatusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card rounded-4 p-5 h-100">
                    <small class="text-muted d-block mb-2">
                        Payment
                    </small>

                    <div>
                        <span class="badge bg-{{ $paymentStatusClass }} px-4 py-3">
                            {{ ucfirst($order->payment_status ?? 'unpaid') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card rounded-4 p-5 h-100">
                    <small class="text-muted d-block mb-2">
                        Order Total
                    </small>

                    <strong class="fs-5">
                        {{ $order->formatted_total }}
                    </strong>
                </div>
            </div>
        </div>

        {{-- Order Information --}}
        <div class="card rounded-4 p-7 mb-7">
            <div class="row g-4">
                <div class="col-md-4">
                    <small class="text-muted d-block mb-1">
                        Order Number
                    </small>

                    <strong>{{ $orderNumber }}</strong>
                </div>

                <div class="col-md-4">
                    <small class="text-muted d-block mb-1">
                        Order Date
                    </small>

                    <strong>
                        {{ $order->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                    </strong>
                </div>

                <div class="col-md-4">
                    <small class="text-muted d-block mb-1">
                        Fulfilment Method
                    </small>

                    <strong>
                        @if ($isPickup)
                            <i class="fa-solid fa-location-dot me-2" aria-hidden="true"></i>
                            Pickup
                        @else
                            <i class="fa-solid fa-truck-fast me-2" aria-hidden="true"></i>
                            Shipping
                        @endif
                    </strong>
                </div>
            </div>
        </div>

        {{-- Items Ordered --}}
        <div class="card rounded-4 p-7 mb-7">
            <div class="card-header bg-transparent px-0 pt-0 pb-6 border-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h4 class="fs-18px mb-0">Items Ordered</h4>

                    <span class="text-muted fs-14px">
                        {{ number_format($totalUnits) }}
                        {{ $totalUnits == 1 ? 'unit' : 'units' }}
                        across
                        {{ $itemCount }}
                        {{ $itemCount === 1 ? 'item' : 'items' }}
                    </span>
                </div>

                @if ($order->isWholesale())
                    <p class="small text-muted mt-3 mb-0">
                        This order was purchased at wholesale prices.
                        The unit prices below are those recorded at checkout.
                    </p>
                @endif
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Purchase Type</th>
                                <th scope="col">Unit Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col" class="text-end">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($item->image)
                                                <img src="{{ asset($item->image) }}" alt="{{ $item->name }}"
                                                    width="55" height="55" loading="lazy"
                                                    class="rounded me-3 flex-shrink-0" style="object-fit: cover;">
                                            @endif

                                            <div>
                                                <strong class="d-block">
                                                    {{ $item->name ?: $item->product?->name ?: 'Product' }}
                                                </strong>

                                                @if ($item->variant_display)
                                                    <small class="text-muted d-block">
                                                        {{ $item->variant_display }}
                                                    </small>
                                                @endif

                                                @if ($item->sku)
                                                    <small class="text-muted d-block">
                                                        SKU: {{ $item->sku }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge rounded-pill {{ $item->purchase_type_badge_class }} px-4 py-2">
                                            {{ $item->purchase_type_label }}
                                        </span>
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $item->formatted_price }}
                                    </td>

                                    <td>
                                        {{ number_format($item->quantity) }}
                                    </td>

                                    <td class="text-end text-nowrap fw-semibold">
                                        {{ $item->formatted_total }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-7 text-muted">
                                        No items found for this order.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- Fulfilment Information --}}
            <div class="col-lg-6">
                <div class="card rounded-4 p-7 h-100">
                    <div class="card-header bg-transparent px-0 pt-0 pb-6 border-0">
                        <h4 class="fs-18px mb-0">
                            {{ $isPickup ? 'Pickup Information' : 'Delivery Information' }}
                        </h4>
                    </div>

                    <div class="card-body p-0">
                        @if ($isPickup)
                            <div class="d-flex align-items-center mb-5">
                                <span class="badge bg-info me-2">Pickup</span>

                                @if ((float) $order->shipping === 0.0)
                                    <span class="badge bg-success">FREE</span>
                                @endif
                            </div>

                            <p class="mb-3">
                                <strong>Pickup Location:</strong><br>
                                {{ $order->pickupLocation?->name ?: 'Dela Moure Pickup Location' }}
                            </p>

                            <p class="mb-3">
                                <strong>Address:</strong><br>
                                <i class="fa-solid fa-location-dot me-2 text-muted" aria-hidden="true"></i>
                                {{ $order->address ?: $order->pickupLocation?->address ?: 'N/A' }}
                            </p>

                            @if ($order->city || $order->pickupLocation?->city)
                                <p class="mb-3">
                                    <strong>City:</strong><br>
                                    {{ $order->city ?: $order->pickupLocation?->city }}
                                </p>
                            @endif

                            <p class="mb-3">
                                <strong>State:</strong><br>
                                {{ $order->state?->name ?: $order->pickupLocation?->state?->name ?: 'N/A' }}
                            </p>

                            @if ($order->pickupLocation?->opening_hours)
                                <p class="mb-3">
                                    <strong>Opening Hours:</strong><br>
                                    <i class="far fa-clock me-2 text-muted" aria-hidden="true"></i>
                                    {{ $order->pickupLocation->opening_hours }}
                                </p>
                            @endif

                            @if ($order->pickupLocation?->pickup_time)
                                <p class="mb-3">
                                    <strong>Pickup Time:</strong><br>
                                    {{ $order->pickupLocation->pickup_time }}
                                </p>
                            @endif

                            @if ($order->pickupLocation?->phone)
                                <p class="mb-3">
                                    <strong>Pickup Contact:</strong><br>
                                    {{ $order->pickupLocation->phone }}
                                </p>
                            @endif

                            @if ($order->status === 'ready_for_pickup')
                                <div class="alert alert-success mt-5 mb-0">
                                    <i class="fa-solid fa-circle-check me-2" aria-hidden="true"></i>

                                    <strong>Your order is ready for pickup.</strong>

                                    <div class="small mt-1">
                                        You may now proceed to the selected
                                        Dela Moure pickup location for collection.
                                    </div>
                                </div>
                            @elseif ($order->status === 'picked_up')
                                <div class="alert alert-success mt-5 mb-0">
                                    <i class="fa-solid fa-circle-check me-2" aria-hidden="true"></i>
                                    This order has been collected successfully.
                                </div>
                            @elseif (in_array($order->status, ['cancelled', 'failed'], true))
                                <div class="alert alert-secondary mt-5 mb-0">
                                    This order is {{ strtolower($orderStatusLabel) }}.
                                </div>
                            @elseif (in_array($order->status, ['completed', 'delivered'], true))
                                <div class="alert alert-success mt-5 mb-0">
                                    This order is {{ strtolower($orderStatusLabel) }}.
                                </div>
                            @else
                                <div class="alert alert-light border mt-5 mb-0">
                                    <i class="fa-solid fa-circle-info me-2" aria-hidden="true"></i>
                                    We will notify you when your order is ready
                                    for collection. Please wait for confirmation
                                    before visiting the pickup location.
                                </div>
                            @endif

                            @if ($order->order_note)
                                <hr>

                                <div>
                                    <small class="text-muted d-block mb-1">
                                        Pickup Note
                                    </small>

                                    {{ $order->order_note }}
                                </div>
                            @endif
                        @else
                            <p class="mb-3">
                                <strong>Name:</strong><br>
                                {{ $customerName }}
                            </p>

                            <p class="mb-3">
                                <strong>Email:</strong><br>
                                {{ $order->email ?: $order->user?->email ?: 'N/A' }}
                            </p>

                            <p class="mb-3">
                                <strong>Phone:</strong><br>
                                {{ $order->phone ?: 'N/A' }}
                            </p>

                            <p class="mb-3">
                                <strong>Street Address:</strong><br>
                                <i class="fa-solid fa-location-dot me-2 text-muted" aria-hidden="true"></i>
                                {{ $order->address ?: 'N/A' }}
                            </p>

                            @if ($order->city)
                                <p class="mb-3">
                                    <strong>Delivery Zone:</strong><br>
                                    {{ $order->city }}
                                </p>
                            @endif

                            <p class="mb-3">
                                <strong>State:</strong><br>
                                {{ $order->state?->name ?: 'N/A' }}
                            </p>

                            <p class="mb-3">
                                <strong>Country:</strong><br>
                                {{ $order->country?->name ?: 'N/A' }}
                            </p>

                            @if ($order->order_note)
                                <hr>

                                <div>
                                    <small class="text-muted d-block mb-1">
                                        Delivery Note
                                    </small>

                                    {{ $order->order_note }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Payment Summary --}}
            <div class="col-lg-6">
                <div class="card rounded-4 p-7 h-100">
                    <div class="card-header bg-transparent px-0 pt-0 pb-6 border-0">
                        <h4 class="fs-18px mb-0">Payment Summary</h4>
                    </div>

                    <div class="card-body p-0">
                        <p class="mb-3">
                            <strong>Purchase Type:</strong><br>

                            <span class="badge {{ $order->purchase_type_badge_class }}">
                                {{ $order->purchase_type_label }}
                            </span>
                        </p>

                        <p class="mb-3">
                            <strong>Payment Method:</strong><br>
                            {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}
                        </p>

                        <p class="mb-3">
                            <strong>Payment Status:</strong><br>

                            <span class="badge bg-{{ $paymentStatusClass }}">
                                {{ ucfirst($order->payment_status ?? 'unpaid') }}
                            </span>
                        </p>

                        @if ($order->payment_reference)
                            <p class="mb-3">
                                <strong>Reference:</strong><br>

                                <span class="text-break">
                                    {{ $order->payment_reference }}
                                </span>
                            </p>
                        @endif

                        @if ($order->paid_at)
                            <p class="mb-3">
                                <strong>Paid At:</strong><br>
                                {{ $order->paid_at->format('d M Y, h:i A') }}
                            </p>
                        @endif

                        <hr>

                        <p class="mb-3 d-flex justify-content-between gap-3">
                            <span>Subtotal</span>
                            <strong>{{ $order->formatted_subtotal }}</strong>
                        </p>

                        <p class="mb-3 d-flex justify-content-between gap-3">
                            <span>{{ $isPickup ? 'Pickup' : 'Shipping' }}</span>

                            <strong>
                                @if ($isPickup && (float) $order->shipping === 0.0)
                                    <span class="text-success">FREE</span>
                                @else
                                    {{ $order->formatted_shipping }}
                                @endif
                            </strong>
                        </p>

                        @if ((float) $order->vat > 0)
                            <p class="mb-3 d-flex justify-content-between gap-3">
                                <span>VAT</span>
                                <strong>{{ $order->formatted_vat }}</strong>
                            </p>
                        @endif

                        @if ((float) $order->discount > 0)
                            <p class="mb-3 d-flex justify-content-between gap-3">
                                <span>Discount</span>
                                <strong>-{{ $order->formatted_discount }}</strong>
                            </p>
                        @endif

                        <hr>

                        <p class="mb-0 d-flex justify-content-between gap-3 fs-5">
                            <strong>Total</strong>
                            <strong>{{ $order->formatted_total }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
