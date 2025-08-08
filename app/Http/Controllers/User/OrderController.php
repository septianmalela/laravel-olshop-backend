<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $product_categories = ProductCategory::all();
        return response()->json([
            'status' => 'success',
            'product_categories' => $product_categories,
        ]);
    }
}
