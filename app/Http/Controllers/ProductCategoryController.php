<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
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
