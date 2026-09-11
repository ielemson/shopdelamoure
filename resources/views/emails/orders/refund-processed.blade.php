@extends('emails.layouts.app')

@section('title', 'Refund Processed - ' . $order->order_no)

@section('eyebrow', 'Refund Update')

@section('heading', 'Your Refund Has Been Processed')

@section('subheading')
    Your refund for the cancelled Dela Moure order has been successfully processed.
@endsection

@section('content')

    @php
        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        if ($customerName === '') {
            $customerName = $order->user?->name ?? 'Customer';
        }
    @endphp

    <p style="margin:0 0 15px; font-size:14px; line-height:1.7; color:#444444;">
        Dear {{ $customerName }},
    </p>

    <p style="margin:0 0 20px; font-size:14px; line-height:1.7; color:#444444;">
        This is to confirm that the refund for order
        <strong>{{ $order->order_no }}</strong>
        has been processed successfully.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0"
        style="margin:0 0 20px; border:1px solid #eadfce; border-radius:8px; overflow:hidden;">

        <tr>
            <td colspan="2" style="background:#F8F4EC; padding:12px 15px; font-size:15px; font-weight:700; color:#4A2C20;">
                Refund Record
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
                Refund Status
            </td>

            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700; color:#198754;">
                Processed
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                Refund Amount
            </td>

            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700;">
                ₦{{ number_format((float) ($order->refund_amount ?? ($order->total ?? 0)), 2) }}
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                Refund Reference
            </td>

            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-size:12px;">
                {{ $order->refund_reference ?: 'N/A' }}
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; color:#777777;">
                Payment Status
            </td>

            <td align="right" style="padding:9px 15px; font-weight:700;">
                Refunded
            </td>
        </tr>

    </table>

    <div
        style="margin:0 0 18px; padding:12px 14px; background:#F8F4EC; border-left:4px solid #C9A227; font-size:13px; line-height:1.7; color:#555555;">
        Please retain this email for your records. Depending on your bank or card issuer,
        it may take some time for the refunded funds to appear in your account.
    </div>

    <p style="margin:0; font-size:14px; line-height:1.7; color:#444444;">
        Thank you for choosing {{ $setting?->website_name ?? 'Dela Moure' }}.
    </p>

@endsection
