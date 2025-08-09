<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('product_category');

        if ($request->has('product_category_id')) {
            $query->where('product_category_id', $request->input('product_category_id'));
        }

        if ($request->filled('name')) {
            $name = trim($request->input('name'));
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($name) . '%']);
        }

        $products = $query->get();

        return response()->json([
            'status' => 'success',
            'products' => $products,
        ]);
    }
}
