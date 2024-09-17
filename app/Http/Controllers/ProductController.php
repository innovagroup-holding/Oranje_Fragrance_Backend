<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // public function getAllProducts()
    // {
    //     $products = Product::with('tags', 'category')->get();
    //     return response()->json([
    //         'success' => 1,
    //         'result' => $products,
    //         'message' => __('messages.products_retrieved_successfully'),
    //     ], 200);
    // }

    // public function getProductsByCategory($categoryId)
    // {
    //     $products = Product::where('category_id', $categoryId)->with('tags')->paginate(10);
    //     return response()->json([
    //         'success' => 1,
    //         'result' => $products,
    //         'message' => __('messages.products_retrieved_successfully'),
    //     ], 200);
    // }
    // public function getProductsByTag($tagId)
    // {
    //     $products = Product::with('category')->whereHas('tags', function ($query) use ($tagId) {
    //         $query->where('tag_id', $tagId);
    //     })->paginate(10);
    //     return response()->json([
    //         'success' => 1,
    //         'result' => $products,
    //         'message' => __('messages.products_retrieved_successfully'),
    //     ], 200);
    // }
    // public function getProductsByCategoryAndTag(Request $request)
    // {

    //     $categoryId = $request->query('category');
    //     $tagId = $request->query('tag');
    //     $limit = $request->query('limit', 10);

    //     $query = Product::query();

    //     if ($categoryId) {
    //         $query->where('category_id', $categoryId);
    //     }

    //     if ($tagId) {
    //         $query->whereHas('tags', function ($tagQuery) use ($tagId) {
    //             $tagQuery->where('tag_id', $tagId);
    //         });
    //     }

    //     $products = $query->limit($limit)->with('tags')->get();

    //     return response()->json([
    //         'success' => 1,
    //         'result' => $products,
    //         'message' => __('messages.products_retrieved_successfully'),
    //     ], 200);
    // }
    // public function getHeroProducts()
    // {
    //     $heroProducts = Product::with('tags', 'category')->where('is_hero', true)
    //         ->get();

    //     return response()->json([
    //         'success' => 1,
    //         'result' => $heroProducts,
    //         'message' => __('messages.success'),
    //     ]);
    // }
    public function getProducts(Request $request)
    {

        $categoryId = $request->query('category');
        $tagId = $request->query('tag');
        $limit = $request->query('limit');
        $isHero = $request->query('only_hero_products', false);


        $query = Product::query();


        if ($categoryId) {
            error_log('category_id');
            $query->where('category_id', $categoryId);
        }


        if ($tagId) {
            error_log('tag_id');
            $query->whereHas('tags', function ($tagQuery) use ($tagId) {
                $tagQuery->where('tag_id', $tagId);
            });
        }


        if ($isHero) {
            error_log('is_hero');
            $query->where('is_hero', true);
        }

        $query->with(['tags', 'category', 'images']);


        if ($limit) {
            error_log('limit');
            $products = $query->limit($limit)->get();
        } else {
            error_log('paginate');
            $products = $query->paginate(10);
        }


        return response()->json([
            'success' => 1,
            'result' => $products,
            'message' => __('messages.products_retrieved_successfully'),
        ], 200);
    }

    public function storeProduct(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image' // Validation for multiple images
        ]);

        // Generate the next unique code (PR0001, PR0002, etc.)
        $latestProduct = Product::latest('id')->first();
        $nextCode = $latestProduct ? 'PR' . str_pad($latestProduct->id + 1, 4, '0', STR_PAD_LEFT) : 'PR0001';

        // Create a new product instance
        $product = new Product;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->unique_code = $nextCode;
        $product->category_id = $request->category_id;
        $product->save();

        // Check if images are provided and store them
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                $originalFileName = $image->getClientOriginalName();
                $path = $image->storeAs('images/products', $originalFileName, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => 'storage/' . $path
                ]);
            }
        }

        // Return a success response
        return response()->json([
            'success' => 1,
            'message' => __('messages.product_created_successfully'),
            'result' => $product
        ], 200);
    }
}