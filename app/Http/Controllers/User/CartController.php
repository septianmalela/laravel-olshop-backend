<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Cart;
use App\Services\CartService;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::guard('api')->user();
        $user->load('cart.cart_items');

        $cart  = $user->cart;
        // handle if cart doesn't exist
        $items = $user->cart ? $user->cart->items : collect();

        return response()->json([
            'status' => 'success',
            'cart'   => $cart,
            'items'  => $items
        ]);
    }

    public function add_to_cart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1'
        ]);

        $user = Auth::guard('api')->user();

        $cartItem = (new CartService)->addToCart($user->id, $request->product_id, $request->qty);

        return response()->json([
            'status'    => 'success',
            'message'   => 'Item added to cart',
            'cart'      => $user->cart()->get(),
            'cart_item' => $cartItem->load('product')
        ]);
    }

    public function delete_cart_item($id)
    {
        $user     = Auth::guard('api')->user();
        $cartItem = (new CartService)->deleteCartItem($user->id, $id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Cart item deleted successfully',
        ]);
    }
}
