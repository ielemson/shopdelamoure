<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderCancelledMail;
use App\Mail\OrderPickedUpMail;
use App\Mail\ReadyForPickupMail;
use App\Mail\ReturnStatusMail;
use App\Mail\ShippingStatusMail;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Order::with([
            'user',
            'state',
            'country',
        ])
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $orders = $query
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Dashboard Statistics
        |--------------------------------------------------------------------------
        */

        $pendingOrders = Order::where(
            'status',
            'pending'
        )->count();

        $paidOrders = Order::where(
            'payment_status',
            'paid'
        )->count();

        $totalSales = Order::where(
            'payment_status',
            'paid'
        )->sum('total');

        return view(
            'admin.orders.index',
            compact(
                'orders',
                'pendingOrders',
                'paidOrders',
                'totalSales'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | View Order
    |--------------------------------------------------------------------------
    */

    public function show(Order $order)
    {
        $order->load([

            /*
            |--------------------------------------------------------------------------
            | Customer
            |--------------------------------------------------------------------------
            */

            'user',

            /*
            |--------------------------------------------------------------------------
            | Order Items
            |--------------------------------------------------------------------------
            */

            'items.product',
            'items.variant',

            /*
            |--------------------------------------------------------------------------
            | Shipping Information
            |--------------------------------------------------------------------------
            */

            'country',
            'state',

            /*
            |--------------------------------------------------------------------------
            | Pickup Information
            |--------------------------------------------------------------------------
            |
            | Used when delivery_method = pickup.
            | Loads the selected pickup location and its state.
            |
            */

            'pickupLocation.state',

            /*
            |--------------------------------------------------------------------------
            | Registered Customer Saved Address
            |--------------------------------------------------------------------------
            |
            | Guest orders will naturally return null here.
            |
            */

            'savedAddress',
        ]);

        return view(
            'admin.orders.show',
            compact('order')
        );
    }

    public function updateStatus(
        Request $request,
        Order $order,
        InventoryService $inventoryService
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,processing,shipped,delivered,ready_for_pickup,picked_up,cancelled',
            ],
        ]);

        $newStatus = $validated['status'];

        /*
        |--------------------------------------------------------------------------
        | Validate Fulfilment Status
        |--------------------------------------------------------------------------
        */

        if (
            $order->delivery_method === 'pickup'
            && in_array($newStatus, ['shipped', 'delivered'], true)
        ) {
            return back()->with(
                'error',
                'Shipping statuses cannot be used for pickup orders.'
            );
        }

        if (
            $order->delivery_method === 'shipping'
            && in_array($newStatus, ['ready_for_pickup', 'picked_up'], true)
        ) {
            return back()->with(
                'error',
                'Pickup statuses cannot be used for shipping orders.'
            );
        }

        $previousStatus = $order->status;

        if ($previousStatus === $newStatus) {
            return back()->with(
                'success',
                'Order status is already set to '
                .ucwords(str_replace('_', ' ', $newStatus))
                .'.'
            );
        }

        $previousStatus = $order->status;

        if ($previousStatus === $newStatus) {
            return back()->with(
                'success',
                'Order status is already set to '
                .ucwords(str_replace('_', ' ', $newStatus))
                .'.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Cancellation Of Completed Orders
        |--------------------------------------------------------------------------
        */

        if (
            $newStatus === 'cancelled'
            && in_array(
                $previousStatus,
                [
                    'delivered',
                    'picked_up',
                    'cancelled',
                ],
                true
            )
        ) {
            return back()->with(
                'error',
                'A completed or already cancelled order cannot be cancelled.'
            );
        }

        /*
|--------------------------------------------------------------------------
| Update Status / Restore Inventory
|--------------------------------------------------------------------------
*/

        try {

            if ($newStatus === 'cancelled') {

                DB::transaction(function () use (
                    $order,
                    $inventoryService
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Restore Inventory
                    |--------------------------------------------------------------------------
                    */

                    $inventoryService
                        ->restoreOrderStock($order);

                    /*
                    |--------------------------------------------------------------------------
                    | Mark Order Cancelled
                    |--------------------------------------------------------------------------
                    */

                    $order->update([
                        'status' => 'cancelled',
                    ]);
                });

            } else {

                /*
                |--------------------------------------------------------------------------
                | Normal Status Update
                |--------------------------------------------------------------------------
                */

                $order->update([
                    'status' => $newStatus,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Refresh Order
            |--------------------------------------------------------------------------
            */

            $order->refresh();

        } catch (\Throwable $e) {

            Log::error(
                'Order status update failed',
                [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'previous_status' => $previousStatus,
                    'new_status' => $newStatus,
                    'message' => $e->getMessage(),
                ]
            );

            return back()->with(
                'error',
                $newStatus === 'cancelled'
                    ? 'The order could not be cancelled because inventory restoration failed.'
                    : 'The order status could not be updated.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Notification Flags
        |--------------------------------------------------------------------------
        */

        $notificationAttempted = false;
        $customerNotified = false;

        /*
        |--------------------------------------------------------------------------
        | Ready For Pickup
        |--------------------------------------------------------------------------
        */

        if (
            $order->delivery_method === 'pickup'
            && $newStatus === 'ready_for_pickup'
        ) {
            $notificationAttempted = true;

            try {
                $order->loadMissing([
                    'user',
                    'items',
                    'items.product',
                    'items.variant',
                    'pickupLocation.state',
                ]);

                if (
                    $order->email
                    && filter_var($order->email, FILTER_VALIDATE_EMAIL)
                ) {
                    Mail::to($order->email)
                        ->send(new ReadyForPickupMail($order));

                    $customerNotified = true;
                } else {
                    Log::warning(
                        'Ready for pickup email not sent because customer email is invalid.',
                        [
                            'order_id' => $order->id,
                            'order_no' => $order->order_no,
                            'email' => $order->email,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                Log::error(
                    'Ready for pickup email failed',
                    [
                        'order_id' => $order->id,
                        'order_no' => $order->order_no,
                        'email' => $order->email,
                        'message' => $e->getMessage(),
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Shipping / Delivered
        |--------------------------------------------------------------------------
        */

        if (
            $order->delivery_method === 'shipping'
            && in_array($newStatus, ['shipped', 'delivered'], true)
        ) {
            $notificationAttempted = true;

            try {
                $order->loadMissing([
                    'user',
                    'items',
                    'items.product',
                    'items.variant',
                    'country',
                    'state',
                ]);

                if (
                    $order->email
                    && filter_var($order->email, FILTER_VALIDATE_EMAIL)
                ) {
                    Mail::to($order->email)
                        ->send(new ShippingStatusMail($order));

                    $customerNotified = true;
                } else {
                    Log::warning(
                        'Shipping status email not sent because customer email is invalid.',
                        [
                            'order_id' => $order->id,
                            'order_no' => $order->order_no,
                            'status' => $newStatus,
                            'email' => $order->email,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                Log::error(
                    'Shipping status email failed',
                    [
                        'order_id' => $order->id,
                        'order_no' => $order->order_no,
                        'status' => $newStatus,
                        'email' => $order->email,
                        'message' => $e->getMessage(),
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Picked Up
        |--------------------------------------------------------------------------
        */

        if (
            $order->delivery_method === 'pickup'
            && $newStatus === 'picked_up'
        ) {
            $notificationAttempted = true;

            try {
                $order->loadMissing([
                    'user',
                    'items',
                    'items.product',
                    'items.variant',
                    'pickupLocation.state',
                ]);

                if (
                    $order->email
                    && filter_var($order->email, FILTER_VALIDATE_EMAIL)
                ) {
                    Mail::to($order->email)
                        ->send(new OrderPickedUpMail($order));

                    $customerNotified = true;
                } else {
                    Log::warning(
                        'Picked up email not sent because customer email is invalid.',
                        [
                            'order_id' => $order->id,
                            'order_no' => $order->order_no,
                            'email' => $order->email,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                Log::error(
                    'Picked up email failed',
                    [
                        'order_id' => $order->id,
                        'order_no' => $order->order_no,
                        'email' => $order->email,
                        'message' => $e->getMessage(),
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Cancelled
        |--------------------------------------------------------------------------
        */

        if ($newStatus === 'cancelled') {
            $notificationAttempted = true;

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
                    && filter_var($order->email, FILTER_VALIDATE_EMAIL)
                ) {
                    Mail::to($order->email)
                        ->send(new OrderCancelledMail($order));

                    $customerNotified = true;
                } else {
                    Log::warning(
                        'Cancellation email not sent because customer email is invalid.',
                        [
                            'order_id' => $order->id,
                            'order_no' => $order->order_no,
                            'email' => $order->email,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                Log::error(
                    'Order cancellation email failed',
                    [
                        'order_id' => $order->id,
                        'order_no' => $order->order_no,
                        'email' => $order->email,
                        'message' => $e->getMessage(),
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        $labels = [
            'ready_for_pickup' => 'Order marked as ready for pickup.',
            'shipped' => 'Order marked as shipped.',
            'delivered' => 'Order marked as delivered.',
            'picked_up' => 'Order marked as picked up.',
            'cancelled' => 'Order cancelled successfully.',
        ];

        if (array_key_exists($newStatus, $labels)) {
            if ($customerNotified) {
                return back()->with(
                    'success',
                    $labels[$newStatus].' The customer has been notified by email.'
                );
            }

            if ($notificationAttempted) {
                return back()->with(
                    'warning',
                    $labels[$newStatus].' However, the customer notification email could not be sent.'
                );
            }
        }

        return back()->with(
            'success',
            'Order status updated successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Payment Status
    |--------------------------------------------------------------------------
    */

    public function updatePaymentStatus(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([
            'payment_status' => [
                'required',
                'in:unpaid,pending,paid,failed,refunded',
            ],
        ]);

        $order->update([
            'payment_status' => $validated['payment_status'],
        ]);

        return back()->with(
            'success',
            'Payment status updated successfully.'
        );
    }

    // public function refund(Order $order)
    // {
    //     /*
    //     |--------------------------------------------------------------------------
    //     | Validate Refund Eligibility
    //     |--------------------------------------------------------------------------
    //     */

    //     if ($order->status !== 'cancelled') {
    //         return back()->with(
    //             'error',
    //             'Only cancelled orders can be refunded.'
    //         );
    //     }

    //     if ($order->payment_method !== 'paystack') {
    //         return back()->with(
    //             'error',
    //             'Only Paystack payments can be refunded through Paystack.'
    //         );
    //     }

    //     if ($order->payment_status !== 'paid') {
    //         return back()->with(
    //             'error',
    //             'Only paid orders can be refunded.'
    //         );
    //     }

    //     if (! $order->payment_reference) {
    //         return back()->with(
    //             'error',
    //             'This order does not have a valid Paystack payment reference.'
    //         );
    //     }

    //     if (
    //         in_array(
    //             $order->refund_status,
    //             [
    //                 'initiating',
    //                 'pending',
    //                 'processing',
    //                 'needs-attention',
    //                 'processed',
    //             ],
    //             true
    //         )
    //     ) {
    //         return back()->with(
    //             'error',
    //             'A refund has already been initiated for this order.'
    //         );
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Paystack Configuration
    //     |--------------------------------------------------------------------------
    //     */

    //     $secretKey =
    //         config('services.paystack.secret_key');

    //     $paymentUrl =
    //         config('services.paystack.payment_url');

    //     if (! $secretKey || ! $paymentUrl) {
    //         return back()->with(
    //             'error',
    //             'Paystack is not properly configured.'
    //         );
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Claim Refund Request
    //     |--------------------------------------------------------------------------
    //     |
    //     | Prevent two admin requests from initiating the same refund.
    //     |
    //     */

    //     try {

    //         DB::transaction(function () use ($order) {

    //             $lockedOrder = Order::query()
    //                 ->lockForUpdate()
    //                 ->findOrFail($order->id);

    //             if (
    //                 in_array(
    //                     $lockedOrder->refund_status,
    //                     [
    //                         'initiating',
    //                         'pending',
    //                         'processing',
    //                         'needs-attention',
    //                         'processed',
    //                     ],
    //                     true
    //                 )
    //             ) {
    //                 throw new \RuntimeException(
    //                     'A refund has already been initiated for this order.'
    //                 );
    //             }

    //             $lockedOrder->update([
    //                 'refund_status' => 'initiating',

    //                 'refund_amount' => $lockedOrder->total,

    //                 'refund_requested_at' => now(),
    //             ]);
    //         });

    //     } catch (\Throwable $e) {

    //         return back()->with(
    //             'error',
    //             $e->getMessage()
    //         );
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Initiate Full Refund With Paystack
    //     |--------------------------------------------------------------------------
    //     |
    //     | Amount is deliberately omitted.
    //     | Paystack therefore refunds the full original transaction.
    //     |
    //     */

    //     try {

    //         $response = Http::withToken($secretKey)
    //             ->acceptJson()
    //             ->timeout(30)
    //             ->connectTimeout(15)
    //             ->post(
    //                 rtrim($paymentUrl, '/').'/refund',
    //                 [
    //                     'transaction' => $order->payment_reference,

    //                     'customer_note' => 'Refund for cancelled Dela Moure order '
    //                         .$order->order_no,

    //                     'merchant_note' => 'Full refund initiated for cancelled order '
    //                         .$order->order_no,
    //                 ]
    //             );

    //         $responseData = $response->json();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Paystack Rejected Refund
    //         |--------------------------------------------------------------------------
    //         */

    //         if (
    //             ! $response->successful()
    //             || ! ($responseData['status'] ?? false)
    //         ) {

    //             $order->update([
    //                 'refund_status' => 'request_failed',

    //                 'refund_gateway_response' => $responseData,
    //             ]);

    //             Log::error(
    //                 'Paystack refund request failed.',
    //                 [
    //                     'order_id' => $order->id,

    //                     'order_no' => $order->order_no,

    //                     'reference' => $order->payment_reference,

    //                     'response' => $responseData,
    //                 ]
    //             );

    //             return back()->with(
    //                 'error',
    //                 $responseData['message']
    //                     ?? 'Paystack could not initiate the refund.'
    //             );
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Paystack Refund Data
    //         |--------------------------------------------------------------------------
    //         */

    //         $data =
    //             $responseData['data'] ?? [];

    //         $refundStatus =
    //             $data['status'] ?? 'pending';

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Save Refund Request
    //         |--------------------------------------------------------------------------
    //         */

    //         $order->update([
    //             'refund_status' => $refundStatus,

    //             'refund_amount' => isset($data['amount'])
    //                     ? ((float) $data['amount'] / 100)
    //                     : $order->total,

    //             'paystack_refund_id' => $data['id'] ?? null,

    //             /*
    //              * Paystack's Create Refund response does not
    //              * necessarily contain refund_reference.
    //              * The refund webhook may provide it later.
    //              */

    //             'refund_reference' => $data['refund_reference'] ?? null,

    //             'refund_gateway_response' => $responseData,

    //             'refund_requested_at' => now(),
    //         ]);

    //         return back()->with(
    //             'success',
    //             'Refund request has been submitted to Paystack and is awaiting confirmation.'
    //         );

    //     } catch (\Throwable $e) {

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Request Exception
    //         |--------------------------------------------------------------------------
    //         */

    //         $order->update([
    //             'refund_status' => 'request_failed',
    //         ]);

    //         Log::error(
    //             'Paystack refund exception.',
    //             [
    //                 'order_id' => $order->id,

    //                 'order_no' => $order->order_no,

    //                 'reference' => $order->payment_reference,

    //                 'message' => $e->getMessage(),

    //                 'file' => $e->getFile(),

    //                 'line' => $e->getLine(),
    //             ]
    //         );

    //         return back()->with(
    //             'error',
    //             'Unable to initiate the refund at this time.'
    //         );
    //     }
    // }

    /*
|--------------------------------------------------------------------------
| Update Return / Refund
|--------------------------------------------------------------------------
*/

    /*
|--------------------------------------------------------------------------
| Update Return / Refund
|--------------------------------------------------------------------------
*/

    public function updateReturn(
        Request $request,
        Order $order,
        InventoryService $inventoryService
    ) {
        $request->validate([
            'action' => [
                'required',
                Rule::in([
                    'start',
                    'approve',
                    'reject',
                    'received',
                    'no_return_required',
                ]),
            ],

            'return_reason' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'return_note' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'return_required' => [
                'nullable',
                'boolean',
            ],
        ]);

        $action = $request->action;

        /*
        |--------------------------------------------------------------------------
        | Only Completed Orders Use Return Workflow
        |--------------------------------------------------------------------------
        */

        if (
            ! in_array(
                $order->status,
                [
                    'delivered',
                    'picked_up',
                ],
                true
            )
        ) {
            return back()->with(
                'error',
                'Only delivered or picked up orders can use the return/refund workflow.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Start Return / Refund
        |--------------------------------------------------------------------------
        */

        if ($action === 'start') {

            if ($order->return_status) {
                return back()->with(
                    'error',
                    'A return/refund process has already been started for this order.'
                );
            }

            $validated = $request->validate([
                'return_reason' => [
                    'required',
                    'string',
                    'max:2000',
                ],

                'return_required' => [
                    'required',
                    'boolean',
                ],
            ]);

            $order->update([
                'return_status' => 'requested',

                'return_reason' => $validated['return_reason'],

                'return_required' => (bool) $validated['return_required'],

                'return_requested_at' => now(),

                'return_approved_at' => null,

                'returned_at' => null,

                'return_rejected_at' => null,
            ]);

            $emailSent = $this->sendReturnStatusEmail(
                $order,
                'requested'
            );

            return back()->with(
                $emailSent ? 'success' : 'warning',
                $emailSent
                    ? 'Return/refund process has been started and the customer has been notified.'
                    : 'Return/refund process has been started, but the customer email could not be sent.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Approve Physical Return
        |--------------------------------------------------------------------------
        */

        if ($action === 'approve') {

            if ($order->return_status !== 'requested') {
                return back()->with(
                    'error',
                    'Only a requested return can be approved.'
                );
            }

            if (! $order->return_required) {
                return back()->with(
                    'error',
                    'This request does not require a physical return.'
                );
            }

            $order->update([
                'return_status' => 'approved',

                'return_approved_at' => now(),

                'return_rejected_at' => null,
            ]);

            $emailSent = $this->sendReturnStatusEmail(
                $order,
                'approved'
            );

            return back()->with(
                $emailSent ? 'success' : 'warning',
                $emailSent
                    ? 'Return request has been approved and the customer has been notified.'
                    : 'Return request has been approved, but the customer email could not be sent.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Approve Refund Without Return
        |--------------------------------------------------------------------------
        */

        if ($action === 'no_return_required') {

            if ($order->return_status !== 'requested') {
                return back()->with(
                    'error',
                    'Only a requested return/refund can be approved.'
                );
            }

            if ($order->return_required) {
                return back()->with(
                    'error',
                    'This request requires the merchandise to be returned.'
                );
            }

            $order->update([
                'return_status' => 'no_return_required',

                'return_approved_at' => now(),

                'return_rejected_at' => null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Important
            |--------------------------------------------------------------------------
            |
            | The customer keeps the merchandise.
            | Inventory must NOT be restored.
            |
            */

            $emailSent = $this->sendReturnStatusEmail(
                $order,
                'no_return_required'
            );

            return back()->with(
                $emailSent ? 'success' : 'warning',
                $emailSent
                    ? 'Refund without physical return has been approved and the customer has been notified.'
                    : 'Refund without physical return has been approved, but the customer email could not be sent.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Reject Return / Refund
        |--------------------------------------------------------------------------
        */

        if ($action === 'reject') {

            if (
                ! in_array(
                    $order->return_status,
                    [
                        'requested',
                        'approved',
                    ],
                    true
                )
            ) {
                return back()->with(
                    'error',
                    'This return/refund request cannot be rejected in its current state.'
                );
            }

            $validated = $request->validate([
                'return_note' => [
                    'required',
                    'string',
                    'min:5',
                    'max:2000',
                ],
            ]);

            $order->update([
                'return_status' => 'rejected',

                'return_note' => trim($validated['return_note']),

                'return_rejected_at' => now(),
            ]);

            $emailSent = $this->sendReturnStatusEmail(
                $order,
                'rejected'
            );

            return back()->with(
                $emailSent ? 'success' : 'warning',
                $emailSent
                    ? 'Return/refund request has been rejected and the customer has been notified.'
                    : 'Return/refund request has been rejected, but the customer email could not be sent.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Mark Returned Merchandise As Received
        |--------------------------------------------------------------------------
        */

        if ($action === 'received') {

            if ($order->return_status !== 'approved') {
                return back()->with(
                    'error',
                    'The return must be approved before it can be marked as received.'
                );
            }

            if (! $order->return_required) {
                return back()->with(
                    'error',
                    'This order does not require physical merchandise to be returned.'
                );
            }

            try {

                DB::transaction(function () use (
                    $order,
                    $inventoryService
                ) {

                    $lockedOrder = Order::query()
                        ->lockForUpdate()
                        ->findOrFail($order->id);

                    if ($lockedOrder->return_status === 'received') {
                        return;
                    }

                    if ($lockedOrder->return_status !== 'approved') {
                        throw new \RuntimeException(
                            'The return is no longer in an approved state.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Restore Returned Inventory
                    |--------------------------------------------------------------------------
                    */

                    $inventoryService
                        ->restoreReturnedOrderStock(
                            $lockedOrder
                        );

                    $lockedOrder->update([
                        'return_status' => 'received',

                        'returned_at' => now(),
                    ]);
                });

                $order->refresh();

                $emailSent = $this->sendReturnStatusEmail(
                    $order,
                    'received'
                );

                return back()->with(
                    $emailSent ? 'success' : 'warning',
                    $emailSent
                        ? 'Returned merchandise has been received, inventory restored, and the customer has been notified.'
                        : 'Returned merchandise has been received and inventory restored, but the customer email could not be sent.'
                );

            } catch (\Throwable $e) {

                Log::error(
                    'Returned order inventory restoration failed.',
                    [
                        'order_id' => $order->id,

                        'order_no' => $order->order_no,

                        'message' => $e->getMessage(),
                    ]
                );

                return back()->with(
                    'error',
                    'The return could not be completed because inventory restoration failed.'
                );
            }
        }

        return back()->with(
            'error',
            'Invalid return/refund action.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Send Return Status Email
    |--------------------------------------------------------------------------
    */

    protected function sendReturnStatusEmail(
        Order $order,
        string $returnEvent
    ): bool {
        try {

            $email =
                $order->email
                ?: $order->user?->email;

            if (
                ! $email
                || ! filter_var(
                    $email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                Log::warning(
                    'Return status email not sent because customer email is invalid.',
                    [
                        'order_id' => $order->id,

                        'order_no' => $order->order_no,

                        'return_event' => $returnEvent,

                        'email' => $email,
                    ]
                );

                return false;
            }

            $order->loadMissing([
                'user',
                'items',
                'items.product',
                'items.variant',
                'country',
                'state',
                'pickupLocation.state',
            ]);

            Mail::to($email)
                ->send(
                    new ReturnStatusMail(
                        $order,
                        $returnEvent
                    )
                );

            return true;

        } catch (\Throwable $e) {

            Log::error(
                'Return status email failed.',
                [
                    'order_id' => $order->id,

                    'order_no' => $order->order_no,

                    'return_event' => $returnEvent,

                    'message' => $e->getMessage(),
                ]
            );

            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Send Return Status Email
    |--------------------------------------------------------------------------
    */

    protected function sendReturnStatusEmail(
        Order $order,
        string $returnEvent
    ): bool {
        try {

            $email =
                $order->email
                ?: $order->user?->email;

            if (
                ! $email
                || ! filter_var(
                    $email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                Log::warning(
                    'Return status email not sent because customer email is invalid.',
                    [
                        'order_id' => $order->id,

                        'order_no' => $order->order_no,

                        'return_event' => $returnEvent,

                        'email' => $email,
                    ]
                );

                return false;
            }

            $order->loadMissing([
                'user',
                'items',
                'items.product',
                'items.variant',
                'country',
                'state',
                'pickupLocation.state',
            ]);

            Mail::to($email)
                ->send(
                    new ReturnStatusMail(
                        $order,
                        $returnEvent
                    )
                );

            return true;

        } catch (\Throwable $e) {

            Log::error(
                'Return status email failed.',
                [
                    'order_id' => $order->id,

                    'order_no' => $order->order_no,

                    'return_event' => $returnEvent,

                    'message' => $e->getMessage(),
                ]
            );

            return false;
        }
    }

    public function refund(Order $order)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Payment Eligibility
        |--------------------------------------------------------------------------
        */

        if ($order->payment_method !== 'paystack') {
            return back()->with(
                'error',
                'Only Paystack payments can be refunded through Paystack.'
            );
        }

        if ($order->payment_status !== 'paid') {
            return back()->with(
                'error',
                'Only paid orders can be refunded.'
            );
        }

        if (! $order->payment_reference) {
            return back()->with(
                'error',
                'This order does not have a valid Paystack payment reference.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Order / Return State
        |--------------------------------------------------------------------------
        |
        | Refunds are allowed when:
        |
        | 1. The order was cancelled before completion, OR
        | 2. A delivered / picked-up order has:
        |       - a physically returned item marked received, OR
        |       - an approved refund without return.
        |
        */

        $cancelledOrder =
            $order->status === 'cancelled';

        $completedReturnEligible =
            in_array(
                $order->status,
                ['delivered', 'picked_up'],
                true
            )
            && in_array(
                $order->return_status,
                ['received', 'no_return_required'],
                true
            );

        if (! $cancelledOrder && ! $completedReturnEligible) {
            return back()->with(
                'error',
                'This order is not eligible for a refund yet.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Refund
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $order->refund_status,
                [
                    'initiating',
                    'pending',
                    'processing',
                    'needs-attention',
                    'processed',
                ],
                true
            )
        ) {
            return back()->with(
                'error',
                'A refund has already been initiated for this order.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Paystack Configuration
        |--------------------------------------------------------------------------
        */

        $secretKey =
            config('services.paystack.secret_key');

        $paymentUrl =
            config('services.paystack.payment_url');

        if (! $secretKey || ! $paymentUrl) {
            return back()->with(
                'error',
                'Paystack is not properly configured.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Claim Refund Request
        |--------------------------------------------------------------------------
        |
        | Lock the order so two admin requests cannot initiate the same refund.
        |
        */

        try {

            DB::transaction(function () use ($order) {

                $lockedOrder = Order::query()
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                if (
                    in_array(
                        $lockedOrder->refund_status,
                        [
                            'initiating',
                            'pending',
                            'processing',
                            'needs-attention',
                            'processed',
                        ],
                        true
                    )
                ) {
                    throw new \RuntimeException(
                        'A refund has already been initiated for this order.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Re-check Eligibility While Locked
                |--------------------------------------------------------------------------
                */

                $cancelledOrder =
                    $lockedOrder->status === 'cancelled';

                $completedReturnEligible =
                    in_array(
                        $lockedOrder->status,
                        ['delivered', 'picked_up'],
                        true
                    )
                    && in_array(
                        $lockedOrder->return_status,
                        ['received', 'no_return_required'],
                        true
                    );

                if (
                    ! $cancelledOrder
                    && ! $completedReturnEligible
                ) {
                    throw new \RuntimeException(
                        'This order is no longer eligible for a refund.'
                    );
                }

                $lockedOrder->update([
                    'refund_status' => 'initiating',

                    'refund_amount' => $lockedOrder->total,

                    'refund_requested_at' => now(),
                ]);
            });

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Initiate Full Refund With Paystack
        |--------------------------------------------------------------------------
        |
        | Amount is omitted so Paystack processes a full refund.
        |
        */

        try {

            $response = Http::withToken($secretKey)
                ->acceptJson()
                ->timeout(30)
                ->connectTimeout(15)
                ->post(
                    rtrim($paymentUrl, '/').'/refund',
                    [
                        'transaction' => $order->payment_reference,

                        'customer_note' => 'Refund for Dela Moure order '
                            .$order->order_no,

                        'merchant_note' => $cancelledOrder
                                ? 'Full refund for cancelled order '
                                    .$order->order_no
                                : 'Full refund for completed return '
                                    .$order->order_no,
                    ]
                );

            $responseData = $response->json();

            /*
            |--------------------------------------------------------------------------
            | Refund Request Rejected
            |--------------------------------------------------------------------------
            */

            if (
                ! $response->successful()
                || ! ($responseData['status'] ?? false)
            ) {

                $order->update([
                    'refund_status' => 'request_failed',

                    'refund_gateway_response' => $responseData,
                ]);

                Log::error(
                    'Paystack refund request failed.',
                    [
                        'order_id' => $order->id,

                        'order_no' => $order->order_no,

                        'reference' => $order->payment_reference,

                        'response' => $responseData,
                    ]
                );

                return back()->with(
                    'error',
                    $responseData['message']
                        ?? 'Paystack could not initiate the refund.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Save Paystack Refund Response
            |--------------------------------------------------------------------------
            */

            $data =
                $responseData['data'] ?? [];

            $refundStatus =
                $data['status'] ?? 'pending';

            $order->update([
                'refund_status' => $refundStatus,

                'refund_amount' => isset($data['amount'])
                        ? ((float) $data['amount'] / 100)
                        : $order->total,

                'paystack_refund_id' => $data['id'] ?? null,

                'refund_reference' => $data['refund_reference'] ?? null,

                'refund_gateway_response' => $responseData,

                'refund_requested_at' => now(),
            ]);

            return back()->with(
                'success',
                'Refund request has been submitted to Paystack and is awaiting confirmation.'
            );

        } catch (\Throwable $e) {

            $order->update([
                'refund_status' => 'request_failed',
            ]);

            Log::error(
                'Paystack refund exception.',
                [
                    'order_id' => $order->id,

                    'order_no' => $order->order_no,

                    'reference' => $order->payment_reference,

                    'message' => $e->getMessage(),

                    'file' => $e->getFile(),

                    'line' => $e->getLine(),
                ]
            );

            return back()->with(
                'error',
                'Unable to initiate the refund at this time.'
            );
        }
    }
}
