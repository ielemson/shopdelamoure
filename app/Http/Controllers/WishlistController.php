<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Wishlist Page
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        if (auth()->check()) {

            $products = auth()->user()
                ->wishlistProducts()
                ->orderByPivot('created_at', 'desc')
                ->get();

        } else {

            $wishlistIds = collect(session()->get('wishlist', []))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $products = Product::whereIn('id', $wishlistIds)->get();
        }

        return view('frontend.wishlist.index', compact('products'));
    }

    /*
    |--------------------------------------------------------------------------
    | Add Product
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, Product $product)
    {
        if (auth()->check()) {

            Wishlist::firstOrCreate([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);

        } else {

            $wishlist = session()->get('wishlist', []);

            $wishlist = collect($wishlist)
                ->map(fn ($id) => (int) $id)
                ->push($product->id)
                ->unique()
                ->values()
                ->all();

            session()->put('wishlist', $wishlist);
        }

        return $this->response(
            $request,
            true,
            'Product added to your wishlist.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Remove Product
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, Product $product)
    {
        if (auth()->check()) {

            Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->delete();

        } else {

            $wishlist = collect(session()->get('wishlist', []))
                ->reject(fn ($id) => (int) $id === (int) $product->id)
                ->values()
                ->all();

            session()->put('wishlist', $wishlist);
        }

        return $this->response(
            $request,
            false,
            'Product removed from your wishlist.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Product
    |--------------------------------------------------------------------------
    |
    | This will be used by the heart button in product-card.blade.php.
    |
    */
    public function toggle(Request $request, Product $product)
    {
        $wishlisted = false;

        if (auth()->check()) {

            $wishlist = Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->first();

            if ($wishlist) {

                $wishlist->delete();

                $wishlisted = false;

            } else {

                Wishlist::create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                ]);

                $wishlisted = true;
            }

        } else {

            $wishlist = collect(session()->get('wishlist', []))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($wishlist->contains((int) $product->id)) {

                $wishlist = $wishlist
                    ->reject(fn ($id) => $id === (int) $product->id)
                    ->values();

                $wishlisted = false;

            } else {

                $wishlist->push((int) $product->id);

                $wishlisted = true;
            }

            session()->put(
                'wishlist',
                $wishlist->unique()->values()->all()
            );
        }

        return $this->response(
            $request,
            $wishlisted,
            $wishlisted
                ? 'Product added to your wishlist.'
                : 'Product removed from your wishlist.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Wishlist Count
    |--------------------------------------------------------------------------
    */
    private function wishlistCount(): int
    {
        if (auth()->check()) {

            return Wishlist::where('user_id', auth()->id())->count();
        }

        return collect(session()->get('wishlist', []))
            ->unique()
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */
    private function response(
        Request $request,
        bool $wishlisted,
        string $message
    ) {
        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,
                'wishlisted' => $wishlisted,
                'count' => $this->wishlistCount(),
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
