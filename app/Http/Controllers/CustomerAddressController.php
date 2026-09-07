<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class CustomerAddressController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $address = $user->address()
            ->with(['country', 'state'])
            ->first();

        $countries = Country::orderBy('name')->get();
        $states = State::orderBy('name')->get();

        return view('customer.addresses.index', compact(
            'user',
            'address',
            'countries',
            'states'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'string', 'max:30'],
            'street_address' => ['required', 'string', 'max:255'],
            'country_id'     => ['required', 'exists:countries,id'],
            'state_id'       => ['required', 'exists:states,id'],
            'city'           => ['required', 'string', 'max:100'],
            'postal_code'    => ['nullable', 'string', 'max:30'],
            'delivery_note'  => ['nullable', 'string', 'max:500'],
        ]);

        Address::updateOrCreate(
            ['user_id' => auth()->id()],
            array_merge($validated, [
                'is_default' => true,
            ])
        );

        return back()->with('success', 'Address saved successfully.');
    }
}