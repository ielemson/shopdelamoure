<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;


class FrontendController extends Controller
{

    public function index()
    {

        // Categories
        $categories = Category::query()
            ->select([
                'id',
                'name',
                'slug',
                'image',
            ])
            ->where('status', 1)
            ->where('is_featured', 1)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(8)
            ->get();

        /*
|--------------------------------------------------------------------------
| Homepage Products
|--------------------------------------------------------------------------
*/

        $productQuery = function () {

            return Product::with([
                'category',

                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order');
                },

                'variants',
            ])
                ->where('status', 1);
        };


        /*
|--------------------------------------------------------------------------
| Standard Product Sections
|--------------------------------------------------------------------------
|
| These sections all use:
| frontend.partials.products
|
| which in turn uses:
| frontend.partials.product-card
|
*/

        $productSections = [

            [
                'title' => 'Featured Products',

                'subtitle' => 'Discover our specially selected fragrances.',

                'products' => $productQuery()
                    ->where('is_featured', 1)
                    ->latest()
                    ->take(8)
                    ->get(),
            ],

            [
                'title' => 'New Arrivals',

                'subtitle' => 'Discover the latest additions to our collection.',

                'products' => $productQuery()
                    ->where('is_new_arrival', 1)
                    ->latest()
                    ->take(8)
                    ->get(),
            ],

            [
                'title' => 'Trending Now',

                'subtitle' => 'Explore the fragrances everyone is talking about.',

                'products' => $productQuery()
                    ->where('is_trending', 1)
                    ->latest()
                    ->take(8)
                    ->get(),
            ],

        ];


        /*
|--------------------------------------------------------------------------
| Most Loved / Best Sellers
|--------------------------------------------------------------------------
|
| This is kept separate because it uses the special
| Most Loved layout instead of the standard slider.
|
*/

        $bestSellers = $productQuery()
            ->where('is_best_seller', 1)
            ->latest()
            ->take(6)
            ->get();


        /*
|--------------------------------------------------------------------------
| Homepage
|--------------------------------------------------------------------------
*/

        return view('frontend.index', compact(
            'categories',
            'productSections',
            'bestSellers'
        ));
    }

    /**
     * About Us Page
     */
    public function about()
    {
        $categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        return view('frontend.about', compact('categories'));
    }

    /**
     * Contact Us Page
     */
    public function contact()
    {
        $categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        return view('frontend.contact', compact('categories'));
    }
}
