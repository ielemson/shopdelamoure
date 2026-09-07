<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Address;
use App\Models\User;
use App\Models\Product;
class HomeController extends Controller
{
 

public function index()
{
    $user = auth()->user();

    if ($user->hasRole('Admin')) {
        return $this->adminDashboard();
    }

    if ($user->hasRole('Customer')) {
        return $this->customerDashboard($user);
    }

    return view('home');
}

private function adminDashboard()
{
    $totalOrders = Order::count();
    $pendingOrders = Order::where('status', 'pending')->count();
    $totalCustomers = User::role('Customer')->count();
    $totalProducts = Product::count();
    $totalSales = Order::where('payment_status', 'paid')->sum('total');

    $recentOrders = Order::with('user')
        ->latest()
        ->take(5)
        ->get();

    return view('admin.dashboard', compact(
        'totalOrders',
        'pendingOrders',
        'totalCustomers',
        'totalProducts',
        'totalSales',
        'recentOrders'
    ));
}

private function customerDashboard($user)
{
    $totalOrders = Order::where('user_id', $user->id)->count();

    $pendingOrders = Order::where('user_id', $user->id)
        ->whereIn('status', ['pending', 'processing'])
        ->count();

    $totalSpent = Order::where('user_id', $user->id)
        ->where('payment_status', 'paid')
        ->sum('total');

    $recentOrders = Order::with('items')
        ->where('user_id', $user->id)
        ->latest()
        ->take(5)
        ->get();

    $savedAddresses = Address::where('user_id', $user->id)
        ->latest()
        ->take(3)
        ->get();

    $wishlistCount = 0;

    return view('customer.dashboard', compact(
        'user',
        'totalOrders',
        'pendingOrders',
        'totalSpent',
        'recentOrders',
        'savedAddresses',
        'wishlistCount'
    ));
}
}