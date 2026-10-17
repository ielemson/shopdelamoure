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
    public function deductOrderStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            if ($lockedOrder->payment_status !== 'paid') {
                throw new RuntimeException('Stock can only be deducted for a paid order.');
            }
            $lockedOrder->load('items');
            [$products, $variants] = $this->lockSources($lockedOrder);
            $pending = [];
            $totals = [];

            foreach ($lockedOrder->items->sortBy('id') as $item) {
                $reference = 'SALE-ORDER-'.$lockedOrder->id.'-ITEM-'.$item->id;
                if (StockMovement::where('reference', $reference)->exists()) {
                    continue;
                }
                $quantity = (int) $item->quantity;
                if ($quantity <= 0 || (float) $item->quantity !== (float) $quantity) {
                    throw new RuntimeException('Invalid quantity for order item '.$item->id);
                }
                $source = $this->source($item, $products, $variants);
                if (! $source->track_stock) {
                    continue;
                }
                $key = $item->product_variant_id ? 'variant:'.$source->id : 'product:'.$source->id;
                $totals[$key] = ($totals[$key] ?? 0) + $quantity;
                $pending[] = [$item, $source, $reference, $key, $quantity];
            }
            // Validate the combined retail and wholesale demand before writing.
            foreach ($pending as [$item, $source, $reference, $key, $quantity]) {
                $available = (int) ($item->product_variant_id ? $source->stock_quantity : $source->quantity);
                if ($available < $totals[$key]) {
                    throw new RuntimeException('Insufficient stock for '.$item->name
                        .'. Available: '.$available.', required: '.$totals[$key]);
                }
            }
            foreach ($pending as [$item, $source, $reference, $key, $quantity]) {
                $field = $item->product_variant_id ? 'stock_quantity' : 'quantity';
                $before = (int) $source->{$field};
                $after = $before - $quantity;
                $source->update([$field => $after, 'stock_status' => $after > 0 ? 'in_stock' : 'out_of_stock']);
                $this->movement($lockedOrder, $item, $reference, 'sale', -$quantity, $before, $after,
                    'Stock deducted after successful payment for '.$lockedOrder->order_no);
            }
        }, 5);
    }

    public function restoreOrderStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $lockedOrder->load('items');
            [$products, $variants] = $this->lockSources($lockedOrder);
            foreach ($lockedOrder->items->sortBy('id') as $item) {
                $reference = 'CANCEL-ORDER-'.$lockedOrder->id.'-ITEM-'.$item->id;
                if (StockMovement::where('reference', $reference)->exists()) {
                    continue;
                }
                $sale = StockMovement::where('reference', 'SALE-ORDER-'.$lockedOrder->id.'-ITEM-'.$item->id)->first();
                if (! $sale) {
                    continue;
                }
                // Restore the amount actually deducted, even if tracking or item quantity changed.
                $quantity = -(int) $sale->quantity;
                if ($quantity <= 0 || (int) $sale->product_id !== (int) $item->product_id
                    || (int) $sale->variant_id !== (int) $item->product_variant_id) {
                    throw new RuntimeException('Invalid sale stock movement for order item '.$item->id);
                }
                $source = $this->source($item, $products, $variants);
                $field = $item->product_variant_id ? 'stock_quantity' : 'quantity';
                $before = (int) $source->{$field};
                $after = $before + $quantity;
                if ($after > 4294967295) {
                    throw new RuntimeException('Restored stock exceeds the supported quantity.');
                }
                $source->update([$field => $after, 'stock_status' => $after > 0 ? 'in_stock' : 'out_of_stock']);
                $this->movement($lockedOrder, $item, $reference, 'restock', $quantity, $before, $after,
                    'Stock restored following order cancellation.', auth()->id());
            }
        }, 5);
    }

    private function lockSources(Order $order): array
    {
        // Match checkout's lock order: products first, variants second, ascending IDs.
        $products = Product::query()->whereIn('id', $order->items->pluck('product_id')->unique())
            ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
        $variants = ProductVariant::query()->whereIn('id', $order->items->pluck('product_variant_id')->filter()->unique())
            ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

        return [$products, $variants];
    }

    private function source($item, $products, $variants)
    {
        $product = $products->get($item->product_id);
        if (! $product) {
            throw new RuntimeException('Product not found for order item '.$item->id);
        }
        if (! $item->product_variant_id) {
            return $product;
        }
        $variant = $variants->get($item->product_variant_id);
        if (! $variant || (int) $variant->product_id !== (int) $item->product_id) {
            throw new RuntimeException('Invalid variant for order item '.$item->id);
        }

        return $variant;
    }

    private function movement(Order $order, $item, string $reference, string $type,
        int $quantity, int $before, int $after, string $note, ?int $createdBy = null): void
    {
        $data = [
            'product_id' => $item->product_id,
            'variant_id' => $item->product_variant_id ?: null,
            'order_id' => $order->id, 'order_item_id' => $item->id,
            'type' => $type, 'quantity' => $quantity,
            'quantity_before' => $before, 'quantity_after' => $after,
            'reference' => $reference, 'note' => $note,
        ];
        if ($type === 'restock') {
            $data['created_by'] = $createdBy;
        }
        StockMovement::create($data);
    }
}
