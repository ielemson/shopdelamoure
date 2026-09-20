<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;

class CheckoutCouponController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:100',
        ]);

        $code = strtoupper(trim($request->coupon_code));

        $coupon = Coupon::where('code', $code)->first();

        /*
        |--------------------------------------------------------------------------
        | Coupon Exists
        |--------------------------------------------------------------------------
        */

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Active
        |--------------------------------------------------------------------------
        */

        if (! $coupon->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon is currently inactive.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Start Date
        |--------------------------------------------------------------------------
        */

        if (
            $coupon->starts_at &&
            now()->lt($coupon->starts_at)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon is not yet available.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Expiry Date
        |--------------------------------------------------------------------------
        */

        if (
            $coupon->expires_at &&
            now()->gt($coupon->expires_at)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has expired.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Overall Usage Limit
        |--------------------------------------------------------------------------
        */

        if (
            $coupon->usage_limit !== null &&
            $coupon->usage_count >= $coupon->usage_limit
        ) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has reached its usage limit.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Cart
        |--------------------------------------------------------------------------
        */

        $cart = Cart::getContent();

        $subtotal = (float) Cart::getSubTotal();

        if ($subtotal <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Minimum Order Amount
        |--------------------------------------------------------------------------
        */

        if (
            $coupon->minimum_order_amount > 0 &&
            $subtotal < (float) $coupon->minimum_order_amount
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Your order does not meet the minimum amount required for this coupon.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Determine Eligible Subtotal
        |--------------------------------------------------------------------------
        */

        $eligibleSubtotal = 0;

        if ($coupon->scope === 'general') {

            $eligibleSubtotal = $subtotal;

        } elseif ($coupon->scope === 'product') {

            foreach ($cart as $item) {

                /*
                |--------------------------------------------------------------------------
                | Product-Specific Coupon
                |--------------------------------------------------------------------------
                |
                | This assumes the cart item's ID is the actual product ID.
                |
                */

                if ((int) $item->id === (int) $coupon->product_id) {

                    $eligibleSubtotal +=
                        (float) $item->price *
                        (int) $item->quantity;
                }
            }

            if ($eligibleSubtotal <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon does not apply to any product in your cart.',
                ], 422);
            }

        } else {

            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon configuration.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Discount
        |--------------------------------------------------------------------------
        */

        if ($coupon->discount_type === 'percentage') {

            $discount =
                $eligibleSubtotal *
                ((float) $coupon->discount_value / 100);

            /*
            |--------------------------------------------------------------------------
            | Maximum Percentage Discount Cap
            |--------------------------------------------------------------------------
            */

            if (
                $coupon->maximum_discount_amount !== null &&
                $discount > (float) $coupon->maximum_discount_amount
            ) {
                $discount =
                    (float) $coupon->maximum_discount_amount;
            }

        } elseif ($coupon->discount_type === 'fixed') {

            $discount =
                (float) $coupon->discount_value;

        } else {

            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon discount type.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Discount Cannot Exceed Eligible Merchandise
        |--------------------------------------------------------------------------
        */

        $discount = min(
            $discount,
            $eligibleSubtotal
        );

        $discount = round(
            max(0, $discount),
            2
        );

        /*
        |--------------------------------------------------------------------------
        | Store Coupon In Session
        |--------------------------------------------------------------------------
        */

        $couponData = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount_value,
            'discount_amount' => $discount,
        ];

        session([
            'coupon' => $couponData,
        ]);

        /*
        |--------------------------------------------------------------------------
        | JSON Response To Checkout
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully.',
            'coupon' => $couponData,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Remove Coupon
    |--------------------------------------------------------------------------
    */

    public function remove(Request $request)
    {
        session()->forget('coupon');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully.',
        ]);
    }
}
