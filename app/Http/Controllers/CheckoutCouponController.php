<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;

class CheckoutCouponController extends Controller
{
    public function apply(Request $request)
    {
        $data = $request->validate([
            'coupon_code' => ['required', 'string', 'max:100'],
        ]);
        $cart = Cart::getContent();

        // Wholesale pricing cannot be combined with promotional codes.
        if ($cart->contains(fn ($item) => $item->attributes->get('purchase_type') === 'wholesale')) {
            session()->forget('coupon');

            return $this->error('Promotional codes are unavailable for carts containing wholesale products.');
        }

        $code = strtoupper(trim($data['coupon_code']));
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            return $this->error('Invalid coupon code.');
        }
        if (! $coupon->is_active) {
            return $this->error('This coupon is currently inactive.');
        }
        $now = now();
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            return $this->error('This coupon is not yet available.');
        }
        if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
            return $this->error('This coupon has expired.');
        }
        if ($coupon->usage_limit !== null && $coupon->usage_count >= $coupon->usage_limit) {
            return $this->error('This coupon has reached its usage limit.');
        }

        $subtotal = (float) Cart::getSubTotal(false);
        if ($cart->isEmpty() || $subtotal <= 0) {
            return $this->error('Your cart is empty.');
        }
        if ($coupon->minimum_order_amount > 0 && $subtotal < (float) $coupon->minimum_order_amount) {
            return $this->error('Your order does not meet the minimum amount required for this coupon.');
        }

        $eligibleSubtotal = 0;
        if ($coupon->scope === 'general') {
            $eligibleSubtotal = $subtotal;
        } elseif ($coupon->scope === 'product') {
            foreach ($cart as $item) {
                // Both simple and variant rows carry the actual product ID here.
                if ((int) $item->attributes->get('product_id') === (int) $coupon->product_id) {
                    $eligibleSubtotal += (float) $item->price * (int) $item->quantity;
                }
            }
            if ($eligibleSubtotal <= 0) {
                return $this->error('This coupon does not apply to any product in your cart.');
            }
        } else {
            return $this->error('Invalid coupon configuration.');
        }

        if ($coupon->discount_type === 'percentage') {
            $discount = $eligibleSubtotal * ((float) $coupon->discount_value / 100);
            if ($coupon->maximum_discount_amount !== null && $discount > (float) $coupon->maximum_discount_amount) {
                $discount = (float) $coupon->maximum_discount_amount;
            }
        } elseif ($coupon->discount_type === 'fixed') {
            $discount = (float) $coupon->discount_value;
        } else {
            return $this->error('Invalid coupon discount type.');
        }
        $discount = round(max(0, min($discount, $eligibleSubtotal)), 2);

        $couponData = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount_value,
            'discount_amount' => $discount,
        ];
        session(['coupon' => $couponData]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully.',
            'coupon' => $couponData,
        ]);
    }

    public function remove(Request $request)
    {
        session()->forget('coupon');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully.',
        ]);
    }

    private function error(string $message)
    {
        return response()->json(['success' => false, 'message' => $message], 422);
    }
}
