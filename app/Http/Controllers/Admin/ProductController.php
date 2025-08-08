<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function store(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'price'                => 'required|numeric',
            'stock'                => 'required|integer',
            'product_category_id'  => 'required|exists:product_categories,id',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name'                => $request->name,
            'price'               => $request->price,
            'stock'               => $request->stock,
            'product_category_id' => $request->product_category_id,
            'image'               => $path
        ]);
        $product->load('product_category');

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'product' => $product,
        ]);
    }

    public function show($id)
    {
        $product = Product::with('product_category')->find($id);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        } else {
            return response()->json([
                'status' => 'success',
                'product' => $product,
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $product = Product::with('product_category')->find($id);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        } else {
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $path           = $request->file('image')->store('products', 'public');
                $product->image = $path;
            }

            $product->name  = $request->name;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->product_category_id = $request->product_category_id;
            $product->save();

            return response()->json([
                'status'           => 'success',
                'message'          => 'Product updated successfully',
                'product' => $product,
            ]);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
        ]);
    }
}
