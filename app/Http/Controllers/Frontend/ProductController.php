<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::with([
            'category',
            'subcategory',

            'images' => function ($query) {
                $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order');
            },

            'variants' => function ($query) {
                $query
                    ->where('is_active', 1)
                    ->orderByDesc('is_default')
                    ->orderBy('sort_order');
            },
        ])
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $relatedProducts = Product::with([
            'category',
            'images',
            'variants' => function ($query) {
                $query
                    ->where('is_active', 1)
                    ->orderByDesc('is_default')
                    ->orderBy('sort_order');
            },
        ])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->latest()
            ->take(8)
            ->get();

        $variantCartQuantities = Cart::getContent()
            ->filter(function ($item) use ($product) {
                return (int) $item->attributes->get('product_id') === (int) $product->id
                    && $item->attributes->get('variant_id');
            })
            ->groupBy(function ($item) {
                return (string) $item->attributes->get('variant_id');
            })
            ->map(function ($items) {
                return (int) $items->sum(function ($item) {
                    return (int) $item->quantity;
                });
            });

        return view('frontend.product.show', compact(
            'product',
            'relatedProducts',
            'variantCartQuantities'
        ));
    }

    public function shop(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

        $currency = session('currency', 'NGN');

        $priceField = $currency === 'USD'
            ? 'price_usd'
            : 'price_ngn';

        $salePriceField = $currency === 'USD'
            ? 'sale_price_usd'
            : 'sale_price_ngn';

        /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

        $categories = Category::query()
            ->where('status', 1)
            ->withCount([
                'products' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

        $products = Product::with([
            'category',
            'images',
            'variants',
        ])
            ->where('status', 1)

            /*
        |--------------------------------------------------------------------------
        | Category Filter
        |--------------------------------------------------------------------------
        */

            ->when($request->filled('category'), function ($query) use ($request) {

                $query->whereHas('category', function ($categoryQuery) use ($request) {

                    $categoryQuery->where(
                        'slug',
                        $request->category
                    );
                });
            })

            /*
        |--------------------------------------------------------------------------
        | Minimum Price
        |--------------------------------------------------------------------------
        */

            ->when($request->filled('min_price'), function ($query) use (
                $request,
                $priceField,
                $salePriceField
            ) {

                $query->whereRaw(
                    "COALESCE(
                    NULLIF({$salePriceField}, 0),
                    {$priceField}
                ) >= ?",
                    [$request->min_price]
                );
            })

            /*
        |--------------------------------------------------------------------------
        | Maximum Price
        |--------------------------------------------------------------------------
        */

            ->when($request->filled('max_price'), function ($query) use (
                $request,
                $priceField,
                $salePriceField
            ) {

                $query->whereRaw(
                    "COALESCE(
                    NULLIF({$salePriceField}, 0),
                    {$priceField}
                ) <= ?",
                    [$request->max_price]
                );
            })

            /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

            ->when(
                $request->sort === 'name_asc',
                fn ($query) => $query->orderBy('name', 'asc')
            )

            ->when(
                $request->sort === 'name_desc',
                fn ($query) => $query->orderBy('name', 'desc')
            )

            ->when(
                $request->sort === 'price_low',
                fn ($query) => $query->orderByRaw(
                    "COALESCE(
                    NULLIF({$salePriceField}, 0),
                    {$priceField}
                ) ASC"
                )
            )

            ->when(
                $request->sort === 'price_high',
                fn ($query) => $query->orderByRaw(
                    "COALESCE(
                    NULLIF({$salePriceField}, 0),
                    {$priceField}
                ) DESC"
                )
            )

            ->when(
                $request->sort === 'latest',
                fn ($query) => $query->latest()
            )

            ->when(
                ! $request->filled('sort'),
                fn ($query) => $query->latest()
            )

            ->paginate(12)
            ->withQueryString();

        return view('frontend.shop.index', compact(
            'products',
            'categories'
        ));
    }

    // Search products modal
    public function search(Request $request)
    {
        $search = trim($request->input('q'));

        $products = Product::with(['category', 'images', 'variants'])
            ->where('status', 1)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($category) use ($search) {
                            $category->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('frontend.shop.index', compact('products', 'search'));
    }

    // public function quickView(Product $product)
    // {
    //     return response()->json([
    //         'id'   => $product->id,
    //         'name' => $product->name,
    //         'slug' => $product->slug,
    //     ]);
    // }

    // public function quickView(Product $product)
    // {
    //     return view('frontend.partials.products.quick-view-content', compact('product'));
    // }

    public function quickView(Product $product)
    {
        $product->load([
            'category',
            'images',
        ]);

        $currency = session('currency', 'NGN');

        $price = $currency === 'USD'
            ? $product->price_usd
            : $product->price_ngn;

        $salePrice = $currency === 'USD'
            ? $product->sale_price_usd
            : $product->sale_price_ngn;

        $symbol = $currency === 'USD' ? '$' : '₦';

        return view('frontend.partials.products.quick-view-content', compact(
            'product',
            'currency',
            'price',
            'salePrice',
            'symbol'
        ));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $products = Product::query()
            ->where('category_id', $category->id)
            ->where('status', 1)
            ->latest()
            ->paginate(12);

        return view('frontend.shop.index', compact(
            'category',
            'products'
        ));
    }
}
