<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Stock List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $products = Product::with('variants')
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {

                    $search =
                        trim($request->search);

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                            ->orWhere(
                                'sku',
                                'like',
                                "%{$search}%"
                            );

                    });
                }
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $simpleStock = Product::where(
            'has_variants',
            false
        )
            ->sum('quantity');

        $variantStock =
            ProductVariant::sum(
                'stock_quantity'
            );

        $totalUnits =
            $simpleStock + $variantStock;

        $outOfStockSimple =
            Product::where(
                'has_variants',
                false
            )
                ->where(function ($query) {

                    $query
                        ->where(
                            'stock_status',
                            'out_of_stock'
                        )
                        ->orWhere(
                            'quantity',
                            '<=',
                            0
                        );

                })
                ->count();

        $outOfStockVariants =
            ProductVariant::where(function ($query) {

                $query
                    ->where(
                        'stock_status',
                        'out_of_stock'
                    )
                    ->orWhere(
                        'stock_quantity',
                        '<=',
                        0
                    );

            })
                ->count();

        $outOfStock =
            $outOfStockSimple
            + $outOfStockVariants;

        return view(
            'admin.inventory.index',
            compact(
                'products',
                'totalUnits',
                'outOfStock'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Low Stock
    |--------------------------------------------------------------------------
    */

    public function lowStock()
    {
        $products = Product::query()
            ->where(
                'has_variants',
                false
            )
            ->where('track_stock', true)
            ->whereColumn(
                'quantity',
                '<=',
                'low_stock_alert'
            )
            ->orderBy('quantity')
            ->get();

        $variants = ProductVariant::with('product')
            ->where('track_stock', true)
            ->whereColumn(
                'stock_quantity',
                '<=',
                'low_stock_threshold'
            )
            ->orderBy('stock_quantity')
            ->get();

        return view(
            'admin.inventory.low-stock',
            compact(
                'products',
                'variants'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Stock Movements
    |--------------------------------------------------------------------------
    */

    public function movements(Request $request)
    {
        $movements = StockMovement::with([
            'product',
            'variant',
            'order',
            'creator',
        ])
            ->when(
                $request->filled('type'),
                fn ($query) => $query->where(
                    'type',
                    $request->type
                )
            )
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view(
            'admin.inventory.movements',
            compact('movements')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manual Adjustment
    |--------------------------------------------------------------------------
    */

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'variant_id' => [
                'nullable',
                'exists:product_variants,id',
            ],

            'adjustment' => [
                'required',
                'integer',
                'not_in:0',
            ],

            'note' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Variant Adjustment
            |--------------------------------------------------------------------------
            */

            if (! empty($validated['variant_id'])) {

                $variant =
                    ProductVariant::query()
                        ->where(
                            'product_id',
                            $validated['product_id']
                        )
                        ->lockForUpdate()
                        ->findOrFail(
                            $validated['variant_id']
                        );

                $before =
                    (int) $variant->stock_quantity;

                $after =
                    $before
                    + (int) $validated['adjustment'];

                if ($after < 0) {
                    abort(
                        422,
                        'Stock cannot be reduced below zero.'
                    );
                }

                $variant->update([
                    'stock_quantity' => $after,

                    'stock_status' => $after <= 0
                            ? 'out_of_stock'
                            : 'in_stock',
                ]);

                StockMovement::create([
                    'product_id' => $validated['product_id'],

                    'variant_id' => $variant->id,

                    'type' => $validated['adjustment'] > 0
                            ? 'restock'
                            : 'adjustment',

                    'quantity' => (int) $validated['adjustment'],

                    'quantity_before' => $before,

                    'quantity_after' => $after,

                    'note' => $validated['note'],

                    'created_by' => auth()->id(),
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Simple Product Adjustment
            |--------------------------------------------------------------------------
            */

            $product =
                Product::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $validated['product_id']
                    );

            $before =
                (int) $product->quantity;

            $after =
                $before
                + (int) $validated['adjustment'];

            if ($after < 0) {
                abort(
                    422,
                    'Stock cannot be reduced below zero.'
                );
            }

            $product->update([
                'quantity' => $after,

                'stock_status' => $after <= 0
                        ? 'out_of_stock'
                        : 'in_stock',
            ]);

            StockMovement::create([
                'product_id' => $product->id,

                'variant_id' => null,

                'type' => $validated['adjustment'] > 0
                        ? 'restock'
                        : 'adjustment',

                'quantity' => (int) $validated['adjustment'],

                'quantity_before' => $before,

                'quantity_after' => $after,

                'note' => $validated['note'],

                'created_by' => auth()->id(),
            ]);

        });

        return back()->with(
            'success',
            'Stock updated successfully.'
        );
    }
}
