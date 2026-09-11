<?php

namespace App\Services;

use App\Mail\AdminNewOrderMail;
use App\Mail\CustomerOrderConfirmationMail;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderPaymentService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Process Successful Payment
    |--------------------------------------------------------------------------
    */

    public function processSuccessfulPayment(
        Order $order,
        array $gatewayData
    ): bool {
        /*
        |--------------------------------------------------------------------------
        | Mark Paid Atomically
        |--------------------------------------------------------------------------
        |
        | Callback and webhook may arrive almost at the same time.
        | Locking prevents the same order from being processed twice.
        |
        */

        $justPaid = DB::transaction(function () use (
            $order,
            $gatewayData
        ) {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->find($order->id);

            if (! $lockedOrder) {
                return false;
            }

            if ($lockedOrder->payment_status === 'paid') {
                return false;
            }

            $lockedOrder->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),

                'payment_gateway_response' => json_encode($gatewayData),
            ]);

            return true;
        });

        /*
        |--------------------------------------------------------------------------
        | Already Processed
        |--------------------------------------------------------------------------
        */

        if (! $justPaid) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Refresh Order
        |--------------------------------------------------------------------------
        */

        $order->refresh();

        $order->loadMissing([
            'items',
            'items.product',
            'items.variant',
            'country',
            'state',
            'pickupLocation.state',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Inventory
        |--------------------------------------------------------------------------
        */

        try {

            $this->inventoryService
                ->deductOrderStock($order);

        } catch (\Throwable $e) {

            Log::critical(
                'Paid order inventory deduction failed',
                [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'message' => $e->getMessage(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Customer Email
        |--------------------------------------------------------------------------
        */

        try {

            if (
                $order->email
                && filter_var(
                    $order->email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                Mail::to($order->email)
                    ->send(
                        new CustomerOrderConfirmationMail(
                            $order
                        )
                    );
            }

        } catch (\Throwable $e) {

            Log::error(
                'Customer order confirmation email failed',
                [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'email' => $order->email,
                    'message' => $e->getMessage(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Admin Email
        |--------------------------------------------------------------------------
        */

        try {

            $adminEmail =
                config('mail.admin_address');

            if (
                $adminEmail
                && filter_var(
                    $adminEmail,
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                Mail::to($adminEmail)
                    ->send(
                        new AdminNewOrderMail(
                            $order
                        )
                    );
            }

        } catch (\Throwable $e) {

            Log::error(
                'Admin new order email failed',
                [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'admin_email' => $adminEmail ?? null,
                    'message' => $e->getMessage(),
                ]
            );
        }

        return true;
    }
}
