@extends('layouts.customer')

@section('CustomerContent')

    @php

        /*
        |--------------------------------------------------------------------------
        | Fulfilment
        |--------------------------------------------------------------------------
        */

        $isPickup = $order->delivery_method === 'pickup';

        /*
        |--------------------------------------------------------------------------
        | Order Status
        |--------------------------------------------------------------------------
        */

        $orderStatusLabel = ucwords(str_replace('_', ' ', $order->status ?? 'pending'));

        $orderStatusClass = match (strtolower($order->status ?? 'pending')) {
            'delivered', 'picked_up', 'completed' => 'success',

            'processing' => 'info',

            'shipped' => 'primary',

            'ready_for_pickup' => 'warning',

            'cancelled', 'failed' => 'danger',

            default => 'warning',
        };

        /*
        |--------------------------------------------------------------------------
        | Payment Status
        |--------------------------------------------------------------------------
        */

        $paymentStatusClass = match (strtolower($order->payment_status ?? 'unpaid')) {
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
            'pending' => 'warning',
            'unpaid' => 'secondary',
            default => 'secondary',
        };

        /*
        |--------------------------------------------------------------------------
        | Customer Name
        |--------------------------------------------------------------------------
        */

        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        if (!$customerName) {
            $customerName = $order->user?->name ?? 'Customer';
        }
    @endphp


    <div class="dashboard-page-content">


        {{-- ==========================================================
            PAGE HEADER
        ========================================================== --}}

        <div class="row mb-9 align-items-center justify-content-between">

            <div class="col-sm-7 mb-6 mb-sm-0">

                <h2 class="fs-4 mb-2">
                    Order Details
                </h2>

                <p class="mb-0 text-muted">

                    {{ $order->order_no ?? 'ORD-' . $order->id }}

                </p>

            </div>


            <div class="col-sm-5 d-flex justify-content-sm-end">

                <a href="{{ route('customer.orders.index') }}" class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left me-2"></i>

                    Back to Orders

                </a>

            </div>

        </div>


        {{-- ==========================================================
            SUMMARY
        ========================================================== --}}

        <div class="row g-4 mb-7">


            {{-- Status --}}
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


            {{-- Payment --}}
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


            {{-- Total --}}
            <div class="col-md-4">

                <div class="card rounded-4 p-5 h-100">

                    <small class="text-muted d-block mb-2">
                        Order Total
                    </small>

                    <strong class="fs-5">

                        ₦{{ number_format((float) ($order->total ?? 0), 2) }}

                    </strong>

                </div>

            </div>

        </div>


        {{-- ==========================================================
            ORDER INFORMATION
        ========================================================== --}}

        <div class="card rounded-4 p-7 mb-7">

            <div class="row g-4">

                <div class="col-md-4">

                    <small class="text-muted d-block mb-1">
                        Order Number
                    </small>

                    <strong>

                        {{ $order->order_no ?? 'ORD-' . $order->id }}

                    </strong>

                </div>


                <div class="col-md-4">

                    <small class="text-muted d-block mb-1">
                        Order Date
                    </small>

                    <strong>

                        {{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A' }}

                    </strong>

                </div>


                <div class="col-md-4">

                    <small class="text-muted d-block mb-1">
                        Fulfilment Method
                    </small>

                    <strong>

                        @if ($isPickup)
                            <i class="fa-solid fa-location-dot me-2"></i>
                            Pickup
                        @else
                            <i class="fa-solid fa-truck-fast me-2"></i>
                            Shipping
                        @endif

                    </strong>

                </div>

            </div>

        </div>


        {{-- ==========================================================
            ITEMS
        ========================================================== --}}

        <div class="card rounded-4 p-7 mb-7">

            <div class="card-header bg-transparent px-0 pt-0 pb-6 border-0">

                <h4 class="fs-18px mb-0">
                    Items Ordered
                </h4>

            </div>


            <div class="card-body px-0 py-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Price
                                </th>

                                <th>
                                    Qty
                                </th>

                                <th class="text-end">
                                    Total
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($order->items as $item)
                                <tr>

                                    <td>

                                        <div class="d-flex align-items-center">

                                            @if ($item->image)
                                                <img src="{{ asset($item->image) }}" alt="{{ $item->name }}"
                                                    width="55" height="55" class="object-fit-cover rounded me-3">
                                            @endif


                                            <div>

                                                <strong class="d-block">

                                                    {{ $item->name ?: $item->product?->name ?: 'Product' }}

                                                </strong>


                                                @if ($item->variant_name)
                                                    <small class="text-muted d-block">

                                                        {{ $item->variant_name }}

                                                    </small>
                                                @endif


                                                @if ($item->sku)
                                                    <small class="text-muted d-block">

                                                        SKU:
                                                        {{ $item->sku }}

                                                    </small>
                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    <td>

                                        ₦{{ number_format((float) ($item->price ?? 0), 2) }}

                                    </td>


                                    <td>

                                        {{ $item->quantity }}

                                    </td>


                                    <td class="text-end">

                                        ₦{{ number_format((float) ($item->total ?? ($item->price ?? 0) * ($item->quantity ?? 1)), 2) }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4" class="text-center py-7 text-muted">

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


            {{-- ======================================================
                FULFILMENT
            ====================================================== --}}

            <div class="col-lg-6">

                <div class="card rounded-4 p-7 h-100">

                    <div class="card-header bg-transparent px-0 pt-0 pb-6 border-0">

                        <h4 class="fs-18px mb-0">

                            {{ $isPickup ? 'Pickup Information' : 'Delivery Information' }}

                        </h4>

                    </div>


                    <div class="card-body px-0 py-0">


                        {{-- ==========================================
                            PICKUP
                        ========================================== --}}

                        @if ($isPickup)
                            <div class="d-flex align-items-center mb-5">

                                <span class="badge bg-info me-2">
                                    Pickup
                                </span>

                                <span class="badge bg-success">
                                    FREE
                                </span>

                            </div>


                            <p class="mb-3">

                                <strong>Pickup Location:</strong>

                                <br>

                                {{ $order->pickupLocation?->name ?: 'Dela Moure Pickup Location' }}

                            </p>


                            <p class="mb-3">

                                <strong>Address:</strong>

                                <br>

                                <i class="fa-solid fa-location-dot me-2 text-muted"></i>

                                {{ $order->pickupLocation?->address ?: $order->address ?: 'N/A' }}

                            </p>


                            @if ($order->pickupLocation?->city || $order->city)
                                <p class="mb-3">

                                    <strong>City:</strong>

                                    <br>

                                    {{ $order->pickupLocation?->city ?: $order->city }}

                                </p>
                            @endif


                            <p class="mb-3">

                                <strong>State:</strong>

                                <br>

                                {{ $order->pickupLocation?->state?->name ?: $order->state?->name ?: 'N/A' }}

                            </p>


                            @if ($order->pickupLocation?->opening_hours)
                                <p class="mb-3">

                                    <strong>Opening Hours:</strong>

                                    <br>

                                    <i class="far fa-clock me-2 text-muted"></i>

                                    {{ $order->pickupLocation->opening_hours }}

                                </p>
                            @endif


                            @if ($order->pickupLocation?->pickup_time)
                                <p class="mb-3">

                                    <strong>Pickup Time:</strong>

                                    <br>

                                    {{ $order->pickupLocation->pickup_time }}

                                </p>
                            @endif


                            @if ($order->pickupLocation?->phone)
                                <p class="mb-3">

                                    <strong>Pickup Contact:</strong>

                                    <br>

                                    {{ $order->pickupLocation->phone }}

                                </p>
                            @endif


                            {{-- Ready State --}}
                            @if ($order->status === 'ready_for_pickup')
                                <div class="alert alert-success mt-5 mb-0">

                                    <i class="fa-solid fa-circle-check me-2"></i>

                                    <strong>
                                        Your order is ready for pickup.
                                    </strong>

                                    <div class="small mt-1">
                                        You may now proceed to the selected
                                        Dela Moure pickup location for collection.
                                    </div>

                                </div>
                            @elseif ($order->status === 'picked_up')
                                <div class="alert alert-success mt-5 mb-0">

                                    <i class="fa-solid fa-circle-check me-2"></i>

                                    This order has been collected successfully.

                                </div>
                            @else
                                <div class="alert alert-light border mt-5 mb-0">

                                    <i class="fa-solid fa-circle-info me-2"></i>

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


                            {{-- ==========================================
                            SHIPPING
                        ========================================== --}}
                        @else
                            <p class="mb-3">

                                <strong>Name:</strong>

                                <br>

                                {{ $customerName }}

                            </p>


                            <p class="mb-3">

                                <strong>Email:</strong>

                                <br>

                                {{ $order->email ?: $order->user?->email ?: 'N/A' }}

                            </p>


                            <p class="mb-3">

                                <strong>Phone:</strong>

                                <br>

                                {{ $order->phone ?: 'N/A' }}

                            </p>


                            <p class="mb-3">

                                <strong>Street Address:</strong>

                                <br>

                                <i class="fa-solid fa-location-dot me-2 text-muted"></i>

                                {{ $order->address ?: 'N/A' }}

                            </p>


                            @if ($order->city)
                                <p class="mb-3">

                                    <strong>Delivery Zone:</strong>

                                    <br>

                                    {{ $order->city }}

                                </p>
                            @endif


                            <p class="mb-3">

                                <strong>State:</strong>

                                <br>

                                {{ $order->state?->name ?: 'N/A' }}

                            </p>


                            <p class="mb-3">

                                <strong>Country:</strong>

                                <br>

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


            {{-- ======================================================
                PAYMENT SUMMARY
            ====================================================== --}}

            <div class="col-lg-6">

                <div class="card rounded-4 p-7 h-100">

                    <div class="card-header bg-transparent px-0 pt-0 pb-6 border-0">

                        <h4 class="fs-18px mb-0">
                            Payment Summary
                        </h4>

                    </div>


                    <div class="card-body px-0 py-0">


                        <p class="mb-3">

                            <strong>Payment Method:</strong>

                            <br>

                            {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}

                        </p>


                        <p class="mb-3">

                            <strong>Payment Status:</strong>

                            <br>

                            <span class="badge bg-{{ $paymentStatusClass }}">

                                {{ ucfirst($order->payment_status ?? 'unpaid') }}

                            </span>

                        </p>


                        @if ($order->payment_reference)
                            <p class="mb-3">

                                <strong>Reference:</strong>

                                <br>

                                {{ $order->payment_reference }}

                            </p>
                        @endif


                        @if ($order->paid_at)
                            <p class="mb-3">

                                <strong>Paid At:</strong>

                                <br>

                                {{ $order->paid_at->format('d M Y, h:i A') }}

                            </p>
                        @endif


                        <hr>


                        <p class="mb-3 d-flex justify-content-between">

                            <span>
                                Subtotal
                            </span>

                            <strong>

                                ₦{{ number_format((float) ($order->subtotal ?? 0), 2) }}

                            </strong>

                        </p>


                        <p class="mb-3 d-flex justify-content-between">

                            <span>

                                {{ $isPickup ? 'Pickup' : 'Shipping' }}

                            </span>


                            <strong>

                                @if ($isPickup)
                                    <span class="text-success">
                                        FREE
                                    </span>
                                @else
                                    ₦{{ number_format((float) ($order->shipping ?? 0), 2) }}
                                @endif

                            </strong>

                        </p>


                        @if ((float) ($order->vat ?? 0) > 0)
                            <p class="mb-3 d-flex justify-content-between">

                                <span>
                                    VAT
                                </span>

                                <strong>

                                    ₦{{ number_format((float) $order->vat, 2) }}

                                </strong>

                            </p>
                        @endif


                        @if ((float) ($order->discount ?? 0) > 0)
                            <p class="mb-3 d-flex justify-content-between">

                                <span>
                                    Discount
                                </span>

                                <strong>

                                    -₦{{ number_format((float) $order->discount, 2) }}

                                </strong>

                            </p>
                        @endif


                        <hr>


                        <p class="mb-0 d-flex justify-content-between fs-5">

                            <strong>
                                Total
                            </strong>

                            <strong>

                                ₦{{ number_format((float) ($order->total ?? 0), 2) }}

                            </strong>

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
