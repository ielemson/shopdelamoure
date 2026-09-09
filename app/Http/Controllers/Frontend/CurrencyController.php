<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function switch(Request $request)
    {
        $validated = $request->validate([
            'currency' => [
                'required',
                'string',
                'in:NGN,USD',
            ],
        ]);

        session([
            'currency' => strtoupper(
                $validated['currency']
            ),
        ]);

        return response()->json([
            'status' => true,
            'currency' => session('currency'),
            'message' => 'Currency changed successfully.',
        ]);
    }
}
