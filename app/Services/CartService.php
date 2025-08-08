<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function addToCart($userId, $productId, $qty)
    {
        return DB::transaction(function () use ($userId, $productId, $qty) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            if ($qty > $product->stock) {
                throw ValidationException::withMessages([
                    'qty' => 'This product out off stock'
                ]);
            }

            // Get or create cart for user
            $cart = Cart::firstOrCreate(['user_id' => $userId]);

            // Find or intialize new cart item
            $cartItem = CartItem::firstOrNew([
                'cart_id'    => $cart->id,
                'product_id' => $productId,
            ]);

            $cartItem->qty   = min(($cartItem->qty ?? 0) + $qty, $product->stock);
            $cartItem->name  = $product->name;
            $cartItem->price = $product->price;
            $cartItem->save();

            // Update cart total
            $this->updateCartTotal($cart);

            return $cartItem;
        });
    }

    public function deleteCartItem($userId, $cartItemId)
    {
        return DB::transaction(function () use ($userId, $cartItemId) {
            $cart = Cart::where('user_id', $userId)->first();

            if (!$cart) {
                throw ValidationException::withMessages([
                    'cart' => 'Cart not found.'
                ]);
            }

            $cartItem = CartItem::where('id', $cartItemId)->where('cart_id', $cart->id)->first();

            if (!$cartItem) {
                throw ValidationException::withMessages([
                    'cart_item' => 'Cart item not found, please try again!'
                ]);
            }

            $cartItem->delete();

            $this->updateCartTotal($cart);

            return true;
        });
    }

    public function updateCartTotal(Cart $cart)
    {
        $total = $cart->cart_items()->with('product')->get()->sum(function ($item) {
            return $item->qty * $item->product->price;
        });

        $cart->total_price = $total;
        $cart->total_item  = $cart->cart_items()->count();;
        $cart->save();
    }
}
