<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->first_name.' '.$request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $user->customerProfile()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country ?? 'Nigeria',
            'status' => 'active',
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
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($customer->id),
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
                Rule::unique('users', 'phone')->ignore($customer->id),
            ],

            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'delivery_note' => ['nullable', 'string', 'max:2000'],

            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'suspended']),
            ],

            'customer_type' => [
                'required',
                Rule::in(['regular', 'wholesale', 'vip']),
            ],

            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => [
                'nullable',
                'string',
                'required_with:password',
            ],
        ]);

        DB::transaction(function () use ($customer, $validated) {
            $customer->name = trim(
                $validated['first_name'].' '.$validated['last_name']
            );
            $customer->email = $validated['email'];
            $customer->phone = $validated['phone'];

            if (! empty($validated['password'])) {
                $customer->password = Hash::make($validated['password']);
            }

            $customer->save();

            $customer->customerProfile()->updateOrCreate(
                [],
                [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'alternate_phone' => $validated['alternate_phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'state' => $validated['state'] ?? null,
                    'country' => $validated['country'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'delivery_note' => $validated['delivery_note'] ?? null,
                    'status' => $validated['status'],
                    'customer_type' => $validated['customer_type'],
                ]
            );
        });

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
