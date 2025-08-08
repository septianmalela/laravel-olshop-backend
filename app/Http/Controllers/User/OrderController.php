<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function index()
    {
        $user   = Auth::guard('api')->user();
        $orders = $user->orders()->with('order_items')->get();

        return response()->json([
            'status' => 'success',
            'orders' => $orders,
        ]);
    }

    public function create_order()
    {
        $user  = Auth::guard('api')->user();
        $order = (new OrderService)->createOrder($user->id);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order->load('order_items'),
        ]);
    }

    public function show($id)
    {
        $user  = Auth::guard('api')->user();
        $order = $user->orders()->with('order_items.product')->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ], 404);
        } else {
            return response()->json([
                'status' => 'success',
                'order' => $order,
            ]);
        }

    }
}
