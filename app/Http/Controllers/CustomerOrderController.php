<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;

class CustomerOrderController extends Controller
{
  public function index()
{
    $user = auth()->user();

    $orders = Order::with([
            'items',
            'address.country',
            'address.state',
        ])
        ->where('user_id', $user->id)
        ->latest()
        ->paginate(15);

    return view('customer.orders.index', compact(
        'user',
        'orders'
    ));
}
public function show(Order $order)
{
    abort_if($order->user_id !== auth()->id(), 403);

    $user = auth()->user();

    $order->load([
        'items.product',
    ]);

    $address = Address::with(['country', 'state'])
        ->where('user_id', $user->id)
        ->first();

    return view('customer.orders.show', compact(
        'user',
        'order',
        'address'
    ));
}
}