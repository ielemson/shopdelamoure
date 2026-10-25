<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class HomeController extends Controller
{
    // public function index()
    // {
    //     $user = auth()->user();

    //     if ($user->hasRole('Admin')) {
    //         return $this->adminDashboard();
    //     }

    //     if ($user->hasRole('Customer')) {
    //         return $this->customerDashboard($user);
    //     }

    //     return view('home');
    // }

    public function index()
    {
        $user = auth()->user();

        abort_unless($user, 401);

        if ($user->hasRole('Admin')) {
            if ($user->can('manage store')) {
                return $this->adminDashboard();
            }

            if ($user->can('manage website settings')) {
                return redirect()->route('admin.website.settings');
            }

            if ($user->can('manage delivery settings')) {
                return redirect()->route('admin.delivery.settings');
            }

            abort(403, 'No administrator access has been assigned to your account.');
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
