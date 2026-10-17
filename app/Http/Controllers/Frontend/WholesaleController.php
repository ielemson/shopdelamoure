<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WholesaleController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'sort' => [
                'nullable',
                Rule::in(['latest', 'name_asc', 'name_desc', 'price_low', 'price_high']),
            ],
        ]);

        if (
            isset($filters['min_price'], $filters['max_price'])
            && $filters['max_price'] < $filters['min_price']
        ) {
            throw ValidationException::withMessages([
                'max_price' => 'The maximum price must be at least the minimum price.',
            ]);
        }

        $search = trim($filters['q'] ?? '');
        $sort = $filters['sort'] ?? 'latest';
        $currency = 'NGN';
        $symbol = '₦';

        $categories = Category::query()
            ->where('status', 1)
            ->withCount([
                'products as wholesale_products_count' => function ($query) {
                    $query->wholesale();
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $query = Product::query()
            ->wholesale()
            ->select('products.*')
            ->selectRaw($this->basePriceExpression().' AS wholesale_from_price_ngn')
            ->with([
                'category',
                'subcategory',
                'images' => function ($query) {
                    $query->orderByDesc('is_primary')->orderBy('sort_order');
                },
                'wholesalePriceTiers',
                'activeVariants' => function ($query) {
                    $query->where('wholesale_enabled', true)
                        ->where('wholesale_price_ngn', '>', 0)
                        ->with('wholesalePriceTiers')
                        ->orderByDesc('is_default');
                },
            ]);

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('products.name', 'like', "%{$search}%")
                    ->orWhere('products.sku', 'like', "%{$search}%")
                    ->orWhere('products.short_description', 'like', "%{$search}%")
                    ->orWhereHas('activeVariants', function ($query) use ($search) {
                        $query->where('wholesale_enabled', true)
                            ->where('wholesale_price_ngn', '>', 0)
                            ->where(function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%")
                                    ->orWhere('sku', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if (! empty($filters['category'])) {
            $slug = $filters['category'];
            $query->where(function ($query) use ($slug) {
                $query->whereHas('category', fn ($query) => $query->where('slug', $slug))
                    ->orWhereHas('subcategory', fn ($query) => $query->where('slug', $slug));
            });
        }

        // Prices are NGN wholesale unit prices at the product minimum.
        // Higher quantity discounts are shown on the wholesale details page.
        if (isset($filters['min_price'])) {
            $query->whereRaw(
                $this->basePriceExpression().' >= ?',
                [$filters['min_price']]
            );
        }

        if (isset($filters['max_price'])) {
            $query->whereRaw(
                $this->basePriceExpression().' <= ?',
                [$filters['max_price']]
            );
        }

        switch ($sort) {
            case 'name_asc':
                $query->orderBy('products.name');
                break;
            case 'name_desc':
                $query->orderByDesc('products.name');
                break;
            case 'price_low':
                $query->orderBy('wholesale_from_price_ngn');
                break;
            case 'price_high':
                $query->orderByDesc('wholesale_from_price_ngn');
                break;
            default:
                $query->orderByDesc('products.created_at');
                break;
        }

        $products = $query->orderByDesc('products.id')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.wholesale.index', compact(
            'products',
            'categories',
            'search',
            'sort',
            'currency',
            'symbol'
        ));
    }

    public function show(Product $product)
    {
        // Recheck eligibility so draft or disabled products cannot be opened
        // through a direct URL. Route binding uses the product slug.
        $product = Product::query()
            ->wholesale()
            ->whereKey($product->id)
            ->with([
                'category',
                'subcategory',
                'images' => function ($query) {
                    $query->orderByDesc('is_primary')->orderBy('sort_order');
                },
                'wholesalePriceTiers',
                'activeVariants' => function ($query) {
                    $query->where('wholesale_enabled', true)
                        ->where('wholesale_price_ngn', '>', 0)
                        ->with('wholesalePriceTiers')
                        ->orderByDesc('is_default');
                },
            ])
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->wholesale()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->select('products.*')
            ->selectRaw($this->basePriceExpression().' AS wholesale_from_price_ngn')
            ->with([
                'category',
                'images',
                'activeVariants' => function ($query) {
                    $query->where('wholesale_enabled', true)
                        ->where('wholesale_price_ngn', '>', 0);
                },
            ])
            ->latest()
            ->take(8)
            ->get();

        $currency = 'NGN';
        $symbol = '₦';

        return view('frontend.wholesale.show', compact(
            'product',
            'relatedProducts',
            'currency',
            'symbol'
        ));
    }

    /**
     * Minimum base price across eligible variants, or the simple product price.
     * This expression contains fixed identifiers; filter values are bound.
     */
    private function basePriceExpression(): string
    {
        return <<<'SQL'
CASE
    WHEN products.has_variants = 1 THEN (
        SELECT MIN(wholesale_variant.wholesale_price_ngn)
        FROM product_variants AS wholesale_variant
        WHERE wholesale_variant.product_id = products.id
          AND wholesale_variant.is_active = 1
          AND wholesale_variant.wholesale_enabled = 1
          AND wholesale_variant.wholesale_price_ngn > 0
    )
    ELSE products.wholesale_price_ngn
END
SQL;
    }
}
