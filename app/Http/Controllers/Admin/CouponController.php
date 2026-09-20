<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    /**
     * Display all coupons.
     */
    public function index()
    {
        $coupons = Coupon::with('product')
            ->latest()
            ->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show coupon creation form.
     */
    public function create()
    {
        $products = Product::query()
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.coupons.create', compact('products'));
    }

    /**
     * Store new coupon.
     */
    public function store(Request $request)
    {
        $validated = $this->validateCoupon($request);

        /*
        |--------------------------------------------------------------------------
        | Normalize Coupon Code
        |--------------------------------------------------------------------------
        */

        $validated['code'] = strtoupper(trim($validated['code']));

        /*
        |--------------------------------------------------------------------------
        | General Coupon
        |--------------------------------------------------------------------------
        |
        | A general coupon must not be tied to a particular product.
        |
        */

        if ($validated['scope'] === 'general') {
            $validated['product_id'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Fixed Discount
        |--------------------------------------------------------------------------
        |
        | Maximum discount is mainly relevant to percentage discounts.
        |
        */

        if ($validated['discount_type'] === 'fixed') {
            $validated['maximum_discount_amount'] = null;
        }

        $validated['is_active'] = $request->boolean('is_active');

        Coupon::create($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    /**
     * Show coupon edit form.
     */
    public function edit(Coupon $coupon)
    {
        $products = Product::query()
            ->orderBy('id', 'desc')
            ->get();

        return view(
            'admin.coupons.edit',
            compact('coupon', 'products')
        );
    }

    /**
     * Update coupon.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $this->validateCoupon($request, $coupon);

        $validated['code'] = strtoupper(trim($validated['code']));

        if ($validated['scope'] === 'general') {
            $validated['product_id'] = null;
        }

        if ($validated['discount_type'] === 'fixed') {
            $validated['maximum_discount_amount'] = null;
        }

        $validated['is_active'] = $request->boolean('is_active');

        $coupon->update($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    /**
     * Activate/deactivate coupon.
     */
    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update([
            'is_active' => ! $coupon->is_active,
        ]);

        return back()->with(
            'success',
            $coupon->is_active
                ? 'Coupon activated successfully.'
                : 'Coupon deactivated successfully.'
        );
    }

    /**
     * Delete coupon.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    /**
     * Coupon validation.
     */
    private function validateCoupon(
        Request $request,
        ?Coupon $coupon = null
    ): array {

        return $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('coupons', 'code')
                    ->ignore($coupon?->id),
            ],

            'discount_type' => [
                'required',
                Rule::in([
                    'percentage',
                    'fixed',
                ]),
            ],

            'discount_value' => [
                'required',
                'numeric',
                'gt:0',
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        $request->discount_type === 'percentage'
                        && $value > 100
                    ) {
                        $fail(
                            'Percentage discount cannot exceed 100%.'
                        );
                    }
                },
            ],

            'scope' => [
                'required',
                Rule::in([
                    'general',
                    'product',
                ]),
            ],

            'product_id' => [
                Rule::requiredIf(
                    $request->scope === 'product'
                ),
                'nullable',
                'exists:products,id',
            ],

            'minimum_order_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'maximum_discount_amount' => [
                'nullable',
                'numeric',
                'gt:0',
            ],

            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'per_customer_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);
    }
}
