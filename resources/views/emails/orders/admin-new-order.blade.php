@extends('emails.layouts.app')

@section('title', 'New Order - ' . $order->order_no)

@section('eyebrow', 'New Order Received')

@section('heading', 'A New Order Has Been Paid')

@section('subheading')
    A customer has completed payment and the order is ready for processing.
@endsection

@section('content')

    @php
        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        if ($customerName === '') {
            $customerName = $order->user?->name ?? 'Customer';
        }

        $isPickup = ($order->delivery_method ?? null) === 'pickup';

        $statusLabel = ucwords(str_replace('_', ' ', $order->status ?? 'processing'));

        $paymentStatusLabel = ucwords(str_replace('_', ' ', $order->payment_status ?? 'paid'));
    @endphp


    {{-- INTRO --}}
    <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#444444;">
        A new order has been successfully paid on
        {{ $setting?->website_name ?? 'Dela Moure' }}.
    </p>


    {{-- ORDER DETAILS --}}
    <table width="100%" cellpadding="0" cellspacing="0"
        style="
            margin:0 0 22px;
            border:1px solid #eadfce;
            border-radius:8px;
            overflow:hidden;
        ">

        <tr>
            <td colspan="2"
                style="
                    background:#F8F4EC;
                    padding:12px 15px;
                    font-size:15px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                Order Details
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                Order Number
            </td>

            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700;">
                {{ $order->order_no }}
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                Order Status
            </td>

            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700;">
                {{ $statusLabel }}
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                Payment Status
            </td>

            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700; color:#198754;">
                {{ $paymentStatusLabel }}
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                Fulfilment
            </td>

            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700;">
                {{ $isPickup ? 'Pickup' : 'Shipping' }}
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; color:#777777;">
                Payment Reference
            </td>

            <td align="right" style="padding:9px 15px; font-size:12px; font-weight:700;">
                {{ $order->payment_reference ?? 'N/A' }}
            </td>
        </tr>

    </table>


    {{-- CUSTOMER DETAILS --}}
    <table width="100%" cellpadding="0" cellspacing="0"
        style="
            margin:0 0 22px;
            border:1px solid #eadfce;
            border-radius:8px;
            overflow:hidden;
        ">

        <tr>
            <td
                style="
                    background:#F8F4EC;
                    padding:12px 15px;
                    font-size:15px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                Customer
            </td>
        </tr>

        <tr>
            <td style="padding:13px 15px; color:#444444; line-height:1.7;">

                <strong>{{ $customerName }}</strong>

                @if ($order->email)
                    <br>
                    {{ $order->email }}
                @endif

                @if ($order->phone)
                    <br>
                    {{ $order->phone }}
                @endif

            </td>
        </tr>

    </table>


    {{-- ITEMS --}}
    <table width="100%" cellpadding="0" cellspacing="0"
        style="
            margin:0 0 22px;
            border-collapse:collapse;
            border:1px solid #eadfce;
        ">

        <thead>
            <tr style="background:#4A2C20; color:#ffffff;">

                <th align="left" style="padding:10px; font-size:12px;">
                    Item
                </th>

                <th align="center" style="padding:10px; font-size:12px;">
                    Qty
                </th>

                <th align="right" style="padding:10px; font-size:12px;">
                    Amount
                </th>

            </tr>
        </thead>

        <tbody>

            @forelse ($order->items as $item)
                <tr>

                    <td style="padding:11px 10px; border-bottom:1px solid #eeeeee;">

                        <strong style="font-size:13px;">
                            {{ $item->name ?? ($item->product?->name ?? 'Product') }}
                        </strong>

                        @if (!empty($item->variant_name))
                            <div style="margin-top:3px; font-size:11px; color:#777777;">
                                {{ $item->variant_name }}
                            </div>
                        @endif

                        @if (!empty($item->sku))
                            <div style="margin-top:2px; font-size:10px; color:#999999;">
                                SKU: {{ $item->sku }}
                            </div>
                        @endif

                    </td>

                    <td align="center" style="padding:11px 10px; border-bottom:1px solid #eeeeee;">
                        {{ $item->quantity ?? 1 }}
                    </td>

                    <td align="right"
                        style="
                            padding:11px 10px;
                            border-bottom:1px solid #eeeeee;
                            white-space:nowrap;
                        ">
                        ₦{{ number_format((float) ($item->total ?? ($item->price ?? 0) * ($item->quantity ?? 1)), 2) }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="3" align="center" style="padding:16px; color:#777777;">
                        No order items found.
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>


    {{-- TOTALS --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 22px;">

        <tr>
            <td style="padding:5px 0; color:#777777;">
                Subtotal
            </td>

            <td align="right" style="padding:5px 0;">
                ₦{{ number_format((float) ($order->subtotal ?? 0), 2) }}
            </td>
        </tr>

        <tr>
            <td style="padding:5px 0; color:#777777;">
                {{ $isPickup ? 'Pickup' : 'Shipping' }}
            </td>

            <td align="right" style="padding:5px 0;">

                @if ($isPickup || (float) ($order->shipping ?? 0) <= 0)
                    FREE
                @else
                    ₦{{ number_format((float) $order->shipping, 2) }}
                @endif

            </td>
        </tr>

        @if ((float) ($order->discount ?? 0) > 0)
            <tr>
                <td style="padding:5px 0; color:#777777;">
                    Discount
                </td>

                <td align="right" style="padding:5px 0; color:#198754;">
                    -₦{{ number_format((float) $order->discount, 2) }}
                </td>
            </tr>
        @endif

        @if ((float) ($order->vat ?? 0) > 0)
            <tr>
                <td style="padding:5px 0; color:#777777;">
                    VAT
                </td>

                <td align="right" style="padding:5px 0;">
                    ₦{{ number_format((float) $order->vat, 2) }}
                </td>
            </tr>
        @endif

        <tr>
            <td
                style="
                    padding:11px 0 0;
                    border-top:1px solid #eadfce;
                    font-size:16px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                Total
            </td>

            <td align="right"
                style="
                    padding:11px 0 0;
                    border-top:1px solid #eadfce;
                    font-size:16px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                ₦{{ number_format((float) ($order->total ?? 0), 2) }}
            </td>
        </tr>

    </table>


    {{-- FULFILMENT --}}
    @if ($isPickup)

        <div
            style="
                margin:0 0 20px;
                padding:15px;
                background:#F8F4EC;
                border:1px solid #eadfce;
                border-radius:8px;
            ">

            <div style="margin-bottom:8px; font-weight:700; color:#4A2C20;">
                Pickup Location
            </div>

            <div style="font-size:13px; line-height:1.7; color:#444444;">

                <strong>
                    {{ $order->pickupLocation?->name ?? 'Dela Moure Pickup Location' }}
                </strong>

                @if ($order->pickupLocation?->address || $order->address)
                    <br>
                    {{ $order->pickupLocation?->address ?? $order->address }}
                @endif

                @if ($order->pickupLocation?->city || $order->city)
                    <br>
                    {{ $order->pickupLocation?->city ?? $order->city }}
                @endif

                @if ($order->pickupLocation?->state?->name || $order->state?->name)
                    ,
                    {{ $order->pickupLocation?->state?->name ?? $order->state?->name }}
                @endif

            </div>

        </div>
    @else
        <div
            style="
                margin:0 0 20px;
                padding:15px;
                background:#F8F4EC;
                border:1px solid #eadfce;
                border-radius:8px;
            ">

            <div style="margin-bottom:8px; font-weight:700; color:#4A2C20;">
                Delivery Address
            </div>

            <div style="font-size:13px; line-height:1.7; color:#444444;">

                {{ $order->address ?? 'N/A' }}

                @if ($order->city)
                    <br>
                    {{ $order->city }}
                @endif

                @if ($order->state?->name)
                    ,
                    {{ $order->state->name }}
                @endif

                @if ($order->country?->name)
                    ,
                    {{ $order->country->name }}
                @endif

            </div>

        </div>

    @endif


    {{-- NOTE --}}
    @if (!empty($order->order_note))
        <div
            style="
                margin:0 0 18px;
                padding:12px 14px;
                background:#fafafa;
                border-left:4px solid #C9A227;
                color:#555555;
                line-height:1.6;
                font-size:13px;
            ">

            <strong>Customer Note:</strong><br>
            {{ $order->order_note }}

        </div>
    @endif


    <p style="margin:0; font-size:13px; line-height:1.7; color:#555555;">
        Please proceed with fulfilment from the Dela Moure admin dashboard.
    </p>

@endsection
