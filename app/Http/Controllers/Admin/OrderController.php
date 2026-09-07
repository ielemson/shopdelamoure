<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  public function index(Request $request)
{
    $query = Order::with('user')->latest();

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $orders = $query->paginate(20)->withQueryString();

    $pendingOrders = Order::where('status', 'pending')->count();
    $paidOrders = Order::where('payment_status', 'paid')->count();
    $totalSales = Order::where('payment_status', 'paid')->sum('total');

    return view('admin.orders.index', compact(
        'orders',
        'pendingOrders',
        'paidOrders',
        'totalSales'
    ));
}

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Order status updated successfully.');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update([
            'payment_status' => $request->payment_status,
        ]);

        return back()->with('success', 'Payment status updated successfully.');
    }
}