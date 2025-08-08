<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createOrder($userId)
    {
        return DB::transaction(function () use ($userId) {
            $cart = Cart::with('cart_items.product')->where('user_id', $userId)->firstOrFail();

            if ($cart->cart_items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Cart not found',
                ]);
            }

            foreach ($cart->cart_items as $cart_item) {
                if ($cart_item->qty > $cart_item->product->stock) {
                    throw ValidationException::withMessages([
                        'stock' => "Product '{$cart_item->product->name}' out of stock",
                    ]);
                }
            }

            $order = Order::create([
                'user_id'     => $userId,
                'total_price' => $cart->total_price
            ]);

            foreach ($cart->cart_items as $cart_item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $cart_item->product_id,
                    'name'       => $cart_item->name,
                    'price'      => $cart_item->price,
                    'qty'        => $cart_item->qty,
                ]);

                $product            = $cart_item->product;
                $product->stock    -= $cart_item->qty;
                $product->sold_qty += $cart_item->qty;
                $product->save();
            }

            $cart->cart_items()->delete();
            $cart->total_price = 0;
            $cart->total_item  = 0;
            $cart->save();

            return $order;
        });
    }
}
