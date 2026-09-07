<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
class CategoryProductController extends Controller
{
    public function show(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $categories = Category::where('status', 1)
            ->withCount(['products' => function ($query) {
                $query->where('status', 1);
            }])
            ->orderBy('name')
            ->get();

        $products = Product::with(['category', 'images'])
            ->where('category_id', $category->id)
            ->where('status', 1)

            ->when($request->min_price, function ($query) use ($request) {
                $query->whereRaw('COALESCE(sale_price, regular_price) >= ?', [
                    $request->min_price
                ]);
            })

            ->when($request->max_price, function ($query) use ($request) {
                $query->whereRaw('COALESCE(sale_price, regular_price) <= ?', [
                    $request->max_price
                ]);
            })

            ->when($request->sort === 'name_asc', fn ($q) => $q->orderBy('name', 'asc'))
            ->when($request->sort === 'name_desc', fn ($q) => $q->orderBy('name', 'desc'))
            ->when($request->sort === 'price_low', fn ($q) => $q->orderByRaw('COALESCE(sale_price, regular_price) ASC'))
            ->when($request->sort === 'price_high', fn ($q) => $q->orderByRaw('COALESCE(sale_price, regular_price) DESC'))
            ->when(!$request->sort, fn ($q) => $q->latest())

            ->paginate(12)
            ->withQueryString();

        return view('frontend.product.category', compact(
            'category',
            'categories',
            'products'
        ));
    }
}