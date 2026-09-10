<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

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

            /*
            |--------------------------------------------------------------------------
            | Shipping Information
            |--------------------------------------------------------------------------
            */

            'country',
            'state',

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

    /*
    |--------------------------------------------------------------------------
    | Update Order Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,processing,shipped,delivered,cancelled',
            ],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

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
}
