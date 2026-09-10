@extends('emails.layouts.app')

@section('title', 'Order Confirmed - ' . $order->order_no)

@section('eyebrow', 'Order Confirmation')

@section('heading', 'Thank You for Your Order')

@section('subheading', 'Your payment has been received successfully and your order is now being processed.')

@section('content')

    <p style="
        margin:0 0 20px;
        font-size:16px;
        line-height:1.8;
        color:#444444;
    ">
        Hello {{ $order->first_name }},
    </p>

    <p style="
        margin:0 0 25px;
        font-size:15px;
        line-height:1.8;
        color:#555555;
    ">
        Thank you for shopping with Dela Moure.
        We have successfully received your payment and your order
        is now being prepared for delivery.
    </p>


    {{-- ORDER SUMMARY --}}
    <table width="100%" cellpadding="0" cellspacing="0"
        style="
            margin:25px 0;
            background:#F8F4EC;
            border:1px solid #eee4d6;
            border-radius:8px;
        ">

        <tr>

            <td style="padding:20px;">

                <div
                    style="
                    font-size:12px;
                    text-transform:uppercase;
                    letter-spacing:1px;
                    color:#8B6B58;
                    margin-bottom:7px;
                ">
                    Order Number
                </div>

                <strong style="
                    font-size:18px;
                    color:#4A2C20;
                ">
                    {{ $order->order_no }}
                </strong>

            </td>


            <td align="right" style="padding:20px;">

                <div
                    style="
                    font-size:12px;
                    text-transform:uppercase;
                    letter-spacing:1px;
                    color:#8B6B58;
                    margin-bottom:7px;
                ">
                    Payment Status
                </div>

                <strong
                    style="
                    color:#2E7D32;
                    text-transform:uppercase;
                ">
                    Paid
                </strong>

            </td>

        </tr>

    </table>


    {{-- ITEMS --}}
    <h3 style="
        margin:30px 0 15px;
        color:#4A2C20;
        font-size:18px;
    ">
        Order Details
    </h3>

    <table width="100%" cellpadding="10" cellspacing="0"
        style="
            border-collapse:collapse;
            font-size:14px;
        ">

        <thead>

            <tr style="background:#F8F4EC;">

                <th align="left" style="color:#4A2C20;">
                    Product
                </th>

                <th align="center" style="color:#4A2C20;">
                    Qty
                </th>

                <th align="right" style="color:#4A2C20;">
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


    {{-- TOTALS --}}
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


        @if ((float) $order->discount > 0)
            <tr>

                <td align="right">
                    Discount
                </td>

                <td align="right">
                    -₦{{ number_format($order->discount, 2) }}
                </td>

            </tr>
        @endif


        <tr>

            <td align="right"
                style="
                    padding-top:15px;
                    border-top:2px solid #C9A227;
                    font-weight:700;
                    font-size:17px;
                    color:#4A2C20;
                ">
                Total Paid
            </td>

            <td align="right"
                style="
                    padding-top:15px;
                    border-top:2px solid #C9A227;
                    font-weight:700;
                    font-size:17px;
                    color:#4A2C20;
                ">
                ₦{{ number_format($order->total, 2) }}
            </td>

        </tr>

    </table>


    {{-- DELIVERY --}}
    <div
        style="
        margin-top:30px;
        padding:20px;
        background:#F8F4EC;
        border-left:4px solid #C9A227;
    ">

        <strong style="
            display:block;
            margin-bottom:10px;
            color:#4A2C20;
        ">
            Delivery Details
        </strong>

        <div style="
            font-size:14px;
            line-height:1.8;
            color:#555555;
        ">

            {{ $order->first_name }}
            {{ $order->last_name }}

            <br>

            {{ $order->address }}

            @if ($order->city)
                <br>
                {{ $order->city }}
            @endif

            <br>

            {{ $order->phone }}

        </div>

    </div>


    @if ($order->order_note)
        <div
            style="
            margin-top:20px;
            padding:15px;
            background:#fffaf1;
            border:1px solid #eee4d6;
        ">

            <strong style="color:#4A2C20;">
                Delivery Note
            </strong>

            <div style="
                margin-top:8px;
                line-height:1.7;
            ">
                {{ $order->order_note }}
            </div>

        </div>
    @endif


    <p style="
        margin:30px 0 0;
        line-height:1.8;
        font-size:15px;
    ">
        We will keep you informed as your order progresses.
        Thank you for choosing Dela Moure.
    </p>

@endsection
