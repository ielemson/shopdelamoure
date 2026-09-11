@extends('emails.layouts.app')

@section('title', 'Order Ready for Pickup - ' . $order->order_no)

@section('eyebrow', 'Pickup Update')

@section('heading', 'Your Order Is Ready for Pickup')

@section('subheading')
    Your Dela Moure order has been prepared and is now ready for collection.
@endsection

@section('content')

    @php
        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        if ($customerName === '') {
            $customerName = $order->user?->name ?? 'Customer';
        }
    @endphp

    <p style="margin:0 0 16px; font-size:14px; line-height:1.7; color:#444444;">
        Dear {{ $customerName }},
    </p>

    <p style="margin:0 0 20px; font-size:14px; line-height:1.7; color:#444444;">
        Good news — your order has been prepared and is now ready for pickup.
        Please bring your order number when collecting your package.
    </p>

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
                Pickup Details
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
                Pickup Location
            </td>

            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700;">
                {{ $order->pickupLocation?->name ?? 'Dela Moure Pickup Location' }}
            </td>
        </tr>

        @if ($order->pickupLocation?->address || $order->address)
            <tr>
                <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                    Address
                </td>

                <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee;">
                    {{ $order->pickupLocation?->address ?? $order->address }}
                </td>
            </tr>
        @endif

        @if ($order->pickupLocation?->city || $order->city)
            <tr>
                <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                    City / State
                </td>

                <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee;">
                    {{ $order->pickupLocation?->city ?? $order->city }}

                    @if ($order->pickupLocation?->state?->name || $order->state?->name)
                        ,
                        {{ $order->pickupLocation?->state?->name ?? $order->state?->name }}
                    @endif
                </td>
            </tr>
        @endif

        @if ($order->pickupLocation?->opening_hours)
            <tr>
                <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                    Opening Hours
                </td>

                <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee;">
                    {{ $order->pickupLocation->opening_hours }}
                </td>
            </tr>
        @endif

        @if ($order->pickupLocation?->phone)
            <tr>
                <td style="padding:9px 15px; color:#777777;">
                    Contact
                </td>

                <td align="right" style="padding:9px 15px;">
                    {{ $order->pickupLocation->phone }}
                </td>
            </tr>
        @endif

    </table>

    <div
        style="
            margin:0 0 20px;
            padding:13px 15px;
            background:#F8F4EC;
            border-left:4px solid #C9A227;
            color:#555555;
            line-height:1.65;
            font-size:13px;
        ">
        Please present your order number
        <strong>{{ $order->order_no }}</strong>
        when collecting your package.
    </div>

    <p style="margin:0; font-size:14px; line-height:1.7; color:#444444;">
        Thank you for choosing {{ $setting?->website_name ?? 'Dela Moure' }}.
    </p>

@endsection
