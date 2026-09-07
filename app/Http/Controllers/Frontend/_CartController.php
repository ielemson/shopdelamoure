<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::getContent();

        return view('frontend.cart.index', compact('cartItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::where('status', 1)
            ->findOrFail($request->product_id);

        $quantity = $request->quantity ?? 1;

        if ($product->stock_status !== 'in_stock' || $product->quantity < $quantity) {
            return response()->json([
                'status' => false,
                'message' => 'Product is out of stock.'
            ], 422);
        }

        $price = $product->sale_price && $product->sale_price < $product->regular_price
            ? $product->sale_price
            : $product->regular_price;

        Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $price,
            'quantity' => $quantity,
            'attributes' => [
                'image' => $product->main_image,
                'slug' => $product->slug,
            ],
            'associatedModel' => $product,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product added to cart.',
            'cart_count' => Cart::getTotalQuantity(),
            'cart_total' => number_format(Cart::getTotal(), 2),
            'side_cart_html' => view("frontend.partials.cart.side-cart")->render(),
        ]);
    }


    public function sidebar()
    {
        return response()->json([
            'status' => true,
            'cart_count' => Cart::getTotalQuantity(),
            'side_cart_html' => view("frontend.partials.cart.side-cart")->render(),
        ]);
    }
  

    public function update(Request $request)
{
    $request->validate([
        'id' => 'required',
        'quantity' => 'required|integer|min:1',
    ]);

    Cart::update($request->id, [
        'quantity' => [
            'relative' => false,
            'value' => $request->quantity,
        ],
    ]);

    $item = Cart::get($request->id);
    $itemTotal = $item ? ($item->price * $item->quantity) : 0;

    $subtotal = Cart::getSubTotal();
    $delivery = 0;
    $discount = 0;
    $total = $subtotal + $delivery - $discount;

    return response()->json([
        'status' => true,
        'message' => 'Cart updated successfully.',
        'cart_count' => Cart::getTotalQuantity(),
        'item_total' => $itemTotal,
        'subtotal' => $subtotal,
        'delivery' => $delivery,
        'discount' => $discount,
        'total' => $total,
        'cart_total' => number_format($total, 2),
        'side_cart_html' => view("frontend.partials.cart.side-cart")->render(),
    ]);
}

public function remove(Request $request)
{
    $request->validate([
        'id' => 'required',
    ]);

    Cart::remove($request->id);

    $subtotal = Cart::getSubTotal();
    $delivery = 0;
    $discount = 0;
    $total = $subtotal + $delivery - $discount;

    return response()->json([
        'status' => true,
        'message' => 'Product removed from cart.',
        'cart_count' => Cart::getTotalQuantity(),
        'subtotal' => $subtotal,
        'delivery' => $delivery,
        'discount' => $discount,
        'total' => $total,
        'cart_total' => number_format($total, 2),
        'side_cart_html' => view('frontend.partials.cart.side-cart')->render(),
    ]);
}

    public function clear()
    {
        Cart::clear();

        return response()->json([
            'status' => true,
            'message' => 'Cart cleared.',
            'cart_count' => 0,
            'cart_total' => '0.00',
        ]);
    }
}
