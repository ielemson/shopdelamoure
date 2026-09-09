<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    /**
     * Toggle Product In Compare
     */
    public function toggle(Request $request, Product $product)
    {
        $compare = session()->get('compare', []);

        /*
        |--------------------------------------------------------------------------
        | Remove From Compare
        |--------------------------------------------------------------------------
        */

        if (in_array($product->id, $compare)) {

            $compare = array_values(
                array_filter($compare, function ($id) use ($product) {
                    return (int) $id !== (int) $product->id;
                })
            );

            session()->put('compare', $compare);

            return response()->json([
                'success' => true,
                'compared' => false,
                'count' => count($compare),
                'message' => 'Product removed from compare.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Maximum Compare Limit
        |--------------------------------------------------------------------------
        */

        if (count($compare) >= 4) {

            return response()->json([
                'success' => false,
                'limit' => true,
                'count' => count($compare),
                'message' => 'You can compare a maximum of 4 products.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Add To Compare
        |--------------------------------------------------------------------------
        */

        $compare[] = $product->id;

        $compare = array_values(
            array_unique($compare)
        );

        session()->put('compare', $compare);

        return response()->json([
            'success' => true,
            'compared' => true,
            'count' => count($compare),
            'message' => 'Product added to compare.',
        ]);
    }

    /**
     * Display Compare Page
     */
    public function index()
    {
        $compareIds = session()->get('compare', []);

        $products = Product::query()
            ->whereIn('id', $compareIds)
            ->get()
            ->sortBy(function ($product) use ($compareIds) {
                return array_search($product->id, $compareIds);
            })
            ->values();

        return view('frontend.compare.index', compact('products'));
    }
}
