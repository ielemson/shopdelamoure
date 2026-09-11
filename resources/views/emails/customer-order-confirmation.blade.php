@extends('emails.layouts.app')

@section('title', 'Order Confirmation')

@section('eyebrow', 'ORDER CONFIRMATION')

@section('heading', 'Thank You for Your Order')

@section('subheading', 'Your Dela Moure order has been received successfully.')

@section('content')

    @php

        $isPickup = $order->delivery_method === 'pickup';

        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        $statusLabel = ucwords(str_replace('_', ' ', $order->status ?? 'pending'));
    @endphp


    {{-- ==========================================================
        INTRODUCTION
    ========================================================== --}}

    <p style="margin:0 0 18px 0; line-height:1.7;">

        Hello {{ $customerName ?: 'Customer' }},

    </p>

    <p style="margin:0 0 24px 0; line-height:1.7;">

        Thank you for shopping with Dela Moure.
        Your order has been received and your payment has been confirmed.

    </p>


    {{-- ==========================================================
        ORDER SUMMARY
    ========================================================== --}}

    <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
        style="
            border-collapse:collapse;
            margin-bottom:25px;
            background:#FAF8F5;
        ">

        <tr>

            <td style="
                    padding:18px;
                    border:1px solid #EEE7DF;
                ">

                <div
                    style="
                        font-size:12px;
                        color:#8A7B70;
                        margin-bottom:5px;
                        text-transform:uppercase;
                    ">

                    Order Number

                </div>

                <strong style="font-size:16px; color:#4A2C20;">

                    {{ $order->order_no }}

                </strong>

            </td>


            <td style="
                    padding:18px;
                    border:1px solid #EEE7DF;
                ">

                <div
                    style="
                        font-size:12px;
                        color:#8A7B70;
                        margin-bottom:5px;
                        text-transform:uppercase;
                    ">

                    Order Status

                </div>

                <strong style="font-size:16px; color:#4A2C20;">

                    {{ $statusLabel }}

                </strong>

            </td>

        </tr>


        <tr>

            <td style="
                    padding:18px;
                    border:1px solid #EEE7DF;
                ">

                <div
                    style="
                        font-size:12px;
                        color:#8A7B70;
                        margin-bottom:5px;
                        text-transform:uppercase;
                    ">

                    Payment

                </div>

                <strong style="font-size:16px; color:#4A2C20;">

                    {{ ucfirst($order->payment_status ?? 'paid') }}

                </strong>

            </td>


            <td style="
                    padding:18px;
                    border:1px solid #EEE7DF;
                ">

                <div
                    style="
                        font-size:12px;
                        color:#8A7B70;
                        margin-bottom:5px;
                        text-transform:uppercase;
                    ">

                    Fulfilment

                </div>

                <strong style="font-size:16px; color:#4A2C20;">

                    {{ $isPickup ? 'Pickup' : 'Shipping' }}

                </strong>

            </td>

        </tr>

    </table>


    {{-- ==========================================================
        ITEMS
    ========================================================== --}}

    <h3 style="
            margin:0 0 15px 0;
            color:#4A2C20;
            font-size:18px;
        ">

        Items Ordered

    </h3>


    <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
        style="
            border-collapse:collapse;
            margin-bottom:25px;
        ">

        <thead>

            <tr>

                <th align="left"
                    style="
                        padding:12px;
                        background:#F5EFE9;
                        color:#4A2C20;
                        border:1px solid #E9DED3;
                        font-size:13px;
                    ">

                    Product

                </th>

                <th align="center"
                    style="
                        padding:12px;
                        background:#F5EFE9;
                        color:#4A2C20;
                        border:1px solid #E9DED3;
                        font-size:13px;
                    ">

                    Qty

                </th>

                <th align="right"
                    style="
                        padding:12px;
                        background:#F5EFE9;
                        color:#4A2C20;
                        border:1px solid #E9DED3;
                        font-size:13px;
                    ">

                    Total

                </th>

            </tr>

        </thead>


        <tbody>

            @foreach ($order->items as $item)
                <tr>

                    <td
                        style="
                            padding:12px;
                            border:1px solid #E9DED3;
                            vertical-align:top;
                        ">

                        <strong style="color:#3F312A;">

                            {{ $item->name }}

                        </strong>


                        @if ($item->variant_name)
                            <div
                                style="
                                    color:#8A7B70;
                                    font-size:12px;
                                    margin-top:4px;
                                ">

                                {{ $item->variant_name }}

                            </div>
                        @endif


                        @if ($item->sku)
                            <div
                                style="
                                    color:#A19489;
                                    font-size:11px;
                                    margin-top:3px;
                                ">

                                SKU: {{ $item->sku }}

                            </div>
                        @endif

                    </td>


                    <td align="center"
                        style="
                            padding:12px;
                            border:1px solid #E9DED3;
                        ">

                        {{ $item->quantity }}

                    </td>


                    <td align="right"
                        style="
                            padding:12px;
                            border:1px solid #E9DED3;
                        ">

                        ₦{{ number_format((float) ($item->total ?? 0), 2) }}

                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>


    {{-- ==========================================================
        TOTALS
    ========================================================== --}}

    <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
        style="
            border-collapse:collapse;
            margin-bottom:28px;
        ">

        <tr>

            <td style="
                    padding:8px 0;
                    color:#75675F;
                ">

                Subtotal

            </td>

            <td align="right"
                style="
                    padding:8px 0;
                    color:#4A2C20;
                    font-weight:600;
                ">

                ₦{{ number_format((float) ($order->subtotal ?? 0), 2) }}

            </td>

        </tr>


        <tr>

            <td style="
                    padding:8px 0;
                    color:#75675F;
                ">

                {{ $isPickup ? 'Pickup' : 'Shipping' }}

            </td>

            <td align="right"
                style="
                    padding:8px 0;
                    color:#4A2C20;
                    font-weight:600;
                ">

                @if ($isPickup)
                    <span style="color:#198754;">
                        FREE
                    </span>
                @else
                    ₦{{ number_format((float) ($order->shipping ?? 0), 2) }}
                @endif

            </td>

        </tr>


        @if ((float) ($order->discount ?? 0) > 0)
            <tr>

                <td style="padding:8px 0; color:#75675F;">
                    Discount
                </td>

                <td align="right"
                    style="
                        padding:8px 0;
                        color:#198754;
                        font-weight:600;
                    ">

                    -₦{{ number_format((float) $order->discount, 2) }}

                </td>

            </tr>
        @endif


        <tr>

            <td
                style="
                    padding:15px 0 5px 0;
                    border-top:2px solid #4A2C20;
                    font-size:17px;
                    font-weight:700;
                    color:#4A2C20;
                ">

                Total

            </td>

            <td align="right"
                style="
                    padding:15px 0 5px 0;
                    border-top:2px solid #4A2C20;
                    font-size:17px;
                    font-weight:700;
                    color:#4A2C20;
                ">

                ₦{{ number_format((float) ($order->total ?? 0), 2) }}

            </td>

        </tr>

    </table>


    {{-- ==========================================================
        PICKUP INFORMATION
    ========================================================== --}}

    @if ($isPickup)

        <div
            style="
                border:1px solid #E5D8CB;
                background:#FBF8F4;
                padding:20px;
                margin-bottom:25px;
            ">

            <h3
                style="
                    margin:0 0 15px 0;
                    color:#4A2C20;
                    font-size:18px;
                ">

                Pickup Information

            </h3>


            <p style="margin:0 0 8px 0; line-height:1.6;">

                <strong>Pickup Location:</strong><br>

                {{ $order->pickupLocation?->name ?: 'Dela Moure Pickup Location' }}

            </p>


            <p style="margin:0 0 8px 0; line-height:1.6;">

                <strong>Address:</strong><br>

                {{ $order->pickupLocation?->address ?: $order->address ?: 'Lagos, Nigeria' }}

            </p>


            @if ($order->pickupLocation?->state?->name || $order->state?->name)
                <p style="margin:0 0 8px 0; line-height:1.6;">

                    <strong>State:</strong><br>

                    {{ $order->pickupLocation?->state?->name ?: $order->state?->name }}

                </p>
            @endif


            @if ($order->pickupLocation?->opening_hours)
                <p style="margin:0 0 8px 0; line-height:1.6;">

                    <strong>Opening Hours:</strong><br>

                    {{ $order->pickupLocation->opening_hours }}

                </p>
            @endif


            @if ($order->pickupLocation?->pickup_time)
                <p style="margin:0; line-height:1.6;">

                    <strong>Pickup Time:</strong><br>

                    {{ $order->pickupLocation->pickup_time }}

                </p>
            @endif

        </div>


        <div
            style="
                border-left:4px solid #C9A227;
                padding:14px 16px;
                background:#FFF9E8;
                margin-bottom:25px;
                line-height:1.6;
            ">

            <strong>
                Please wait for pickup confirmation.
            </strong>

            <br>

            We will notify you when your order is ready for collection.
            Please do not visit the pickup location until you receive
            confirmation that your order is ready.

        </div>


        {{-- ==========================================================
        SHIPPING INFORMATION
    ========================================================== --}}
    @else
        <div
            style="
                border:1px solid #E5D8CB;
                background:#FBF8F4;
                padding:20px;
                margin-bottom:25px;
            ">

            <h3
                style="
                    margin:0 0 15px 0;
                    color:#4A2C20;
                    font-size:18px;
                ">

                Delivery Information

            </h3>


            <p style="margin:0 0 8px 0; line-height:1.6;">

                <strong>{{ $customerName }}</strong>

            </p>


            <p style="margin:0 0 8px 0; line-height:1.6;">

                {{ $order->address ?: 'N/A' }}

            </p>


            @if ($order->city)
                <p style="margin:0 0 8px 0; line-height:1.6;">

                    <strong>Delivery Zone:</strong>
                    {{ $order->city }}

                </p>
            @endif


            <p style="margin:0 0 8px 0; line-height:1.6;">

                {{ $order->state?->name ?? '' }}

                @if ($order->state?->name && $order->country?->name)
                    ,
                @endif

                {{ $order->country?->name ?? '' }}

            </p>


            <p style="margin:0; line-height:1.6;">

                <strong>Phone:</strong>

                {{ $order->phone ?: 'N/A' }}

            </p>

        </div>

    @endif


    {{-- ==========================================================
        NOTE
    ========================================================== --}}

    @if ($order->order_note)
        <div
            style="
                margin-bottom:25px;
                padding:16px;
                background:#F7F7F7;
                border:1px solid #EEEEEE;
            ">

            <strong
                style="
                    display:block;
                    margin-bottom:5px;
                    color:#4A2C20;
                ">

                {{ $isPickup ? 'Pickup Note' : 'Delivery Note' }}

            </strong>

            {{ $order->order_note }}

        </div>
    @endif


    {{-- ==========================================================
        CLOSING
    ========================================================== --}}

    <p style="margin:0 0 10px 0; line-height:1.7;">

        If you have any questions about your order,
        please contact our customer care team.

    </p>

    <p style="margin:0; line-height:1.7;">

        Thank you for choosing
        <strong>Dela Moure</strong>.

    </p>

@endsection
