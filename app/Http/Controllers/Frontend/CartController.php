<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private const SUPPORTED_CURRENCIES = ['NGN', 'USD'];

    public function index()
    {
        // Make sure every line in the cart is priced in the active currency.
        $this->syncCurrency($this->currency());

        $cartItems = Cart::getContent();

        return view('frontend.cart.index', compact('cartItems'));
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::with('variants')
            ->where('status', 1)
            ->find($request->product_id);

        if (! $product) {
            return $this->error('This product is no longer available.', 404);
        }

        $quantity = (int) ($request->quantity ?? 1);
        $currency = $this->currency();

        $this->syncCurrency($currency);

        /*
        |----------------------------------------------------------------------
        | Resolve Variant
        |----------------------------------------------------------------------
        */
        $variant = null;

        if ($product->has_variants) {
            if (! $request->variant_id) {
                return $this->error('Please select a product option.');
            }

            $variant = $product->variants->firstWhere('id', (int) $request->variant_id);

            if (! $variant) {
                return $this->error('Invalid product option selected.');
            }
        }

        /*
        |----------------------------------------------------------------------
        | Unique Cart Row
        |----------------------------------------------------------------------
        | Simple product: "10"   |   Variant: "10_3"
        */
        $rowId = $this->rowId($product, $variant);

        $existingItem = Cart::get($rowId);
        $existingQuantity = $existingItem ? (int) $existingItem->quantity : 0;
        $requestedTotal = $existingQuantity + $quantity;

        /*
        |----------------------------------------------------------------------
        | Stock Availability
        |----------------------------------------------------------------------
        */
        $stock = $this->resolveStock($product, $variant);

        if ($stock['status'] === 'out_of_stock') {
            return $this->error('Product is out of stock.');
        }

        if ($stock['track'] && $requestedTotal > $stock['available']) {
            if ($stock['available'] <= 0) {
                $message = 'Product is out of stock.';
            } elseif ($existingQuantity > 0) {
                $message = "Only {$stock['available']} item(s) available, and you already have {$existingQuantity} in your cart.";
            } else {
                $message = "Only {$stock['available']} item(s) available.";
            }

            return $this->error($message, 422, [
                'available_quantity' => $stock['available'],
            ]);
        }

        /*
        |----------------------------------------------------------------------
        | Resolve Currency Price
        |----------------------------------------------------------------------
        */
        $price = $this->resolvePrice($product, $variant, $currency);

        if ($price <= 0) {
            return $this->error('This product is currently unavailable for purchase.');
        }

        /*
        |----------------------------------------------------------------------
        | Add / Update Cart
        |----------------------------------------------------------------------
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
            ]);
        }

        return $this->cartResponse('Product added to cart.');
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Cart::get($request->id);

        if (! $item) {
            return $this->error('Cart item not found.', 404);
        }

        $productId = $item->attributes->get('product_id');
        $variantId = $item->attributes->get('variant_id');

        $product = Product::with('variants')
            ->where('status', 1)
            ->find($productId);

        if (! $product) {
            return $this->error('Product is no longer available.');
        }

        /*
        |----------------------------------------------------------------------
        | Resolve Variant
        |----------------------------------------------------------------------
        */
        $variant = null;

        if ($variantId) {
            $variant = $product->variants->firstWhere('id', (int) $variantId);

            if (! $variant) {
                return $this->error('Selected product option is no longer available.');
            }
        }

        /*
        |----------------------------------------------------------------------
        | Validate Stock (same rules as add())
        |----------------------------------------------------------------------
        */
        $quantity = (int) $request->quantity;
        $stock = $this->resolveStock($product, $variant);

        if (
            $stock['status'] === 'out_of_stock' ||
            ($stock['track'] && $quantity > $stock['available'])
        ) {
            $message = $stock['available'] > 0
                ? "Only {$stock['available']} item(s) available."
                : 'Product is out of stock.';

            return $this->error($message, 422, [
                'available_quantity' => $stock['available'],
            ]);
        }

        /*
        |----------------------------------------------------------------------
        | Refresh Price Using Current Currency
        |----------------------------------------------------------------------
        */
        $currency = $this->currency();
        $price = $this->resolvePrice($product, $variant, $currency);

        if ($price <= 0) {
            return $this->error('This product is currently unavailable for purchase.');
        }

        Cart::update($request->id, [
            'quantity' => [
                'relative' => false,
                'value' => $quantity,
            ],
            'price' => $price,
            'attributes' => array_merge(
                $item->attributes->toArray(),
                ['currency' => $currency]
            ),
        ]);

        // Keep every other line consistent with the active currency too.
        $this->syncCurrency($currency);

        $item = Cart::get($request->id);

        return $this->cartResponse('Cart updated successfully.', [
            'item_total' => $item ? $item->price * $item->quantity : 0,
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
        ]);

        Cart::remove($request->id);

        return $this->cartResponse('Product removed from cart.');
    }

    public function removeCartItem($id): JsonResponse
    {
        if (! Cart::get($id)) {
            return $this->error('Cart item not found.', 404);
        }

        Cart::remove($id);

        return $this->cartResponse('Item removed from your bag.');
    }

    public function clear(): JsonResponse
    {
        Cart::clear();

        return response()->json([
            'status' => true,
            'message' => 'Cart cleared.',
            'cart_count' => 0,
            'cart_total' => '0.00',
            'cart_total_raw' => 0,
            'currency' => $this->currency(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function currency(): string
    {
        $currency = strtoupper((string) session('currency', 'NGN'));

        return in_array($currency, self::SUPPORTED_CURRENCIES, true)
            ? $currency
            : 'NGN';
    }

    private function rowId($product, $variant = null): string
    {
        return $variant
            ? $product->id.'_'.$variant->id
            : (string) $product->id;
    }

    /**
     * Single source of truth for stock rules (product or variant).
     */
    private function resolveStock($product, $variant = null): array
    {
        if ($variant) {
            return [
                'available' => (int) ($variant->stock_quantity ?? 0),
                'track' => (bool) $variant->track_stock,
                'status' => $variant->stock_status,
            ];
        }

        return [
            'available' => (int) ($product->quantity ?? 0),
            'track' => (bool) $product->track_stock,
            'status' => $product->stock_status,
        ];
    }

    private function resolvePrice($product, $variant, string $currency): float
    {
        $regularField = $currency === 'USD' ? 'price_usd' : 'price_ngn';
        $saleField = $currency === 'USD' ? 'sale_price_usd' : 'sale_price_ngn';

        $source = $product;

        if ($variant && ! is_null($variant->{$regularField})) {
            $source = $variant;
        }

        $regularPrice = (float) $source->{$regularField};
        $salePrice = (float) ($source->{$saleField} ?? 0);

        if ($salePrice > 0 && $salePrice < $regularPrice) {
            return $salePrice;
        }

        return $regularPrice;
    }

    /**
     * Re-price any cart line that was added under a different currency.
     * Lines whose product/variant is gone or has no price in the new
     * currency are removed so the cart never mixes currencies.
     */
    private function syncCurrency(string $currency): void
    {
        $stale = Cart::getContent()->filter(
            fn ($item) => $item->attributes->get('currency') !== $currency
        );

        if ($stale->isEmpty()) {
            return;
        }

        $products = Product::with('variants')
            ->where('status', 1)
            ->whereIn('id', $stale->map(fn ($i) => $i->attributes->get('product_id'))->unique()->all())
            ->get()
            ->keyBy('id');

        foreach ($stale as $item) {
            $product = $products->get($item->attributes->get('product_id'));
            $variantId = $item->attributes->get('variant_id');
            $variant = ($product && $variantId)
                ? $product->variants->firstWhere('id', (int) $variantId)
                : null;

            if (! $product || ($variantId && ! $variant)) {
                Cart::remove($item->id);

                continue;
            }

            $price = $this->resolvePrice($product, $variant, $currency);

            if ($price <= 0) {
                Cart::remove($item->id);

                continue;
            }

            Cart::update($item->id, [
                'price' => $price,
                'attributes' => array_merge(
                    $item->attributes->toArray(),
                    ['currency' => $currency]
                ),
            ]);
        }
    }

    /**
     * Standard success payload with totals and re-rendered side cart.
     */
    private function cartResponse(string $message, array $extra = []): JsonResponse
    {
        $subtotal = Cart::getSubTotal();
        $delivery = 0;
        $discount = 0;
        $total = $subtotal + $delivery - $discount;

        return response()->json(array_merge([
            'status' => true,
            'message' => $message,
            'cart_count' => Cart::getTotalQuantity(),
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'discount' => $discount,
            'total' => $total,
            'cart_total' => number_format($total, 2),
            'cart_total_raw' => $total,
            'currency' => $this->currency(),
            'side_cart_html' => view('frontend.partials.cart.side-cart')->render(),
        ], $extra));
    }

    private function error(string $message, int $code = 422, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'status' => false,
            'message' => $message,
        ], $extra), $code);
    }
}
