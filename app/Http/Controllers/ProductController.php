<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('product_category')->get();
        return response()->json([
            'status' => 'success',
            'products' => $products,
        ]);
    }
}
