<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
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

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|unique:product_categories|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
        }

        $product_category = ProductCategory::create([
            'name'  => $request->name,
            'image' => $path
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product category created successfully',
            'product_category' => $product_category,
        ]);
    }

    public function show($id)
    {
        $product_category = ProductCategory::find($id);
        if (!$product_category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product category not found',
            ], 404);
        } else {
            return response()->json([
                'status' => 'success',
                'product_category' => $product_category,
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories')->ignore($id),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $product_category = ProductCategory::find($id);
        if (!$product_category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product category not found',
            ], 404);
        } else {
            if ($request->hasFile('image')) {
                if ($product_category->image) {
                    Storage::disk('public')->delete($product_category->image);
                }

                $path = $request->file('image')->store('categories', 'public');
                $product_category->image = $path;
            }

            $product_category->name = $request->name;
            $product_category->save();

            return response()->json([
                'status'           => 'success',
                'message'          => 'Product category updated successfully',
                'product_category' => $product_category,
            ]);
        }
    }

    public function destroy($id)
    {
        $product_category = ProductCategory::find($id);
        if (!$product_category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product category not found',
            ], 404);
        }

        $product_category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product category deleted successfully',
        ]);
    }
}
