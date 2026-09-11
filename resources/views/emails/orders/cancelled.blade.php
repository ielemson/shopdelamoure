@extends('emails.layouts.app')

@section('title', 'Order Cancelled - ' . $order->order_no)
@section('eyebrow', 'Order Update')
@section('heading', 'Your Order Has Been Cancelled')

@section('subheading')
    This email confirms the cancellation of your Dela Moure order.
@endsection

@section('content')

    @php
        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        if ($customerName === '') {
            $customerName = $order->user?->name ?? 'Customer';
        }

        $isPickup = ($order->delivery_method ?? null) === 'pickup';

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
        This is to confirm that order <strong>{{ $order->order_no }}</strong>
        has been cancelled.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0"
        style="margin:0 0 20px; border:1px solid #eadfce; border-radius:8px; overflow:hidden;">

        <tr>
            <td colspan="2" style="background:#F8F4EC; padding:12px 15px; font-size:15px; font-weight:700; color:#4A2C20;">
                Cancellation Record
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
            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700; color:#b02a37;">
                Cancelled
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                Fulfilment
            </td>
            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee;">
                {{ $isPickup ? 'Pickup' : 'Shipping' }}
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
            <td style="padding:9px 15px; color:#777777;">
                Order Total
            </td>
            <td align="right" style="padding:9px 15px; font-weight:700; color:#4A2C20;">
                ₦{{ number_format((float) ($order->total ?? 0), 2) }}
            </td>
        </tr>
    </table>

    @if (($order->payment_status ?? null) === 'paid')
        <div
            style="margin:0 0 18px; padding:12px 14px; background:#F8F4EC; border-left:4px solid #C9A227; font-size:13px; line-height:1.7; color:#555555;">
            This email confirms the order cancellation only.
            Any applicable refund will be communicated separately.
        </div>
    @elseif (($order->payment_method ?? null) === 'pay_on_pickup')
        <div
            style="margin:0 0 18px; padding:12px 14px; background:#F8F4EC; border-left:4px solid #C9A227; font-size:13px; line-height:1.7; color:#555555;">
            No pickup payment is due for this cancelled order.
        </div>
    @endif

    <p style="margin:0; font-size:14px; line-height:1.7; color:#444444;">
        If you need assistance regarding this cancellation, please contact
        {{ $setting?->website_name ?? 'Dela Moure' }} support.
    </p>

@endsection
