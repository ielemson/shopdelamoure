@extends('emails.layouts.app')

@section('title', 'Order Confirmation - ' . $order->order_no)

@section('eyebrow', 'Order Confirmation')

@section('heading', 'Thank You for Your Order')

@section('subheading')
    Your payment has been received successfully and your order is now being processed.
@endsection

@section('content')

    @php
        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        if ($customerName === '') {
            $customerName = $order->user?->name ?? 'Customer';
        }

        $isPickup = ($order->delivery_method ?? null) === 'pickup';

        $statusLabel = ucwords(str_replace('_', ' ', $order->status ?? 'processing'));
    @endphp


    <p style="margin:0 0 20px; font-size:15px; line-height:1.8; color:#444444;">
        Dear {{ $customerName }},
    </p>

    <p style="margin:0 0 25px; font-size:15px; line-height:1.8; color:#444444;">
        Thank you for shopping with {{ $setting?->website_name ?? 'Dela Moure' }}.
        We have received your payment and your order is now being processed.
    </p>


    {{-- ORDER SUMMARY --}}
    <table width="100%" cellpadding="0" cellspacing="0"
        style="
            margin:0 0 30px;
            border:1px solid #eadfce;
            border-radius:8px;
            overflow:hidden;
        ">

        <tr>
            <td colspan="2"
                style="
                    background:#F8F4EC;
                    padding:15px 18px;
                    font-size:16px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                Order Summary
            </td>
        </tr>

        <tr>
            <td style="padding:12px 18px; border-bottom:1px solid #eeeeee; color:#777777;">
                Order Number
            </td>

            <td align="right" style="padding:12px 18px; border-bottom:1px solid #eeeeee; font-weight:700; color:#333333;">
                {{ $order->order_no }}
            </td>
        </tr>

        <tr>
            <td style="padding:12px 18px; border-bottom:1px solid #eeeeee; color:#777777;">
                Order Status
            </td>

            <td align="right" style="padding:12px 18px; border-bottom:1px solid #eeeeee; font-weight:700; color:#333333;">
                {{ $statusLabel }}
            </td>
        </tr>

        <tr>
            <td style="padding:12px 18px; border-bottom:1px solid #eeeeee; color:#777777;">
                Payment
            </td>

            <td align="right" style="padding:12px 18px; border-bottom:1px solid #eeeeee; font-weight:700; color:#198754;">
                Paid
            </td>
        </tr>

        <tr>
            <td style="padding:12px 18px; color:#777777;">
                Fulfilment
            </td>

            <td align="right" style="padding:12px 18px; font-weight:700; color:#333333;">
                {{ $isPickup ? 'Pickup' : 'Shipping' }}
            </td>
        </tr>

    </table>


    {{-- ITEMS --}}
    <table width="100%" cellpadding="0" cellspacing="0"
        style="
            margin:0 0 30px;
            border-collapse:collapse;
            border:1px solid #eadfce;
        ">

        <thead>
            <tr style="background:#4A2C20; color:#ffffff;">
                <th align="left" style="padding:12px; font-size:13px;">
                    Item
                </th>

                <th align="center" style="padding:12px; font-size:13px;">
                    Qty
                </th>

                <th align="right" style="padding:12px; font-size:13px;">
                    Amount
                </th>
            </tr>
        </thead>

        <tbody>

            @forelse ($order->items as $item)
                <tr>

                    <td style="padding:14px 12px; border-bottom:1px solid #eeeeee;">

                        <strong style="color:#333333;">
                            {{ $item->name ?? ($item->product?->name ?? 'Product') }}
                        </strong>

                        @if (!empty($item->variant_name))
                            <div style="margin-top:4px; font-size:12px; color:#777777;">
                                {{ $item->variant_name }}
                            </div>
                        @endif

                        @if (!empty($item->sku))
                            <div style="margin-top:3px; font-size:11px; color:#999999;">
                                SKU: {{ $item->sku }}
                            </div>
                        @endif

                    </td>

                    <td align="center" style="padding:14px 12px; border-bottom:1px solid #eeeeee;">
                        {{ $item->quantity ?? 1 }}
                    </td>

                    <td align="right" style="padding:14px 12px; border-bottom:1px solid #eeeeee; white-space:nowrap;">
                        ₦{{ number_format((float) ($item->total ?? ($item->price ?? 0) * ($item->quantity ?? 1)), 2) }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="3" align="center" style="padding:20px; color:#777777;">
                        No order items found.
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>


    {{-- TOTALS --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 30px;">

        <tr>
            <td style="padding:6px 0; color:#777777;">
                Subtotal
            </td>

            <td align="right" style="padding:6px 0; color:#333333;">
                ₦{{ number_format((float) ($order->subtotal ?? 0), 2) }}
            </td>
        </tr>

        <tr>
            <td style="padding:6px 0; color:#777777;">
                {{ $isPickup ? 'Pickup' : 'Shipping' }}
            </td>

            <td align="right" style="padding:6px 0; color:#333333;">
                @if ($isPickup || (float) ($order->shipping ?? 0) <= 0)
                    FREE
                @else
                    ₦{{ number_format((float) $order->shipping, 2) }}
                @endif
            </td>
        </tr>

        @if ((float) ($order->discount ?? 0) > 0)
            <tr>
                <td style="padding:6px 0; color:#777777;">
                    Discount
                </td>

                <td align="right" style="padding:6px 0; color:#198754;">
                    -₦{{ number_format((float) $order->discount, 2) }}
                </td>
            </tr>
        @endif

        @if ((float) ($order->vat ?? 0) > 0)
            <tr>
                <td style="padding:6px 0; color:#777777;">
                    VAT
                </td>

                <td align="right" style="padding:6px 0; color:#333333;">
                    ₦{{ number_format((float) $order->vat, 2) }}
                </td>
            </tr>
        @endif

        <tr>
            <td
                style="
                    padding:14px 0 0;
                    border-top:1px solid #eadfce;
                    font-size:17px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                Total
            </td>

            <td align="right"
                style="
                    padding:14px 0 0;
                    border-top:1px solid #eadfce;
                    font-size:17px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                ₦{{ number_format((float) ($order->total ?? 0), 2) }}
            </td>
        </tr>

    </table>


    {{-- FULFILMENT DETAILS --}}
    @if ($isPickup)

        <div
            style="
                margin:0 0 30px;
                padding:20px;
                background:#F8F4EC;
                border:1px solid #eadfce;
                border-radius:8px;
            ">

            <div
                style="
                    margin-bottom:12px;
                    font-size:16px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                Pickup Information
            </div>

            <p style="margin:0 0 7px; line-height:1.7; color:#444444;">
                <strong>
                    {{ $order->pickupLocation?->name ?? 'Dela Moure Pickup Location' }}
                </strong>
            </p>

            @if ($order->pickupLocation?->address || $order->address)
                <p style="margin:0 0 7px; line-height:1.7; color:#555555;">
                    {{ $order->pickupLocation?->address ?? $order->address }}
                </p>
            @endif

            @if ($order->pickupLocation?->city || $order->city)
                <p style="margin:0 0 7px; line-height:1.7; color:#555555;">
                    {{ $order->pickupLocation?->city ?? $order->city }}
                    @if ($order->pickupLocation?->state?->name || $order->state?->name)
                        ,
                        {{ $order->pickupLocation?->state?->name ?? $order->state?->name }}
                    @endif
                </p>
            @endif

            @if ($order->pickupLocation?->opening_hours)
                <p style="margin:0 0 7px; line-height:1.7; color:#555555;">
                    <strong>Opening Hours:</strong>
                    {{ $order->pickupLocation->opening_hours }}
                </p>
            @endif

            @if ($order->pickupLocation?->pickup_time)
                <p style="margin:0 0 7px; line-height:1.7; color:#555555;">
                    <strong>Pickup Time:</strong>
                    {{ $order->pickupLocation->pickup_time }}
                </p>
            @endif

            <p
                style="
                    margin:15px 0 0;
                    padding:12px;
                    background:#ffffff;
                    border-left:4px solid #C9A227;
                    line-height:1.6;
                    color:#555555;
                ">
                Please wait for our pickup-ready confirmation before visiting the pickup location.
            </p>

        </div>
    @else
        <div
            style="
                margin:0 0 30px;
                padding:20px;
                background:#F8F4EC;
                border:1px solid #eadfce;
                border-radius:8px;
            ">

            <div
                style="
                    margin-bottom:12px;
                    font-size:16px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                Delivery Information
            </div>

            <p style="margin:0 0 7px; line-height:1.7; color:#444444;">
                <strong>{{ $customerName }}</strong>
            </p>

            @if ($order->address)
                <p style="margin:0 0 7px; line-height:1.7; color:#555555;">
                    {{ $order->address }}
                </p>
            @endif

            @if ($order->city)
                <p style="margin:0 0 7px; line-height:1.7; color:#555555;">
                    {{ $order->city }}
                </p>
            @endif

            @if ($order->state?->name)
                <p style="margin:0 0 7px; line-height:1.7; color:#555555;">
                    {{ $order->state->name }}
                    @if ($order->country?->name)
                        , {{ $order->country->name }}
                    @endif
                </p>
            @endif

            @if ($order->phone)
                <p style="margin:0; line-height:1.7; color:#555555;">
                    <strong>Phone:</strong>
                    {{ $order->phone }}
                </p>
            @endif

        </div>

    @endif


    @if (!empty($order->order_note))
        <div
            style="
                margin:0 0 25px;
                padding:15px 18px;
                background:#fafafa;
                border-left:4px solid #C9A227;
                color:#555555;
                line-height:1.7;
            ">

            <strong>Order Note:</strong><br>
            {{ $order->order_note }}

        </div>
    @endif


    <p style="margin:0; font-size:15px; line-height:1.8; color:#444444;">
        We will keep you updated as your order progresses.
        Thank you for choosing {{ $setting?->website_name ?? 'Dela Moure' }}.
    </p>

@endsection
