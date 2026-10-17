<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class CartController extends Controller
{
    private const SUPPORTED_CURRENCIES = ['NGN', 'USD'];

    private const MAX_QUANTITY = 4294967295;

    /*
    |--------------------------------------------------------------------------
    | Cart Display
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        try {
            $this->refreshCurrency();
        } catch (ValidationException $e) {
            session()->flash(
                'error',
                collect($e->errors())->flatten()->first()
            );
        }

        $cartItems = Cart::getContent();

        return view('frontend.cart.index', compact('cartItems'));
    }

    public function sidebar(Request $request)
    {
        try {
            $this->refreshCurrency();
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first();

            if ($request->wantsJson()) {
                return $this->error($message, 422, [
                    'errors' => $e->errors(),
                ]);
            }

            // Keep HTML callers from receiving unexpected JSON.
            return response(
                '<div class="alert alert-danger mb-0" role="alert">'
                . e($message)
                . '</div>',
                422
            );
        }

        if ($request->wantsJson()) {
            return $this->cartResponse('Cart refreshed.');
        }

        return view('frontend.partials.cart.side-cart', [
            'cartItems' => Cart::getContent(),
        ]);
    }

    public function refreshCurrency(): void
    {
        $this->commit($this->plan($this->rows()));
    }

    /*
    |--------------------------------------------------------------------------
    | Add Retail Products
    |--------------------------------------------------------------------------
    */

    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'variant_id' => [
                'nullable',
                'integer',
                'exists:product_variants,id',
            ],
            'quantity' => [
                'nullable',
                'integer',
                'min:1',
                'max:' . self::MAX_QUANTITY,
            ],
        ]);

        $id = $this->rowId(
            (int) $data['product_id'],
            $data['variant_id'] ?? null
        );

        $rows = $this->rows();

        $rows[$id] = $this->candidate(
            $id,
            (int) $data['product_id'],
            $data['variant_id'] ?? null,
            (int) ($rows[$id]['quantity'] ?? 0)
                + (int) ($data['quantity'] ?? 1),
            false
        );

        return $this->mutate($rows, 'Product added to cart.');
    }

    /*
    |--------------------------------------------------------------------------
    | Add Wholesale Products
    |--------------------------------------------------------------------------
    */

    public function addWholesale(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],
            'items.*' => [
                'required',
                'array:variant_id,quantity',
            ],
            'items.*.variant_id' => [
                'nullable',
                'integer',
                'exists:product_variants,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:' . self::MAX_QUANTITY,
            ],
        ]);

        $rows = $this->rows();
        $seen = [];

        foreach ($data['items'] as $item) {
            $id = $this->rowId(
                (int) $data['product_id'],
                $item['variant_id'] ?? null,
                true
            );

            if (isset($seen[$id])) {
                return $this->error(
                    'Each product option may appear only once in this request.'
                );
            }

            $seen[$id] = true;

            $rows[$id] = $this->candidate(
                $id,
                (int) $data['product_id'],
                $item['variant_id'] ?? null,
                (int) ($rows[$id]['quantity'] ?? 0)
                    + (int) $item['quantity'],
                true
            );
        }

        return $this->mutate(
            $rows,
            'Wholesale products added to cart.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update / Remove / Clear
    |--------------------------------------------------------------------------
    */

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => ['required', 'string'],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:' . self::MAX_QUANTITY,
            ],
        ]);

        $rows = $this->rows();

        if (!isset($rows[$data['id']])) {
            return $this->error('Cart item not found.', 404);
        }

        $rows[$data['id']]['quantity'] = (int) $data['quantity'];

        return $this->mutate(
            $rows,
            'Cart updated successfully.',
            $data['id']
        );
    }

    public function remove(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => ['required', 'string'],
        ]);

        return $this->removeCartItem($data['id']);
    }

    public function removeCartItem($id): JsonResponse
    {
        $rows = $this->rows();

        if (!isset($rows[$id])) {
            return $this->error('Cart item not found.', 404);
        }

        unset($rows[$id]);

        return $this->mutate(
            $rows,
            'Item removed from your bag.'
        );
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
    | Validate and Apply Cart Changes
    |--------------------------------------------------------------------------
    */

    private function mutate(
        array $rows,
        string $message,
        ?string $updatedId = null
    ): JsonResponse {
        try {
            // Validate the complete proposed cart before changing it.
            $plan = $this->plan($rows);
        } catch (ValidationException $e) {
            return $this->error(
                collect($e->errors())->flatten()->first(),
                422,
                ['errors' => $e->errors()]
            );
        }

        $this->commit($plan);

        $extra = [];

        if ($updatedId !== null) {
            $item = Cart::get($updatedId);

            $extra['item_total'] = $item
                ? $item->price * $item->quantity
                : 0;
        }

        return $this->cartResponse($message, $extra);
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Cart Rows
    |--------------------------------------------------------------------------
    */

    private function rows(): array
    {
        $rows = [];

        foreach (Cart::getContent() as $item) {
            $rows[(string) $item->id] = $this->candidate(
                (string) $item->id,
                (int) $item->attributes->get('product_id'),
                $item->attributes->get('variant_id'),
                (int) $item->quantity,
                $item->attributes->get('purchase_type') === 'wholesale'
            );
        }

        return $rows;
    }

    private function candidate(
        string $id,
        int $productId,
        $variantId,
        int $quantity,
        bool $wholesale
    ): array {
        return [
            'id' => $id,
            'product_id' => $productId,
            'variant_id' => $variantId === null
                ? null
                : (int) $variantId,
            'quantity' => $quantity,
            'wholesale' => $wholesale,
        ];
    }

    private function rowId(
        int $productId,
        $variantId = null,
        bool $wholesale = false
    ): string {
        $id = $variantId !== null
            ? $productId . '_' . (int) $variantId
            : (string) $productId;

        return $wholesale ? 'wholesale_' . $id : $id;
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Eligibility / Inventory / Prices
    |--------------------------------------------------------------------------
    */

    private function plan(array $rows): array
    {
        $hasWholesale = collect($rows)->contains(
            fn ($row) => $row['wholesale']
        );

        $hasRetail = collect($rows)->contains(
            fn ($row) => !$row['wholesale']
        );

        if ($hasWholesale && $hasRetail) {
            $this->invalid(
                'Retail and wholesale products cannot be purchased together. '
                . 'Please complete or clear your current cart before switching.'
            );
        }

        $currency = $hasWholesale ? 'NGN' : $this->currency();

        // Preserve the existing restriction on discounted wholesale carts.
        if (
            $hasWholesale
            && (
                collect(Cart::getConditions())->isNotEmpty()
                || Cart::getContent()->contains(
                    fn ($item) => !empty($item->conditions)
                )
            )
        ) {
            $this->invalid(
                'Remove the applied discount before adding or updating wholesale products.'
            );
        }

        $products = Product::with([
            'variants.wholesalePriceTiers',
            'wholesalePriceTiers',
        ])
            ->where('status', 1)
            ->whereIn(
                'id',
                array_unique(array_column($rows, 'product_id'))
            )
            ->get()
            ->keyBy('id');

        $sources = [];
        $stockTotals = [];
        $wholesaleTotals = [];

        foreach ($rows as $id => $row) {
            if (
                $row['quantity'] < 1
                || $row['quantity'] > self::MAX_QUANTITY
            ) {
                $this->invalid(
                    'Cart quantities must be positive whole numbers within the supported limit.'
                );
            }

            $product = $products->get($row['product_id']);

            if (!$product) {
                $this->invalid(
                    'A product in your cart is no longer available. Remove it to continue.'
                );
            }

            $variant = null;

            if ($product->has_variants) {
                $variant = $product->variants->firstWhere(
                    'id',
                    $row['variant_id']
                );

                if (!$variant || !$variant->is_active) {
                    $this->invalid(
                        "Select an available option for {$product->name}."
                    );
                }
            } elseif ($row['variant_id'] !== null) {
                $this->invalid(
                    "{$product->name} does not accept a product option."
                );
            }

            $source = $variant ?? $product;

            $stockKey = $product->id . ':' . ($variant?->id ?? 'simple');

            $stockTotals[$stockKey] =
                ($stockTotals[$stockKey] ?? 0) + $row['quantity'];

            $sources[$id] = [$product, $variant, $stockKey];

            if ($row['wholesale']) {
                if (
                    !$product->wholesale_enabled
                    || (int) $product->wholesale_min_quantity < 1
                    || ($variant && !$variant->wholesale_enabled)
                    || $this->kobo($source->wholesale_price_ngn) <= 0
                ) {
                    $this->invalid(
                        "{$product->name} or its selected option is unavailable for wholesale."
                    );
                }

                $wholesaleTotals[$product->id] =
                    ($wholesaleTotals[$product->id] ?? 0)
                    + $row['quantity'];
            }
        }

        $lines = [];

        foreach ($rows as $id => $row) {
            [$product, $variant, $stockKey] = $sources[$id];

            $stock = $this->resolveStock($product, $variant);

            if (
                $stock['status'] === 'out_of_stock'
                || (
                    $stock['track']
                    && (
                        $stock['status'] !== 'in_stock'
                        || $stockTotals[$stockKey] > $stock['available']
                    )
                )
            ) {
                $this->invalid(
                    "{$product->name}: only {$stock['available']} unit(s) "
                    . 'available for this option. Retail and wholesale quantities share this stock.'
                );
            }

            $qualifying = null;

            if ($row['wholesale']) {
                $qualifying = $product->has_variants
                    && $product->wholesale_allow_mixed_variants
                        ? $wholesaleTotals[$product->id]
                        : $row['quantity'];

                if ($qualifying < (int) $product->wholesale_min_quantity) {
                    $this->invalid(
                        "{$product->name} requires at least "
                        . "{$product->wholesale_min_quantity} wholesale units"
                        . (
                            $product->has_variants
                            && !$product->wholesale_allow_mixed_variants
                                ? ' per option.'
                                : '.'
                        )
                    );
                }

                $price = $this->wholesalePrice(
                    $variant ?? $product,
                    $qualifying
                );
            } else {
                $price = $this->resolvePrice(
                    $product,
                    $variant,
                    $currency
                );
            }

            if ($price <= 0) {
                $this->invalid(
                    "{$product->name} has no valid purchase price in {$currency}."
                );
            }

            $existing = Cart::get($id);

            $attributes = array_merge(
                $existing?->attributes->toArray() ?? [],
                [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'variant_options' => $variant?->options,
                    'variant_name' => $variant?->name,
                    'sku' => $variant?->sku ?? $product->sku,
                    'image' => $variant?->image ?: $product->main_image,
                    'slug' => $product->slug,
                    'currency' => $currency,
                    'purchase_type' => $row['wholesale']
                        ? 'wholesale'
                        : 'retail',
                    'wholesale_min_quantity' => $row['wholesale']
                        ? (int) $product->wholesale_min_quantity
                        : null,
                    'wholesale_qualifying_quantity' => $qualifying,
                    'wholesale_allow_mixed_variants' => $row['wholesale']
                        && (bool) $product->wholesale_allow_mixed_variants,
                ]
            );

            $lines[$id] = [
                'id' => (string) $id,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $row['quantity'],
                'attributes' => $attributes,
            ];
        }

        return [
            'currency' => $currency,
            'lines' => $lines,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Commit Validated Cart
    |--------------------------------------------------------------------------
    */

    private function commit(array $plan): void
    {
        $before = [];

        foreach (Cart::getContent() as $item) {
            $before[] = [
                'id' => (string) $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'attributes' => $item->attributes->toArray(),
                'conditions' => $item->conditions ?? [],
            ];
        }

        try {
            foreach (Cart::getContent() as $item) {
                if (!isset($plan['lines'][$item->id])) {
                    if (Cart::remove($item->id) === false) {
                        throw new \RuntimeException(
                            'The cart removal was cancelled.'
                        );
                    }
                }
            }

            foreach ($plan['lines'] as $line) {
                if (Cart::get($line['id'])) {
                    $result = Cart::update($line['id'], [
                        'name' => $line['name'],
                        'price' => $line['price'],
                        'quantity' => [
                            'relative' => false,
                            'value' => $line['quantity'],
                        ],
                        'attributes' => $line['attributes'],
                    ]);
                } else {
                    $result = Cart::add($line);
                }

                if ($result === false) {
                    throw new \RuntimeException(
                        'The cart update was cancelled.'
                    );
                }
            }
        } catch (Throwable $e) {
            Cart::clear();

            foreach ($before as $line) {
                Cart::add($line);
            }

            throw $e;
        }

        // Clear session coupons only after a successful wholesale cart write.
        if (
            collect($plan['lines'])->contains(
                fn ($line) =>
                    $line['attributes']['purchase_type'] === 'wholesale'
            )
        ) {
            session()->forget('coupon');
        }

        session(['currency' => $plan['currency']]);
    }

    /*
    |--------------------------------------------------------------------------
    | Stock and Pricing Helpers
    |--------------------------------------------------------------------------
    */

    private function resolveStock($product, $variant = null): array
    {
        $source = $variant ?? $product;

        return [
            'available' => (int) (
                $variant
                    ? $variant->stock_quantity
                    : $product->quantity
            ),
            'track' => (bool) $source->track_stock,
            'status' => $source->stock_status,
        ];
    }

    private function resolvePrice(
        $product,
        $variant,
        string $currency
    ): float {
        $regularField = $currency === 'USD' ? 'price_usd' : 'price_ngn';
        $saleField = $currency === 'USD' ? 'sale_price_usd' : 'sale_price_ngn';

        $source = $variant && $variant->{$regularField} !== null
            ? $variant
            : $product;

        $regularPrice = (float) $source->{$regularField};
        $salePrice = (float) ($source->{$saleField} ?? 0);
        $now = now();

        $saleActive = (
            !$product->sale_starts_at
            || $now->gte($product->sale_starts_at)
        ) && (
            !$product->sale_ends_at
            || $now->lte($product->sale_ends_at)
        );

        return $saleActive
            && $salePrice > 0
            && $salePrice < $regularPrice
                ? $salePrice
                : $regularPrice;
    }

    private function wholesalePrice($source, int $quantity): float
    {
        $price = $this->kobo($source->wholesale_price_ngn);

        foreach (
            $source->wholesalePriceTiers->sortBy('min_quantity') as $tier
        ) {
            if ((int) $tier->min_quantity <= $quantity) {
                $tierPrice = $this->kobo($tier->price_ngn);

                if ($tierPrice <= 0 || $tierPrice >= $price) {
                    $this->invalid(
                        'Wholesale price tiers require correction before ordering.'
                    );
                }

                $price = $tierPrice;
            }
        }

        // Compare prices in integer kobo before converting for the cart.
        return $price / 100;
    }

    private function kobo($price): int
    {
        if (
            !preg_match(
                '/^\d{1,13}(?:\.\d{1,2})?$/',
                (string) $price
            )
        ) {
            return 0;
        }

        $parts = explode('.', (string) $price, 2);

        return ((int) $parts[0] * 100)
            + (int) str_pad($parts[1] ?? '', 2, '0');
    }

    private function currency(): string
    {
        $currency = strtoupper(
            (string) session('currency', 'NGN')
        );

        return in_array($currency, self::SUPPORTED_CURRENCIES, true)
            ? $currency
            : 'NGN';
    }

    /*
    |--------------------------------------------------------------------------
    | Responses and Validation Errors
    |--------------------------------------------------------------------------
    */

    private function cartResponse(
        string $message,
        array $extra = []
    ): JsonResponse {
        $subtotal = Cart::getSubTotal(false);
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
            'side_cart_html' => view(
                'frontend.partials.cart.side-cart',
                ['cartItems' => Cart::getContent()]
            )->render(),
        ], $extra));
    }

    private function error(
        string $message,
        int $code = 422,
        array $extra = []
    ): JsonResponse {
        return response()->json(array_merge([
            'status' => false,
            'message' => $message,
        ], $extra), $code);
    }

    private function invalid(string $message): void
    {
        throw ValidationException::withMessages([
            'cart' => $message,
        ]);
    }
}