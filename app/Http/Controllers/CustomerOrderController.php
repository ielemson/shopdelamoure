<?php

namespace App\Http\Controllers;

use App\Models\Order;

class CustomerOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Customer Order List
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();

        $orders = Order::query()
            ->where('user_id', $user->id)

            // Load only the fields needed by the order purchase-type helpers.
            ->with([
                'items' => function ($query) {
                    $query->select([
                        'id',
                        'order_id',
                        'variant_options',
                    ]);
                },
            ])

            // Preserve the existing item count used by the order list.
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view(
            'customer.orders.index',
            compact('user', 'orders')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Customer Order Details
    |--------------------------------------------------------------------------
    */

    public function show(Order $order)
    {
        // Prevent customers from accessing another customer's order.
        abort_unless(
            auth()->check()
                && (int) $order->user_id === (int) auth()->id(),
            403
        );

        $user = auth()->user();

        $order->load([
            'user',
            'items.product',
            'items.variant',
            'country',
            'state',
            'pickupLocation.state',
        ]);

        return view(
            'customer.orders.show',
            compact('user', 'order')
        );
    }
}