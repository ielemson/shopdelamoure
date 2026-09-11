<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    /*
    |--------------------------------------------------------------------------
    | Deduct Stock For Paid Order
    |--------------------------------------------------------------------------
    */

    public function deductOrderStock(Order $order): void
    {
        DB::transaction(function () use ($order) {

            /*
            |--------------------------------------------------------------------------
            | Lock Order
            |--------------------------------------------------------------------------
            |
            | Helps prevent simultaneous callbacks from processing the same
            | order inventory at the same time.
            |
            */

            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            /*
            |--------------------------------------------------------------------------
            | Load Order Items
            |--------------------------------------------------------------------------
            */

            $lockedOrder->loadMissing([
                'items.product',
                'items.variant',
            ]);

            foreach ($lockedOrder->items as $item) {

                /*
                |--------------------------------------------------------------------------
                | Unique Stock Movement Reference
                |--------------------------------------------------------------------------
                */

                $reference =
                    'SALE-ORDER-'
                    .$lockedOrder->id
                    .'-ITEM-'
                    .$item->id;

                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate Deduction
                |--------------------------------------------------------------------------
                */

                if (
                    StockMovement::where(
                        'reference',
                        $reference
                    )->exists()
                ) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Variant Product
                |--------------------------------------------------------------------------
                |
                | OrderItem uses product_variant_id as the foreign key.
                |
                */

                if ($item->product_variant_id) {

                    $variant = ProductVariant::query()
                        ->lockForUpdate()
                        ->find(
                            $item->product_variant_id
                        );

                    if (! $variant) {

                        throw new RuntimeException(
                            'Variant not found for order item '
                            .$item->id
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Verify Variant Belongs To Product
                    |--------------------------------------------------------------------------
                    */

                    if (
                        (int) $variant->product_id
                        !==
                        (int) $item->product_id
                    ) {

                        throw new RuntimeException(
                            'Variant does not belong to the product for order item '
                            .$item->id
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Stock Tracking Disabled
                    |--------------------------------------------------------------------------
                    */

                    if (! $variant->track_stock) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Current Stock
                    |--------------------------------------------------------------------------
                    */

                    $before =
                        (int) $variant->stock_quantity;

                    $quantity =
                        (int) $item->quantity;

                    /*
                    |--------------------------------------------------------------------------
                    | Validate Quantity
                    |--------------------------------------------------------------------------
                    */

                    if ($quantity <= 0) {

                        throw new RuntimeException(
                            'Invalid quantity for order item '
                            .$item->id
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Prevent Negative Inventory
                    |--------------------------------------------------------------------------
                    */

                    if ($before < $quantity) {

                        throw new RuntimeException(
                            'Insufficient stock for '
                            .$item->name
                            .'. Available: '
                            .$before
                            .', required: '
                            .$quantity
                        );

                    }

                    $after =
                        $before - $quantity;

                    /*
                    |--------------------------------------------------------------------------
                    | Update Variant Stock
                    |--------------------------------------------------------------------------
                    */

                    $variant->update([
                        'stock_quantity' => $after,

                        'stock_status' => $after <= 0
                                ? 'out_of_stock'
                                : 'in_stock',
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Record Stock Movement
                    |--------------------------------------------------------------------------
                    |
                    | stock_movements.variant_id refers to product_variants.id.
                    |
                    */

                    StockMovement::create([
                        'product_id' => $item->product_id,

                        'variant_id' => $variant->id,

                        'order_id' => $lockedOrder->id,

                        'order_item_id' => $item->id,

                        'type' => 'sale',

                        'quantity' => -$quantity,

                        'quantity_before' => $before,

                        'quantity_after' => $after,

                        'reference' => $reference,

                        'note' => 'Stock deducted after successful payment for '
                            .$lockedOrder->order_no,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Variant Complete
                    |--------------------------------------------------------------------------
                    */

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Simple Product
                |--------------------------------------------------------------------------
                */

                $product = Product::query()
                    ->lockForUpdate()
                    ->find(
                        $item->product_id
                    );

                if (! $product) {

                    throw new RuntimeException(
                        'Product not found for order item '
                        .$item->id
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Stock Tracking Disabled
                |--------------------------------------------------------------------------
                */

                if (! $product->track_stock) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Current Stock
                |--------------------------------------------------------------------------
                */

                $before =
                    (int) $product->quantity;

                $quantity =
                    (int) $item->quantity;

                /*
                |--------------------------------------------------------------------------
                | Validate Quantity
                |--------------------------------------------------------------------------
                */

                if ($quantity <= 0) {

                    throw new RuntimeException(
                        'Invalid quantity for order item '
                        .$item->id
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Prevent Negative Inventory
                |--------------------------------------------------------------------------
                */

                if ($before < $quantity) {

                    throw new RuntimeException(
                        'Insufficient stock for '
                        .$item->name
                        .'. Available: '
                        .$before
                        .', required: '
                        .$quantity
                    );

                }

                $after =
                    $before - $quantity;

                /*
                |--------------------------------------------------------------------------
                | Update Product Stock
                |--------------------------------------------------------------------------
                */

                $product->update([
                    'quantity' => $after,

                    'stock_status' => $after <= 0
                            ? 'out_of_stock'
                            : 'in_stock',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Record Stock Movement
                |--------------------------------------------------------------------------
                */

                StockMovement::create([
                    'product_id' => $product->id,

                    'variant_id' => null,

                    'order_id' => $lockedOrder->id,

                    'order_item_id' => $item->id,

                    'type' => 'sale',

                    'quantity' => -$quantity,

                    'quantity_before' => $before,

                    'quantity_after' => $after,

                    'reference' => $reference,

                    'note' => 'Stock deducted after successful payment for '
                        .$lockedOrder->order_no,
                ]);
            }
        });
    }

    public function restoreOrderStock(Order $order): void
    {
        DB::transaction(function () use ($order) {

            $order = Order::query()
                ->with([
                    'items.product',
                    'items.variant',
                ])
                ->lockForUpdate()
                ->findOrFail($order->id);

            foreach ($order->items as $item) {

                /*
                |--------------------------------------------------------------------------
                | Idempotency
                |--------------------------------------------------------------------------
                |
                | If this cancellation has already restored this item,
                | do nothing.
                |
                */

                $restoreReference =
                    'CANCEL-ORDER-'
                    .$order->id
                    .'-ITEM-'
                    .$item->id;

                if (
                    StockMovement::query()
                        ->where('reference', $restoreReference)
                        ->exists()
                ) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Confirm Stock Was Originally Deducted
                |--------------------------------------------------------------------------
                */

                $saleReference =
                    'SALE-ORDER-'
                    .$order->id
                    .'-ITEM-'
                    .$item->id;

                $saleMovement = StockMovement::query()
                    ->where('reference', $saleReference)
                    ->first();

                if (! $saleMovement) {

                    Log::warning(
                        'Cancellation stock restoration skipped because no sale movement exists.',
                        [
                            'order_id' => $order->id,
                            'order_item_id' => $item->id,
                            'sale_reference' => $saleReference,
                        ]
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Variant Stock
                |--------------------------------------------------------------------------
                */

                if ($item->product_variant_id) {

                    $variant = ProductVariant::query()
                        ->lockForUpdate()
                        ->find($item->product_variant_id);

                    if (! $variant) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Only Restore Tracked Stock
                    |--------------------------------------------------------------------------
                    */

                    if ($variant->track_stock) {

                        $before = (int) $variant->stock_quantity;

                        $after =
                            $before
                            + (int) $item->quantity;

                        $variant->update([
                            'stock_quantity' => $after,
                            'stock_status' => $after > 0
                                    ? 'in_stock'
                                    : 'out_of_stock',
                        ]);

                        StockMovement::create([
                            'product_id' => $variant->product_id,
                            'variant_id' => $variant->id,

                            'order_id' => $order->id,
                            'order_item_id' => $item->id,

                            'type' => 'restock',

                            'quantity' => (int) $item->quantity,

                            'quantity_before' => $before,

                            'quantity_after' => $after,

                            'reference' => $restoreReference,

                            'note' => 'Stock restored following order cancellation.',

                            'created_by' => auth()->id(),
                        ]);
                    }

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Product Stock
                |--------------------------------------------------------------------------
                */

                $product = Product::query()
                    ->lockForUpdate()
                    ->find($item->product_id);

                if (! $product) {
                    continue;
                }

                $before =
                    (int) $product->quantity;

                $after =
                    $before
                    + (int) $item->quantity;

                $product->update([
                    'quantity' => $after,

                    'stock_status' => $after > 0
                            ? 'in_stock'
                            : 'out_of_stock',
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'variant_id' => null,

                    'order_id' => $order->id,
                    'order_item_id' => $item->id,

                    'type' => 'restock',

                    'quantity' => (int) $item->quantity,

                    'quantity_before' => $before,

                    'quantity_after' => $after,

                    'reference' => $restoreReference,

                    'note' => 'Stock restored following order cancellation.',

                    'created_by' => auth()->id(),
                ]);
            }
        });
    }
}
