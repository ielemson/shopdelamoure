@extends('emails.layouts.app')

@section('title', $order->status === 'delivered' ? 'Order Delivered - ' . $order->order_no : 'Order Shipped - ' .
    $order->order_no)

@section('eyebrow', $order->status === 'delivered' ? 'Delivery Update' : 'Shipping Update')

@section('heading', $order->status === 'delivered' ? 'Your Order Has Been Delivered' : 'Your Order Is On Its Way')

@section('subheading')

    @if ($order->status === 'delivered')
        Your Dela Moure order has been marked as delivered.
    @else
        Your Dela Moure order has been shipped and is on its way to you.
    @endif

@endsection


@section('content')

    @php

        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        if ($customerName === '') {
            $customerName = $order->user?->name ?? 'Customer';
        }
    @endphp


    <p
        style="
            margin:0 0 16px;
            font-size:14px;
            line-height:1.7;
            color:#444444;
        ">
        Dear {{ $customerName }},
    </p>


    @if ($order->status === 'delivered')
        <p
            style="
                margin:0 0 20px;
                font-size:14px;
                line-height:1.7;
                color:#444444;
            ">
            Your order has been marked as successfully delivered.
            We hope you enjoy your purchase.
        </p>
    @else
        <p
            style="
                margin:0 0 20px;
                font-size:14px;
                line-height:1.7;
                color:#444444;
            ">
            Your order has left our fulfilment process and is now
            on its way to your delivery address.
        </p>
    @endif


    {{-- ORDER INFORMATION --}}
    <table width="100%" cellpadding="0" cellspacing="0"
        style="
            margin:0 0 20px;
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
                Order Information
            </td>

        </tr>


        <tr>

            <td
                style="
                    padding:9px 15px;
                    border-bottom:1px solid #eeeeee;
                    color:#777777;
                ">
                Order Number
            </td>

            <td align="right"
                style="
                    padding:9px 15px;
                    border-bottom:1px solid #eeeeee;
                    font-weight:700;
                ">
                {{ $order->order_no }}
            </td>

        </tr>


        <tr>

            <td
                style="
                    padding:9px 15px;
                    border-bottom:1px solid #eeeeee;
                    color:#777777;
                ">
                Status
            </td>

            <td align="right"
                style="
                    padding:9px 15px;
                    border-bottom:1px solid #eeeeee;
                    font-weight:700;
                    color:#198754;
                ">
                {{ ucwords(str_replace('_', ' ', $order->status)) }}
            </td>

        </tr>


        <tr>

            <td style="
                    padding:9px 15px;
                    color:#777777;
                ">
                Total
            </td>

            <td align="right"
                style="
                    padding:9px 15px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                ₦{{ number_format((float) $order->total, 2) }}
            </td>

        </tr>

    </table>


    {{-- DELIVERY ADDRESS --}}
    <div
        style="
            margin:0 0 20px;
            padding:14px 15px;
            background:#F8F4EC;
            border:1px solid #eadfce;
            border-radius:8px;
        ">

        <div
            style="
                margin-bottom:7px;
                font-weight:700;
                color:#4A2C20;
            ">
            Delivery Address
        </div>

        <div
            style="
                font-size:13px;
                line-height:1.7;
                color:#444444;
            ">

            {{ $order->address }}

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


    @if ($order->status === 'delivered')
        <p
            style="
                margin:0;
                font-size:14px;
                line-height:1.7;
                color:#444444;
            ">
            Thank you for choosing
            {{ $setting?->website_name ?? 'Dela Moure' }}.
            We look forward to serving you again.
        </p>
    @else
        <p
            style="
                margin:0;
                font-size:14px;
                line-height:1.7;
                color:#444444;
            ">
            We will continue to keep you informed as your order
            progresses.
        </p>
    @endif

@endsection
