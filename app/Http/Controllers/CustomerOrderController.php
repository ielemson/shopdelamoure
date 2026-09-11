<?php

namespace App\Http\Controllers;

use App\Models\Order;

class CustomerOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Customer Orders
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();

        $orders = Order::query()

            /*
            |--------------------------------------------------------------------------
            | Item Count
            |--------------------------------------------------------------------------
            |
            | The order index only needs the number of purchased items.
            | This avoids loading every OrderItem record unnecessarily.
            |
            */

            ->withCount('items')

            /*
            |--------------------------------------------------------------------------
            | Customer Orders Only
            |--------------------------------------------------------------------------
            */

            ->where(
                'user_id',
                $user->id
            )

            ->latest()

            ->paginate(15);

        return view(
            'customer.orders.index',
            compact(
                'user',
                'orders'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Customer Order Details
    |--------------------------------------------------------------------------
    */

    public function show(Order $order)
    {
        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        |
        | A customer must never be able to view another customer's order
        | simply by changing the order ID in the URL.
        |
        */

        abort_unless(
            (int) $order->user_id === (int) auth()->id(),
            403
        );

        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Order Relationships
        |--------------------------------------------------------------------------
        */

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
            |
            */

            'pickupLocation.state',

        ]);

        return view(
            'customer.orders.show',
            compact(
                'user',
                'order'
            )
        );
    }
}
