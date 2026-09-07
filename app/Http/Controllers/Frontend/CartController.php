<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{

    public function index()
    {
        $cartItems = Cart::getContent();

        return view('frontend.cart.index', compact('cartItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $product = Product::with('variants')
            ->where('status', 1)
            ->findOrFail($request->product_id);

        $quantity = (int) ($request->quantity ?? 1);
        $currency = strtoupper(session('currency', 'NGN'));

        /*
    |--------------------------------------------------------------------------
    | Resolve Variant
    |--------------------------------------------------------------------------
    */
        $variant = null;

        if ($product->has_variants) {

            if (!$request->variant_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please select a product option.',
                ], 422);
            }

            $variant = $product->variants
                ->firstWhere('id', (int) $request->variant_id);

            if (!$variant) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid product option selected.',
                ], 422);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Resolve Stock
    |--------------------------------------------------------------------------
    */
        if ($variant) {

            $availableQuantity = (int) ($variant->stock_quantity ?? 0);
            $trackStock = (bool) $variant->track_stock;
            $stockStatus = $variant->stock_status;
        } else {

            $availableQuantity = (int) ($product->quantity ?? 0);
            $trackStock = (bool) $product->track_stock;
            $stockStatus = $product->stock_status;
        }

        /*
    |--------------------------------------------------------------------------
    | Unique Cart Row
    |--------------------------------------------------------------------------
    |
    | Simple product:
    | 10
    |
    | Variant:
    | 10_3
    |
    */
        $rowId = $variant
            ? "{$product->id}_{$variant->id}"
            : (string) $product->id;

        $existingItem = Cart::get($rowId);

        $existingQuantity = $existingItem
            ? (int) $existingItem->quantity
            : 0;

        $requestedTotal = $existingQuantity + $quantity;

        /*
    |--------------------------------------------------------------------------
    | Stock Availability
    |--------------------------------------------------------------------------
    */
        if ($stockStatus === 'out_of_stock') {
            return response()->json([
                'status' => false,
                'message' => 'Product is out of stock.',
            ], 422);
        }

        if (
            $trackStock &&
            $requestedTotal > $availableQuantity
        ) {
            return response()->json([
                'status' => false,
                'message' => $availableQuantity > 0
                    ? "Only {$availableQuantity} item(s) available."
                    : 'Product is out of stock.',
                'available_quantity' => $availableQuantity,
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Resolve Currency Price
    |--------------------------------------------------------------------------
    */
        $price = $this->resolvePrice(
            $product,
            $variant,
            $currency
        );

        if ($price <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'This product is currently unavailable for purchase.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Add / Update Cart
    |--------------------------------------------------------------------------
    */
        if ($existingItem) {

            Cart::update($rowId, [
                'quantity' => [
                    'relative' => true,
                    'value' => $quantity,
                ],
                'price' => $price,
            ]);
        } else {

            Cart::add([
                'id' => $rowId,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,

                'attributes' => [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'variant_options' => $variant?->options,
                    'variant_name' => $variant?->name,
                    'sku' => $variant?->sku ?? $product->sku,
                    'image' => $variant?->image ?: $product->main_image,
                    'slug' => $product->slug,
                    'currency' => $currency,
                ],

                'associatedModel' => $product,
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */
        return response()->json([
            'status' => true,
            'message' => 'Product added to cart.',
            'cart_count' => Cart::getTotalQuantity(),
            'cart_total' => Cart::getTotal(),
            'currency' => $currency,
            'side_cart_html' => view(
                'frontend.partials.cart.side-cart'
            )->render(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'       => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Cart::get($request->id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found.',
            ], 404);
        }

        $productId = $item->attributes->get('product_id');
        $variantId = $item->attributes->get('variant_id');

        $product = Product::with('variants')
            ->where('status', 1)
            ->find($productId);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product is no longer available.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Resolve Stock Source
    |--------------------------------------------------------------------------
    */
        $variant = null;

        if ($variantId) {
            $variant = $product->variants
                ->firstWhere('id', (int) $variantId);

            if (!$variant) {
                return response()->json([
                    'status' => false,
                    'message' => 'Selected product option is no longer available.',
                ], 422);
            }
        }

        $stockSource = $variant ?: $product;

        /*
    |--------------------------------------------------------------------------
    | Validate Stock
    |--------------------------------------------------------------------------
    */
        $availableQuantity = $variantId
            ? (int) $stockSource->stock_quantity
            : (int) $stockSource->quantity;

        if (
            $stockSource->stock_status !== 'in_stock' ||
            $request->quantity > $availableQuantity
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Only ' . $availableQuantity . ' item(s) available.',
                'available_quantity' => $availableQuantity,
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Refresh Price Using Current Currency
    |--------------------------------------------------------------------------
    */
        $currency = strtoupper(session('currency', 'NGN'));

        $price = $this->resolvePrice(
            $product,
            $variant,
            $currency
        );

        /*
    |--------------------------------------------------------------------------
    | Update Cart
    |--------------------------------------------------------------------------
    */
        Cart::update($request->id, [
            'quantity' => [
                'relative' => false,
                'value' => (int) $request->quantity,
            ],
            'price' => $price,
        ]);

        $item = Cart::get($request->id);

        /*
    |--------------------------------------------------------------------------
    | Totals
    |--------------------------------------------------------------------------
    */
        $itemTotal = $item
            ? $item->price * $item->quantity
            : 0;

        $subtotal = Cart::getSubTotal();

        $delivery = 0;
        $discount = 0;

        $total = $subtotal + $delivery - $discount;

        return response()->json([
            'status' => true,
            'message' => 'Cart updated successfully.',
            'cart_count' => Cart::getTotalQuantity(),

            'item_total' => $itemTotal,
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'discount' => $discount,
            'total' => $total,

            'cart_total' => number_format($total, 2),
            'currency' => $currency,

            'side_cart_html' => view(
                'frontend.partials.cart.side-cart'
            )->render(),
        ]);
    }
    private function resolvePrice($product, $variant, string $currency): float
    {
        $regularField = $currency === 'USD'
            ? 'price_usd'
            : 'price_ngn';

        $saleField = $currency === 'USD'
            ? 'sale_price_usd'
            : 'sale_price_ngn';

        $source = $product;

        if (
            $variant &&
            !is_null($variant->{$regularField})
        ) {
            $source = $variant;
        }

        $regularPrice = (float) $source->{$regularField};
        $salePrice = (float) ($source->{$saleField} ?? 0);

        if (
            $salePrice > 0 &&
            $salePrice < $regularPrice
        ) {
            return $salePrice;
        }

        return $regularPrice;
    }

    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        Cart::remove($request->id);

        $subtotal = Cart::getSubTotal();
        $delivery = 0;
        $discount = 0;
        $total = $subtotal + $delivery - $discount;

        return response()->json([
            'status' => true,
            'message' => 'Product removed from cart.',
            'cart_count' => Cart::getTotalQuantity(),
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'discount' => $discount,
            'total' => $total,
            'cart_total' => number_format($total, 2),
            'side_cart_html' => view('frontend.partials.cart.side-cart')->render(),
        ]);
    }

    public function clear()
    {
        Cart::clear();

        return response()->json([
            'status' => true,
            'message' => 'Cart cleared.',
            'cart_count' => 0,
            'cart_total' => '0.00',
        ]);
    }

    public function removeCartItem($id)
    {
        $item = Cart::get($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found.',
            ], 404);
        }

        Cart::remove($id);

        return response()->json([
            'status' => true,
            'message' => 'Item removed from your bag.',
            'cart_count' => Cart::getTotalQuantity(),
            'cart_total' => Cart::getTotal(),
            'cart_html' => view('frontend.partials.cart.side-cart')->render(),
        ]);
    }
}
