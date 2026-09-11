<?php

namespace App\Http\Controllers;

use App\Mail\RefundProcessedMail;
use App\Models\Order;
use App\Services\OrderPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaystackWebhookController extends Controller
{
    public function handle(
        Request $request,
        OrderPaymentService $orderPaymentService
    ) {
        /*
        |--------------------------------------------------------------------------
        | Paystack Secret Key
        |--------------------------------------------------------------------------
        */

        $secretKey = config('services.paystack.secret_key');

        if (! $secretKey) {
            Log::critical(
                'Paystack webhook secret key is not configured.'
            );

            return response()->json(
                ['status' => false],
                500
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Paystack Signature
        |--------------------------------------------------------------------------
        */

        $signature = $request->header(
            'x-paystack-signature'
        );

        if (! $signature) {
            Log::warning(
                'Paystack webhook received without signature.'
            );

            return response()->json(
                ['status' => false],
                401
            );
        }

        $expectedSignature = hash_hmac(
            'sha512',
            $request->getContent(),
            $secretKey
        );

        if (! hash_equals($expectedSignature, $signature)) {
            Log::warning(
                'Invalid Paystack webhook signature.'
            );

            return response()->json(
                ['status' => false],
                401
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Event Payload
        |--------------------------------------------------------------------------
        */

        $event = $request->input('event');

        $data = $request->input(
            'data',
            []
        );

        if (! is_array($data)) {
            Log::warning(
                'Invalid Paystack webhook data.',
                [
                    'event' => $event,
                ]
            );

            return response()->json([
                'status' => true,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Successful Payment
        |--------------------------------------------------------------------------
        */

        if ($event === 'charge.success') {
            return $this->handleSuccessfulCharge(
                $data,
                $orderPaymentService
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Refund Events
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $event,
                [
                    'refund.pending',
                    'refund.processing',
                    'refund.needs-attention',
                    'refund.failed',
                    'refund.processed',
                ],
                true
            )
        ) {
            return $this->handleRefundEvent(
                $event,
                $data
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ignore Other Events
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'status' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Successful Charge
    |--------------------------------------------------------------------------
    */

    protected function handleSuccessfulCharge(
        array $data,
        OrderPaymentService $orderPaymentService
    ) {
        $reference = $data['reference'] ?? null;

        if (! $reference) {
            Log::warning(
                'Paystack charge.success received without payment reference.'
            );

            return response()->json([
                'status' => true,
            ]);
        }

        $order = Order::query()
            ->where(
                'payment_reference',
                $reference
            )
            ->first();

        if (! $order) {
            Log::error(
                'Paystack charge.success order not found.',
                [
                    'reference' => $reference,
                ]
            );

            return response()->json([
                'status' => true,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Payment Status
        |--------------------------------------------------------------------------
        */

        if (($data['status'] ?? '') !== 'success') {
            Log::warning(
                'Paystack charge.success event contains non-success status.',
                [
                    'order_id' => $order->id,
                    'reference' => $reference,
                    'status' => $data['status'] ?? null,
                ]
            );

            return response()->json([
                'status' => true,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Currency
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper(
                $data['currency'] ?? ''
            ) !== 'NGN'
        ) {
            Log::warning(
                'Paystack webhook currency mismatch.',
                [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'reference' => $reference,
                    'currency' => $data['currency'] ?? null,
                ]
            );

            return response()->json([
                'status' => true,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Amount
        |--------------------------------------------------------------------------
        */

        $expectedAmount = (int) round(
            (float) $order->total * 100
        );

        $paidAmount = (int) (
            $data['amount'] ?? 0
        );

        if ($paidAmount !== $expectedAmount) {
            Log::warning(
                'Paystack webhook amount mismatch.',
                [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'reference' => $reference,
                    'expected_amount' => $expectedAmount,
                    'paid_amount' => $paidAmount,
                ]
            );

            return response()->json([
                'status' => true,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Process Payment
        |--------------------------------------------------------------------------
        */

        try {
            $orderPaymentService
                ->processSuccessfulPayment(
                    $order,
                    $data
                );
        } catch (\Throwable $e) {
            Log::error(
                'Paystack webhook payment processing failed.',
                [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'reference' => $reference,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return response()->json(
                ['status' => false],
                500
            );
        }

        return response()->json([
            'status' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Refund Event
    |--------------------------------------------------------------------------
    */

    protected function handleRefundEvent(
        string $event,
        array $data
    ) {
        /*
        |--------------------------------------------------------------------------
        | Original Transaction Reference
        |--------------------------------------------------------------------------
        |
        | Paystack refund webhook payloads identify the original payment using
        | transaction_reference.
        |
        */

        $transactionReference =
            $data['transaction_reference'] ?? null;

        if (! $transactionReference) {
            Log::warning(
                'Paystack refund webhook received without transaction reference.',
                [
                    'event' => $event,
                    'refund_reference' => $data['refund_reference'] ?? null,
                ]
            );

            return response()->json([
                'status' => true,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Find Order
        |--------------------------------------------------------------------------
        */

        $order = Order::query()
            ->where(
                'payment_reference',
                $transactionReference
            )
            ->first();

        if (! $order) {
            Log::error(
                'Paystack refund webhook order not found.',
                [
                    'event' => $event,
                    'transaction_reference' => $transactionReference,
                    'refund_reference' => $data['refund_reference'] ?? null,
                ]
            );

            return response()->json([
                'status' => true,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Currency
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['currency'])
            && strtoupper($data['currency']) !== 'NGN'
        ) {
            Log::warning(
                'Paystack refund currency mismatch.',
                [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'currency' => $data['currency'],
                ]
            );

            return response()->json([
                'status' => true,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize Refund Status
        |--------------------------------------------------------------------------
        */

        $refundStatus = match ($event) {
            'refund.pending' => 'pending',
            'refund.processing' => 'processing',
            'refund.needs-attention' => 'needs-attention',
            'refund.failed' => 'failed',
            'refund.processed' => 'processed',
            default => $data['status'] ?? null,
        };

        /*
        |--------------------------------------------------------------------------
        | Refund Amount
        |--------------------------------------------------------------------------
        |
        | Paystack sends amount in the currency subunit.
        |
        */

        $refundAmount = isset($data['amount'])
            ? ((float) $data['amount'] / 100)
            : $order->refund_amount;

        /*
        |--------------------------------------------------------------------------
        | Was Refund Already Processed?
        |--------------------------------------------------------------------------
        |
        | Used to ensure the customer receives the completion email only once.
        |
        */

        $wasAlreadyProcessed =
            $order->refund_status === 'processed';

        /*
        |--------------------------------------------------------------------------
        | Update Refund Record
        |--------------------------------------------------------------------------
        */

        $updates = [
            'refund_status' => $refundStatus,

            'refund_amount' => $refundAmount,

            'refund_reference' => $data['refund_reference']
                    ?? $order->refund_reference,

            'refund_gateway_response' => $data,
        ];

        /*
        |--------------------------------------------------------------------------
        | Final Refund State
        |--------------------------------------------------------------------------
        */

        if ($refundStatus === 'processed') {
            $updates['payment_status'] =
                'refunded';

            $updates['refunded_at'] =
                ! empty($data['refunded_at'])
                    ? Carbon::parse($data['refunded_at'])
                    : now();
        }

        /*
        |--------------------------------------------------------------------------
        | Failed Refund
        |--------------------------------------------------------------------------
        |
        | The transaction remains paid because Paystack says failed refunds
        | return the transaction to its successful state.
        |
        */

        if ($refundStatus === 'failed') {
            $updates['payment_status'] =
                'paid';

            $updates['refunded_at'] =
                null;
        }

        $order->update($updates);

        $order->refresh();

        /*
        |--------------------------------------------------------------------------
        | Customer Refund Confirmation
        |--------------------------------------------------------------------------
        */

        if (
            $refundStatus === 'processed'
            && ! $wasAlreadyProcessed
        ) {
            try {
                $order->loadMissing([
                    'user',
                    'items',
                    'items.product',
                    'items.variant',
                    'country',
                    'state',
                    'pickupLocation.state',
                ]);

                if (
                    $order->email
                    && filter_var(
                        $order->email,
                        FILTER_VALIDATE_EMAIL
                    )
                ) {
                    Mail::to($order->email)
                        ->send(
                            new RefundProcessedMail($order)
                        );
                } else {
                    Log::warning(
                        'Refund processed email not sent because customer email is invalid.',
                        [
                            'order_id' => $order->id,
                            'order_no' => $order->order_no,
                            'email' => $order->email,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                Log::error(
                    'Refund processed customer email failed.',
                    [
                        'order_id' => $order->id,
                        'order_no' => $order->order_no,
                        'email' => $order->email,
                        'message' => $e->getMessage(),
                    ]
                );
            }
        }

        Log::info(
            'Paystack refund webhook processed.',
            [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'event' => $event,
                'refund_status' => $refundStatus,
                'refund_reference' => $order->refund_reference,
            ]
        );

        return response()->json([
            'status' => true,
        ]);
    }
}
