<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class CurrencyController extends Controller
{
    public function switch(Request $request, CartController $cartController)
    {
        $validated = $request->validate([
            'currency' => ['required', 'string', 'in:NGN,USD'],
        ]);
        $currency = strtoupper($validated['currency']);
        $hasWholesale = Cart::getContent()->contains(
            fn ($item) => $item->attributes->get('purchase_type') === 'wholesale'
        );

        if ($hasWholesale && $currency !== 'NGN') {
            // Reject the requested switch without recording it as a manual choice.
            session(['currency' => 'NGN']);

            return response()->json([
                'status' => false,
                'currency' => 'NGN',
                'symbol' => '₦',
                'currency_locked' => true,
                'message' => 'Wholesale orders are priced in NGN. Remove wholesale items before switching to USD.',
            ], 422);
        }

        $previousCurrency = session('currency', 'NGN');
        $previousManual = session('currency_manual', false);
        session([
            'currency' => $currency,
            'currency_manual' => true,
        ]);
        try {
            // Refresh the entire cart before returning success, including its side-cart prices.
            $cartController->refreshCurrency();
        } catch (Throwable $e) {
            session(['currency' => $previousCurrency, 'currency_manual' => $previousManual]);
            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => false, 'currency' => $previousCurrency,
                    'symbol' => $previousCurrency === 'USD' ? '$' : '₦',
                    'message' => collect($e->errors())->flatten()->first(),
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }
        // A saved coupon amount belongs to the previous currency and needs reapplication.
        if ($previousCurrency !== $currency) {
            session()->forget('coupon');
        }

        return response()->json([
            'status' => true,
            'currency' => $currency,
            'symbol' => $currency === 'USD' ? '$' : '₦',
            'currency_locked' => $hasWholesale,
            'message' => 'Currency changed successfully.',
        ]);
    }
}
