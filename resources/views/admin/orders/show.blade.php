@extends('layouts.admin')

@section('content')

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
            'pending' => 'warning',
            'unpaid' => 'secondary',
            default => 'secondary',
        };


        /*
        |--------------------------------------------------------------------------
        | Customer Name
        |--------------------------------------------------------------------------
        */

        $customerName = trim(
            ($order->first_name ?? '')
            . ' '
            . ($order->last_name ?? '')
        );

        if (!$customerName) {
            $customerName =
                $order->user?->name
                ?? 'Guest Customer';
        }

    @endphp


    <div class="body d-flex py-3">

        <div class="container-xxl">


            {{-- Page Header --}}
            <div class="row align-items-center mb-4">

                <div class="col">

                    <h3 class="fw-bold mb-0">
                        Order Details
                    </h3>

                    <small class="text-muted">
                        {{ $order->order_no ?? 'ORD-' . $order->id }}
                    </small>

                </div>


                <div class="col-auto">

                    <a href="{{ route('admin.orders.index') }}"
                        class="btn btn-secondary">

                        <i class="fa-solid fa-arrow-left me-1"></i>

                        Back

                    </a>

                </div>

            </div>


            {{-- Success --}}
            @if (session('success'))

                <div class="alert alert-success alert-dismissible fade show">

                    {{ session('success') }}

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

            @endif


            {{-- Error --}}
            @if (session('error'))

                <div class="alert alert-danger alert-dismissible fade show">

                    {{ session('error') }}

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

            @endif


            {{-- Summary Cards --}}
            <div class="row g-3 mb-3 row-cols-1 row-cols-sm-2 row-cols-xl-4">


                {{-- Order ID --}}
                <div class="col">

                    <div class="alert alert-primary mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-primary text-light">

                                <i class="fa-solid fa-bag-shopping fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Order ID
                                </div>

                                <span class="small">
                                    {{ $order->order_no ?? 'ORD-' . $order->id }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Total --}}
                <div class="col">

                    <div class="alert alert-info mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div class="avatar rounded no-thumbnail bg-info text-light">

                                <i class="fa-solid fa-naira-sign fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Total Amount
                                </div>

                                <span class="small">

                                    ₦{{ number_format(
                                        (float) ($order->total ?? 0),
                                        2
                                    ) }}

                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Payment --}}
                <div class="col">

                    <div class="alert alert-{{ $paymentClass }} mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div
                                class="avatar rounded no-thumbnail bg-{{ $paymentClass }} text-light">

                                <i class="fa-solid fa-credit-card fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Payment
                                </div>

                                <span class="small">

                                    {{ ucfirst(
                                        $order->payment_status ?? 'unpaid'
                                    ) }}

                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Status --}}
                <div class="col">

                    <div class="alert alert-{{ $statusClass }} mb-0 h-100">

                        <div class="d-flex align-items-center">

                            <div
                                class="avatar rounded no-thumbnail bg-{{ $statusClass }} text-light">

                                <i class="fa-solid fa-truck-fast fa-lg"></i>

                            </div>

                            <div class="flex-fill ms-3">

                                <div class="h6 mb-0">
                                    Order Status
                                </div>

                                <span class="small">

                                    {{ ucfirst(
                                        $order->status ?? 'pending'
                                    ) }}

                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="row g-3">


                {{-- Left --}}
                <div class="col-xl-8 col-lg-8">


                    {{-- Ordered Items --}}
                    <div class="card mb-3">

                        <div class="card-header py-3 bg-transparent">

                            <h6 class="m-0 fw-bold">
                                Ordered Items
                            </h6>

                        </div>


                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-hover align-middle mb-0">

                                    <thead>

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
                                                Subtotal
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @forelse ($order->items as $item)

                                            <tr>

                                                <td>

                                                    <strong>

                                                        {{ $item->name
                                                            ?: $item->product?->name
                                                            ?: 'Product Removed' }}

                                                    </strong>

                                                </td>


                                                <td>

                                                    ₦{{ number_format(
                                                        (float) ($item->price ?? 0),
                                                        2
                                                    ) }}

                                                </td>


                                                <td>

                                                    {{ $item->quantity }}

                                                </td>


                                                <td class="text-end">

                                                    ₦{{ number_format(
                                                        (float) ($item->total
                                                            ?? (
                                                                ($item->price ?? 0)
                                                                *
                                                                ($item->quantity ?? 1)
                                                            )),
                                                        2
                                                    ) }}

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td colspan="4"
                                                    class="text-center py-4 text-muted">

                                                    No order items found.

                                                </td>

                                            </tr>

                                        @endforelse

                                    </tbody>


                                    <tfoot>

                                        <tr>

                                            <th colspan="3"
                                                class="text-end">

                                                Subtotal

                                            </th>

                                            <th class="text-end">

                                                ₦{{ number_format(
                                                    (float) ($order->subtotal ?? 0),
                                                    2
                                                ) }}

                                            </th>

                                        </tr>


                                        <tr>

                                            <th colspan="3"
                                                class="text-end">

                                                Shipping

                                            </th>

                                            <th class="text-end">

                                                ₦{{ number_format(
                                                    (float) ($order->shipping ?? 0),
                                                    2
                                                ) }}

                                            </th>

                                        </tr>


                                        @if ((float) ($order->discount ?? 0) > 0)

                                            <tr>

                                                <th colspan="3"
                                                    class="text-end">

                                                    Discount

                                                </th>

                                                <th class="text-end">

                                                    -₦{{ number_format(
                                                        (float) $order->discount,
                                                        2
                                                    ) }}

                                                </th>

                                            </tr>

                                        @endif


                                        <tr>

                                            <th colspan="3"
                                                class="text-end">

                                                Total

                                            </th>

                                            <th class="text-end">

                                                ₦{{ number_format(
                                                    (float) ($order->total ?? 0),
                                                    2
                                                ) }}

                                            </th>

                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

                        </div>

                    </div>


                    {{-- Payment Information --}}
                    <div class="card mb-3">

                        <div class="card-header py-3 bg-transparent">

                            <h6 class="m-0 fw-bold">
                                Payment Information
                            </h6>

                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <small class="text-muted d-block">
                                        Payment Method
                                    </small>

                                    <strong>
                                        {{ ucfirst(
                                            $order->payment_method ?? 'N/A'
                                        ) }}
                                    </strong>

                                </div>


                                <div class="col-md-6">

                                    <small class="text-muted d-block">
                                        Payment Status
                                    </small>

                                    <span class="badge bg-{{ $paymentClass }}">

                                        {{ ucfirst(
                                            $order->payment_status ?? 'unpaid'
                                        ) }}

                                    </span>

                                </div>


                                <div class="col-md-6">

                                    <small class="text-muted d-block">
                                        Payment Reference
                                    </small>

                                    <strong>

                                        {{ $order->payment_reference ?: 'N/A' }}

                                    </strong>

                                </div>


                                <div class="col-md-6">

                                    <small class="text-muted d-block">
                                        Paid At
                                    </small>

                                    <strong>

                                        {{ $order->paid_at
                                            ? \Carbon\Carbon::parse($order->paid_at)
                                                ->format('d M, Y h:i A')
                                            : 'N/A' }}

                                    </strong>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Right --}}
                <div class="col-xl-4 col-lg-4">


                    {{-- Customer Information --}}
                    <div class="card mb-3">

                        <div class="card-header py-3 bg-transparent">

                            <h6 class="m-0 fw-bold">
                                Customer Information
                            </h6>

                        </div>


                        <div class="card-body">

                            <p class="mb-2">

                                <strong>Name:</strong>

                                {{ $customerName }}

                            </p>


                            <p class="mb-2">

                                <strong>Email:</strong>

                                {{ $order->email
                                    ?: $order->user?->email
                                    ?: 'N/A' }}

                            </p>


                            <p class="mb-2">

                                <strong>Phone:</strong>

                                {{ $order->phone ?: 'N/A' }}

                            </p>


                            <p class="mb-0">

                                <strong>Date:</strong>

                                {{ $order->created_at
                                    ? $order->created_at->format(
                                        'd M, Y h:i A'
                                    )
                                    : 'N/A' }}

                            </p>

                        </div>

                    </div>


                    {{-- Shipping Address --}}
                    <div class="card mb-3">

                        <div class="card-header py-3 bg-transparent">

                            <h6 class="m-0 fw-bold">
                                Shipping Address
                            </h6>

                        </div>


                        <div class="card-body">

                            <p class="mb-2 fw-semibold">

                                {{ $customerName }}

                            </p>


                            {{-- Street Address --}}
                            <p class="mb-2">

                                <i class="fa-solid fa-location-dot me-2 text-muted"></i>

                                {{ $order->address ?: 'N/A' }}

                            </p>


                            {{-- Delivery Zone --}}
                            @if ($order->city)

                                <p class="mb-2">

                                    <strong>Delivery Zone:</strong>

                                    {{ $order->city }}

                                </p>

                            @endif


                            {{-- State --}}
                            <p class="mb-2">

                                <strong>State:</strong>

                                {{ $order->state?->name ?: 'N/A' }}

                            </p>


                            {{-- Country --}}
                            <p class="mb-2">

                                <strong>Country:</strong>

                                {{ $order->country?->name ?: 'N/A' }}

                            </p>


                            {{-- Delivery Method --}}
                            @if ($order->delivery_method)

                                <p class="mb-2">

                                    <strong>Delivery Method:</strong>

                                    {{ ucfirst($order->delivery_method) }}

                                </p>

                            @endif


                            {{-- Phone --}}
                            <p class="mb-0">

                                <strong>Phone:</strong>

                                {{ $order->phone ?: 'N/A' }}

                            </p>


                            {{-- Delivery Note --}}
                            @if ($order->order_note)

                                <hr>

                                <div
                                    class="alert alert-light border mb-0">

                                    <small class="text-muted d-block mb-1">
                                        Delivery Note
                                    </small>

                                    {{ $order->order_note }}

                                </div>

                            @endif

                        </div>

                    </div>


                    {{-- Attend To Order --}}
                    <div class="card">

                        <div class="card-header py-3 bg-transparent">

                            <h6 class="m-0 fw-bold">
                                Attend To Order
                            </h6>

                        </div>


                        <div class="card-body">


                            {{-- Order Status --}}
                            <form
                                action="{{ route(
                                    'admin.orders.updateStatus',
                                    $order->id
                                ) }}"
                                method="POST"
                                class="mb-4">

                                @csrf
                                @method('PATCH')


                                <label class="form-label">
                                    Order Status
                                </label>


                                <select name="status"
                                    class="form-select mb-2">

                                    <option value="pending"
                                        {{ $order->status === 'pending'
                                            ? 'selected'
                                            : '' }}>
                                        Pending
                                    </option>

                                    <option value="processing"
                                        {{ $order->status === 'processing'
                                            ? 'selected'
                                            : '' }}>
                                        Processing
                                    </option>

                                    <option value="shipped"
                                        {{ $order->status === 'shipped'
                                            ? 'selected'
                                            : '' }}>
                                        Shipped
                                    </option>

                                    <option value="delivered"
                                        {{ $order->status === 'delivered'
                                            ? 'selected'
                                            : '' }}>
                                        Delivered
                                    </option>

                                    <option value="cancelled"
                                        {{ $order->status === 'cancelled'
                                            ? 'selected'
                                            : '' }}>
                                        Cancelled
                                    </option>

                                </select>


                                <button type="submit"
                                    class="btn btn-primary w-100">

                                    <i class="fa-solid fa-truck-fast me-1"></i>

                                    Update Order Status

                                </button>

                            </form>


                            {{-- Payment Status --}}
                            <form
                                action="{{ route(
                                    'admin.orders.updatePaymentStatus',
                                    $order->id
                                ) }}"
                                method="POST">

                                @csrf
                                @method('PATCH')


                                <label class="form-label">
                                    Payment Status
                                </label>


                                <select name="payment_status"
                                    class="form-select mb-2">

                                    <option value="unpaid"
                                        {{ $order->payment_status === 'unpaid'
                                            ? 'selected'
                                            : '' }}>
                                        Unpaid
                                    </option>

                                    <option value="pending"
                                        {{ $order->payment_status === 'pending'
                                            ? 'selected'
                                            : '' }}>
                                        Pending
                                    </option>

                                    <option value="paid"
                                        {{ $order->payment_status === 'paid'
                                            ? 'selected'
                                            : '' }}>
                                        Paid
                                    </option>

                                    <option value="failed"
                                        {{ $order->payment_status === 'failed'
                                            ? 'selected'
                                            : '' }}>
                                        Failed
                                    </option>

                                    <option value="refunded"
                                        {{ $order->payment_status === 'refunded'
                                            ? 'selected'
                                            : '' }}>
                                        Refunded
                                    </option>

                                </select>


                                <button type="submit"
                                    class="btn btn-success w-100">

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