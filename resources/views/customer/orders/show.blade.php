@extends('layouts.customer')

@section('content')
<section class="ec-page-content ec-vendor-dashboard section-space-p">
    <div class="container">
        <div class="row">

            @include('customer.partials.sidebar')

            <div class="ec-shop-rightside col-lg-9 col-md-12">

                <div class="ec-vendor-dashboard-card space-bottom-30">
                    <div class="ec-vendor-card-header">
                        <h5>Order Details</h5>

                        <div class="ec-header-btn">
                            <a href="{{ route('customer.orders.index') }}" class="btn btn-lg btn-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="ec-vendor-card-body">
                        <p class="mb-1">
                            <strong>Order No:</strong> {{ $order->order_no }}
                        </p>

                        <p class="mb-0">
                            <strong>Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>

                <div class="row">

                    <div class="col-lg-4 col-md-6">
                        <div class="ec-vendor-dashboard-sort-card color-blue">
                            <h5>Order Status</h5>
                            <h3>{{ ucfirst($order->status ?? 'Pending') }}</h3>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="ec-vendor-dashboard-sort-card color-green">
                            <h5>Payment</h5>
                            <h3>{{ ucfirst($order->payment_status ?? 'Pending') }}</h3>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="ec-vendor-dashboard-sort-card color-orange">
                            <h5>Total</h5>
                            <h3>₦{{ number_format($order->total, 2) }}</h3>
                        </div>
                    </div>

                </div>

                <div class="ec-vendor-dashboard-card space-bottom-30">
                    <div class="ec-vendor-card-header">
                        <h5>Items Ordered</h5>
                    </div>

                    <div class="ec-vendor-card-body">
                        <div class="ec-vendor-card-table">
                            <table class="table ec-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->image)
                                                        <img src="{{ asset($item->image) }}"
                                                             alt="{{ $item->name }}"
                                                             style="width: 55px; height: 55px; object-fit: cover; border-radius: 6px; margin-right: 10px;">
                                                    @endif

                                                    <div>
                                                        <strong>{{ $item->name }}</strong>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>₦{{ number_format($item->price, 2) }}</td>

                                            <td>{{ $item->quantity }}</td>

                                            <td>₦{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                No items found for this order.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">

                   <div class="col-lg-6">
    <div class="ec-vendor-dashboard-card space-bottom-30">
        <div class="ec-vendor-card-header">
            <h5>Delivery Information</h5>
        </div>

        <div class="ec-vendor-card-body">
    @if($address)
        <p class="mb-1">
            <strong>Name:</strong>
            {{ $address->first_name }} {{ $address->last_name }}
        </p>

        <p class="mb-1">
            <strong>Email:</strong>
            {{ $address->email ?? $user->email ?? 'N/A' }}
        </p>

        <p class="mb-1">
            <strong>Phone:</strong>
            {{ $address->phone ?? 'N/A' }}
        </p>

        <p class="mb-1">
            <strong>Address:</strong>
            {{ $address->street_address ?? 'N/A' }},
            {{ $address->city ?? 'N/A' }},
            {{ $address->state?->name ?? 'N/A' }},
            {{ $address->country?->name ?? 'N/A' }}
        </p>
    @else
        <p class="mb-1 text-muted">
            No delivery address found for this order.
        </p>
    @endif

    <p class="mb-1">
        <strong>Delivery Method:</strong>
        {{ ucfirst(str_replace('_', ' ', $order->delivery_method)) }}
    </p>

    @if($order->order_note)
        <p class="mb-0">
            <strong>Order Note:</strong>
            {{ $order->order_note }}
        </p>
    @endif
</div>
    </div>
</div>

                    <div class="col-lg-6">
                        <div class="ec-vendor-dashboard-card space-bottom-30">
                            <div class="ec-vendor-card-header">
                                <h5>Payment Summary</h5>
                            </div>

                            <div class="ec-vendor-card-body">
                                <p class="mb-1">
                                    <strong>Payment Method:</strong>
                                    {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                </p>

                                <p class="mb-1">
                                    <strong>Payment Status:</strong>
                                    {{ ucfirst($order->payment_status ?? 'Pending') }}
                                </p>

                                @if($order->payment_reference)
                                    <p class="mb-1">
                                        <strong>Reference:</strong>
                                        {{ $order->payment_reference }}
                                    </p>
                                @endif

                                @if($order->paid_at)
                                    <p class="mb-1">
                                        <strong>Paid At:</strong>
                                        {{ $order->paid_at->format('d M Y, h:i A') }}
                                    </p>
                                @endif

                                <hr>

                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Subtotal:</span>
                                    <strong>₦{{ number_format($order->subtotal, 2) }}</strong>
                                </p>

                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Shipping:</span>
                                    <strong>₦{{ number_format($order->shipping, 2) }}</strong>
                                </p>

                                <p class="mb-1 d-flex justify-content-between">
                                    <span>VAT:</span>
                                    <strong>₦{{ number_format($order->vat, 2) }}</strong>
                                </p>

                                <p class="mb-1 d-flex justify-content-between">
                                    <span>Discount:</span>
                                    <strong>₦{{ number_format($order->discount, 2) }}</strong>
                                </p>

                                <hr>

                                <p class="mb-0 d-flex justify-content-between">
                                    <span>Total:</span>
                                    <strong>₦{{ number_format($order->total, 2) }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</section>
@endsection