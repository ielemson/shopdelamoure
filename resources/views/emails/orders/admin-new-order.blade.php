@extends('emails.layouts.app')

@section('title', 'New Paid Order - ' . $order->order_no)

@section('eyebrow', 'Order Notification')

@section('heading', 'New Paid Order Received')

@section('subheading', 'A customer has successfully completed payment on the Dela Moure store.')

@section('content')

    <table width="100%" cellpadding="10" cellspacing="0"
        style="
            background:#F8F4EC;
            border:1px solid #eee4d6;
            margin-bottom:30px;
            font-size:14px;
        ">

        <tr>
            <td>
                <strong>Order Number</strong>
            </td>

            <td align="right">
                {{ $order->order_no }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Customer</strong>
            </td>

            <td align="right">
                {{ $order->first_name }}
                {{ $order->last_name }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Email</strong>
            </td>

            <td align="right">
                {{ $order->email }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Phone</strong>
            </td>

            <td align="right">
                {{ $order->phone }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Payment</strong>
            </td>

            <td align="right"
                style="
                    color:#2E7D32;
                    font-weight:700;
                ">
                PAID
            </td>
        </tr>

        <tr>
            <td>
                <strong>Paystack Reference</strong>
            </td>

            <td align="right">
                {{ $order->payment_reference }}
            </td>
        </tr>

    </table>


    <h3 style="
        color:#4A2C20;
        margin-bottom:15px;
    ">
        Purchased Items
    </h3>

    <table width="100%" cellpadding="10" cellspacing="0"
        style="
            border-collapse:collapse;
            font-size:14px;
        ">

        <thead>

            <tr style="background:#F8F4EC;">

                <th align="left">
                    Product
                </th>

                <th align="center">
                    Qty
                </th>

                <th align="right">
                    Total
                </th>

            </tr>

        </thead>

        <tbody>

            @foreach ($order->items as $item)
                <tr>

                    <td style="
                        border-bottom:1px solid #eeeeee;
                    ">
                        {{ $item->name }}
                    </td>

                    <td align="center"
                        style="
                            border-bottom:1px solid #eeeeee;
                        ">
                        {{ $item->quantity }}
                    </td>

                    <td align="right"
                        style="
                            border-bottom:1px solid #eeeeee;
                        ">
                        ₦{{ number_format($item->total, 2) }}
                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>


    <table width="100%" cellpadding="7" cellspacing="0" style="margin-top:20px;">

        <tr>

            <td align="right">
                Subtotal
            </td>

            <td align="right" width="150">
                ₦{{ number_format($order->subtotal, 2) }}
            </td>

        </tr>

        <tr>

            <td align="right">
                Delivery
            </td>

            <td align="right">
                ₦{{ number_format($order->shipping, 2) }}
            </td>

        </tr>

        <tr>

            <td align="right"
                style="
                    border-top:2px solid #C9A227;
                    padding-top:15px;
                    font-size:17px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                Total Paid
            </td>

            <td align="right"
                style="
                    border-top:2px solid #C9A227;
                    padding-top:15px;
                    font-size:17px;
                    font-weight:700;
                    color:#4A2C20;
                ">
                ₦{{ number_format($order->total, 2) }}
            </td>

        </tr>

    </table>


    <div
        style="
        margin-top:30px;
        background:#F8F4EC;
        padding:20px;
        border-left:4px solid #C9A227;
    ">

        <strong style="
            color:#4A2C20;
        ">
            Delivery Address
        </strong>

        <p style="
            margin:10px 0 0;
            line-height:1.8;
        ">

            {{ $order->address }}

            @if ($order->city)
                <br>
                Delivery Zone: {{ $order->city }}
            @endif

            @if ($order->delivery_method)
                <br>
                Delivery Method: {{ $order->delivery_method }}
            @endif

        </p>

    </div>


    @if ($order->order_note)
        <div style="
            margin-top:20px;
            background:#fffaf1;
            padding:15px;
        ">

            <strong style="color:#4A2C20;">
                Customer Note
            </strong>

            <div style="
                margin-top:8px;
                line-height:1.7;
            ">
                {{ $order->order_note }}
            </div>

        </div>
    @endif

@endsection
