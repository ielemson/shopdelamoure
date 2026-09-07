<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::role('Customer')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|string|max:20|unique:users,phone',
            'password'   => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $user->customerProfile()->create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'address'    => $request->address,
            'city'       => $request->city,
            'state'      => $request->state,
            'country'    => $request->country ?? 'Nigeria',
            'status'     => 'active',
            'customer_type' => 'regular',
        ]);

        $user->assignRole('Customer');

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(User $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(User $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'required|unique:users,phone,' . $customer->id,
        ]);

        $customer->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $customer->customerProfile()->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
        ]);

        return back()->with('success', 'Customer updated successfully.');
    }

    public function destroy(User $customer)
    {
        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}