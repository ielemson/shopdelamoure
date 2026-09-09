<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Country;
use App\Models\ShippingRate;
use App\Models\State;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingRateController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Shipping Rates
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        // Nigeria only for now
        $countries = Country::where('name', 'Nigeria')
            ->orderBy('name')
            ->get();

        $shippingRates = ShippingRate::with([
            'country',
            'state',
            'areas',
        ])
            ->orderBy('country_id')
            ->orderBy('state_id')
            ->orderBy('zone_name')
            ->get();

        return view(
            'admin.shipping-rates.index',
            compact(
                'countries',
                'shippingRates'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | States By Country
    |--------------------------------------------------------------------------
    */

    public function states($countryId)
    {
        $states = State::where('country_id', $countryId)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return response()->json($states);
    }


    /*
    |--------------------------------------------------------------------------
    | Store Shipping Zone
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],

            'state_id' => [
                'required',
                'integer',
                'exists:states,id',
            ],

            'zone_name' => [
                'required',
                'string',
                'max:150',
            ],

            'areas' => [
                'required',
                'string',
            ],

            'shipping_cost' => [
                'required',
                'numeric',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Confirm State Belongs To Country
        |--------------------------------------------------------------------------
        */

        $stateExists = State::where(
            'id',
            $validated['state_id']
        )
            ->where(
                'country_id',
                $validated['country_id']
            )
            ->exists();

        if (!$stateExists) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'The selected state does not belong to the selected country.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Zone
        |--------------------------------------------------------------------------
        */

        $duplicate = ShippingRate::where(
            'country_id',
            $validated['country_id']
        )
            ->where(
                'state_id',
                $validated['state_id']
            )
            ->whereRaw(
                'LOWER(zone_name) = ?',
                [
                    strtolower(
                        trim($validated['zone_name'])
                    )
                ]
            )
            ->exists();

        if ($duplicate) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'This delivery zone already exists for the selected state.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prepare Covered Areas
        |--------------------------------------------------------------------------
        */

        $areas = $this->parseAreas(
            $validated['areas']
        );

        if ($areas->isEmpty()) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Please enter at least one covered area or location.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Save Shipping Zone
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $request,
            $validated,
            $areas
        ) {

            $shippingRate = ShippingRate::create([
                'country_id' =>
                $validated['country_id'],

                'state_id' =>
                $validated['state_id'],

                'zone_name' =>
                trim($validated['zone_name']),

                'shipping_cost' =>
                $validated['shipping_cost'],

                'is_active' =>
                $request->boolean('is_active'),
            ]);


            foreach ($areas as $area) {

                $shippingRate->areas()->create([
                    'name' => $area,
                ]);
            }
        });


        return redirect()
            ->route('admin.shipping-rates.index')
            ->with(
                'success',
                'Shipping zone created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Shipping Zone
    |--------------------------------------------------------------------------
    */

    public function edit(ShippingRate $shippingRate)
    {
        $shippingRate->load([
            'country',
            'state',
            'areas',
        ]);


        $countries = Country::where(
            'name',
            'Nigeria'
        )
            ->orderBy('name')
            ->get();


        $states = State::where(
            'country_id',
            $shippingRate->country_id
        )
            ->orderBy('name')
            ->get();


        return view(
            'admin.shipping-rates.edit',
            compact(
                'shippingRate',
                'countries',
                'states'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Shipping Zone
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        ShippingRate $shippingRate
    ) {

        $validated = $request->validate([
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],

            'state_id' => [
                'required',
                'integer',
                'exists:states,id',
            ],

            'zone_name' => [
                'required',
                'string',
                'max:150',
            ],

            'areas' => [
                'required',
                'string',
            ],

            'shipping_cost' => [
                'required',
                'numeric',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Confirm State Belongs To Country
        |--------------------------------------------------------------------------
        */

        $stateExists = State::where(
            'id',
            $validated['state_id']
        )
            ->where(
                'country_id',
                $validated['country_id']
            )
            ->exists();

        if (!$stateExists) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'The selected state does not belong to the selected country.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Zone
        |--------------------------------------------------------------------------
        */

        $duplicate = ShippingRate::where(
            'country_id',
            $validated['country_id']
        )
            ->where(
                'state_id',
                $validated['state_id']
            )
            ->where(
                'id',
                '!=',
                $shippingRate->id
            )
            ->whereRaw(
                'LOWER(zone_name) = ?',
                [
                    strtolower(
                        trim($validated['zone_name'])
                    )
                ]
            )
            ->exists();

        if ($duplicate) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'This delivery zone already exists for the selected state.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prepare Covered Areas
        |--------------------------------------------------------------------------
        */

        $areas = $this->parseAreas(
            $validated['areas']
        );

        if ($areas->isEmpty()) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Please enter at least one covered area or location.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Update Shipping Zone
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $request,
            $validated,
            $shippingRate,
            $areas
        ) {

            $shippingRate->update([
                'country_id' =>
                $validated['country_id'],

                'state_id' =>
                $validated['state_id'],

                'zone_name' =>
                trim($validated['zone_name']),

                'shipping_cost' =>
                $validated['shipping_cost'],

                'is_active' =>
                $request->boolean('is_active'),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Replace Covered Areas
            |--------------------------------------------------------------------------
            */

            $shippingRate->areas()->delete();


            foreach ($areas as $area) {

                $shippingRate->areas()->create([
                    'name' => $area,
                ]);
            }
        });


        return redirect()
            ->route('admin.shipping-rates.index')
            ->with(
                'success',
                'Shipping zone updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Shipping Zone
    |--------------------------------------------------------------------------
    */

    public function destroy(
        ShippingRate $shippingRate
    ) {

        $shippingRate->delete();


        return redirect()
            ->route('admin.shipping-rates.index')
            ->with(
                'success',
                'Shipping zone deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Parse Covered Areas
    |--------------------------------------------------------------------------
    |
    | Supports:
    |
    | Ministers Hill
    | Maitama Extension
    | Mpape
    |
    | OR:
    |
    | Ministers Hill, Maitama Extension, Mpape
    |
    */

    private function parseAreas(string $areas)
    {
        return collect(
            preg_split(
                '/[\r\n,]+/',
                $areas
            )
        )
            ->map(
                fn($area) =>
                trim($area)
            )
            ->filter()
            ->unique(
                fn($area) =>
                strtolower($area)
            )
            ->values();
    }
}
