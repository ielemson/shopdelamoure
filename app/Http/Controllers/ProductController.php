<?php
    
namespace App\Http\Controllers;
    
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{ 



// public function show($slug)
// {
//     $product = Product::with(['category', 'images'])
//         ->where('slug', $slug)
//         ->where('status', 1)
//         ->firstOrFail();

//     return view('frontend.product.show', compact('product'));
// }



public function show($slug)
{
    $product = Product::with(['category', 'subcategory', 'images'])
        ->where('slug', $slug)
        ->where('status', 1)
        ->firstOrFail();

    $relatedProducts = Product::with(['category', 'images'])
        ->where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->where('status', 1)
        ->latest()
        ->take(8)
        ->get();

    return view('frontend.product.show', compact('product', 'relatedProducts'));
}



public function shop(Request $request)
{
    $categories = Category::where('status', 1)
        ->withCount(['products' => function ($query) {
            $query->where('status', 1);
        }])
        ->orderBy('name')
        ->get();

    $products = Product::with(['category', 'images'])
        ->where('status', 1)

        ->when($request->category, function ($query) use ($request) {
            $query->whereHas('category', function ($cat) use ($request) {
                $cat->where('slug', $request->category);
            });
        })

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
        ->when(!request('sort'), fn ($q) => $q->latest())

        ->paginate(12)
        ->withQueryString();

    return view('frontend.shop.index', compact('products', 'categories'));
}

}