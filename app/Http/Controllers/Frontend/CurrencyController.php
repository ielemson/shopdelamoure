<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function switch(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Currency
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'currency' => [
                'required',
                'string',
                'in:NGN,USD',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Selected Currency
        |--------------------------------------------------------------------------
        */

        $currency = strtoupper(
            $validated['currency']
        );

        /*
        |--------------------------------------------------------------------------
        | Store Manual Selection
        |--------------------------------------------------------------------------
        |
        | currency_manual tells DetectCurrency middleware not to override
        | the customer's chosen currency.
        |
        */

        session([
            'currency' => $currency,
            'currency_manual' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'status' => true,
            'currency' => $currency,
            'symbol' => $currency === 'USD' ? '$' : '₦',
            'message' => 'Currency changed successfully.',
        ]);
    }
}
