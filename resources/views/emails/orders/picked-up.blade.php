@extends('emails.layouts.app')

@section('title', 'Pickup Completed - ' . $order->order_no)
@section('eyebrow', 'Order Completed')
@section('heading', 'Your Order Has Been Collected')

@section('subheading')
    Your Dela Moure pickup order has been successfully completed.
@endsection

@section('content')

    @php
        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        if ($customerName === '') {
            $customerName = $order->user?->name ?? 'Customer';
        }

        $paymentMethod = match ($order->payment_method) {
            'pay_on_pickup' => 'Pay on Pickup',
            'paystack' => 'Paystack',
            default => ucwords(str_replace('_', ' ', $order->payment_method ?? 'N/A')),
        };

        $paymentStatus = ucwords(str_replace('_', ' ', $order->payment_status ?? 'N/A'));
    @endphp

    <p style="margin:0 0 15px; font-size:14px; line-height:1.7; color:#444444;">
        Dear {{ $customerName }},
    </p>

    <p style="margin:0 0 20px; font-size:14px; line-height:1.7; color:#444444;">
        This email confirms that your Dela Moure order has been successfully collected
        from our pickup location.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0"
        style="margin:0 0 20px; border:1px solid #eadfce; border-radius:8px; overflow:hidden;">

        <tr>
            <td colspan="2" style="background:#F8F4EC; padding:12px 15px; font-size:15px; font-weight:700; color:#4A2C20;">
                Collection Record
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
                Status
            </td>
            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700; color:#198754;">
                Picked Up
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                Payment Method
            </td>
            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee;">
                {{ $paymentMethod }}
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                Payment Status
            </td>
            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700;">
                {{ $paymentStatus }}
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                Pickup Location
            </td>
            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee;">
                {{ $order->pickupLocation?->name ?? 'Dela Moure Pickup Location' }}
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; color:#777777;">
                Total
            </td>
            <td align="right" style="padding:9px 15px; font-weight:700; color:#4A2C20;">
                ₦{{ number_format((float) ($order->total ?? 0), 2) }}
            </td>
        </tr>
    </table>

    <div
        style="margin:0 0 18px; padding:12px 14px; background:#F8F4EC; border-left:4px solid #C9A227; font-size:13px; line-height:1.7; color:#555555;">
        Please retain this email as confirmation that order
        <strong>{{ $order->order_no }}</strong>
        has been collected.
    </div>

    <p style="margin:0; font-size:14px; line-height:1.7; color:#444444;">
        Thank you for choosing {{ $setting?->website_name ?? 'Dela Moure' }}.
        We look forward to serving you again.
    </p>

@endsection
