@extends('emails.layouts.app')

@section('title', 'Return / Refund Update - ' . $order->order_no)

@section('eyebrow', 'Order Update')

@section('heading')
    @switch($returnEvent)
        @case('requested')
            Return / Refund Process Started
        @break

        @case('approved')
            Your Return Has Been Approved
        @break

        @case('no_return_required')
            Refund Approved — No Return Required
        @break

        @case('received')
            Your Returned Item Has Been Received
        @break

        @case('rejected')
            Return / Refund Request Update
        @break

        @default
            Return / Refund Update
    @endswitch
@endsection

@section('subheading')
    Order {{ $order->order_no }}
@endsection

@section('content')

    @php
        $customerName = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));

        if ($customerName === '') {
            $customerName = $order->user?->name ?? 'Customer';
        }

        $statusLabel = match ($returnEvent) {
            'requested' => 'Requested',
            'approved' => 'Approved',
            'no_return_required' => 'No Return Required',
            'received' => 'Received',
            'rejected' => 'Rejected',
            default => ucwords(str_replace('_', ' ', $returnEvent)),
        };
    @endphp

    <p style="margin:0 0 15px; font-size:14px; line-height:1.7; color:#444444;">
        Dear {{ $customerName }},
    </p>


    @if ($returnEvent === 'requested')

        <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#444444;">
            The return/refund process for your Dela Moure order
            <strong>{{ $order->order_no }}</strong>
            has been started.
        </p>

        @if ($order->return_required)
            <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#444444;">
                The merchandise will need to be returned before the refund can proceed.
                We will update you as the process moves forward.
            </p>
        @else
            <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#444444;">
                This request is being reviewed as a refund without physical return.
                We will update you once it is approved.
            </p>
        @endif
    @elseif ($returnEvent === 'approved')
        <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#444444;">
            Your return for order
            <strong>{{ $order->order_no }}</strong>
            has been approved.
        </p>

        <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#444444;">
            Please follow the return instructions provided by Dela Moure.
            Your refund will be processed after the returned merchandise has been received
            and accepted.
        </p>
    @elseif ($returnEvent === 'no_return_required')
        <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#444444;">
            Your refund request for order
            <strong>{{ $order->order_no }}</strong>
            has been approved without requiring you to return the merchandise.
        </p>

        <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#444444;">
            This email confirms the approval only. A separate notification will be sent
            when the payment refund has actually been processed.
        </p>
    @elseif ($returnEvent === 'received')
        <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#444444;">
            We have received and accepted the returned merchandise for order
            <strong>{{ $order->order_no }}</strong>.
        </p>

        <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#444444;">
            If a payment refund is due, it will now proceed separately.
            You will receive another email once the refund has been processed.
        </p>
    @elseif ($returnEvent === 'rejected')
        <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#444444;">
            The return/refund request associated with order
            <strong>{{ $order->order_no }}</strong>
            could not be approved.
        </p>

        @if ($order->return_note)
            <div
                style="margin:0 0 18px; padding:12px 14px; background:#F8F4EC; border-left:4px solid #C9A227; font-size:13px; line-height:1.7; color:#555555;">
                <strong>Note:</strong>
                {{ $order->return_note }}
            </div>
        @endif

    @endif


    <table width="100%" cellpadding="0" cellspacing="0"
        style="margin:0 0 20px; border:1px solid #eadfce; border-radius:8px; overflow:hidden;">

        <tr>
            <td colspan="2"
                style="background:#F8F4EC; padding:12px 15px; font-size:15px; font-weight:700; color:#4A2C20;">
                Return / Refund Record
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
                Return Status
            </td>

            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700;">
                {{ $statusLabel }}
            </td>
        </tr>

        <tr>
            <td style="padding:9px 15px; border-bottom:1px solid #eeeeee; color:#777777;">
                Physical Return Required
            </td>

            <td align="right" style="padding:9px 15px; border-bottom:1px solid #eeeeee; font-weight:700;">
                {{ $order->return_required ? 'Yes' : 'No' }}
            </td>
        </tr>

        @if ($order->return_reason)
            <tr>
                <td style="padding:9px 15px; color:#777777; vertical-align:top;">
                    Reason
                </td>

                <td align="right" style="padding:9px 15px; font-weight:700;">
                    {{ $order->return_reason }}
                </td>
            </tr>
        @endif

    </table>


    <p style="margin:0; font-size:14px; line-height:1.7; color:#444444;">
        Thank you for choosing {{ $setting?->website_name ?? 'Dela Moure' }}.
    </p>

@endsection
